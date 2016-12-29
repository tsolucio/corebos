<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('modules/Users/Users.php');
require_once('include/logging.php');
require_once('include/utils/UserInfoUtil.php');
$log = LoggerManager::getLogger('index');

global $adb;
$user_name = empty($_REQUEST['userName']) ? '' : vtlib_purify($_REQUEST['userName']);
if(isset($_REQUEST['status']) && $_REQUEST['status'] != '')
	$_REQUEST['status']= vtlib_purify ($_REQUEST['status']);
else
	$_REQUEST['status']='Active';

if(isset($_REQUEST['dup_check']) && $_REQUEST['dup_check'] != '')
{
	$user_query = "SELECT user_name FROM vtiger_users WHERE user_name =?";
	$user_result = $adb->pquery($user_query, array($user_name));
	$group_query = "SELECT groupname FROM vtiger_groups WHERE groupname =?";
	$group_result = $adb->pquery($group_query, array($user_name));
	if($adb->num_rows($user_result) > 0) {
		echo $mod_strings['LBL_USERNAME_EXIST'];
		die;
	} elseif($adb->num_rows($group_result) > 0) {
		echo $mod_strings['LBL_GROUPNAME_EXIST'];
		die;
	} else {
		echo 'SUCCESS';
		die;
	}
}
if($_REQUEST['user_role'] != '' && !is_admin($current_user) && $_REQUEST['user_role'] != $current_user->roleid){
	$log->fatal("SECURITY:Non-Admin user:". $current_user->id . " attempted to change user role");
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>SECURITY: Non-Admin user attempted to change user role</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='index.php?module=Users&action=Logout'> ".$app_strings['LBL_GO_BACK']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
	exit;
}

if((empty($_SESSION['Users_FORM_TOKEN']) || $_SESSION['Users_FORM_TOKEN']
		!== (int)$_REQUEST['form_token']) && $_REQUEST['deleteImage'] != 'true' &&
		$_REQUEST['changepassword'] != 'true') {
	header("Location: index.php?action=Error&module=Users&error_string=".urlencode($app_strings['LBL_PERMISSION']));
	die;
}

if (isset($_POST['record']) && !is_admin($current_user) && $_POST['record'] != $current_user->id) echo ("Unauthorized access to user administration.");
elseif (!isset($_POST['record']) && !is_admin($current_user)) echo ("Unauthorized access to user administration.");

$focus = new Users();
if(isset($_REQUEST["record"]) && $_REQUEST["record"] != '')
{
	$focus->mode='edit';
	$focus->id = vtlib_purify($_REQUEST["record"]);
}
else
{
	$focus->mode='';
}

if(isset($_REQUEST['deleteImage']) && $_REQUEST['deleteImage'] == 'true') {
	$focus->id = vtlib_purify($_REQUEST['recordid']);
	$focus->deleteImage();
	echo "SUCCESS";
	exit;
}

if(isset($_REQUEST['changepassword']) && $_REQUEST['changepassword'] == 'true') {
	$focus->retrieve_entity_info($_REQUEST['record'],'Users');
	$focus->id = vtlib_purify($_REQUEST['record']);
	if (isset($_REQUEST['new_password'])) {
		if (!$focus->change_password(vtlib_purify($_REQUEST['old_password']), vtlib_purify($_REQUEST['new_password']))) {
			header("Location: index.php?action=DetailView&module=Users&record=".$focus->id."&error_string=".urlencode($focus->error_string));
			exit;
		}
	}
}

//save user Image
if(empty($_REQUEST['changepassword']) || $_REQUEST['changepassword'] != 'true')
{
	if(strtolower($current_user->is_admin) == 'off' && $current_user->id != $focus->id)
	{
		$log->fatal("SECURITY:Non-Admin ". $current_user->id . " attempted to change settings for user:". $focus->id);
		header("Location: index.php?module=Users&action=Logout");
		exit;
	}
	if(strtolower($current_user->is_admin) == 'off' && isset($_POST['is_admin']) && strtolower($_POST['is_admin']) == 'on')
	{
		$log->fatal("SECURITY:Non-Admin ". $current_user->id . " attempted to change is_admin settings for user:". $focus->id);
		header("Location: index.php?module=Users&action=Logout");
		exit;
	}

	if (!isset($_POST['is_admin'])) $_REQUEST["is_admin"] = 'off';
	//Code contributed by mike crowe for rearrange the home page and tab
	if (!isset($_POST['deleted'])) $_REQUEST["deleted"] = '0';
	if (!isset($_POST['homeorder']) || $_POST['homeorder'] == "" ) $_REQUEST["homeorder"] = 'ILTI,QLTQ,ALVT,PLVT,CVLVT,HLT,OLV,GRT,OLTSO';
	if(isset($_REQUEST['internal_mailer']) && $_REQUEST['internal_mailer'] == 'on')
		$focus->column_fields['internal_mailer'] = 1;
	else
		$focus->column_fields['internal_mailer'] = 0;
	if(isset($_SESSION['internal_mailer']) && $_SESSION['internal_mailer'] != $focus->column_fields['internal_mailer'])
		coreBOS_Session::set('internal_mailer', $focus->column_fields['internal_mailer']);
	setObjectValuesFromRequest($focus);

	if(empty($focus->column_fields['roleid']) && !empty($_POST['user_role'])) {
		$focus->column_fields['roleid'] = $_POST['user_role'];
	}
	$focus->save("Users");

	$return_id = $focus->id;

	if (isset($_POST['user_name']) && isset($_POST['new_password'])) {
		$new_pass = $_POST['new_password'];
		$new_passwd = $_POST['new_password'];
		$new_pass = md5($new_pass);
		$uname = $_POST['user_name'];
		if (!$focus->change_password($_POST['confirm_new_password'], $_POST['new_password'])) {
			header("Location: index.php?action=DetailView&module=Users&record=".$focus->id."&error_string=".urlencode($focus->error_string));
			exit;
		}
	}

	if(isset($focus->id) && $focus->id != '')
	{
		if(isset($_POST['group_name']) && $_POST['group_name'] != '') {
			updateUsers2GroupMapping($_POST['group_name'],$focus->id);
		}
	}
}
if(isset($_POST['return_module']) && $_POST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "Users";
if(isset($_POST['return_action']) && $_POST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_POST['return_id']) && $_POST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);
if(isset($_POST['parenttab'])) $parenttab = getParentTab();

$log->debug("Saved record with id of ".$return_id);

// Check to see if the mode is User Creation and if yes, then sending the email notification to the User with Login details.
$error_str = '';
if($_REQUEST['mode'] == 'create') {
	global $app_strings, $mod_strings, $default_charset;
	require_once('modules/Emails/mail.php');
	$user_emailid = $focus->column_fields['email1'];
	// send email on Create user only if NOTIFY_OWNER_EMAILS is set to true

	$subject = $mod_strings['User Login Details'];
	$email_body = $app_strings['MSG_DEAR']." ". $focus->column_fields['last_name'] .",<br><br>";
	$email_body .= $app_strings['LBL_PLEASE_CLICK'] . " <a href='" . $site_URL . "' target='_blank'>"
				. $app_strings['LBL_HERE'] . "</a> " . $mod_strings['LBL_TO_LOGIN'] . "<br><br>";
	$email_body .= $mod_strings['LBL_USER_NAME'] . " : " . $focus->column_fields['user_name'] . "<br>";
	$email_body .= $mod_strings['LBL_PASSWORD'] . " : " . $focus->column_fields['user_password'] . "<br>";
	$email_body .= $mod_strings['LBL_ROLE_NAME'] . " : " . getRoleName($_POST['user_role']) . "<br>";
	$email_body .= "<br>" . $app_strings['MSG_THANKS'] . "<br>" . $current_user->user_name;
	//$email_body = htmlentities($email_body, ENT_QUOTES, $default_charset);  // not needed anymore, PHPMailer takes care of it

	$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail','support@your_support_domain.tld','HelpDesk');
	$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name','your-support name','HelpDesk');
	$mail_status = send_mail('Users',$user_emailid,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
	if($mail_status != 1) {
		$mail_status_str = $user_emailid."=".$mail_status."&&&";
		$error_str = getMailErrorString($mail_status_str);
	}
}
$location = "Location: index.php?action=".vtlib_purify($return_action)."&module=".vtlib_purify($return_module)."&record=".vtlib_purify($return_id);

if(empty($_REQUEST['modechk']) || $_REQUEST['modechk'] != 'prefview') {
	$location .= "&parenttab=".vtlib_purify($parenttab);
}

if ($error_str != '') {
	$user = $focus->column_fields['user_name'];
	$location .= "&user=$user&$error_str";
}

header($location);
?>
