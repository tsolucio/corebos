<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$candelete = true;
$cyp = CRMEntity::getInstance('CobroPago');
$record = vtlib_purify($_REQUEST['record']);
$cyprs = $adb->pquery('select paid from vtiger_cobropago where cobropagoid=?', array($record));
if ($cyprs && $adb->num_rows($cyprs)==1) {
	$cyp->column_fields['paid'] = $adb->query_result($cyprs, 0, 0);
	$candelete = $cyp->permissiontoedit();
} else {
	$candelete = false;
}
if (!$candelete) {
	$log->debug("You don't have permission to deleted CobroPago $record");
	require_once 'Smarty_setup.php';
	$smarty = new vtigerCRM_Smarty();
	global $app_strings;
	$smarty->assign('APP', $app_strings);
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	exit;
}
require_once 'modules/Vtiger/Delete.php';
?>
