<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

function __vt_equals($arr) {
	return $arr[0] == $arr[1];
}

function __cb_distinct($arr) {
	return $arr[0] != $arr[1];
}

function __vt_ltequals($arr) {
	return $arr[0] <= $arr[1];
}

function __vt_gtequals($arr) {
	return $arr[0] >= $arr[1];
}

function __vt_lt($arr) {
	return $arr[0] < $arr[1];
}

function __vt_gt($arr) {
	return $arr[0] > $arr[1];
}

?>