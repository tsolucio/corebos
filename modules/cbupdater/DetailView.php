<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log, $adb;

$smarty = new vtigerCRM_Smarty();

if (!empty($_REQUEST['record'])) {
	$record = vtlib_purify($_REQUEST['record']);
	$cbu = $adb->pquery('select appcs from vtiger_cbupdater where cbupdaterid=?', array($record));
	if ($cbu && $adb->num_rows($cbu)>0) {
		if ($cbu->fields['appcs']=='1') {
			include 'modules/cbupdater/cbupdButtons.php';

			require_once 'modules/Vtiger/DetailView.php';

			$singlepane_view = 'true';
			$smarty->assign('SinglePane_View', $singlepane_view);
			$smarty->assign('EDIT_PERMISSION', 'no');
			$smarty->assign('CREATE_PERMISSION', 'no');
			$smarty->assign('DELETE', 'notpermitted');
			$smarty->assign('IS_REL_LIST', isPresentRelatedLists($currentModule));
		} else {
			require_once 'modules/Vtiger/DetailView.php';
		}
		$smarty->display('DetailView.tpl');
	} else {
		require_once 'Smarty_setup.php';
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('OPERATION_MESSAGE', getTranslatedString('LBL_PERMISSION'));
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	}
} else {
	$smarty->assign('APP', $app_strings);
	$smarty->assign('OPERATION_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
}
?>
