<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/utils.php');
require_once('include/logging.php');
global $log, $current_user, $upload_badext;
$vtigerpath = $_SERVER['REQUEST_URI'];
$vtigerpath = str_replace("/index.php?module=uploads&action=add2db", "", $vtigerpath);

$crmid = vtlib_purify($_REQUEST['return_id']);
$log->debug("In add2db.php");

	if(isset($_REQUEST['filename_hidden'])) {
		$file = vtlib_purify($_REQUEST['filename_hidden']);
	} else {
		$file = $_FILES['filename']['name'];
	}
	$binFile = sanitizeUploadFileName($file, $upload_badext);
	$_FILES["filename"]["name"] = $binFile;

	//decide the file path where we should upload the file in the server
	$upload_filepath = decideFilePath();

	$current_id = $adb->getUniqueID("vtiger_crmentity");

	if (move_uploaded_file($_FILES['filename']['tmp_name'],$upload_filepath.$current_id.'_'.$_FILES['filename']['name'])) {
		$filename = ltrim(basename(' '.$binFile)); //allowed filename like UTF-8 characters
		$filetype= $_FILES['filename']['type'];
		$filesize = $_FILES['filename']['size'];

		if ($filesize != 0) {
			$desc = vtlib_purify($_REQUEST['txtDescription']);
			$subject = vtlib_purify($_REQUEST['uploadsubject']);
			$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
			$current_date = getdate();
			$current_date = $adb->formatDate(date('Y-m-d H:i:s'), true);
			$query = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
			$params = array($current_id, $current_user->id, $current_user->id, vtlib_purify($_REQUEST['return_module']).' Attachment', $desc, $date_var, $current_date);
			$result = $adb->pquery($query, $params);

			# Attachments added to contacts are also added to their accounts
			$log->debug("return_module: ".$_REQUEST['return_module']);
			if ($_REQUEST['return_module'] == 'Contacts')
			{
				$crmid = vtlib_purify($_REQUEST['return_id']);
				$query = 'select accountid from vtiger_contactdetails where contactid=?';
				$result = $adb->pquery($query, array($crmid));
				if($adb->num_rows($result) != 0)
				{
					$log->debug("Returned a row");
					$associated_account = $adb->query_result($result,0,"accountid");
					# Now make sure that we haven't already got this attachment associated to this account
					# Hmmm... if this works, should we NOT upload the attachment again, and just set the relation for the contact too?
					$log->debug("Associated Account: ".$associated_account);
					$query = "select attachmentsid, name, path from vtiger_attachments where name=?";
					$result = $adb->pquery($query, array($filename));
					if($adb->num_rows($result) != 0)
					{
						$log->debug("Matched a row");
						# Whoops! We matched the name. Is it the same size?
						$fname = $adb->query_result($result,0,"name");
						$fpath = $adb->query_result($result,0,"path");
						$fid = $adb->query_result($result,0,"attachmentsid");
						$dg_size = filesize($fpath . "/".$fid."_". $fname);
						//$dg_size = $adb->query_result($result,0,"attachmentsize");
						$log->debug("These should be the same size: ".$dg_size." ".$filesize);
						if ($dg_size == $filesize)
						{
							# Yup, it is probably the same file
							$associated_account = '';
						}
					}
				}
				else
				{
					$associated_account = '';
				}
			}

			$sql = "insert into vtiger_attachments(attachmentsid, name, description, type,path,subject) values(?,?,?,?,?,?)";
			$params = array($current_id, $filename, $desc, $filetype, $upload_filepath, $subject);
			$result = $adb->pquery($sql, $params);


			$sql1 = "insert into vtiger_seattachmentsrel values(?,?)";
			$params1 = array($crmid, $current_id);
			$result = $adb->pquery($sql1, $params1);

			# Attachments added to contacts are also added to their accounts
			if ($associated_account)
			{
				$log->debug("inserting into vtiger_seattachmentsrel from add2db 2");
				$sql1 = "insert into vtiger_seattachmentsrel values(?,?)";
				$params1 = array($associated_account, $current_id);
				$result = $adb->pquery($sql1, $params1);
			}

			echo '<script>window.opener.location.href = window.opener.location.href;self.close();</script>';
		}
		else
		{
			$errormessage = "<font color='red'><b>".getTranslatedString('Error Message','Settings')."<ul>
				<li><font color='red'>".getTranslatedString('Invalid_file','Settings')."</font>
				<li><font color='red'>".getTranslatedString('File_has_no_data','Settings').'/font>
				</ul></b></font><br>';
			header('Location: index.php?module=uploads&action=uploadsAjax&msg=true&file=upload&errormessage='.urlencode($errormessage));
		}
	} else {
		$errorCode =  $_FILES['binFile']['error'];
		$errormessage = "";

		if($errorCode == 4)
		{
			$errormessage = "<b><font color='red'>".getTranslatedString('LBL_PLEASE_ATTACH','Emails').'</font></b><br>';
		}
		else if($errorCode == 2)
		{
			$errormessage = "<b><font color='red'>".getTranslatedString('FILESIZE_EXCEEDS_INFO_CONFIG_INC','Settings').'</font></b><br>';
		}
		else if($errorCode == 6)
		{
			$errormessage = '<b>'.getTranslatedString('LBL_KINDLY_UPLOAD','Emails').'</b><br>';
		}
		else if($errorCode == 3 || $errorcode == '')
		{
			$errormessage = "<b><font color='red'>".getTranslatedString('PROBLEMS_IN_FILEUPLOAD','Settings').'</font></b><br>';
		}

		if($errormessage != '')
		{
			echo $errormessage;
			include("upload.php");
		}
	}

?>