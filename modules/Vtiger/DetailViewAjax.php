<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;
$modObj = CRMEntity::getInstance($currentModule);
$ajaxaction = $_REQUEST['ajxaction'];
if ($ajaxaction == 'DETAILVIEW') {
	$crmid = vtlib_purify($_REQUEST['recordid']);
	$fieldname = vtlib_purify($_REQUEST['fldName']);
	$fieldvalue = utf8RawUrlDecode($_REQUEST['fieldValue']);
	if ($crmid != '') {
		$modObj->retrieve_entity_info($crmid, $currentModule);
		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = 'edit';
		list($saveerror,$errormessage,$error_action,$returnvalues) = $modObj->preSaveCheck($_REQUEST);
		if ($saveerror) { // there is an error so we report error
			echo ':#:ERR'.$errormessage;
		} else {
			$modObj->save($currentModule);
			if ($modObj->id != '') {
				echo ':#:SUCCESS';
			} else {
				echo ':#:FAILURE';
			}
		}
	} else {
		echo ':#:FAILURE';
	}
} elseif ($ajaxaction == 'LOADRELATEDLIST' || $ajaxaction == 'DISABLEMODULE') {
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>
