<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/GetGroupUsers.php';
require_once 'include/utils/UserInfoUtil.php';

global $adb, $current_user;

//set the return module and return action and set the return id based on return module and record
$returnmodule = isset($_REQUEST['return_module']) ? vtlib_purify($_REQUEST['return_module']) : '';
$returnaction = isset($_REQUEST['return_action']) ? vtlib_purify($_REQUEST['return_action']) : '';
if ((($returnmodule != 'Emails') || ($returnmodule == 'Emails' && empty($_REQUEST['record']))) && !empty($_REQUEST['return_id'])) {
	$returnid = vtlib_purify($_REQUEST['return_id']);
} else {
	$returnid = $focus->id;
}

$adb->println("\n\nMail Sending Process has been started.");
//This function call is used to send mail to the assigned to user. In this mail CC and BCC addresses will be added.
if (isset($_REQUEST['assigntype']) && $_REQUEST['assigntype'] == 'T' && !empty($_REQUEST['assigned_group_id'])) {
	$grp_obj = new GetGroupUsers();
	$grp_obj->getAllUsersInGroup($_REQUEST['assigned_group_id']);
	$users_list = constructList($grp_obj->group_users, 'INTEGER');
	if (count($users_list) > 0) {
		$sql = 'select first_name, last_name, email1, email2, secondaryemail from vtiger_users where id in ('. generateQuestionMarks($users_list) .')';
		$params = array($users_list);
	} else {
		$sql = 'select first_name, last_name, email1, email2, secondaryemail from vtiger_users';
		$params = array();
	}
	$res = $adb->pquery($sql, $params);
	$user_email = '';
	while ($user_info = $adb->fetch_array($res)) {
		$email = $user_info['email1'];
		if ($email == '' || $email == 'NULL') {
			$email = $user_info['email2'];
			if ($email == '' || $email == 'NULL') {
				$email = $user_info['secondaryemail '];
			}
		}
		if ($user_email=='') {
			$user_email .= $user_info['first_name'].' '.$user_info['last_name'].'<'.$email.'>';
		} else {
			$user_email .= ','.$user_info['first_name'].' '.$user_info['last_name'].'<'.$email.'>';
		}
		$email='';
	}
	$to_email = $user_email;
} else {
	$to_email = getUserEmailId('id', $focus->column_fields['assigned_user_id']);
}
$replyto = $_REQUEST['replyto'];
$cc = $_REQUEST['ccmail'];
$bcc = $_REQUEST['bccmail'];
$errorheader1 = 0;
$errorheader2 = 0;
if ($to_email == '' && $cc == '' && $bcc == '') {
	$adb->println('Mail Error : send_mail function not called because To email id of assigned to user, CC and BCC are empty');
	$mail_status_str = "'".$to_email."'=0&&&";
	$errorheader1 = 1;
} else {
	$res1 = $adb->pquery('select email1 from vtiger_users where id =?', array($current_user->id));
	$val = $adb->query_result($res1, 0, 'email1');
	$query = 'update vtiger_emaildetails set email_flag ="SENT",from_email =? where emailid=?';
	$adb->pquery($query, array($val, $focus->id));
	$mail_status_str = '';
}

$parentid= vtlib_purify($_REQUEST['parent_id']);
$myids=explode('|', $parentid);
$all_to_emailids = array();
$from_name = $current_user->user_name;
$from_address = $current_user->column_fields['email1'];
if (!empty($_REQUEST['from_email'])) {
	$from_address = $_REQUEST['from_email'];
	if ($from_address!=$val) {
		$query = "update vtiger_emaildetails set from_email = concat(from_email,' > ',?) where emailid=?";
		$adb->pquery($query, array($from_address, $focus->id));
	}
	$from_address = 'FROM:::>'.$from_address;
}

// Group emails
if (isset($_REQUEST['individual_emails']) && $_REQUEST['individual_emails'] == 'on') {
	$individual_emails = 1;
} else {
	$individual_emails = 0;
}
$logo = '';
$subject = '';
$description = '';

for ($i=0; $i<(count($myids)-1); $i++) {
	$realid=explode('@', $myids[$i]);
	$nemail=count($realid);
	$mycrmid=$realid[0];
	if (getModuleForField($realid[1]) == 'Users') {
		//handle the mail send to vtiger_users
		$rs = $adb->pquery('select email1 from vtiger_users where id=?', array($mycrmid));
		$emailadd = $adb->query_result($rs, 0, 'email1');
		$pmodule = 'Users';
		$description = getMergedDescription($_REQUEST['description'], $mycrmid, $pmodule);
		$all_to_emailids[]= $emailadd;
		if ($individual_emails) {
			$mail_status = send_mail('Emails', $emailadd, $from_name, $from_address, $_REQUEST['subject'], $description, $cc, '', 'all', $focus->id);
			$mail_status_str .= $emailadd.'='.$mail_status.'&&&';
		}
	} else {
		//Send mail to account, lead or contact based on their ids
		$pmodule=getSalesEntityType($mycrmid);
		$subject = $_REQUEST['subject'];
		$description = $_REQUEST['description'];

		// Merge template
		$ids = array();
		if (isset($_REQUEST['merge_template_with']) && $_REQUEST['merge_template_with'] != '') {
			$ids = explode(',', $_REQUEST['merge_template_with']);
		}
		if (count($ids) > 0) {
			foreach ($ids as $id) {
				$module = getSalesEntityType($id);
				$subject = getMergedDescription($subject, $id, $module);
				$description = getMergedDescription($description, $id, $module);
			}
		}

		for ($j=1; $j<$nemail; $j++) {
			$temp=$realid[$j];
			$myquery='select fieldname from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)';
			$fresult=$adb->pquery($myquery, array($temp));
			// vtlib customization: Enabling mail send from other modules
			$myfocus = CRMEntity::getInstance($pmodule);
			$myfocus->retrieve_entity_info($mycrmid, $pmodule);

			$subject=getMergedDescription($subject, $mycrmid, $pmodule);
			$description = getMergedDescription($description, $mycrmid, $pmodule);

			$subject=getMergedDescription($subject, $current_user->id, 'Users');
			$description=getMergedDescription($description, $current_user->id, 'Users');

			$accid = getRelatedAccountContact($mycrmid, 'Accounts');
			if (!empty($accid)) {
				$subject=getMergedDescription($subject, $accid, 'Accounts');
				$description=getMergedDescription($description, $accid, 'Accounts');
			}
			$fldname=$adb->query_result($fresult, 0, 'fieldname');
			$emailadd=br2nl($myfocus->column_fields[$fldname]);

			if ($emailadd != '') {
				//Email Open/Stat Tracking
				global $site_URL, $application_unique_key;
				$EMail_OpenTrackingEnabled = GlobalVariable::getVariable('EMail_OpenTrackingEnabled', 1, 'Emails');
				if ($EMail_OpenTrackingEnabled) {
					$emailid = $focus->id;
					$track_URL = "$site_URL/modules/Emails/TrackAccess.php?record=$mycrmid&mailid=$emailid&app_key=$application_unique_key";
					$description = "$description<img src='$track_URL' alt='' width='1' height='1'>";
				}

				$pos = strpos($description, '$logo$');
				if ($pos !== false) {
					$description =str_replace('$logo$', '<img src="cid:logo" />', $description);
					$logo=1;
				} else {
					$logo = 0;
				}

				$all_to_emailids[]= $emailadd;

				if ($individual_emails) {
					if (isPermitted($pmodule, 'DetailView', $mycrmid) == 'yes') {
						$mail_status = send_mail('Emails', $emailadd, $from_name, $from_address, $subject, $description, $cc, '', 'all', $focus->id, $logo, $replyto);
					}
					$mail_status_str .= $emailadd.'='.$mail_status.'&&&';
					//added to get remain the EditView page if an error occurs in mail sending
					if ($mail_status != 1) {
						$errorheader2 = 1;
					}
				}
			}
		}
	}
}

// Sending group emails
if (!$individual_emails) {
	$mail_status = send_mail('Emails', implode(',', $all_to_emailids), $from_name, $from_address, $subject, $description, $cc, '', 'all', $focus->id, $logo, $replyto);
	$mail_status_str .= $mail_status.'&&&';
	//added to get remain the EditView page if an error occurs in mail sending
	if ($mail_status != 1) {
		$errorheader2 = 1;
	}
}

//Added to redirect the page to Emails/EditView if there is an error in mail sending
if ($errorheader1 == 1 || $errorheader2 == 1) {
	$returnmodule = 'Emails';
	$returnaction = 'EditView';
	//This condition is added to set the record(email) id when we click on send mail button after returning mail error
	if ($_REQUEST['mode'] == 'edit') {
		$returnid = vtlib_purify($_REQUEST['record']);
	} else {
		$returnid = vtlib_purify($_REQUEST['currentid']);
	}
	$returnset = 'return_module='.$returnmodule.'&return_action='.$returnaction.'&return_id='.$returnid;
} else {
	global $adb;
	$date_var = date('Ymd');
	$query = 'update vtiger_activity set date_start =? where activityid = ?';
	$adb->pquery($query, array($date_var, $returnid));
}

//The following function call is used to parse and form a encoded error message and then pass to result page
$mail_error_str = getMailErrorString($mail_status_str);
$adb->println("Mail Sending Process has been finished.\n\n");
if (isset($_REQUEST['popupaction']) && $_REQUEST['popupaction'] != '') {
	echo '<script>window.opener.location.href=window.opener.location.href;window.self.close();</script>';
	die(); // to avoid unnecessay output to closing screen
}
?>