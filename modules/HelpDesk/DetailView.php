<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

if(isPermitted('HelpDesk','Merge','') == 'yes') {
	require("user_privileges/user_privileges_".$current_user->id.".php");
	require_once('include/utils/UserInfoUtil.php');
	$wordTemplateResult = fetchWordTemplateList("HelpDesk");
	$tempCount = $adb->num_rows($wordTemplateResult);
	$tempVal = $adb->fetch_array($wordTemplateResult);
	$optionString = array();
	for($templateCount=0;$templateCount<$tempCount;$templateCount++) {
		$optionString[$tempVal["templateid"]]=$tempVal["filename"];
		$tempVal = $adb->fetch_array($wordTemplateResult);
	}
	if($is_admin)
		$smarty->assign('MERGEBUTTON','permitted');
	elseif($tempCount >0)
		$smarty->assign('MERGEBUTTON','permitted');
	$smarty->assign('TEMPLATECOUNT',$tempCount);
	$smarty->assign('WORDTEMPLATEOPTIONS',$app_strings['LBL_SELECT_TEMPLATE_TO_MAIL_MERGE']);
	$smarty->assign('TOPTIONS',$optionString);
}

require_once 'modules/Vtiger/DetailView.php';

//Added code for Error display in sending mail to assigned to user when ticket is created or updated.
if(!empty($_REQUEST['mail_error'])) {
	require_once("modules/Emails/mail.php");
	$ticket_owner = getUserFullName($focus->column_fields['assigned_user_id']);
	$error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $app_strings['LBL_MAIL_NOT_SENT_TO_USER']. ' ' . $ticket_owner. '. ' .$app_strings['LBL_PLS_CHECK_EMAIL_N_SERVER'];
	echo $mod_strings['LBL_MAIL_SEND_STATUS'].' <b><font class="warning">'.$error_msg.'</font></b>';
}

//Added button to Convert the ticket to FAQ
if(isPermitted('Faq','CreateView','') == 'yes')
	$smarty->assign('CONVERTASFAQ','permitted');

//Added to display the ticket comments information
$smarty->assign('COMMENT_BLOCK',$focus->getCommentInformation($record));
$smarty->assign('TICKETID', $record);


$smarty->display('DetailView.tpl');
?>