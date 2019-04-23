<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

$local_log = LoggerManager::getLogger('EmailsAjax');
global $adb, $currentModule;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = isset($_REQUEST['ajxaction']) ? vtlib_purify($_REQUEST['ajxaction']) : '';
if ($ajaxaction == 'DETAILVIEW') {
	$crmid = vtlib_purify($_REQUEST['recordid']);
	$tablename = vtlib_purify($_REQUEST['tableName']);
	$fieldname = vtlib_purify($_REQUEST['fldName']);
	$fieldvalue = vtlib_purify($_REQUEST['fieldValue']);
	if ($crmid != '') {
		$modObj->retrieve_entity_info($crmid, 'Emails');
		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = 'edit';
		$modObj->save('Emails');
		if ($modObj->id != '') {
			echo ':#:SUCCESS';
		} else {
			echo ':#:FAILURE';
		}
	} else {
		echo ':#:FAILURE';
	}
} elseif ($ajaxaction == 'LOADRELATEDLIST' || $ajaxaction == 'DISABLEMODULE') {
	require_once 'include/ListView/RelatedListViewContents.php';
} elseif (isset($_REQUEST['ajaxmode']) && $_REQUEST['ajaxmode'] == 'qcreate') {
	require_once 'quickcreate.php';
} else {
	require_once 'include/Ajax/CommonAjax.php';
}
?>
