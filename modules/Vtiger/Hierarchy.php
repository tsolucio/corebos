<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/utils/utils.php';
global $app_strings, $default_charset, $currentModule, $current_user, $theme;
$smarty = new vtigerCRM_Smarty;
if (!isset($where)) {
	$where = '';
}
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
global $current_language;
$smarty->assign('LANGUAGE', $current_language);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('LBL_HIERARCHY', getTranslatedString('LBL_HIERARCHY'));
$check_button = Button_Check($currentModule);
$check_button['Import'] = 'no';
$check_button['Export'] = 'no';
$check_button['DuplicatesHandling'] = 'no';
$check_button['moduleSettings'] = 'no';
$smarty->assign('CHECK', $check_button);
$focus = CRMEntity::getInstance($currentModule);
$recordid = vtlib_purify($_REQUEST['recordid']);
if (!empty($recordid)) {
	$hierarchy = $focus->getHierarchy($recordid, $currentModule);
}
$smarty->assign('HEADERS', json_encode($hierarchy['header'], JSON_PRETTY_PRINT));
$smarty->assign('MODULE_HIERARCHY', json_encode($hierarchy['entries'], JSON_PRETTY_PRINT));
$smarty->display('Actions/Hierarchy.tpl');
?>