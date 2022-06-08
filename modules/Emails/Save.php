<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

//check for mail server configuration through ajax
if (isset($_REQUEST['server_check']) && $_REQUEST['server_check'] == 'true') {
	if (GlobalVariable::getVariable('Debug_Email_Send_To_Inbucket', '')!='') {
		echo 'SUCCESS';
		die();
	}
	list($systemEmailClassName, $systemEmailClassPath)=cbEventHandler::do_filter('corebos.filter.systemEmailClass.getname', array('Emails', 'modules/Emails/Emails.php'));
	require_once $systemEmailClassPath;
	if (call_user_func(array($systemEmailClassName, 'emailServerCheck'))) {
		$upload_file_path = decideFilePath();
		if (!is_writable($upload_file_path)) {
			echo 'FAILURESTORAGE';
		} else {
			echo 'SUCCESS';
		}
	} else {
		echo 'FAILUREEMAIL';
	}
	die;
}

require_once 'modules/Emails/Emails.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'data/CRMEntity.php';
$focus = new Emails();

global $current_user,$mod_strings,$app_strings;
if (isset($_REQUEST['description']) && $_REQUEST['description'] !='') {
	if (GlobalVariable::getVariable('EMail_Less_Strict_ContentClean', '1', 'Emails')=='1') {
		$_REQUEST['description'] = from_html($_REQUEST['description']);
	} else {
		$_REQUEST['description'] = vtlib_purify($_REQUEST['description']);
	}
}

$all_to_ids = $_REQUEST['hidden_toid'];
$all_to_ids .= $_REQUEST['saved_toid'];
$_REQUEST['saved_toid'] = $all_to_ids;
//we always save the email with 'save' status and when it is sent it is marked as SENT
$_REQUEST['email_flag'] = 'SAVED';
setObjectValuesFromRequest($focus);
//Check if the file exists or not
if (isset($_REQUEST['filename_hidden'])) {
	$file_name = $_REQUEST['filename_hidden'];
} else {
	$file_name = $_FILES['filename']['name'];
}
$errorCode = isset($_FILES['filename']) ? $_FILES['filename']['error'] : 0;
$errormessage = '';
if ($file_name != '' && $_FILES['filename']['size'] == 0) {
	if ($errorCode == 4 || $errorCode == 0 || $errorCode == 3) {
		if ($_FILES['filename']['size'] == 0) {
			$errormessage = '<b><span style="color:red;">'.$mod_strings['LBL_PLEASE_ATTACH'].'</span></b><br>';
		}
	} elseif ($errorCode == 2) {
		$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000);
		$errormessage = '<b><span style="color:red;">'.$mod_strings['LBL_EXCEED_MAX'].$upload_maxsize.$mod_strings['LBL_BYTES'].' </span></b><br>';
	} elseif ($errorCode == 6) {
		$errormessage = '<b>'.$mod_strings['LBL_KINDLY_UPLOAD'].'</b> <br>';
	}
	if ($errormessage != '') {
		$ret_error = 1;
		$ret_parentid = vtlib_purify($_REQUEST['parent_id']);
		$ret_toadd = vtlib_purify($_REQUEST['parent_name']);
		$ret_subject = vtlib_purify($_REQUEST['subject']);
		$ret_replyto = vtlib_purify($_REQUEST['replyto']);
		$ret_ccaddress = vtlib_purify($_REQUEST['ccmail']);
		$ret_bccaddress = vtlib_purify($_REQUEST['bccmail']);
		$ret_description = vtlib_purify($_REQUEST['description']);
		echo $errormessage;
		include 'EditView.php';
		exit();
	}
}

if (isset($_FILES['filename']) && $_FILES['filename']['size'] == 0 && $_FILES['filename']['name'] != '') {
	$file_upload_error = true;
	$_FILES = '';
}

if ((isset($_REQUEST['deletebox']) && $_REQUEST['deletebox'] != null) && $_REQUEST['addbox'] == null) {
	imap_delete($mbox, $_REQUEST['deletebox']);
	imap_expunge($mbox);
	header('Location: index.php?module=Emails&action=index');
	exit();
}

function checkIfContactExists($mailid) {
	global $log;
	$log->debug('> checkIfContactExists '.$mailid);
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Contacts');
	global $adb;
	$sql = 'select contactid
		from vtiger_contactdetails
		inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
		where vtiger_crmentity.deleted=0 and email= ?';
	$result = $adb->pquery($sql, array($mailid));
	$numRows = $adb->num_rows($result);
	if ($numRows > 0) {
		$log->debug('< checkIfContactExists');
		return $adb->query_result($result, 0, 'contactid');
	} else {
		$log->debug('< checkIfContactExists');
		return -1;
	}
}
//assign the focus values
$focus->filename = isset($_REQUEST['file_name']) ? $_REQUEST['file_name'] : '';
$focus->parent_id = vtlib_purify($_REQUEST['parent_id']);
$focus->parent_type = vtlib_purify($_REQUEST['parent_type']);
$focus->column_fields['assigned_user_id']=$current_user->id;
$focus->column_fields['activitytype']='Emails';
$new_date = new DateTimeField(null);
$focus->column_fields['date_start']= $new_date->getDisplayDate($current_user);//This will be converted to db date format in save
$focus->column_fields['time_start']= $new_date->getDisplayTime($current_user);
if (!empty($_REQUEST['record']) && !$_REQUEST['send_mail'] && !empty($_REQUEST['mode'])) {
	$focus->mode = 'edit';
} elseif (empty($_REQUEST['record']) || (empty($_REQUEST['mode']) && (!$_REQUEST['send_mail'] || $_REQUEST['send_mail']))) {
	$focus->mode = '';
	$focus->id = '';
} else {
	$focus->mode = 'edit';
}
$focus->save('Emails');
$return_id = $focus->id;
$crmEntityTable1 = CRMEntity::getcrmEntityTableAlias('Emails', true);
require_once 'modules/Emails/mail.php';
if ($current_user->column_fields['send_email_to_sender']=='1' && isset($_REQUEST['send_mail']) && $_REQUEST['send_mail'] && $_REQUEST['parent_id'] != '') {
	$user_mail_status = send_mail('Emails', $current_user->column_fields['email1'], $current_user->user_name, '', $_REQUEST['subject'], $_REQUEST['description'], $_REQUEST['ccmail'], $_REQUEST['bccmail'], 'all', $focus->id);
	if ($user_mail_status != 1) {
		$adb->pquery('delete from '.$crmEntityTable1.' where crmid=?', array($focus->id));
		$adb->pquery('delete from vtiger_emaildetails where emailid=?', array($focus->id));
		$error_msg = '<font color=red><strong>'.$mod_strings['LBL_CHECK_USER_MAILID'].'</strong></font>';
		$ret_error = 1;
		$ret_parentid = vtlib_purify($_REQUEST['parent_id']);
		$ret_toadd = vtlib_purify($_REQUEST['parent_name']);
		$ret_subject = vtlib_purify($_REQUEST['subject']);
		$ret_replyto = vtlib_purify($_REQUEST['replyto']);
		$ret_ccaddress = vtlib_purify($_REQUEST['ccmail']);
		$ret_bccaddress = vtlib_purify($_REQUEST['bccmail']);
		$ret_description = vtlib_purify($_REQUEST['description']);
		echo $error_msg;
		include 'EditView.php';
		exit();
	}
}
$focus->retrieve_entity_info($return_id, 'Emails');

$module = empty($_REQUEST['source_module']) ? 'users' : $_REQUEST['source_module'];

if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '') {
	$return_module = vtlib_purify($_REQUEST['return_module']);
} else {
	$return_module = 'Emails';
}

if (isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != '') {
	$return_action = vtlib_purify($_REQUEST['return_action']);
} else {
	$return_action = 'DetailView';
}

if (isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != '') {
	$return_id = vtlib_purify($_REQUEST['return_id']);
}

if (isset($_REQUEST['filename']) && $_REQUEST['filename'] != '') {
	$filename = vtlib_purify($_REQUEST['filename']);
}

if (isset($_REQUEST['send_mail']) && $_REQUEST['send_mail'] && $_REQUEST['parent_id'] == '') {
	if ($_REQUEST['parent_name'] != '' && isset($_REQUEST['parent_name'])) {
		include 'modules/Emails/webmailsend.php';
	}
} elseif (isset($_REQUEST['send_mail']) && $_REQUEST['send_mail']) {
	include 'modules/Emails/mailsend.php';
}

if (isset($_REQUEST['return_action']) && $_REQUEST['return_action'] == 'mailbox') {
	header('Location: index.php?action=index&module='.urlencode($return_module));
} elseif (isset($_REQUEST['return_action'])) {
	$url = 'index.php?action='.urlencode($return_action).'&module='.urlencode($return_module).'&record='.urlencode($return_id);
	echo '<script>if (window.opener) {
		window.opener.location.href=window.opener.location.href;
		window.self.close();
	} else {
		window.location.href="'.$url.'";
	}</script>';
	die(); // to avoid unnecessay output to closing screen
}
?>
