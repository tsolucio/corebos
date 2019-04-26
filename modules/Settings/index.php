<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/utils/utils.php';
global $mod_strings, $app_strings, $theme, $adb;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$tool_buttons = array(
'EditView' => 'yes',
'CreateView' => 'yes',
'index' => 'yes',
'Import' => 'yes',
'Export' => 'yes',
'Merge' => 'yes',
'DuplicatesHandling' => 'yes',
'Calendar' => 'yes',
'moduleSettings' => 'yes',
);

$smarty = new vtigerCRM_Smarty;
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', 'Settings');
$smarty->assign('SINGLE_MOD', getTranslatedString('Settings', 'Settings'));
$smarty->assign('CATEGORY', 'Settings');
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('CUSTOM_MODULE', false);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('IMAGES', 'themes/images/');
$smarty->assign('BLOCKS', getSettingsBlocks());
$smarty->assign('FIELDS', getSettingsFields());
$smarty->assign('NUMBER_OF_COLUMNS', 4);	//this is the number of columns in the settings page

$smarty->display('Settings.tpl');
?>
