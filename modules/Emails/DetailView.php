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
require_once 'data/Tracker.php';
require_once 'include/upload_file.php';
require_once 'include/utils/utils.php';

global $log, $app_strings, $mod_strings, $currentModule, $theme, $default_charset;

$focus = CRMEntity::getInstance($currentModule);

$smarty = new vtigerCRM_Smarty;
if (isset($_REQUEST['record'])) {
	global $adb;
	$focus->retrieve_entity_info($_REQUEST['record'], 'Emails');
	$log->info('Entity info successfully retrieved for DetailView.');
	$focus->id = $_REQUEST['record'];
	$query = 'select email_flag,from_email,to_email,cc_email,bcc_email,date_start,time_start
		from vtiger_emaildetails
		left join vtiger_activity on vtiger_emaildetails.emailid = vtiger_activity.activityid
		where emailid = ?';
	$result = $adb->pquery($query, array($focus->id));
	$smarty->assign('FROM_MAIL', $adb->query_result($result, 0, 'from_email'));
	$to_email = json_decode($adb->query_result($result, 0, 'to_email'), true);
	$cc_email = json_decode($adb->query_result($result, 0, 'cc_email'), true);
	$smarty->assign('TO_MAIL', vt_suppressHTMLTags(@implode(',', $to_email)));
	$smarty->assign('CC_MAIL', vt_suppressHTMLTags(@implode(',', $cc_email)));
	$bcc_email = json_decode($adb->query_result($result, 0, 'bcc_email'), true);
	$smarty->assign('BCC_MAIL', vt_suppressHTMLTags(@implode(',', $bcc_email)));
	$smarty->assign('EMAIL_FLAG', $adb->query_result($result, 0, 'email_flag'));

	$dt = new DateTimeField($adb->query_result($result, 0, 'date_start'));
	$fmtdate = $dt->getDisplayDate($current_user);
	$smarty->assign('DATE_START', $fmtdate);
	$smarty->assign('TIME_START', $adb->query_result($result, 0, 'time_start'));
	if (!empty($focus->column_fields['name'])) {
		$focus->name = $focus->column_fields['name'];
	} else {
		$focus->name = $focus->column_fields['subject'];
	}
}
if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = '';
}

//needed when creating a new email with default values passed in
if (isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) {
	$focus->contact_name = $_REQUEST['contact_name'];
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) {
	$focus->contact_id = $_REQUEST['contact_id'];
}
if (isset($_REQUEST['opportunity_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['opportunity_name'];
}
if (isset($_REQUEST['opportunity_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['opportunity_id'];
}
if (isset($_REQUEST['account_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['account_name'];
}
if (isset($_REQUEST['account_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['account_id'];
}
if (isset($_REQUEST['parent_name'])) {
	$focus->parent_name = $_REQUEST['parent_name'];
}
if (isset($_REQUEST['parent_id'])) {
	$focus->parent_id = $_REQUEST['parent_id'];
}
if (isset($_REQUEST['parent_type'])) {
	$focus->parent_type = $_REQUEST['parent_type'];
} else {
	if (GlobalVariable::getVariable('Application_B2B', '1')) {
		$focus->parent_type = 'Accounts';
	} else {
		$focus->parent_type = 'Contacts';
	}
}
if (isset($_REQUEST['filename']) && is_null($focus->filename)) {
	$focus->filename = $_REQUEST['filename'];
}

$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('LBL_CHARSET', $default_charset);
$smarty->assign('UPDATEINFO', updateInfo($focus->id));
if (isset($_REQUEST['return_module'])) {
	$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
}
if (isset($_REQUEST['return_action'])) {
	$smarty->assign('RETURN_ACTION', vtlib_purify($_REQUEST['return_action']));
}
if (isset($_REQUEST['return_id'])) {
	$smarty->assign('RETURN_ID', vtlib_purify($_REQUEST['return_id']));
}
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', 'themes/'.$theme.'/images/');
$category = getParentTab();
$smarty->assign('CATEGORY', $category);

if (isset($focus->name)) {
	$smarty->assign('NAME', $focus->name);
} else {
	$smarty->assign('NAME', '');
}

$entries = getBlocks($currentModule, 'detail_view', '', $focus->column_fields);
//changed this to view description in all langauge - bharath
$smarty->assign('BLOCKS', $entries[$mod_strings['LBL_EMAIL_INFORMATION']]);
$smarty->assign('SINGLE_MOD', 'Email');

if (isPermitted($currentModule, 'CreateView', $record) == 'yes') {
	$smarty->assign('CREATE_PERMISSION', 'permitted');
}

if (isPermitted('Emails', 'Delete', $_REQUEST['record']) == 'yes') {
	$smarty->assign('DELETE', 'permitted');
}
$smarty->assign('ID', $focus->id);

$check_button = Button_Check($module);
$smarty->assign('CHECK', $check_button);

$smarty->assign('MODULE', $currentModule);
if ($_REQUEST['module']=='cbCalendar') {
	$smarty->assign('FROMCALENDAR', 'true');
}
$smarty->display('EmailDetailView.tpl');
?>