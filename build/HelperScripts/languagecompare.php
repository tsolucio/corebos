<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Documentation.
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
 *  Module       : coreBOS Language Compare Utility
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Compare Language Files</title>
	<style type="text/css">
	  body { font-size: 80%; font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif; }
	  ul#tabs { list-style-type: none; margin: 30px 0 0 0; padding: 0 0 0.3em 0;  }
	  ul#tabs li { display: block; }
	  ul#tabs li a { color: #42454a; background-color: #dedbde; border: 1px solid #c9c3ba; border-bottom: none; padding: 0.3em; text-decoration: none;  line-height: 30px; }
	  ul#tabs li a:hover { background-color: #f1f0ee; }
	  ul#tabs li a.selected { color: #000; background-color: #f1f0ee; font-weight: bold; padding: 0.7em 0.3em 0.38em 0.3em; }
	  div.tabContent { border: 1px solid #c9c3ba; padding: 0.5em; background-color: #f1f0ee; }
	  div.tabContent.hide { display: none; }
	  input { border:none; background-color:#f1f0ee;}
	</style>
</head>
<body>
<?php
function diff($lo, $ln) {
	$equ = array_intersect_key($lo, $ln);
	$ins = array_diff_key($ln, $lo);
	$del = array_diff_key($lo, $ln);
	echo "<h2>Insert</h2>";
	echo "<pre>";
	foreach ($ins as $key => $value) {
		echo "'$key' => '$value',\n";
	}
	echo "</pre>";
	echo "<h2>Remove</h2>";
	foreach ($del as $key => $value) {
		echo "<p><del>$key</del> => $value</p>";
	}
}

if (empty($_REQUEST['org']) || empty($_REQUEST['dst'])) {
	echo "<br>languagecompare script compares two language files and shows you the differences between them<br>";
	echo "It's goal is to make a little easier the translation of modules<br>";
	echo "You can compare between different language files as the comparision is done on the keys, not the translations<br>";
	echo "It is mandatory to launch this script from the root of your install and you must give the full path to both files with the parameters <b>org</b> and <b>dst</b><br><br>";
	echo "languagecompare.php?org=modules/HelpDesk/language/en_us.lang.php&dst=modules/HelpDesk/language/pt_br.lang.php";
} else {
	include $_REQUEST['org'];
	$arrdest = $mod_strings;
	include $_REQUEST['dst'];
	$arrnew = $mod_strings;
	echo diff($arrdest, $arrnew);
	echo "<h2>---------</h2>";
}
?>
</body>
</html>
