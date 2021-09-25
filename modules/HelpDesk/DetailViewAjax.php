<?php
/************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';
global $currentModule, $mod_strings, $app_strings, $current_user, $theme;

$smarty = new vtigerCRM_Smarty();
$modObj = CRMEntity::getInstance($currentModule);
$ajaxaction = $_REQUEST['ajxaction'];
if ($ajaxaction == 'DETAILVIEW') {
	$crmid = vtlib_purify($_REQUEST['recordid']);
	$fieldname = vtlib_purify($_REQUEST['fldName']);
	$fieldvalue = utf8RawUrlDecode($_REQUEST['fieldValue']);
	if ($crmid != '') {
		$modObj->retrieve_entity_info($crmid, $currentModule);
		// Added to avoid the comment save, when we edit other fields through ajax edit
		if ($fieldname != 'comments') {
			$modObj->column_fields['comments'] = '';
		}
		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = 'edit';
		list($saveerror,$errormessage,$error_action,$returnvalues) = $modObj->preSaveCheck($_REQUEST);
		if ($saveerror) { // there is an error so we report error
			echo ':#:ERR'.$errormessage;
		} else {
			$modObj->save($currentModule);
			if ($modObj->id != '') {
				echo ':#:SUCCESS:#:';
				require_once 'modules/'.$currentModule.'/DetailView.php';
			} else {
				echo ':#:FAILURE';
			}
		}
	} else {
		echo ':#:FAILURE';
	}
} elseif ($ajaxaction == 'DETAILVIEWLOAD') {
	echo ':#:SUCCESS:#:';
	require_once 'modules/'.$currentModule.'/DetailView.php';
} elseif ($ajaxaction == 'LOADRELATEDLIST' || $ajaxaction == 'DISABLEMODULE') {
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>
