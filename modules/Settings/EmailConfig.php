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
global $mod_strings, $app_strings, $adb, $theme;

//Display the mail send status
$smarty = new vtigerCRM_Smarty;
if (!empty($_REQUEST['mail_error'])) {
	require_once 'modules/Emails/mail.php';
	$error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $mod_strings['LBL_MAILSENDERROR'];
	$smarty->assign('ERROR_MESSAGE', $mod_strings['LBL_TESTMAILSTATUS'].':&nbsp;'.$error_msg);
}

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

if (isset($_REQUEST['emailconfig_mode']) && $_REQUEST['emailconfig_mode'] == 'delete') {
	$result = $adb->pquery('delete from vtiger_systems where server_type = ?', array('email'));
	$_REQUEST['emailconfig_mode'] = 'view';
}

$result = $adb->pquery('select * from vtiger_systems where server_type = ?', array('email'));
$mail_server = $adb->query_result($result, 0, 'server');
$mail_server_username = $adb->query_result($result, 0, 'server_username');
$mail_server_password = $adb->query_result($result, 0, 'server_password');
$smtp_auth = $adb->query_result($result, 0, 'smtp_auth');
$from_email_field = $adb->query_result($result, 0, 'from_email_field');
$servername = isset($_REQUEST['server_name']) ? vtlib_purify($_REQUEST['server_name']) : '';
$username = isset($_REQUEST['server_user']) ? vtlib_purify($_REQUEST['server_user']) : '';

if (!empty($servername)) {
	$smarty->assign('MAILSERVER', $servername);
} elseif (isset($mail_server)) {
	$smarty->assign('MAILSERVER', $mail_server);
}

if (!empty($username)) {
	$smarty->assign('USERNAME', $username);
} elseif (isset($mail_server_username)) {
	$smarty->assign('USERNAME', $mail_server_username);
}

if (isset($mail_server_password)) {
	$smarty->assign('PASSWORD', $mail_server_password);
}
if (isset($_REQUEST['from_email_field'])) {
	$smarty->assign('FROM_EMAIL_FIELD', vtlib_purify($_REQUEST['from_email_field']));
} elseif (isset($from_email_field)) {
	$smarty->assign('FROM_EMAIL_FIELD', $from_email_field);
}
$SMTP_AUTH_OPTIONS = array(
	'false' => getTranslatedString('LBL_NO'),
	'true' => getTranslatedString('LBL_YES'),
	'ssl' => 'SSL '.getTranslatedString('LBL_CERT_VAL', 'Settings'),
	'sslnc' => 'SSL',
	'tls' => 'TLS '.getTranslatedString('LBL_CERT_VAL', 'Settings'),
	'tlsnc' => 'TLS',
);
if (isset($_REQUEST['auth_check'])) {
	$smarty->assign('SMTP_AUTH', vtlib_purify($_REQUEST['auth_check']));
	$smarty->assign('SMTP_AUTH_SHOW', $SMTP_AUTH_OPTIONS[vtlib_purify($_REQUEST['auth_check'])]);
} else {
	$smarty->assign('SMTP_AUTH', $smtp_auth);
	$smarty->assign('SMTP_AUTH_SHOW', (isset($SMTP_AUTH_OPTIONS[$smtp_auth]) ? $SMTP_AUTH_OPTIONS[$smtp_auth] : ''));
}

if (isset($_REQUEST['emailconfig_mode']) && $_REQUEST['emailconfig_mode'] != '') {
	$smarty->assign('EMAILCONFIG_MODE', vtlib_purify($_REQUEST['emailconfig_mode']));
} else {
	$smarty->assign('EMAILCONFIG_MODE', 'view');
}
$smarty->assign('SMTP_AUTH_OPTIONS', $SMTP_AUTH_OPTIONS);
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', $mod_strings);
$smarty->display('Settings/EmailConfig.tpl');
?>