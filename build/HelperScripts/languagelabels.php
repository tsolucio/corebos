<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS HelperScripts.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : coreBOS Language Repeated Labels Utility
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Language Repeated Labels</title>
<style type="text/css">
	body { font-size: 80%; font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif; }
</style>
</head>
<body>
<?php
if (empty($_REQUEST['langfile']) || !file_exists($_REQUEST['langfile'])) {
	echo "<br>languagelabels script reads a language file and returns a list of repeated labels<br>";
	echo "It is mandatory to launch this script from the root of your install and you must give the full path to the file with the parameter <b>langfile</b><br><br>";
	echo "languagelabels.php?langfile=modules/HelpDesk/language/en_us.lang.php<br>";
} else {
	$handle = fopen($_REQUEST['langfile'], 'r');
	if ($handle) {
		$lbls = array();
		while (($line = fgets($handle)) !== false) {
			if (strpos($line, '=>')>0) {
				list($label, $void) = explode('=>', $line);
				$label = trim($label);
				$label = trim($label, "'");
				$label = trim($label, '"');
				$lbls[] = $label;
			}
		}
		fclose($handle);
		$frec = array_count_values($lbls);
		$dups = array_filter($frec, function ($f) {
			return ($f>1);
		});
		if (count($dups)>0) {
			echo '<h3>Duplicate labels FOUND</h3>';
			foreach ($dups as $lbl => $f) {
				echo "'$lbl' found $f times<br>";
			}
		} else {
			echo '<h3>NO duplicate labels in the file</h3>';
		}
	} else {
		echo '<h3>ERROR reading file!</h3>';
	}
}
?>
</body>
</html>
