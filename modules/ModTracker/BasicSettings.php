<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'modules/ModTracker/ModTrackerUtils.php';

global $app_strings, $mod_strings, $current_language,$currentModule, $theme,$current_user;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$category = getParentTab();

$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
if (!is_admin($current_user)) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	die;
}
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('CATEGORY', $category);

$tabid = isset($_REQUEST['tabid']) ? vtlib_purify($_REQUEST['tabid']) : '';
$status = isset($_REQUEST['status']) ? vtlib_purify($_REQUEST['status']) : '';

if ($status != '' && $tabid != '') {
	ModTrackerUtils::modTrac_changeModuleVisibility($tabid, $status);
}
$infomodules = ModTrackerUtils::modTrac_getModuleinfo();
$smarty->assign('INFOMODULES', $infomodules);
$smarty->assign('MODULE', $module);

if (empty($_REQUEST['ajax']) || $_REQUEST['ajax'] != true) {
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'BasicSettings.tpl'));
} else {
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'BasicSettingsContents.tpl'));
}
?>