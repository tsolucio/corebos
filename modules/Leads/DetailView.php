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

require_once 'modules/Vtiger/DetailView.php';

if (useInternalMailer() == 1) {
	$smarty->assign('INT_MAILER', 'true');
}
if (isPermitted('Emails', 'CreateView', '') == 'yes') {
	//Added to pass the parents list as hidden for Emails
	$parent_email = getEmailParentsList('Leads', $_REQUEST['record'], $focus);
	$smarty->assign('HIDDEN_PARENTS_LIST', $parent_email);
}

if (!leadCanBeConverted($record)) {
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LeadAlreadyConverted', 'Leads'));
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
}

$smarty->display('DetailView.tpl');
?>
