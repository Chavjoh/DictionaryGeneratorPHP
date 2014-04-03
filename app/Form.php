<?php
/**
 * Form to generate dictionary
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

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />

	<title>PHP Benchmark</title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!-- jQuery -->
	<script type="text/javascript" src="package/jquery/jquery-2.1.0.min.js"></script>

	<!-- Bootstrap -->
	<link href="package/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

	<!-- Main design -->
	<link href="style/design.css" rel="stylesheet" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script type="text/javascript">
		$(function() {
			$("#submit").click(function() {

				var docHeight = $(document).height();

				$("#loading")
					.height(docHeight)
					.css({
						'opacity' : 0.4,
						'position': 'absolute',
						'top': 0,
						'left': 0,
						'background-color': 'black',
						'width': '100%',
						'z-index': 5000
					});

				$("#loading").show();
			});
		});
	</script>
</head>
<body>
	<div class="container">
		<div class="header">
			<h3 class="text-muted">DictionaryGeneratorPHP</h3>
		</div>

		<div class="jumbotron">
			<h1>Dictionary Generator</h1>

			<p class="lead">
				...
			</p>
		</div>

		<form action="" method="post">
			<div class="row marketing">
				<div class="col-lg-6">
					<fieldset>
						<legend>Basic alphabet</legend>
						<label><input type="checkbox" name="alphabet[]" value="letters" checked="checked" />Letters</label> <br />
						<!-- Majuscules / Minuscules -->
						<label><input type="checkbox" name="alphabet[]" value="numbers" checked="checked" />Numbers</label> <br />
						<!-- 0 1 2 3 4 5 6 7 8 9 -->
						<label><input type="checkbox" name="alphabet[]" value="special" />Special characters</label>
						<!-- - _ , . : ; + " * # % & / ( ) = ? ` ' ^ ! $ [ ] { } < > @ -->
					</fieldset>

					<fieldset>
						<legend>Size</legend>
						<label>Minimum size <input type="text" name="minimum" value="<?= $minSize; ?>" /> <br /></label>
						<label>Maximum size <input type="text" name="maximum" value="<?= $maxSize; ?>" /></label>
					</fieldset>
				</div>

				<div class="col-lg-6">
					<fieldset>
						<legend>Results</legend>
						<label><input type="radio" name="result" value="show" checked="checked" />Show results in page</label> <br />
						<label><input type="radio" name="result" value="download" />Download results</label> <br />
					</fieldset>
					<fieldset>
						<legend>Launch</legend>
						Start generator. It could take awhile. <br />
						<input type="submit" name="submit" id="submit" value="Generate dictionary" />
					</fieldset>
				</div>
			</div>
		</form>
		<div id="loading">
			<img src="image/loading.gif" alt="Loading" />
		</div>
		<div class="row marketing">
			Memory usage :
			<?= convert(memory_get_usage(true)) ?>
			<div class="result">
				<?php
				if (isset($_POST['submit']))
				{
					$dictionary->showResult();
				}
				?>
			</div>
		</div>
	</div>
</body>
</html>