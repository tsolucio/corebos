<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('Smarty_setup.php');

include_once dirname(__FILE__) . '/SMSNotifier.php';

global $currentModule, $mod_strings, $app_strings, $current_user, $adb;

$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
$idstring = vtlib_purify($_REQUEST['idstring']);
$idstring = trim($idstring, ';');
$idlist = getSelectedRecords($_REQUEST,$_REQUEST['sourcemodule'],$idstring,$excludedRecords);//explode(';', $idstring);

$sourcemodule = vtlib_purify($_REQUEST['sourcemodule']);
$message = vtlib_purify($_REQUEST['message']);

$phonefields = vtlib_purify($_REQUEST['phonefields']);
$phonefields = trim($phonefields, ';');
$phonefieldlist = explode(';', $phonefields);

$tonumbers = array();
$recordids = array();

foreach($idlist as $recordid) {
	$focusInstance = CRMEntity::getInstance($sourcemodule);
	$focusInstance->retrieve_entity_info($recordid, $sourcemodule);
	$numberSelected = false;
	foreach($phonefieldlist as $fieldname) {
		if(!empty($focusInstance->column_fields[$fieldname])) {
			$tonumbers[] = $focusInstance->column_fields[$fieldname];
			$numberSelected = true;
		}
	}
	if($numberSelected) {
		$recordids[] = $recordid;
	}	
}

if(!empty($tonumbers)) {
	SMSNotifier::sendsms($message, $tonumbers, $current_user->id, $recordids, $sourcemodule);	
}

echo "DONE";

?>
