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

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

require_once 'modules/Vtiger/DetailView.php';

if ($focus->permissiontoedit()) {
	$smarty->assign('DETAILVIEW_AJAX_EDIT', GlobalVariable::getVariable('Application_DetailView_Inline_Edit', 1));
} else {
	$smarty->assign('DETAILVIEW_AJAX_EDIT', false); // no permission
}
$smarty->display('DetailView.tpl');
?>
