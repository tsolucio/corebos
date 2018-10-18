<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $adb, $singlepane_view;

if (isset($_REQUEST['dup_check']) && $_REQUEST['dup_check'] != '') {
	$value = vtlib_purify($_REQUEST['accountname']);
	$query = 'SELECT 1 FROM vtiger_account,vtiger_crmentity WHERE accountname=? and vtiger_account.accountid=vtiger_crmentity.crmid and vtiger_crmentity.deleted!=1';
	$params = array($value);
	$id = vtlib_purify($_REQUEST['record']);
	if (isset($id) && $id !='') {
		$query .= ' and vtiger_account.accountid != ?';
		$params[] = $id;
	}
	$result = $adb->pquery($query, $params);
	if ($adb->num_rows($result) > 0) {
		echo $mod_strings['LBL_ACCOUNT_EXIST'];
	} else {
		echo 'SUCCESS';
	}
	die;
}

require_once 'modules/Vtiger/Save.php';
?>
