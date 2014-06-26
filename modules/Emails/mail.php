<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/Emails/PHPMailerAutoload.php';
require_once 'include/utils/CommonUtils.php';

/**   Function used to send email
  *   $module 		-- current module
  *   $to_email 	-- to email address
  *   $from_name	-- currently loggedin user name
  *   $from_email	-- currently loggedin vtiger_users's email id. you can give as '' if you are not in HelpDesk module
  *   $subject		-- subject of the email you want to send
  *   $contents		-- body of the email you want to send
  *   $cc		-- add email ids with comma seperated. - optional
  *   $bcc		-- add email ids with comma seperated. - optional.
  *   $attachment	-- whether we want to attach the currently selected file or all files.[values = current,all] - optional
  *   $emailid		-- id of the email object which will be used to get the vtiger_attachments
  */
function send_mail($module,$to_email,$from_name,$from_email,$subject,$contents,$cc='',$bcc='',$attachment='',$emailid='',$logo='')
{

	global $adb, $log;
	global $root_directory;
	global $HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;

	$uploaddir = $root_directory ."/test/upload/";

	$adb->println("To id => '".$to_email."'\nSubject ==>'".$subject."'\nContents ==> '".$contents."'");

	//Get the email id of assigned_to user -- pass the value and name, name must be "user_name" or "id"(field names of vtiger_users vtiger_table)
	//$to_email = getUserEmailId('id',$assigned_user_id);

	//if module is HelpDesk then from_email will come based on support email id
	if($from_email == '') {
			//if from email is not defined, then use the useremailid as the from address
			$from_email = getUserEmailId('user_name',$from_name);
	}

	//if the newly defined from email field is set, then use this email address as the from address
	//and use the username as the reply-to address
	$query = "select * from vtiger_systems where server_type=?";
	$params = array('email');
	$result = $adb->pquery($query,$params);
	$from_email_field = $adb->query_result($result,0,'from_email_field');
	if(isUserInitiated()) {
		$replyToEmail = $from_email;
	} else {
		$replyToEmail = $from_email_field;
	}
	if(isset($from_email_field) && $from_email_field!=''){
		//setting from _email to the defined email address in the outgoing server configuration
		$from_email = $from_email_field;
	}

	if($module != "Calendar")
		$contents = addSignature($contents,$from_name);

	$mail = new PHPMailer();

	setMailerProperties($mail,$subject,$contents,$from_email,$from_name,trim($to_email,","),$attachment,$emailid,$module,$logo);
	setCCAddress($mail,'cc',$cc);
	setCCAddress($mail,'bcc',$bcc);
	if(!empty($replyToEmail)) {
		$mail->AddReplyTo($replyToEmail);
	}

	// vtmailscanner customization: If Support Reply to is defined use it.
	global $HELPDESK_SUPPORT_EMAIL_REPLY_ID;
	if($HELPDESK_SUPPORT_EMAIL_REPLY_ID && $HELPDESK_SUPPORT_EMAIL_ID != $HELPDESK_SUPPORT_EMAIL_REPLY_ID) {
		$mail->AddReplyTo($HELPDESK_SUPPORT_EMAIL_REPLY_ID);
	}
	// END

	// Fix: Return immediately if Outgoing server not configured
    if(empty($mail->Host)) {
		return 0;
    }
    // END

	$mail_status = MailSend($mail);

	if($mail_status != 1)
	{
		$mail_error = getMailError($mail,$mail_status,$mailto);
	}
	else
	{
		$mail_error = $mail_status;
	}

	return $mail_error;
}

/**	Function to get the user Email id based on column name and column value
  *	$name -- column name of the vtiger_users vtiger_table
  *	$val  -- column value
  */
function getUserEmailId($name,$val)
{
	global $adb;
	$adb->println("Inside the function getUserEmailId. --- ".$name." = '".$val."'");
	if($val != '')
	{
		//done to resolve the PHP5 specific behaviour
		$sql = "SELECT email1, email2, secondaryemail  from vtiger_users WHERE status='Active' AND ". $adb->sql_escape_string($name)." = ?";
		$res = $adb->pquery($sql, array($val));
		$email = $adb->query_result($res,0,'email1');
		if($email == '')
		{
			$email = $adb->query_result($res,0,'email2');
			if($email == '')
			{
				$email = $adb->query_result($res,0,'secondaryemail ');
			}
		}
		$adb->println("Email id is selected  => '".$email."'");
		return $email;
	}
	else
	{
		$adb->println("User id is empty. so return value is ''");
		return '';
	}
}

/**	Funtion to add the user's signature with the content passed
  *	$contents -- where we want to add the signature
  *	$fromname -- which user's signature will be added to the contents
  */
function addSignature($contents, $fromname)
{
	global $adb;
	$adb->println("Inside the function addSignature");

	$sign = nl2br($adb->query_result($adb->pquery("select signature from vtiger_users where user_name=?", array($fromname)),0,"signature"));
	if($sign != '')
	{
		$contents .= '<br><br>'.$sign;
		$adb->println("Signature is added with the body => '.".$sign."'");
	}
	else
	{
		$adb->println("Signature is empty for the user => '".$fromname."'");
	}
	return $contents;
}

/**	Function to set all the Mailer properties
  *	$mail 		-- reference of the mail object
  *	$subject	-- subject of the email you want to send
  *	$contents	-- body of the email you want to send
  *	$from_email	-- from email id which will be displayed in the mail
  *	$from_name	-- from name which will be displayed in the mail
  *	$to_email 	-- to email address  -- This can be an email in a single string, a comma separated
  *			   list of emails or an array of email addresses
  *	$attachment	-- whether we want to attach the currently selected file or all files.
  				[values = current,all] - optional
  *	$emailid	-- id of the email object which will be used to get the vtiger_attachments - optional
  */
function setMailerProperties($mail,$subject,$contents,$from_email,$from_name,$to_email,$attachment='',$emailid='',$module='',$logo='')
{
	global $adb;
	$adb->println("Inside the function setMailerProperties");
	if($module == "Support" || $logo ==1)
		$mail->AddEmbeddedImage('themes/images/logo_mail.jpg', 'logo', 'logo.jpg',"base64","image/jpg");

	$mail->Subject = $subject;
	$mail->Body = $contents;
	//$mail->Body = html_entity_decode(nl2br($contents));	//if we get html tags in mail then we will use this line
	$mail->AltBody = strip_tags(preg_replace(array("/<p>/i","/<br>/i","/<br \/>/i"),array("\n","\n","\n"),$contents));

	$mail->IsSMTP();		//set mailer to use SMTP
	//$mail->Host = "smtp1.example.com;smtp2.example.com";  // specify main and backup server

	setMailServerProperties($mail);

	//Handle the from name and email for HelpDesk
	$mail->From = $from_email;
	$rs = $adb->pquery("select first_name,last_name from vtiger_users where user_name=?", array($from_name));
	$num_rows = $adb->num_rows($rs);
	if($num_rows > 0)
		$from_name = getFullNameFromQResult($rs, 0, 'Users');
	
	$mail->FromName = decode_html($from_name);

	$mail->Sender= getReturnPath($mail->Host, $from_email);

	if($to_email != '')
	{
		if(is_array($to_email)) {
			for($j=0,$num=count($to_email);$j<$num;$j++) {
				$mail->addAddress($to_email[$j]);
			}
		} else {
			$_tmp = explode(",",$to_email);
			for($j=0,$num=count($_tmp);$j<$num;$j++) {
				$mail->addAddress($_tmp[$j]);
			}
		}
	}

	//commented so that it does not add from_email in reply to
	//$mail->AddReplyTo($from_email);
	$mail->WordWrap = 50;

	//If we want to add the currently selected file only then we will use the following function
	if($attachment == 'current' && $emailid != '')
	{
		if (isset($_REQUEST['filename_hidden'])) {
			$file_name = $_REQUEST['filename_hidden'];
		} else {
			$file_name = $_FILES['filename']['name'];
		}
		addAttachment($mail,$file_name,$emailid);
	}

	//This will add all the files which are related to this record or email
	if($attachment == 'all' && $emailid != '')
	{
		addAllAttachments($mail,$emailid);
	}

	//If we send attachments from Reports
	if($attachment == 'attReports')
	{
		if (isset($_REQUEST['filename_hidden_pdf'])) {
			$file_name = $_REQUEST['filename_hidden_pdf'];
			addAttachment($mail,$file_name,$emailid);
		} 
		if (isset($_REQUEST['filename_hidden_xls'])){
			$file_name = $_REQUEST['filename_hidden_xls'];
			addAttachment($mail,$file_name,$emailid);
		}
	}

	$mail->IsHTML(true);		// set email format to HTML

	return;
}

/**	Function to set the Mail Server Properties in the object passed
  *	$mail -- reference of the mailobject
  */
function setMailServerProperties($mail)
{
	global $adb;
	$adb->println("Inside the function setMailServerProperties");

	$res = $adb->pquery("select * from vtiger_systems where server_type=?", array('email'));
	if(isset($_REQUEST['server']))
		$server = $_REQUEST['server'];
	else
		$server = $adb->query_result($res,0,'server');
	if(isset($_REQUEST['server_username']))
		$username = $_REQUEST['server_username'];
	else
	        $username = $adb->query_result($res,0,'server_username');
	if(isset($_REQUEST['server_password']))
		$password = $_REQUEST['server_password'];
	else
        	$password = $adb->query_result($res,0,'server_password');
	// Prasad: First time read smtp_auth from the request
	if(isset($_REQUEST['smtp_auth'])) {
		$smtp_auth = $_REQUEST['smtp_auth'];
	} else {
		$smtp_auth = $adb->query_result($res,0,'smtp_auth');
	}

	$adb->println("Mail server name,username & password => '".$server."','".$username."','".$password."'");
	if ("false" != $smtp_auth) {
		$mail->SMTPAuth = true;
		if ("true" != $smtp_auth) {
			$mail->SMTPSecure = $smtp_auth;
		}
	}
	$mail->Host = $server;		// specify main and backup server
	$mail->Username = $username ;	// SMTP username
	$mail->Password = $password ;	// SMTP password

	return;
}

/**	Function to add the file as attachment with the mail object
  *	$mail -- reference of the mail object
  *	$filename -- filename which is going to added with the mail
  *	$record -- id of the record - optional
  */
function addAttachment($mail,$filename,$record)
{
	global $adb, $root_directory;
	$adb->println("Inside the function addAttachment");
	$adb->println("The file name is => '".$filename."'");

	//This is the file which has been selected in Email EditView
        if(is_file($root_directory.$filename) && ($root_directory.$filename) != '')
        {
                $mail->AddAttachment($root_directory.$filename);
        }
}

/**     Function to add all the files as attachment with the mail object
  *     $mail -- reference of the mail object
  *     $record -- email id ie., record id which is used to get the all vtiger_attachments from database
  */
function addAllAttachments($mail,$record)
{
	global $adb,$log, $root_directory;
        $adb->println("Inside the function addAllAttachments");

	//Retrieve the files from database where avoid the file which has been currently selected
	$sql = "select vtiger_attachments.* from vtiger_attachments inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid where vtiger_crmentity.deleted=0 and vtiger_seattachmentsrel.crmid=?";
	$res = $adb->pquery($sql, array($record));
	$count = $adb->num_rows($res);

	for($i=0;$i<$count;$i++)
	{
		$fileid = $adb->query_result($res,$i,'attachmentsid');
		$filename = decode_html($adb->query_result($res,$i,'name'));
		$filepath = $adb->query_result($res,$i,'path');
		$filewithpath = $root_directory.$filepath.$fileid."_".$filename;

		//if the file is exist in test/upload directory then we will add directly
		//else get the contents of the file and write it as a file and then attach (this will occur when we unlink the file)
		if(is_file($filewithpath))
		{
			$mail->AddAttachment($filewithpath,$filename);
		}
	}
}

/**	Function to set the CC or BCC addresses in the mail
  *	$mail -- reference of the mail object
  *	$cc_mod -- mode to set the address ie., cc or bcc
  *	$cc_val -- addresss with comma seperated to set as CC or BCC in the mail
  */
function setCCAddress($mail,$cc_mod,$cc_val)
{
	global $adb;
	$adb->println("Inside the functin setCCAddress");

	if($cc_mod == 'cc')
		$method = 'AddCC';
	if($cc_mod == 'bcc')
		$method = 'AddBCC';
	if($cc_val != '')
	{
		$ccmail = explode(",",trim($cc_val,","));
		for($i=0;$i<count($ccmail);$i++)
		{
			$addr = $ccmail[$i];
			$cc_name = preg_replace('/([^@]+)@(.*)/', '$1', $addr); // First Part Of Email
			if(stripos($addr, '<')) {
				$name_addr_pair = explode("<",$ccmail[$i]);
				$cc_name = $name_addr_pair[0];
				$addr = trim($name_addr_pair[1],">");
			}
			if($ccmail[$i] != '')
				$mail->$method($addr,$cc_name);
		}
	}
}

/**	Function to send the mail which will be called after set all the mail object values
  *	$mail -- reference of the mail object
  */
function MailSend($mail)
{
	global $log;
         $log->info("Inside of Send Mail function.");
	if(!$mail->Send())
        {
		$log->debug("Error in Mail Sending : Error log = '".$mail->ErrorInfo."'");
		return $mail->ErrorInfo;
        }
	else
	{
		 $log->info("Mail has been sent from the vtigerCRM system : Status : '".$mail->ErrorInfo."'");
		return 1;
	}
}

/**	Function to get the Parent email id from HelpDesk to send the details about the ticket via email
  *	$returnmodule -- Parent module value. Contact or Account for send email about the ticket details
  *	$parentid -- id of the parent ie., contact or vtiger_account
  */
function getParentMailId($parentmodule,$parentid)
{
	global $adb;
	$adb->println("Inside the function getParentMailId. \n parent module and id => ".$parentmodule."&".$parentid);

        if($parentmodule == 'Contacts')
        {
                $tablename = 'vtiger_contactdetails';
                $idname = 'contactid';
		$first_email = 'email';
		$second_email = 'secondaryemail';
        }
        if($parentmodule == 'Accounts')
        {
                $tablename = 'vtiger_account';
                $idname = 'accountid';
		$first_email = 'email1';
		$second_email = 'email2';
        }
	if($parentid != '')
	{
	   	//$query = 'select * from '.$tablename.' where '.$idname.' = '.$parentid;
	   	$query = 'select * from '.$tablename.' where '. $idname.' = ?';
		$res = $adb->pquery($query, array($parentid));
	    $mailid = $adb->query_result($res,0,$first_email);
		$mailid2 = $adb->query_result($res,0,$second_email);
	}
        if($mailid == '' && $mailid2 != '')
        	$mailid = $mailid2;

	return $mailid;
}

/**	Function to parse and get the mail error
  *	$mail -- reference of the mail object
  *	$mail_status -- status of the mail which is sent or not
  *	$to -- the email address to whom we sent the mail and failes
  *	return -- Mail error occured during the mail sending process
  */
function getMailError($mail,$mail_status,$to)
{
	//Error types in class.phpmailer.php
	/*
	provide_address, mailer_not_supported, execute, instantiate, file_access, file_open, encoding, data_not_accepted, authenticate,
	connect_host, recipients_failed, from_failed
	*/

	global $adb;
	$adb->println("Inside the function getMailError");

	$msg = array_search($mail_status,$mail->getTranslations());
	$adb->println("Error message ==> ".$msg);

	if($msg == 'connect_host')
	{
		$error_msg =  $msg;
	}
	elseif(strstr($msg,'from_failed'))
	{
		$error_msg = $msg;
	}
	elseif(strstr($msg,'recipients_failed'))
	{
		$error_msg = $msg;
	}
	else
	{
		$adb->println("Mail error is not as connect_host or from_failed or recipients_failed");
		//$error_msg = $msg;
	}

	$adb->println("return error => ".$error_msg);
	return $error_msg;
}

/**	Function to get the mail status string (string of sent mail status)
  *	$mail_status_str -- concatenated string with all the error messages with &&& seperation
  *	return - the error status as a encoded string
  */
function getMailErrorString($mail_status_str)
{
	global $adb;
	$adb->println("Inside getMailErrorString function.\nMail status string ==> ".$mail_status_str);

	$mail_status_str = trim($mail_status_str,"&&&");
	$mail_status_array = explode("&&&",$mail_status_str);
	$adb->println("All Mail status ==>\n".$mail_status_str."\n");

	foreach($mail_status_array as $key => $val)
	{
		$list = explode("=",$val);
		$adb->println("Mail id & status ==> ".$list[0]." = ".$list[1]);
		if($list[1] == 0)
		{
			$mail_error_str .= $list[0]."=".$list[1]."&&&";
		}
	}
	$adb->println("Mail error string => '".$mail_error_str."'");
	if($mail_error_str != '')
	{
		$mail_error_str = 'mail_error='.base64_encode($mail_error_str);
	}
	return $mail_error_str;
}

/**	Function to parse the error string
  *	$mail_error_str -- base64 encoded string which contains the mail sending errors as concatenated with &&&
  *	return - Error message to display
  */
function parseEmailErrorString($mail_error_str)
{
	//TODO -- we can modify this function for better email error handling in future
	global $adb, $mod_strings;
	$adb->println("Inside the parseEmailErrorString function.\n encoded mail error string ==> ".$mail_error_str);

	$mail_error = base64_decode($mail_error_str);
	$adb->println("Original error string => ".$mail_error);
	$mail_status = explode("&&&",trim($mail_error,"&&&"));
	foreach($mail_status as $key => $val)
	{
		$status_str = explode("=",$val);
		$adb->println('Mail id => "'.$status_str[0].'".........status => "'.$status_str[1].'"');
		if($status_str[1] != 1 && $status_str[1] != '')
		{
			$adb->println("Error in mail sending");
			if($status_str[1] == 'connect_host')
			{
				$adb->println("if part - Mail sever is not configured");
				$errorstr .= '<br><b><font color=red>'.$mod_strings['MESSAGE_CHECK_MAIL_SERVER_NAME'].'</font></b>';
				break;
			}
			elseif($status_str[1] == '0')
			{
				$adb->println("first elseif part - status will be 0 which is the case of assigned to vtiger_users's email is empty.");
				$errorstr .= '<br><b><font color=red> '.$mod_strings['MESSAGE_MAIL_COULD_NOT_BE_SEND'].' '.$mod_strings['MESSAGE_PLEASE_CHECK_FROM_THE_MAILID'].'</font></b>';
				//Added to display the message about the CC && BCC mail sending status
				if($status_str[0] == 'cc_success')
				{
                                        $cc_msg = 'But the mail has been sent to CC & BCC addresses.';
					$errorstr .= '<br><b><font color=purple>'.$cc_msg.'</font></b>';
				}
			}
			elseif(strstr($status_str[1],'from_failed'))
			{
				$adb->println("second elseif part - from email id is failed.");
				$from = explode('from_failed',$status_str[1]);
				$errorstr .= "<br><b><font color=red>".$mod_strings['MESSAGE_PLEASE_CHECK_THE_FROM_MAILID']." '".$from[1]."'</font></b>";
			}
			else
			{
				$adb->println("else part - mail send process failed due to the following reason.");
				$errorstr .= "<br><b><font color=red> ".$mod_strings['MESSAGE_MAIL_COULD_NOT_BE_SEND_TO_THIS_EMAILID']." '".$status_str[0]."'. ".$mod_strings['PLEASE_CHECK_THIS_EMAILID']."</font></b>";
			}
		}
	}
	$adb->println("Return Error string => ".$errorstr);
	return $errorstr;
}

function isUserInitiated() {
	return (($_REQUEST['module'] == 'Emails' || $_REQUEST['module'] == 'Webmails') &&
			($_REQUEST['action'] == 'mailsend' || $_REQUEST['action'] == 'webmailsend' || $_REQUEST['action'] == 'Save'));
}

/**
 * Function to get the group users Email ids
 */
function getDefaultAssigneeEmailIds($groupId) {
	global $adb;
	$emails = Array();
	if($groupId != '') {
		require_once 'include/utils/GetGroupUsers.php';
		$userGroups = new GetGroupUsers();
		$userGroups->getAllUsersInGroup($groupId);
		$result = $adb->pquery('SELECT email1,email2,secondaryemail FROM vtiger_users WHERE vtiger_users.id IN
											('.  generateQuestionMarks($userGroups->group_users).') AND vtiger_users.status= ?',
								array($userGroups->group_users, 'Active'));
		$rows = $adb->num_rows($result);
		for($i = 0;$i < $rows; $i++) {
			$email = $adb->query_result($result,$i,'email1');
			if($email == '') {
				$email = $adb->query_result($result,$i,'email2');
				if($email == '') {
					$email = $adb->query_result($result,$i,'secondaryemail');
				} else {
					$email = '';
				}
			}
			array_push($emails,$email);
		}
		$adb->println("Email ids are selected  => '".$emails."'");
		return $emails;
	} else {
		$adb->println("User id is empty. so return value is ''");
		return '';
	}
}


?>
