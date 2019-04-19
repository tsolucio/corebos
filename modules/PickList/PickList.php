<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/CommonUtils.php';
require_once 'modules/PickList/PickListUtils.php';

global $app_strings, $current_language, $currentModule, $theme, $current_user;

$smarty = new vtigerCRM_Smarty;
$smarty->assign('APP', $app_strings);

if (!is_admin($current_user)) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	die;
}

$modules = getPickListModules();
if (!empty($_REQUEST['moduleName'])) {
	$fld_module = vtlib_purify($_REQUEST['moduleName']);
} else {
	$module = array_keys($modules);
	$fld_module = $module[0];
}

if (!empty($_REQUEST['roleid'])) {
	$roleid = vtlib_purify($_REQUEST['roleid']);
} else {
	$roleid = 'H2';		//set default to CEO
}

$temp_module_strings = return_module_language($current_language, $fld_module);
$picklists_entries = getUserFldArray($fld_module, $roleid);
$value = count($picklists_entries);
if (($value % 3) != 0) {
	$value = $value + 3 - $value % 3;
}
$available_module_picklist = array();
$picklist_fields = array();
if (!empty($picklists_entries)) {
	$available_module_picklist = get_available_module_picklist($picklists_entries);
	$picklist_fields = array_chunk(array_pad($picklists_entries, $value, ''), 3);
}
$mods = array();
foreach ($modules as $lbl => $m) {
	if ($m == 'Calendar' || $m == 'Events') {
		continue;
	}
	$mods[$m] = getTranslatedString($lbl, $m);
}
$smarty->assign('MODULE_LISTS', $mods);
$smarty->assign('ROLE_LISTS', getrole2picklist());
$smarty->assign('ALL_LISTS', $available_module_picklist);

$smarty->assign('MOD', return_module_language($current_language, 'Settings'));	//the settings module language file
$smarty->assign('MOD_PICKLIST', return_module_language($current_language, 'PickList'));	//the picklist module language files
$smarty->assign('TEMP_MOD', $temp_module_strings);	//the selected modules' language file

$smarty->assign('MODULE', $fld_module);
$smarty->assign('PICKLIST_VALUES', $picklist_fields);
$smarty->assign('THEME', $theme);
$uitype = (!empty($_REQUEST['uitype']) ? vtlib_purify($_REQUEST['uitype']) : '');
$smarty->assign('UITYPE', $uitype);
$smarty->assign('SEL_ROLEID', $roleid);

if (empty($_REQUEST['directmode']) || $_REQUEST['directmode'] != 'ajax') {
	$smarty->display('modules/PickList/PickList.tpl');
} else {
	$smarty->display('modules/PickList/PickListContents.tpl');
}
?>
