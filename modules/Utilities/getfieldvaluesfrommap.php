<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $adb, $current_user;

$sourceModule = $adb->sql_escape_string(vtlib_purify($_REQUEST['sourceModule']));
$inputVal = $adb->sql_escape_string(vtlib_purify($_REQUEST['inputVal']));
$fieldname = $adb->sql_escape_string(vtlib_purify($_REQUEST['fieldname']));
$bmapname = $sourceModule . '_FieldInfo';
$cbMapid = GlobalVariable::getVariable('BusinessMapping_FieldInfo', cbMap::getMapIdByName($bmapname), $sourceModule, $current_user->id);

if (strlen($inputVal) > 0) {
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$cbMapFI = $cbMap->FieldInfo();
		$cbMapFI = $cbMapFI['fields'];
		if (in_array($cbMapFI[$fieldname], $cbMapFI)) {
			$values = $cbMapFI[$fieldname]['combobox'][$sourceModule];
			echo $values;
		} else {
			echo '';
		}
	}
}