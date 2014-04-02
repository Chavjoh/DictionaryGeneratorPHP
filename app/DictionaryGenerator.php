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
	protected $generatedDictionary;
	protected $alphabet;

	public function __construct()
	{
		$this->generatedDictionary = array();
		$this->alphabet = "";
	}

	public function addAlphabet($content)
	{
		$this->alphabet .= $content;
	}

	public function generate($minSize, $maxSize)
	{
		if (empty($this->alphabet))
			throw new Exception("Empty alphabet");

		$alphabetSize = strlen($this->alphabet);
		$currentAlphabetIndex = array();

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

					$inversePosition = $currentSize - $currentPosition - 1;

					$indexValue = floor(($currentIndex / pow($alphabetSize, $inversePosition)))  % $alphabetSize;
					//echo $indexValue.'<br />';
					$word .= $this->alphabet[$indexValue];
				}

				$this->generatedDictionary[] = $word;
				$currentIndex += 1;
			}
			while ($currentIndex < $currentIndexMax);
		}
	}

	public function getDictionary()
	{
		return $this->generatedDictionary;
	}
} 