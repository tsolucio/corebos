<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailScannerInfo.php';
require_once 'modules/Settings/MailScanner/core/MailScannerRule.php';
require_once 'Smarty_setup.php';

global $app_strings, $mod_strings, $currentModule, $theme, $current_language;

$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

$scannername = vtlib_purify($_REQUEST['scannername']);
$scannerruleid = isset($_REQUEST['ruleid']) ? vtlib_purify($_REQUEST['ruleid']) : '';
$scannerinfo = new Vtiger_MailScannerInfo($scannername);
$scannerrule = new Vtiger_MailScannerRule($scannerruleid);

$smarty->assign('SCANNERINFO', $scannerinfo->getAsMap());
$smarty->assign('SCANNERRULE', $scannerrule);

//Set Assigned To
$result = get_group_options();
if ($result) {
	$nameArray = $adb->fetch_array($result);
}

$assigned_user_id = empty($value) ? $current_user->id : $value;
$ua = get_user_array(false, 'Active', $assigned_user_id);
$users_combo = get_select_options_array($ua, $assigned_user_id);
$ga = get_group_array(false, 'Active', $assigned_user_id);
$groups_combo = get_select_options_array($ga, $assigned_user_id);

$smarty->assign('fldvalue', $users_combo);
$smarty->assign('secondvalue', $groups_combo);

$smarty->display('MailScanner/MailScannerRuleEdit.tpl');
?>
