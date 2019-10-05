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

$parent_tab=getParentTab();
$smarty->assign('CATEGORY', $parent_tab);

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

// Pass on the authenticated user language
global $current_language;
$smarty->assign('LANGUAGE', $current_language);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule), 'Contacts');
$check_button = Button_Check($currentModule);
$check_button['Import'] = 'no';
$check_button['Export'] = 'no';
$check_button['DuplicatesHandling'] = 'no';
$check_button['moduleSettings'] = 'no';
$smarty->assign('CHECK', $check_button);

$focus = CRMEntity::getInstance($currentModule);
$contactid = vtlib_purify($_REQUEST['contactid']);
if (!empty($contactid)) {
	$hierarchy = $focus->getContactHierarchy($contactid);
}
$smarty->assign('CONTACT_HIERARCHY', $hierarchy);
$smarty->display('modules/Contacts/ContactHierarchy.tpl');
?>