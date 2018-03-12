<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'modules/PickList/PickListUtils.php';

global $adb, $current_user;

$moduleName = vtlib_purify($_REQUEST['moduleName']);
$tableName = vtlib_purify($_REQUEST['fieldname']);
$roleid = vtlib_purify($_REQUEST['roleid']);
$values = vtlib_purify($_REQUEST['values']);
$otherRoles = vtlib_purify($_REQUEST['otherRoles']);

if (empty($tableName)) {
	echo 'Table name is empty';
	exit;
}

$values = json_decode($values, true);

$result = $adb->pquery('SELECT * FROM vtiger_picklist WHERE name = ?', array($tableName));
if ($adb->num_rows($result) > 0) {
	$picklistid = $adb->query_result($result, 0, 'picklistid');
}

if (!empty($roleid)) {
	assignValues($picklistid, $roleid, $values, $tableName);
}

$otherRoles = json_decode($otherRoles, true);
if (!empty($otherRoles)) {
	foreach ($otherRoles as $role) {
		assignValues($picklistid, $role, $values, $tableName);
	}
}

echo 'SUCCESS';

function assignValues($picklistid, $roleid, $values, $tableName) {
	global $adb;
	$count = count($values);
	//delete older values
	$adb->pquery('DELETE FROM vtiger_role2picklist WHERE roleid=? AND picklistid=?', array($roleid,$picklistid));

	//insert the new values
	$inssql = 'INSERT INTO vtiger_role2picklist VALUES (?,?,?,?)';
	for ($i=0; $i<$count; $i++) {
		$tableName = $adb->sql_escape_string($tableName);
		$result = $adb->pquery("SELECT * FROM vtiger_$tableName WHERE $tableName=?", array($values[$i]));
		if ($adb->num_rows($result) > 0) {
			$picklistvalueid = $adb->query_result($result, 0, 'picklist_valueid');
			$sortid = $i+1;
			$adb->pquery($inssql, array($roleid, $picklistvalueid, $picklistid, $sortid));
		}
	}
}
?>