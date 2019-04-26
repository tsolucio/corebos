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
if (empty($argv[1]) || !file_exists($argv[1])) {
	exit(1);
} else {
	$handle = fopen($argv[1], 'r');
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
		$exit = (count($dups)>0 ? 1 : 0);
		exit($exit);
	} else {
		exit(1);
	}
}
?>
