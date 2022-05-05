<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

function __vt_concat($arr) {
	return implode($arr);
}

function __vt_substring($arr) {
	if (count($arr)<2 || count($arr)>3) {
		return $arr[0];
	}
	if (count($arr)==2) {
		return substr($arr[0], $arr[1]);
	} else {
		return substr($arr[0], $arr[1], $arr[2]);
	}
}

function __cb_stringposition($arr) {
	if (count($arr)!=2) {
		return -1;
	}
	$ret = stripos($arr[0], $arr[1]);
	if ($ret === false) {
		return -1;
	} else {
		return $ret;
	}
}

function __cb_stringlength($arr) {
	return strlen($arr[0]);
}

function __cb_stringreplace($arr) {
	return str_replace($arr[0], $arr[1], $arr[2]);
}

function __cb_regexreplace($arr) {
	return preg_replace('/'.$arr[0].'/', $arr[1], $arr[2]);
}

function __cb_randomstring($arr) {
	if (empty($arr) || empty($arr[0])) {
		$arr[0] = 10;
	}
	return bin2hex(random_bytes($arr[0]));
}

function __vt_uppercase($arr) {
	if (empty($arr)) {
		return '';
	} else {
		return strtoupper($arr[0]);
	}
}

function __vt_lowercase($arr) {
	if (empty($arr)) {
		return '';
	} else {
		return strtolower($arr[0]);
	}
}

function __vt_uppercasefirst($arr) {
	if (empty($arr)) {
		return '';
	} else {
		return ucfirst($arr[0]);
	}
}

function __vt_uppercasewords($arr) {
	if (empty($arr)) {
		return '';
	} else {
		return ucwords(strtolower($arr[0]));
	}
}

function __cb_coalesce($arr) {
	$result = '';
	foreach ($arr as $value) {
		if (!empty($value)) {
			$result = $value;
			break;
		}
	}
	return $result;
}

function __cb_num2str($arr) {
	require_once 'modules/cbtranslation/number2string.php';
	$lang = (isset($arr[1]) ? $arr[1] : '');
	return number2string::convert($arr[0], $lang);
}

function __cb_translate($arr) {
	require_once 'modules/cbtranslation/cbtranslation.php';
	return cbtranslation::get($arr[0]);
}

function __cb_hash($arr) {
	if (empty($arr) || count($arr)>2) {
		return -1;
	} elseif (count($arr)==1) {
		return sha1($arr[0]);
	}
	switch ($arr[1]) {
		case 'md5':
			return md5($arr[0]);
		case 'crc32':
			return hash('crc32', $arr[0]);
		case 'sha1':
		default:
			return sha1($arr[0]);
	}
}

function __cb_jsonencode($arr) {
	return json_encode($arr[0], JSON_NUMERIC_CHECK);
}

function __cb_jsondecode($arr) {
	return json_decode($arr[0], true);
}

function __cb_implode($arr) {
	if (count($arr)!=2) {
		return '';
	}
	return implode($arr[0], $arr[1]);
}

function __cb_explode($arr) {
	if (count($arr)!=2) {
		return '';
	}
	return explode($arr[0], $arr[1]);
}

function cb_getCRMIDFromWSID($arr) {
	if (!empty($arr) && is_array($arr) && !empty($arr[0])) {
		$ret = explode('x', $arr[0]);
		if (isset($ret[1])) {
			return $ret[1];
		}
	}
	return '';
}
?>
