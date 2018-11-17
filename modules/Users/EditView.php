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
require_once 'modules/Users/Users.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'modules/Users/Forms.php';
require_once 'include/database/PearDatabase.php';
require_once 'modules/Leads/ListViewTop.php';

global $app_strings, $mod_strings, $currentModule, $default_charset;

function showLicense() {
	include 'modules/Users/showLicense.php';
	die();
}

$smarty=new vtigerCRM_Smarty;
$focus = new Users();

if (isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
	$smarty->assign('ID', vtlib_purify($_REQUEST['record']));
	$mode='edit';
	if (!is_admin($current_user) && $_REQUEST['record'] != $current_user->id) {
		die('Unauthorized access to user administration.');
	}
	$focus->retrieve_entity_info(vtlib_purify($_REQUEST['record']), 'Users');
	$smarty->assign('USERNAME', getFullNameFromArray('Users', $focus->column_fields));
} else {
	$mode='create';
	// ODController user creation acceptance
	if ($coreBOSOnDemandActive && $cbodShowLicenseOnUserCreation && empty($_REQUEST['creation_accepted'])) {
		showLicense();
	}
}

if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = '';
	$focus->user_name = '';
	$mode='create';
	// ODController user creation acceptance
	if ($coreBOSOnDemandActive && $cbodShowLicenseOnUserCreation && empty($_REQUEST['creation_accepted'])) {
		showLicense();
	}

	//When duplicating the user the password fields should be empty
	$focus->column_fields['user_password']='';
	$focus->column_fields['confirm_password']='';
}
if (empty($focus->column_fields['time_zone'])) {
	$focus->column_fields['time_zone'] = DateTimeField::getDBTimeZone();
}
if ($mode != 'edit') {
	setObjectValuesFromRequest($focus);
}
global $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$log->info('User edit view');

$smarty->assign('JAVASCRIPT', get_validate_record_js());
$smarty->assign('UMOD', $mod_strings);
global $current_language;
$smod_strings = return_module_language($current_language, 'Settings');
$smarty->assign('MOD', $smod_strings);
$smarty->assign('CURRENT_USERID', $current_user->id);
$smarty->assign('APP', $app_strings);

if (isset($_REQUEST['error_string'])) {
	$smarty->assign('ERROR_STRING', "<font class='error'>Error: ".vtlib_purify($_REQUEST['error_string']).'</font>');
}
if (isset($_REQUEST['return_module'])) {
	$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
	$RETURN_MODULE=vtlib_purify($_REQUEST['return_module']);
}
if (isset($_REQUEST['return_action'])) {
	$smarty->assign('RETURN_ACTION', vtlib_purify($_REQUEST['return_action']));
	$RETURN_ACTION = vtlib_purify($_REQUEST['return_action']);
}
if ((empty($_REQUEST['isDuplicate']) || $_REQUEST['isDuplicate'] != 'true') && isset($_REQUEST['return_id'])) {
	$smarty->assign('RETURN_ID', vtlib_purify($_REQUEST['return_id']));
	$RETURN_ID = vtlib_purify($_REQUEST['return_id']);
} else {
	$smarty->assign('RETURN_ID', 0);
}
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$focus->mode = $mode;
$smarty->assign('MASS_EDIT', '0');
$disp_view = getView($focus->mode);
$smarty->assign('IMAGENAME', isset($focus->imagename) ? $focus->imagename : '');
$smarty->assign('MASS_EDIT', '0');
$blocks = getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields);
$smarty->assign('BLOCKS', $blocks);
$smarty->assign('MODULE', 'Settings');
$smarty->assign('MODE', $focus->mode);
$smarty->assign('HOUR_FORMAT', isset($focus->hour_format) ? $focus->hour_format : '');
$smarty->assign('START_HOUR', isset($focus->start_hour) ? $focus->start_hour : '');
if (isset($_REQUEST['Edit']) && $_REQUEST['Edit'] == ' Edit ') {
	$smarty->assign('READONLY', 'readonly');
	$smarty->assign('USERNAME_READONLY', 'readonly');
}
if ((empty($_REQUEST['isDuplicate']) || $_REQUEST['isDuplicate'] != 'true') && isset($_REQUEST['record'])) {
	$smarty->assign('USERNAME_READONLY', 'readonly');
}
$HomeValues = $focus->getHomeStuffOrder($focus->id);
$smarty->assign('TAGCLOUDVIEW', $HomeValues['Tag Cloud']);
$smarty->assign('SHOWTAGAS', $HomeValues['showtagas']);
unset($HomeValues['Tag Cloud'], $HomeValues['showtagas']);
$smarty->assign('HOMEORDER', $HomeValues);

$smarty->assign('tagshow_options', array(
 'flat' => $mod_strings['flat'],
 'hring' => $mod_strings['hring'],
 'vring' => $mod_strings['vring'],
 'hcylinder' => $mod_strings['hcylinder'],
 'vcylinder' => $mod_strings['vcylinder'],
));
$smarty->assign('DUPLICATE', (isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : ''));
$smarty->assign('USER_MODE', $mode);
$smarty->assign('PARENTTAB', getParentTab());
coreBOS_Session::set('Users_FORM_TOKEN', rand(5, 2000) * rand(2, 7));
$smarty->assign('FORM_TOKEN', $_SESSION['Users_FORM_TOKEN']);

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));

// When creating a new user with LDAP or AD access, add a button in the template which allows to load
// full name, email address, telephone, etc from the LDAP server, so the admin doesn't have to enter this information manually
$LdapBtnText = '';
if ($mode == 'create') {
	$authType = GlobalVariable::getVariable('User_AuthenticationType', 'SQL');
	switch (strtoupper($authType)) {
		case 'LDAP':
			$LdapBtnText = 'LDAP';
			break;
		case 'AD':
			$LdapBtnText = 'Active Dir.';
			break;
	}
}
$smarty->assign('LDAP_BUTTON', $LdapBtnText);

$smarty->display('UserEditView.tpl');
?>
