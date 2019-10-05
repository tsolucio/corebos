<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/

function gd_info() {
	$array = array (
		'GD Version' => '',
		'FreeType Support' => 0,
		'FreeType Support' => 0,
		'FreeType Linkage' => '',
		'T1Lib Support' => 0,
		'GIF Read Support' => 0,
		'GIF Create Support' => 0,
		'JPG Support' => 0,
		'PNG Support' => 0,
		'WBMP Support' => 0,
		'XBM Support' => 0
	);
	$gif_support = 0;

	ob_start();
	phpinfo();
	$info = ob_get_contents();
	ob_end_clean();

	foreach (explode("\n", $info) as $line) {
		if (strpos($line, "GD Version") !== false) {
			$array ['GD Version'] = trim(str_replace('GD Version', '', strip_tags($line)));
		}
		if (strpos($line, 'FreeType Support') !== false) {
			$array['FreeType Support'] = trim(str_replace('FreeType Support', '', strip_tags($line)));
		}
		if (strpos($line, 'FreeType Linkage') !== false) {
			$array['FreeType Linkage'] = trim(str_replace('FreeType Linkage', '', strip_tags($line)));
		}
		if (strpos($line, 'T1Lib Support') !== false) {
			$array['T1Lib Support'] = trim(str_replace('T1Lib Support', '', strip_tags($line)));
		}
		if (strpos($line, 'GIF Read Support') !== false) {
			$array['GIF Read Support'] = trim(str_replace('GIF Read Support', '', strip_tags($line)));
		}
		if (strpos($line, 'GIF Create Support') !== false) {
			$array['GIF Create Support'] = trim(str_replace('GIF Create Support', '', strip_tags($line)));
		}
		if (strpos($line, 'GIF Support') !== false) {
			$gif_support = trim(str_replace('GIF Support', '', strip_tags($line)));
		}
		if (strpos($line, 'JPG Support') !== false) {
			$array['JPG Support'] = trim(str_replace('JPG Support', '', strip_tags($line)));
		}
		if (strpos($line, 'PNG Support') !== false) {
			$array['PNG Support'] = trim(str_replace('PNG Support', '', strip_tags($line)));
		}
		if (strpos($line, 'WBMP Support') !== false) {
			$array['WBMP Support'] = trim(str_replace('WBMP Support', '', strip_tags($line)));
		}
		if (strpos($line, 'XBM Support') !== false) {
			$array['XBM Support'] = trim(str_replace('XBM Support', '', strip_tags($line)));
		}
	}

	if ($gif_support === 'enabled') {
		$array['GIF Read Support'] = 1;
		$array['GIF Create Support'] = 1;
	}

	if ($array['FreeType Support'] === 'enabled') {
		$array['FreeType Support'] = 1;
	}

	if ($array['T1Lib Support'] === 'enabled') {
		$array['T1Lib Support'] = 1;
	}

	if ($array['GIF Read Support'] === 'enabled') {
		$array['GIF Read Support'] = 1;
	}

	if ($array['GIF Create Support'] === 'enabled') {
		$array['GIF Create Support'] = 1;
	}

	if ($array['JPG Support'] === 'enabled') {
		$array['JPG Support'] = 1;
	}

	if ($array['PNG Support'] === 'enabled') {
		$array['PNG Support'] = 1;
	}

	if ($array['WBMP Support'] === 'enabled') {
		$array['WBMP Support'] = 1;
	}

	if ($array['XBM Support'] === 'enabled') {
		$array['XBM Support'] = 1;
	}

	return $array;
}