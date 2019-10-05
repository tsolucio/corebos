<?php
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
header('X-Frame-Options: DENY');
define('IN_LOGIN', true);

include_once 'vtlib/Vtiger/Language.php';

// Retrieve username and password from the session if possible.
if (isset($_SESSION['login_user_name'])) {
	if (isset($_REQUEST['default_user_name'])) {
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	} elseif (isset($_REQUEST['login_user_name'])) {
		$login_user_name = trim(vtlib_purify($_REQUEST['login_user_name']), '"\'');
	} else {
		$login_user_name = trim(vtlib_purify($_SESSION['login_user_name']), '"\'');
	}
} else {
	if (isset($_REQUEST['default_user_name'])) {
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	} elseif (isset($_REQUEST['ck_login_id_vtiger'])) {
		$login_user_name = getUserName(vtlib_purify($_REQUEST['ck_login_id_vtiger']));
	} else {
		$login_user_name = $default_user_name;
	}
	coreBOS_Session::set('login_user_name', $login_user_name);
}

// Retrieve username and password from the session if possible.
if (isset($_SESSION['login_password'])) {
	$login_password = trim(vtlib_purify($_SESSION['login_password']), '"\'');
} else {
	$login_password = $default_password;
	coreBOS_Session::set('login_password', $login_password);
}

if (isset($_SESSION['login_error'])) {
	$login_error = $_SESSION['login_error'];
} else {
	$login_error = '';
}

require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
require_once 'vtigerversion.php';

global $currentModule, $adb, $coreBOS_app_version, $current_language;
$image_path='include/images/';

$current_language = $default_language;
$currentModule = 'Users';
$app_strings = return_application_language($current_language);
$mod_strings = return_module_language($current_language, $currentModule);
$current_module_strings = return_module_language($current_language, $currentModule);

$smarty=new vtigerCRM_Smarty;
$smarty->assign('APP', $app_strings);
$smarty->assign('LBL_CHARSET', $default_charset);

$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('VTIGER_VERSION', $coreBOS_app_version);

$companyDetails = retrieveCompanyDetails();
$smarty->assign('COMPANY_DETAILS', $companyDetails);
$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name, 'Users', Users::getActiveAdminId()));
$smarty->assign('currentLoginIP', Vtiger_Request::get_ip());
$smarty->assign('LOGIN_ERROR', $login_error);
$currentYear = date('Y');
$smarty->assign('currentYear', $currentYear);
$smarty->assign('LoginPage', $cbodLoginPage);
$smarty->assign('CAN_UNBLOCK', (empty($_SESSION['can_unblock']) ? 'false' : 'true'));
$smarty->display('Login.tpl');
coreBOS_Session::delete('login_error');
?>