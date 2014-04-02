<?php
/**
 * Dictionary generator execution
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

// It could take a lot of time
set_time_limit(0);

// Hard mode here :D
error_reporting(E_ALL);

// Define directories
define("ROOT", dirname(__FILE__).'/');
define("PATH_APP", ROOT.'app/');

// Load packages
include_once './package/autoload.php';

// Include classes
require_once(PATH_APP.'DictionaryGenerator.php');

if (isset($_POST['submit']))
{
    // Get settings

    // Launch generator
    $dictionary = new DictionaryGenerator(/* Settings */);
	$dictionary->addAlphabet("abc");
    $dictionary->generate(2, 4);
}

// Show generator form
require_once(PATH_APP.'Form.php');

