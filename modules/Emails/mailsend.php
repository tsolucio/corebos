<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once("include/utils/GetGroupUsers.php");
require_once("include/utils/UserInfoUtil.php");

global $adb, $current_user;

//set the return module and return action and set the return id based on return module and record
$returnmodule = isset($_REQUEST['return_module']) ? vtlib_purify($_REQUEST['return_module']) : '';
$returnaction = isset($_REQUEST['return_action']) ? vtlib_purify($_REQUEST['return_action']) : '';
if ((($returnmodule != 'Emails') || ($returnmodule == 'Emails' && empty($_REQUEST['record']))) && !empty($_REQUEST['return_id'])) {
	$returnid = vtlib_purify($_REQUEST['return_id']);
}
else
{
	$returnid = $focus->id;//$_REQUEST['record'];
}

$adb->println("\n\nMail Sending Process has been started.");
//This function call is used to send mail to the assigned to user. In this mail CC and BCC addresses will be added.
if (isset($_REQUEST['assigntype']) && $_REQUEST['assigntype'] == 'T' && !empty($_REQUEST['assigned_group_id'])) {
	$grp_obj = new GetGroupUsers();
	$grp_obj->getAllUsersInGroup($_REQUEST['assigned_group_id']);
	$users_list = constructList($grp_obj->group_users,'INTEGER');
	if (count($users_list) > 0) {
		$sql = "select first_name, last_name, email1, email2, secondaryemail from vtiger_users where id in (". generateQuestionMarks($users_list) .")";
		$params = array($users_list);
	} else {
		$sql = "select first_name, last_name, email1, email2, secondaryemail from vtiger_users";
		$params = array();
	}
	$res = $adb->pquery($sql, $params);
	$user_email = '';
	while ($user_info = $adb->fetch_array($res))
	{
		$email = $user_info['email1'];
		if($email == '' || $email == 'NULL')
		{
			$email = $user_info['email2'];
			if($email == '' || $email == 'NULL')
			{
				$email = $user_info['secondaryemail '];
			}
		}
		if($user_email=='')
		$user_email .= $user_info['first_name']." ".$user_info['last_name']."<".$email.">";
		else
		$user_email .= ",".$user_info['first_name']." ".$user_info['last_name']."<".$email.">";
		$email='';
	}
	$to_email = $user_email;
}
else
{
	$to_email = getUserEmailId('id',$focus->column_fields["assigned_user_id"]);
}
$cc = $_REQUEST['ccmail'];
$bcc = $_REQUEST['bccmail'];
$errorheader1 = 0;
$errorheader2 = 0;
if($to_email == '' && $cc == '' && $bcc == '')
{
	$adb->println("Mail Error : send_mail function not called because To email id of assigned to user, CC and BCC are empty");
	$mail_status_str = "'".$to_email."'=0&&&";
	$errorheader1 = 1;
}
else
{
	$query1 = 'select email1 from vtiger_users where id =?';
	$res1 = $adb->pquery($query1, array($current_user->id));
	$val = $adb->query_result($res1,0,'email1');
	$query = 'update vtiger_emaildetails set email_flag ="SENT",from_email =? where emailid=?';
	$adb->pquery($query, array($val, $focus->id));
	$mail_status_str = '';
}

$parentid= vtlib_purify($_REQUEST['parent_id']);
$myids=explode("|",$parentid);
$all_to_emailids = Array();
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

for ($i=0;$i<(count($myids)-1);$i++)
{
	$realid=explode("@",$myids[$i]);
	$nemail=count($realid);
	$mycrmid=$realid[0];
	if (getModuleForField($realid[1]) == 'Users') {
		//handle the mail send to vtiger_users
		$emailadd = $adb->query_result($adb->pquery("select email1 from vtiger_users where id=?", array($mycrmid)),0,'email1');
		$pmodule = 'Users';
		$description = getMergedDescription($_REQUEST['description'],$mycrmid,$pmodule);
		$mail_status = send_mail('Emails',$emailadd,$from_name,$from_address,$_REQUEST['subject'],$description,'','','all',$focus->id);
		$all_to_emailids []= $emailadd;
		$mail_status_str .= $emailadd."=".$mail_status."&&&";
	} else {
		//Send mail to account, lead or contact based on their ids
		$pmodule=getSalesEntityType($mycrmid);
		$subject = $_REQUEST['subject'];
		$description = $_REQUEST['description'];
		for ($j=1;$j<$nemail;$j++) {
			$temp=$realid[$j];
			$myquery='Select columnname from vtiger_field where fieldid = ? and vtiger_field.presence in (0,2)';
			$fresult=$adb->pquery($myquery, array($temp));
			if ($pmodule=='Contacts')
			{
				require_once('modules/Contacts/Contacts.php');
				$myfocus = new Contacts();
				$myfocus->retrieve_entity_info($mycrmid,"Contacts");

				$subject=getMergedDescription($subject,$mycrmid,$pmodule);
				$description=getMergedDescription($description,$mycrmid,$pmodule);

				$subject=getMergedDescription($subject,$myfocus->column_fields['account_id'],'Accounts');
				$description=getMergedDescription($description,$myfocus->column_fields['account_id'],'Accounts');

				$subject=getMergedDescription($subject,$current_user->id,'Users');
				$description=getMergedDescription($description,$current_user->id,'Users');
			}
			elseif ($pmodule=='Accounts')
			{
				require_once('modules/Accounts/Accounts.php');
				$myfocus = new Accounts();
				$myfocus->retrieve_entity_info($mycrmid,"Accounts");

				$subject=getMergedDescription($subject,$mycrmid,$pmodule);
				$description=getMergedDescription($description,$mycrmid,$pmodule);

				$subject=getMergedDescription($subject,$current_user->id,'Users');
				$description=getMergedDescription($description,$current_user->id,'Users');
			}
			elseif ($pmodule=='Leads')
			{
				require_once('modules/Leads/Leads.php');
				$myfocus = new Leads();
				$myfocus->retrieve_entity_info($mycrmid,"Leads");

				$subject=getMergedDescription($subject,$mycrmid,$pmodule);
				$description=getMergedDescription($description,$mycrmid,$pmodule);

				$subject=getMergedDescription($subject,$current_user->id,'Users');
				$description=getMergedDescription($description,$current_user->id,'Users');
			}
			elseif ($pmodule=='Vendors')
			{
				require_once('modules/Vendors/Vendors.php');
				$myfocus = new Vendors();
				$myfocus->retrieve_entity_info($mycrmid,"Vendors");
			}
			else
			{
				// vtlib customization: Enabling mail send from other modules
				$myfocus = CRMEntity::getInstance($pmodule);
				$myfocus->retrieve_entity_info($mycrmid, $pmodule);

				$subject=getMergedDescription($subject,$mycrmid,$pmodule);
				$description = getMergedDescription($description,$mycrmid,$pmodule);

				$subject=getMergedDescription($subject,$current_user->id,'Users');
				$description=getMergedDescription($description,$current_user->id,'Users');
			}
			$fldname=$adb->query_result($fresult,0,"columnname");
			$emailadd=br2nl($myfocus->column_fields[$fldname]);

			if($emailadd != '') {
				//Email Open/Stat Tracking
				global $site_URL, $application_unique_key;
				$EMail_OpenTrackingEnabled = GlobalVariable::getVariable('EMail_OpenTrackingEnabled',1,'Emails');
				if ($EMail_OpenTrackingEnabled) {
					$emailid = $focus->id;
					$track_URL = "$site_URL/modules/Emails/TrackAccess.php?record=$mycrmid&mailid=$emailid&app_key=$application_unique_key";
					$description = "$description<img src='$track_URL' alt='' width='1' height='1'>";
				}

				$pos = strpos($description, '$logo$');
				if ($pos !== false)
				{
					$description =str_replace('$logo$','<img src="cid:logo" />',$description);
					$logo=1;
				} else {
					$logo = 0;
				}
				if(isPermitted($pmodule,'DetailView',$mycrmid) == 'yes')
				{
					$mail_status = send_mail('Emails',$emailadd,$from_name,$from_address,$subject,$description,'','','all',$focus->id,$logo);
				}

				$all_to_emailids []= $emailadd;
				$mail_status_str .= $emailadd."=".$mail_status."&&&";
				//added to get remain the EditView page if an error occurs in mail sending
				if($mail_status != 1)
				{
					$errorheader2 = 1;
				}
			}
		}
	}
}
//Added to redirect the page to Emails/EditView if there is an error in mail sending
if($errorheader1 == 1 || $errorheader2 == 1)
{
	$returnset = 'return_module='.$returnmodule.'&return_action='.$returnaction.'&return_id='.vtlib_purify($_REQUEST['return_id']);
	$returnmodule = 'Emails';
	$returnaction = 'EditView';
	//This condition is added to set the record(email) id when we click on send mail button after returning mail error
	if($_REQUEST['mode'] == 'edit')
	{
		$returnid = $_REQUEST['record'];
	}
	else
	{
		$returnid = $_REQUEST['currentid'];
	}
}
else
{
	global $adb;
	$date_var = date('Ymd');
	$query = 'update vtiger_activity set date_start =? where activityid = ?';
	$adb->pquery($query, array($date_var, $returnid));
}

//The following function call is used to parse and form a encoded error message and then pass to result page
$mail_error_str = getMailErrorString($mail_status_str);
$adb->println("Mail Sending Process has been finished.\n\n");
if(isset($_REQUEST['popupaction']) && $_REQUEST['popupaction'] != '')
{
	echo '<script>window.opener.location.href=window.opener.location.href;window.self.close();</script>';
	die(); // to avoid unnecessay output to closing screen
}
?>