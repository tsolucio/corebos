<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $mod_strings,$adb;

checkFileAccessForInclusion('include/database/PearDatabase.php');
require_once 'include/database/PearDatabase.php';

$server=vtlib_purify($_REQUEST['server']);
$port=(empty($_REQUEST['port']) ? 0 : vtlib_purify($_REQUEST['port']));
$server_username=vtlib_purify($_REQUEST['server_username']);
$server_password=vtlib_purify($_REQUEST['server_password']);
$server_type = vtlib_purify($_REQUEST['server_type']);
$server_path = isset($_REQUEST['server_path']) ? vtlib_purify($_REQUEST['server_path']) : '';
$from_email_field = vtlib_purify($_REQUEST['from_email_field']);
$smtp_auth = vtlib_purify($_REQUEST['smtp_auth']);

//Added code to send a test mail to the currently logged in user
require_once 'modules/Emails/mail.php';
global $current_user;
$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');

$to_email = getUserEmailId('id', $current_user->id);
$from_email = $to_email;
$subject = 'Test mail about the mail server configuration.';
$description = 'Dear '.$current_user->user_name.', <br><br><b> This is a test mail sent to confirm if a mail is actually being sent through the smtp server that you'
	.'have configured. </b><br>Feel free to delete this mail.<br><br>Thanks and Regards,<br> '.$HELPDESK_SUPPORT_NAME.' <br>';
if ($to_email != '') {
	$mail_status = send_mail('Users', $to_email, $current_user->user_name, $from_email, $subject, $description);
	$mail_status_str = $to_email.'='.$mail_status.'&&&';
} else {
	$mail_status_str = "'".$to_email."'=0&&&";
}
$error_str = getMailErrorString($mail_status_str);
$action = 'EmailConfig';
if ($mail_status != 1) {
	$action = 'EmailConfig&emailconfig_mode=edit&server_name='.
		urlencode(vtlib_purify($_REQUEST['server'])).'&server_user='.
		urlencode(vtlib_purify($_REQUEST['server_username'])).'&auth_check='.
		urlencode(vtlib_purify($_REQUEST['smtp_auth']));
} else {
	$idrs = $adb->pquery('select * from vtiger_systems where server_type = ?', array($server_type));
	if ($idrs && $adb->num_rows($idrs)>0) {
		$id=$adb->query_result($idrs, 0, 'id');
		$sql='update vtiger_systems set server=?, server_username=?, server_password=?, smtp_auth=?, server_type=?, server_port=?,from_email_field=? where id=?';
		$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port,$from_email_field,$id);
	} else {
		$id = $adb->getUniqueID('vtiger_systems');
		$sql='insert into vtiger_systems values(?,?,?,?,?,?,?,?,?)';
		$params = array($id, $server, $port, $server_username, $server_password, $server_type, $smtp_auth, '',$from_email_field);
	}
	$adb->pquery($sql, $params);
}
header("Location: index.php?module=Settings&parenttab=Settings&action=$action&$error_str");
?>
