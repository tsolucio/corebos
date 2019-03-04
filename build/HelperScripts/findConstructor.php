<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS.
 * The MIT License (MIT)
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *************************************************************************************************/
// run: php findConstructor.php /project_path

$project_path = $argv[1];

$output = shell_exec('grep -r --include=*.php "class " '.$project_path);
echo "STARTING SCRIPT... \n";

$array = explode("\n", $output);
$files = array();
foreach ($array as $key => $value) {
	if (!empty($value)) {
		$field = array();
		$delimiter_pos = stripos($value, ':');
		$file_path = substr($value, 0, $delimiter_pos);
		if (!empty($file_path)) {
			$field['filepath'] = $file_path;
			$class_end = stripos($value, 'class') + strlen("class");
			$second_part = trim(substr($value, $class_end));
			$class_name = strtok($second_part, " ");
			if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $class_name)) {
				$field['class_name'] = $class_name;
				$files[] = $field;
			}
		}
	}
}

$i = 0;
foreach ($files as $file) {
	$f = "function ".$file['class_name'];
	$out = shell_exec('grep "'.$f.'(" '.$file['filepath']);
	if (!empty($out)) {
		$i++;
		echo "\n> ".$i."\n";
		echo ">   filepath => " .$file['filepath']."\n";
		echo ">   class_name => ".$file['class_name']."\n\n";
		echo "----------------------------------------------------- \n";
	}
}
echo "\n";
?>
