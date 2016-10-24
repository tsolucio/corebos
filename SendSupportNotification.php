<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
require_once('include/utils/utils.php');
require("modules/Emails/mail.php");
require_once('include/logging.php');
require("config.php");

global $adb, $log;
$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail','support@your_support_domain.tld','HelpDesk');
$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name','your-support name','HelpDesk');
$log =& LoggerManager::getLogger('SendSupportNotification');
$log->debug(" invoked SendSupportNotification ");

// retrieve the translated strings.
$app_strings = return_application_language($current_language);

//To send email notification before a week
$query="select vtiger_contactdetails.contactid,vtiger_contactdetails.email,vtiger_contactdetails.firstname,vtiger_contactdetails.lastname,contactid  from vtiger_customerdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_customerdetails.customerid inner join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_customerdetails.customerid  where vtiger_crmentity.deleted=0 and support_end_date=DATE_ADD(now(), INTERVAL 1 WEEK)";
$result = $adb->pquery($query, array());

if($adb->num_rows($result) >= 1)
{
	while($result_set = $adb->fetch_array($result))
	{
		$content=getcontent_week($result_set["contactid"]);
		$body=$content["body"];
		$body = str_replace('$logo$','<img src="cid:logo" />',$body);
		$subject=$content["subject"];
		$status=send_mail("Support",$result_set["email"],$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$body,'',$HELPDESK_SUPPORT_EMAIL_ID);
	}

}
//comment / uncomment this line if you want to hide / show the sent mail status
//showstatus($status);
$log->debug(" Send Support Notification Befoe a week - Status: ".$status);

//To send email notification before a month
$query="select vtiger_contactdetails.contactid,vtiger_contactdetails.email,vtiger_contactdetails.firstname,vtiger_contactdetails.lastname,contactid  from vtiger_customerdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_customerdetails.customerid inner join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_customerdetails.customerid  where vtiger_crmentity.deleted=0 and support_end_date=DATE_ADD(now(), INTERVAL 1 MONTH)";
$result = $adb->pquery($query, array());


if($adb->num_rows($result) >= 1)
{
	while($result_set = $adb->fetch_array($result))
	{
		$content=getcontent_month($result_set["contactid"]);
		$body=$content["body"];
		$body = str_replace('$logo$','<img src="cid:logo" />',$body);
		$subject=$content["subject"];
		$status=send_mail("Support",$result_set["email"],$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$body,'',$HELPDESK_SUPPORT_EMAIL_ID);
	}

}

//comment / uncomment this line if you want to hide / show the sent mail status
//showstatus($status);
$log->debug(" Send Support Notification Befoe a Month - Status: ".$status);

//used to dispaly the sent mail status
function showstatus($status)
{
	if($status == 1)
		echo "Mails sent successfully";
	else if($status == "")
		echo "No contacts matched";
	else
		echo "Error while sending mails: ".$status;
}

//function used to get the header and body content of the mail to be sent.
function getcontent_month($id)
{
	global $adb;
	$query='select vtiger_emailtemplates.subject,vtiger_emailtemplates.body from vtiger_notificationscheduler inner join vtiger_emailtemplates on vtiger_emailtemplates.templateid=vtiger_notificationscheduler.notificationbody where schedulednotificationid=7';
	$result = $adb->pquery($query, array());
	$body=$adb->query_result($result,0,'body');
	$body=getMergedDescription($body,$id,"Contacts");
	$body=getMergedDescription($body,$id,"Users");
	$res_array["subject"]=$adb->query_result($result,0,'subject');
	$res_array["body"]=$body;
	return $res_array;

}

//function used to get the header and body content of the mail to be sent.
function getcontent_week($id)
{
	global $adb;
	$query='select vtiger_emailtemplates.subject,vtiger_emailtemplates.body from vtiger_notificationscheduler inner join vtiger_emailtemplates on vtiger_emailtemplates.templateid=vtiger_notificationscheduler.notificationbody where schedulednotificationid=6';
	$result = $adb->pquery($query, array());
	$body=$adb->query_result($result,0,'body');
	$body=getMergedDescription($body,$id,"Contacts");
	$body=getMergedDescription($body,$id,"Users");
	$res_array["subject"]=$adb->query_result($result,0,'subject');
	$res_array["body"]=$body;
	return $res_array;
}

?>
