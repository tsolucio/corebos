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

define("IN_LOGIN", true);

include_once('vtlib/Vtiger/Language.php');

// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_user_name"]))
{
	if (isset($_REQUEST['default_user_name']))
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	else
		$login_user_name =  trim(vtlib_purify($_REQUEST['login_user_name']), '"\'');
}
else
{
	if (isset($_REQUEST['default_user_name']))
	{
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	}
	elseif (isset($_REQUEST['ck_login_id_vtiger'])) {
		$login_user_name = getUserName(vtlib_purify($_REQUEST['ck_login_id_vtiger']));
	}
	else
	{
		$login_user_name = $default_user_name;
	}
	$_session['login_user_name'] = $login_user_name;
}

$current_module_strings['VLD_ERROR'] = base64_decode('UGxlYXNlIHJlcGxhY2UgdGhlIFN1Z2FyQ1JNIGxvZ29zLg==');

// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_password"]))
{
	$login_password = trim(vtlib_purify($_REQUEST['login_password']), '"\'');
}
else
{
	$login_password = $default_password;
	$_session['login_password'] = $login_password;
}

if(isset($_SESSION["login_error"]))
{
	$login_error = $_SESSION['login_error'];
}

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once("include/utils/utils.php");
require_once 'vtigerversion.php';

global $currentModule, $moduleList, $adb, $coreBOS_app_version;
$image_path="include/images/";

$app_strings = return_application_language('en_us');

$smarty=new vtigerCRM_Smarty;
$smarty->assign("APP", $app_strings);

if(isset($app_strings['LBL_CHARSET'])) {
	$smarty->assign("LBL_CHARSET", $app_strings['LBL_CHARSET']);
} else {
	$smarty->assign("LBL_CHARSET", $default_charset);
}

$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("VTIGER_VERSION", $coreBOS_app_version);

// We check if we have the two new logo fields > if not we create them
$cnorg=$adb->getColumnNames('vtiger_organizationdetails');
if (!in_array('faviconlogo', $cnorg)) {
	$adb->query('ALTER TABLE `vtiger_organizationdetails` ADD `frontlogo` VARCHAR(150) NOT NULL, ADD `faviconlogo` VARCHAR(150) NOT NULL');
}
$sql="select * from vtiger_organizationdetails";
$result = $adb->pquery($sql, array());
//Handle for allowed organation logo/logoname likes UTF-8 Character
$companyDetails = array();
$companyDetails['name'] = $adb->query_result($result,0,'organizationname');
$companyDetails['website'] = $adb->query_result($result,0,'website');
$companyDetails['logo'] = decode_html($adb->query_result($result,0,'logoname'));
if(decode_html($adb->query_result($result,0,'faviconlogo'))=='')
$favicon='themes/images/favicon.ico';
else $favicon='test/logo/'.decode_html($adb->query_result($result,0,'faviconlogo'));
$companyDetails['favicon'] = $favicon;
$smarty->assign("COMPANY_DETAILS",$companyDetails);
$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name',$coreBOS_app_name));
if(isset($login_error) && $login_error != "") {
	$smarty->assign("LOGIN_ERROR", $login_error);
}
$currentYear = date('Y');
$smarty->assign('currentYear',$currentYear);
$smarty->display('Login.tpl');

?>