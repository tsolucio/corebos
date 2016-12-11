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

if (isPermitted('Emails', 'CreateView', '') == 'yes') {
	//Added to pass the parents list as hidden for Emails -- 09-11-2005
	$parent_email = getEmailParentsList('Leads', $_REQUEST['record'], $focus);
	$smarty->assign('HIDDEN_PARENTS_LIST', $parent_email);
	$vtwsObject = VtigerWebserviceObject::fromName($adb, $currentModule);
	$vtwsCRMObjectMeta = new VtigerCRMObjectMeta($vtwsObject, $current_user);
	$emailFields = $vtwsCRMObjectMeta->getEmailFields();

	$smarty->assign('SENDMAILBUTTON','permitted');
	$emails=array();
	foreach($emailFields as $key => $value) {
		$emails[]=$value;
	}
	$smarty->assign('EMAILS', $emails);
	$cond="LTrim('%s') !=''";
	$condition=array();
	foreach($emails as $key => $value) {
		$condition[]=sprintf($cond,$value);
	}
	$condition_str=implode('||',$condition);
	$js="if(".$condition_str."){fnvshobj(this,'sendmail_cont');sendmail('".$currentModule."',".vtlib_purify($_REQUEST['record']).");}else{OpenCompose('','create');}";
	$smarty->assign('JS',$js);
}

if (isPermitted('Leads', 'Merge', '') == 'yes') {
	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . ".php");

	$wordTemplateResult = fetchWordTemplateList('Leads');
	$tempCount = $adb->num_rows($wordTemplateResult);
	$tempVal = $adb->fetch_array($wordTemplateResult);
	$optionString = array();
	for ($templateCount = 0; $templateCount < $tempCount; $templateCount++) {
		$optionString[$tempVal['templateid']] = $tempVal['filename'];
		$tempVal = $adb->fetch_array($wordTemplateResult);
	}
	if ($is_admin)
		$smarty->assign('MERGEBUTTON', 'permitted');
	elseif ($tempCount > 0)
		$smarty->assign('MERGEBUTTON', 'permitted');

	$smarty->assign('TEMPLATECOUNT', $tempCount);
	$smarty->assign('WORDTEMPLATEOPTIONS', $app_strings['LBL_SELECT_TEMPLATE_TO_MAIL_MERGE']);
	$smarty->assign('TOPTIONS', $optionString);
}

$smarty->assign('USE_ASTERISK', get_use_asterisk($current_user->id));
if (useInternalMailer() == 1)
	$smarty->assign('INT_MAILER', 'true');

require_once 'modules/Vtiger/DetailView.php';

if(isPermitted($currentModule, 'CreateView', $record) == 'yes') {
	$smarty->assign('CREATE_PERMISSION', 'permitted');
	require_once 'modules/Leads/ConvertLeadUI.php';
	$uiinfo = new ConvertLeadUI($record, $current_user);
	if (isPermitted('Leads', 'ConvertLead') == 'yes'
		&& (isPermitted('Accounts', 'CreateView') == 'yes' || isPermitted('Contacts', 'CreateView') == 'yes')
		&& (vtlib_isModuleActive('Contacts') || vtlib_isModuleActive('Accounts'))
		&& !isLeadConverted($focus->id)
		&& (($uiinfo->getCompany() != null) || ($uiinfo->isModuleActive('Contacts') == true))
	) {
		$smarty->assign('CONVERTLEAD', 'permitted');
	} else {
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('LeadAlreadyConverted','Leads'));
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
	}
}

$smarty->display('DetailView.tpl');
?>