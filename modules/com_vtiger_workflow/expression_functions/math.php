<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

function __vt_add($arr) {
	if (count($arr) == 1) {
		return $arr[0];
	} else {
		if (is_numeric($arr[0]) && is_numeric($arr[1])) {
			return $arr[0]+$arr[1];
		} elseif (is_numeric($arr[0])) {
			return $arr[0];
		} elseif (is_numeric($arr[1])) {
			return $arr[1];
		} else {
			return 0;
		}
	}
}

function __vt_sub($arr) {
	if (count($arr) == 1) {
		return -$arr[0];
	} else {
		if (is_numeric($arr[0]) && is_numeric($arr[1])) {
			return $arr[0]-$arr[1];
		} elseif (is_numeric($arr[0])) {
			return $arr[0];
		} elseif (is_numeric($arr[1])) {
			return $arr[1];
		} else {
			return 0;
		}
	}
}

function __vt_mul($arr) {
	if (count($arr) == 1) {
		return 0;
	}
	return $arr[0]*$arr[1];
}

function __vt_div($arr) {
	if (count($arr) == 1 || empty($arr[1])) {
		return 0;
	}
	return $arr[0]/$arr[1];
}

function __vt_round($arr) {
	if (!is_array($arr) || empty($arr)) {
		return 0;
	}
	$decs = (isset($arr[1]) ? $arr[1] : 0);
	if (is_numeric($arr[0]) && is_numeric($decs)) {
		return round($arr[0], $decs);
	} else {
		return $arr[0];
	}
}

function __vt_ceil($num) {
	if (is_numeric($num[0])) {
		return ceil($num[0]);
	} else {
		return 0;
	}
}

function __vt_floor($num) {
	if (is_numeric($num[0])) {
		return floor($num[0]);
	} else {
		return 0;
	}
}

function __cb_modulo($arr) {
	if (count($arr) == 1 || empty($arr[1])) {
		return 0;
	}
	return $arr[0] % $arr[1];
}

function __vt_power($elements) {
	if (!empty($elements[0])) {
		$exponent = (isset($elements[1])) ? $elements[1] : 0;
		return pow($elements[0], $exponent);
	}
	return 0;
}

function __cb_number_format($arr) {
	if (!empty($arr)) {
		$number = $arr[0];
		$decimals = isset($arr[1]) ? $arr[1] : 0;
		$dec_points = isset($arr[2]) ? $arr[2] : '.';
		$thousands_sep = isset($arr[3]) ? $arr[3] : ',';
		return number_format($number, $decimals, $dec_points, $thousands_sep);
	} else {
		return '';
	}
}

function __cb_logarithm($arr) {
	if (empty($arr)) {
		return 0;
	} else {
		if (empty($arr[1])) {
			return log($arr[0]);
		} else {
			return log($arr[0], $arr[1]);
		}
	}
}

function cb_average($arr) {
	if (empty($arr)) {
		return null;
	}
	foreach ($arr as $averageValue) {
		if (!is_numeric($averageValue)) {
			return null;
		}
	}
	return array_sum($arr)/count($arr);
}
?>
