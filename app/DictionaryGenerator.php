<?php
/**
 * Dictionary generator class
 *
 * @package DictionaryGeneratorPHP
 * @author Chavaillaz Johan
 * @since 1.0.0
 * @license Apache 2.0 License
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

class DictionaryGenerator
{
	const PART_SIZE = 1000000; // Reduce to avoid memory problems
	const PART_MERGE = 'dictionary.txt';
	const PART_ZIP = 'dictionary.zip';
	const PATTERN_ALL = '*.txt';
	const PATTERN_PART = 'part*.txt';

	protected $folder;
	protected $generatedDictionary;
	protected $alphabet;

	/**
	 * Create a new dictionary generator.
	 * Session need to be initialized (used for multi-user)
	 */
	public function __construct()
	{
		// Variable initialization
		$this->generatedDictionary = array();
		$this->alphabet = "";
		$this->folder = './data/'.session_id().'/';

		// Create folder for
		if (!is_dir($this->folder))
		{
			// Recursive directory creation
			mkdir($this->folder, 0777, true);
		}

		// Clean all (old generated dictionary)
		$this->clean(true);
	}

	/**
	 * Path to zip file that contains a compressed version of the text file
	 *
	 * @return string Path to zip file
	 */
	public function getZipPath()
	{
		return $this->folder.self::PART_ZIP;
	}

	/**
	 * Path to text file that contains all generated values.
	 * Created during merge process of all parts.
	 *
	 * @return string Path to text file
	 */
	public function getTxtPath()
	{
		return $this->folder.self::PART_MERGE;
	}

	/**
	 * Add new alphabet used for generation
	 *
	 * @param $content String that contains all characters used in alphabet
	 */
	public function addAlphabet($content)
	{
		$this->alphabet .= $content;
	}

	/**
	 * Generate all possible words with indicated parameters and given alphabet
	 *
	 * @param int $minSize Minimum size of the generated words
	 * @param int $maxSize Maximum size of the generated words
	 * @throws Exception Exception if generation settings are invalid
	 */
	public function generate($minSize, $maxSize)
	{
		if (empty($this->alphabet))
			throw new Exception("Empty alphabet");

		if ($minSize > $maxSize)
			throw new Exception("Invalid size");

		// Variable initialization
		$alphabetSize = strlen($this->alphabet);
		$currentAlphabetIndex = array();

		if (DEBUG) echo 'AlphabetSize : '.$alphabetSize.'-'.$this->alphabet.'-<br />';

		// Foreach size needed
		for ($currentSize = $minSize; $currentSize <= $maxSize; ++$currentSize)
		{
			$currentIndex = 0;

			// Calculate the number of possibilities
			$currentIndexMax = pow($alphabetSize, $currentSize);

			if (DEBUG) echo '<h1>New size '.$currentSize.' -> '.($currentSize * $alphabetSize).'</h1>';

			do
			{
				$word = "";

				if (DEBUG) echo '<h2>New Word : '.$currentIndex.'</h2>';

				// Current position in current size to be generated
				for ($currentPosition = 0; $currentPosition < $currentSize; ++$currentPosition)
				{
					// Initialization of alphabet index for each position
					if (!isset($currentAlphabetIndex[$currentPosition]))
						$currentAlphabetIndex[$currentPosition] = 0;

					// Calculate reverse position to calculate indexValue
					// Reverse because we begin to change on the right of the word
					$reversePosition = $currentSize - 1 - $currentPosition;

					// Calculate the index of the current character in alphabet to use
					$indexValue = floor(($currentIndex / pow($alphabetSize, $reversePosition)))  % $alphabetSize;

					// Add the character of the alphabet to the word
					$word .= $this->alphabet[$indexValue];
				}

				$this->generatedDictionary[] = $word;
				$currentIndex += 1;

				// Save in part file to save memory space if needed
				if (($currentIndex % DictionaryGenerator::PART_SIZE) == 0)
					$this->savePart();
			}
			// While until the number of possibilities is reached
			while ($currentIndex < $currentIndexMax);
		}

		$this->savePart();
		$this->mergePart();
		$this->compress();
		$this->clean(false);
	}

	/**
	 * Save current generated words to a file and clean memory.
	 * Used to avoid memory problem (big data).
	 */
	public function savePart()
	{
		static $partNumber = 0;

		// Send content of the generated dictionary in memory to the file, separated by new line
		file_put_contents($this->folder.'part'.$partNumber.'.txt', implode("\n", $this->generatedDictionary));

		// Reset generated dictionary in memory
		$this->generatedDictionary = array();

		++$partNumber;
	}

	/**
	 * Clean all files or just part files
	 *
	 * @param bool $cleanAll
	 */
	public function clean($cleanAll = false)
	{
		// Choose pattern to use to delete files
		if ($cleanAll)
			$pattern = self::PATTERN_ALL;
		else
			$pattern = self::PATTERN_PART;

		// Get all files corresponding to the pattern
		$files = glob($this->folder.$pattern);

		foreach ($files AS $file)
		{
			unlink($file);
		}
	}

	/**
	 * Merge all parts generated into one
	 */
	public function mergePart()
	{
		// Get all part files
		$partFiles = glob($this->folder.self::PATTERN_ALL);

		// Delete old merged file
		if (file_exists($this->getTxtPath()))
		{
			unlink($this->getTxtPath());
		}

		// Create new one
		$out = fopen($this->getTxtPath(), "w");

		foreach ($partFiles as $file)
		{
			// Open each part file
			$in = fopen($file, "r");

			// Read line by line and write to merged file
			// Be careful, line by line to avoid memory problem
			while ($line = fgets($in))
			{
				fwrite($out, $line);
			}

			// Close part file
			fclose($in);
		}

		// Close merged file
		fclose($out);
	}

	/**
	 * Compress merged file to send it to user (for bandwidth)
	 *
	 * @throws Exception Creating or archiving problem
	 */
	public function compress()
	{
		// Variable initialization
		$zip = new ZipArchive();
		$zipPath = $this->getZipPath();
		$dictionaryPath = $this->getTxtPath();

		// Delete old zip file
		if (file_exists($zipPath))
		{
			unlink($zipPath);
		}

		// Create new one
		if ($zip->open($zipPath, ZIPARCHIVE::CM_PKWARE_IMPLODE) !== true)
		{
			throw new Exception("Could not create $zipPath");
		}

		// Archive dictionary files
		if (!$zip->addFile($dictionaryPath, self::PART_MERGE))
		{
			throw new Exception("Error archiving $dictionaryPath in $zipPath");
		}

		// Close zip file
		$zip->close();
	}
} 