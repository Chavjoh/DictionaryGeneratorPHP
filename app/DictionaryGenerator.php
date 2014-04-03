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
	const FOLDER = './data/';
	const PART_SIZE = 1000000; // Reduce to avoid memory problems
	const PART_MERGE = 'dictionary.txt';
	const PART_ZIP = 'dictionary.zip';
	const PATTERN_ALL = '*.txt';
	const PATTERN_PART = 'part*.txt';

	protected $generatedDictionary;
	protected $alphabet;

	public function __construct()
	{
		$this->generatedDictionary = array();
		$this->alphabet = "";

		if (!is_dir(self::FOLDER))
		{
			mkdir(self::FOLDER);
		}

		$this->clean(true);
	}

	public function getZipPath()
	{
		return self::FOLDER.self::PART_ZIP;
	}

	public function getTxtPath()
	{
		return self::FOLDER.self::PART_MERGE;
	}

	public function addAlphabet($content)
	{
		$this->alphabet .= $content;
	}

	public function generate($minSize, $maxSize)
	{
		if (empty($this->alphabet))
			throw new Exception("Empty alphabet");

		if ($minSize > $maxSize)
			throw new Exception("Invalid size");

		$alphabetSize = strlen($this->alphabet);
		$currentAlphabetIndex = array();

		//echo 'AlphabetSize : '.$alphabetSize.'-'.$this->alphabet.'-<br />';

		for ($currentSize = $minSize; $currentSize <= $maxSize; ++$currentSize)
		{
			$currentIndex = 0;
			$currentIndexMax = pow($alphabetSize, $currentSize);
			//echo '<h1>New size '.$currentSize.' -> '.($currentSize * $alphabetSize).'</h1>';
			do
			{
				$word = "";
				//echo '<h2>New Word : '.$currentIndex.'</h2>';

				// Current position in current size to be generated
				for ($currentPosition = 0; $currentPosition < $currentSize; ++$currentPosition)
				{
					// Initialization of alphabet index for each position
					if (!isset($currentAlphabetIndex[$currentPosition]))
						$currentAlphabetIndex[$currentPosition] = 0;

					$inversePosition = $currentSize - 1 - $currentPosition;

					$indexValue = floor(($currentIndex / pow($alphabetSize, $inversePosition)))  % $alphabetSize;
					//echo $indexValue.'<br />';
					$word .= $this->alphabet[$indexValue];
				}

				$this->generatedDictionary[] = $word;
				$currentIndex += 1;

				if (($currentIndex % DictionaryGenerator::PART_SIZE) == 0)
					$this->savePart();
			}
			while ($currentIndex < $currentIndexMax);
		}

		$this->savePart();
		$this->mergePart();
		$this->compress();
		$this->clean(false);
	}

	public function launchDownload()
	{
		// Redirect to zip file
		header('Location: '.$this->getZipPath());

		// Stop script
		exit(0);
	}

	public function showResult()
	{
		echo implode('<br />', $this->generatedDictionary);
	}

	/**
	 * To avoid memory problem
	 */
	public function savePart()
	{
		static $partNumber = 0;

		file_put_contents(DictionaryGenerator::FOLDER.'part'.$partNumber.'.txt', implode("\n", $this->generatedDictionary));
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
		if ($cleanAll)
			$pattern = self::PATTERN_ALL;
		else
			$pattern = self::PATTERN_PART;

		$files = glob(DictionaryGenerator::FOLDER.$pattern);

		foreach ($files AS $file)
		{
			unlink($file);
		}
	}

	public function mergePart()
	{
		$partFiles = glob(self::FOLDER.self::PATTERN_ALL);

		$out = fopen($this->getTxtPath(), "w");

		foreach ($partFiles as $file)
		{
			$in = fopen($file, "r");

			while ($line = fgets($in))
			{
				fwrite($out, $line);
			}

			fclose($in);
		}

		fclose($out);
	}

	public function compress()
	{
		$zip = new ZipArchive();
		$zipPath = $this->getZipPath();
		$dictionaryPath = $this->getTxtPath();

		if (file_exists($zipPath))
		{
			unlink($zipPath);
		}

		if ($zip->open($zipPath, ZIPARCHIVE::CM_PKWARE_IMPLODE) !== true)
		{
			throw new Exception("Could not Create $zipPath");
		}

		if (!$zip->addFile($dictionaryPath, self::PART_MERGE))
		{
			throw new Exception("Error archiving $dictionaryPath in $zipPath");
		}

		$zip->close();
	}

	public function getDictionary()
	{
		return $this->generatedDictionary;
	}
} 