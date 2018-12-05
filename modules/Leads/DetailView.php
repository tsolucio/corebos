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
	$parent_email = getEmailParentsList('Leads', $_REQUEST['record'], $focus);
	$smarty->assign('HIDDEN_PARENTS_LIST', $parent_email);
}

if (isPermitted('Leads', 'Merge', '') == 'yes') {
	global $current_user;
	$wordTemplateResult = fetchWordTemplateList('Leads');
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

require_once 'modules/Vtiger/DetailView.php';

if (!leadCanBeConverted($record)) {
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LeadAlreadyConverted', 'Leads'));
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
}

$smarty->display('DetailView.tpl');
?>
