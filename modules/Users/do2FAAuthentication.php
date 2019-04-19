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
//we don't want the parent module's string file, but rather the string file specifc to this subpanel
global $current_language;
$current_module_strings = return_module_language($current_language, 'Users');

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

global $currentModule, $adb, $coreBOS_app_version;
$image_path='include/images/';

$app_strings = return_application_language('en_us');

include_once 'modules/Users/authTypes/TwoFactorAuth/autoload.php';
use \RobThree\Auth\TwoFactorAuth;

$tfa = new TwoFactorAuth('coreBOSWebApp');
$twofasecret = coreBOS_Settings::getSetting('coreBOS_2FA_Secret_'.$focus->id, false);
if ($twofasecret===false) {
	$secret = $tfa->createSecret(160);
	$twofasecret = $secret;
	coreBOS_Settings::setSetting('coreBOS_2FA_Secret_'.$focus->id, $twofasecret);
}
$code = $tfa->getCode($twofasecret);
coreBOS_Settings::setSetting('coreBOS_2FA_Code_'.$focus->id, $code);
Users::send2FACode($code, $focus->id);

$smarty=new vtigerCRM_Smarty;
$smarty->assign('APP', $app_strings);
$smarty->assign('uname', $login_user_name);
$smarty->assign('authuserid', $focus->id);
$smarty->assign('LBL_CHARSET', $default_charset);

$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('VTIGER_VERSION', $coreBOS_app_version);

$companyDetails = retrieveCompanyDetails();
$smarty->assign('COMPANY_DETAILS', $companyDetails);
$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name, 'Users', Users::getActiveAdminId()));
$smarty->assign('LOGIN_ERROR', $login_error);
$currentYear = date('Y');
$smarty->assign('currentYear', $currentYear);
$smarty->assign('LoginPage', $cbodLoginPage.'2fa');
$smarty->display('Login.tpl');
?>
