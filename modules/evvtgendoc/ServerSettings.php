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
require_once 'vtlib/Vtiger/Module.php';
global $app_strings, $mod_strings, $current_language,$currentModule, $theme,$current_user,$log;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty;

$smarty->assign('MODULE', 'evvtgendoc');
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);

if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	$gendoc_url = GlobalVariable::getVariable('GenDoc_Convert_URL', '', 'evvtgendoc');
	$pdflinkactive = coreBOS_Settings::getSetting('cbgendoc_showpdflinks', 0)!=0 ? 'checked' : '';
	$smarty->assign('pdflinkactive', $pdflinkactive);
	$gendoc_active = coreBOS_Settings::getSetting('cbgendoc_active', 0);
	$gendoc_server = coreBOS_Settings::getSetting('cbgendoc_server', '');
	$gendoc_user = coreBOS_Settings::getSetting('cbgendoc_user', '');
	$gendoc_accesskey = coreBOS_Settings::getSetting('cbgendoc_accesskey', '');
	$active = ($gendoc_active == 1 ? 'checked' : '');
	$smarty->assign('active', $active);
	$smarty->assign('server', $gendoc_server);
	$smarty->assign('user', $gendoc_user);
	$smarty->assign('key', $gendoc_accesskey);
	$smarty->assign('gendocurl', $gendoc_url);
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'ServerSettings.tpl'));
}
?>