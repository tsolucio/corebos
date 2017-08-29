<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('modules/CustomView/CustomView.php');
global $adb,$log;

$cvid = vtlib_purify($_REQUEST['record']);
$status = vtlib_purify($_REQUEST['status']);
$module = vtlib_purify($_REQUEST['dmodule']);
$now_action = vtlib_purify($_REQUEST['action']);
if (isset($cvid) && $cvid != '') {
	$oCustomView = new CustomView($module);
	if ($oCustomView->isPermittedCustomView($cvid,$now_action,$oCustomView->customviewmodule) == 'yes') {
		$updateStatusSql = 'update vtiger_customview set status=? where cvid=? and entitytype=?';
		$updateresult = $adb->pquery($updateStatusSql, array($status, $cvid, $module));
		if(!$updateresult)
			echo ':#:FAILURE:#:';
		else 
			echo ':#:SUCCESS:#:';
	}
	else
	{
		global $app_strings;
		require_once('Smarty_setup.php');
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
		exit;
	}
}
?>