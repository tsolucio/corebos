<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

if (useInternalMailer() == 1) {
	$smarty->assign('INT_MAILER', 'true');
}
if (isPermitted('Emails', 'CreateView', '') == 'yes') {
	//Added to pass the parents list as hidden for Emails
	$parent_email = getEmailParentsList('Contacts', $_REQUEST['record'], $focus);
	$smarty->assign('HIDDEN_PARENTS_LIST', $parent_email);
	$vtwsObject = VtigerWebserviceObject::fromName($adb, $currentModule);
	$vtwsCRMObjectMeta = new VtigerCRMObjectMeta($vtwsObject, $current_user);
	$emailFields = $vtwsCRMObjectMeta->getEmailFields();
	$smarty->assign('SENDMAILBUTTON', 'permitted');
	$emails=array();
	foreach ($emailFields as $value) {
		$emails[]=$value;
	}
	$smarty->assign('EMAILS', $emails);
	$cond="LTrim('%s') !=''";
	$condition=array();
	foreach ($emails as $value) {
		$condition[]=sprintf($cond, $value);
	}
	$condition_str=implode('||', $condition);
	$js='if('.$condition_str."){fnvshobj(this,'sendmail_cont');sendmail('".$currentModule."',".vtlib_purify($_REQUEST['record']).");}else{OpenCompose('','create');}";
	$smarty->assign('JS', $js);
} else {
	$smarty->assign('SENDMAILBUTTON', 'NOTpermitted');
}

if (isPermitted('Contacts', 'Merge', '') == 'yes') {
	$wordTemplateResult = fetchWordTemplateList('Contacts');
	$tempCount = $adb->num_rows($wordTemplateResult);
	$tempVal = $adb->fetch_array($wordTemplateResult);
	$optionString = array();
	for ($templateCount=0; $templateCount<$tempCount; $templateCount++) {
		$optionString[$tempVal['templateid']]=$tempVal['filename'];
		$tempVal = $adb->fetch_array($wordTemplateResult);
	}
	if (is_admin($current_user)) {
		$smarty->assign('MERGEBUTTON', 'permitted');
	} elseif ($tempCount >0) {
		$smarty->assign('MERGEBUTTON', 'permitted');
	}
	$smarty->assign('TEMPLATECOUNT', $tempCount);
	$smarty->assign('WORDTEMPLATEOPTIONS', $app_strings['LBL_SELECT_TEMPLATE_TO_MAIL_MERGE']);
	$smarty->assign('TOPTIONS', $optionString);
}

$smarty->assign('CONTACT_PERMISSION', CheckFieldPermission('contact_id', 'Calendar'));

require_once 'modules/Vtiger/DetailView.php';

$sql = $adb->pquery('select accountid from vtiger_contactdetails where contactid=?', array($focus->id));
$accountid = $adb->query_result($sql, 0, 'accountid');
if ($accountid == 0) {
	$accountid='';
}
$smarty->assign('accountid', $accountid);

$smarty->display('DetailView.tpl');
?>
