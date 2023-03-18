<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/utils/utils.php';

global $mod_strings, $app_strings, $adb, $theme, $theme_path, $image_path;

$smarty = new vtigerCRM_Smarty;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$category = vtlib_purify($_REQUEST['category']);

$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('THEME', $theme);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('MODE', 'view');
$smarty->display('Settings/import_xml.tpl');
?>