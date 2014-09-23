<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme,$current_user,$adb,$log;

require_once('Smarty_setup.php');
require('user_privileges/user_privileges_'.$current_user->id.'.php');
require_once('modules/Webforms/Webforms.php');
require_once('modules/Webforms/model/WebformsModel.php');

Webforms::checkAdminAccess($current_user);

$webforms = Webforms_Model::listAll();

$smarty = new vtigerCRM_Smarty();

$category = getParentTab();
$smarty->assign('WEBFORMS',$webforms);
$smarty->assign('ENABLED',$enabled);
$smarty->assign('ACTION','list');
$smarty->assign("THEME", $theme);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('LANGUAGE',$current_language);

$smarty->display(vtlib_getModuleTemplate($currentModule,'ListView.tpl'));
?>