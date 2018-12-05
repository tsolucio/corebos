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
require_once 'include/utils/utils.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/GetUserGroups.php';
require_once 'modules/Users/UserTimeZonesArray.php';
global $current_user, $theme, $default_language, $adb, $currentModule, $app_strings, $mod_strings;

$focus = new Users();

if (!empty($_REQUEST['record'])) {
	$focus->retrieve_entity_info(vtlib_purify($_REQUEST['record']), 'Users');
	$focus->id = vtlib_purify($_REQUEST['record']);
}

$smarty = new vtigerCRM_Smarty;

if (empty($_REQUEST['record']) || $focus->user_name == '') {
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERR_USER_DOESNOT_EXISTS', 'Users'));
	$smarty->display('applicationmessage.tpl');
	exit;
}

if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = '';
}

global $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

//the user might belong to multiple groups
$log->info('User detail view');

$category = getParenttab();

$smarty->assign('UMOD', $mod_strings);
global $current_language;
$smod_strings = return_module_language($current_language, 'Settings');
$smarty->assign('MOD', $smod_strings);
$smarty->assign('APP', $app_strings);

$oGetUserGroups = new GetUserGroups();
$oGetUserGroups->getAllUserGroups($focus->id);
if (useInternalMailer() == 1) {
	$smarty->assign('INT_MAILER', 'true');
}

$smarty->assign('GROUP_COUNT', count($oGetUserGroups->user_groups));
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('ID', $focus->id);
$smarty->assign('CATEGORY', $category);

if (!empty($_REQUEST['modechk'])) {
	$modepref = vtlib_purify($_REQUEST['modechk']);
	if ($_REQUEST['modechk'] == 'prefview') {
		$parenttab = '';
	} else {
		$parenttab = 'Settings';
	}
} else {
	$parenttab = 'Settings';
}

$smarty->assign('PARENTTAB', $parenttab);
$smarty->assign('CHANGE_PW_BUTTON', '');
if ((is_admin($current_user) || $_REQUEST['record'] == $current_user->id)
		&& isset($default_user_name)
		&& $default_user_name == $focus->user_name
		&& isset($lock_default_user_name)
		&& $lock_default_user_name == true
) {
	$buttons = "<input title='".$app_strings['LBL_EDIT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_EDIT_BUTTON_KEY']."' class='crmButton small edit'"
		." onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.return_id.value='$focus->id'; "
		."this.form.action.value='EditView';\" type='submit' name='Edit' value='  ".$app_strings['LBL_EDIT_BUTTON_LABEL']."  '>";
	$smarty->assign('EDIT_BUTTON', $buttons);
} elseif ((is_admin($current_user) && !in_array($focus->user_name, $cbodBlockedUsers)) || $_REQUEST['record'] == $current_user->id) {
	$buttons = "<input title='".$app_strings['LBL_EDIT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_EDIT_BUTTON_KEY']."' class='crmButton small edit'"
		." onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.return_id.value='$focus->id'; "
		."this.form.action.value='EditView';\" type='submit' name='Edit' value='  ".$app_strings['LBL_EDIT_BUTTON_LABEL']."  '>";
	$smarty->assign('EDIT_BUTTON', $buttons);
	$authType = GlobalVariable::getVariable('User_AuthenticationType', 'SQL');
	if (is_admin($current_user)) {
		$authType = 'SQL'; // admin users always login locally
	}
	switch (strtoupper($authType)) {
		case 'AD':
			$buttons = 'Active Directory';
			break;
		case 'LDAP':
			$buttons = 'LDAP';
			break;
		default:
			$buttons = "<input title='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_TITLE']."' accessKey='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_KEY']
				."' class='crmButton password small' onclick='return window.open(\"index.php?module=Users&action=ChangePassword&form=DetailView\",\"test\","
				."\"width=700,height=490,resizable=no,scrollbars=0, toolbar=no, titlebar=no, left=200, top=226, screenX=100, screenY=126\");' type='button' "
				."name='password' value='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_LABEL']."'>";
			break;
	}
	$smarty->assign('CHANGE_PW_BUTTON', $buttons);
}
if (is_admin($current_user) && !in_array($focus->user_name, $cbodBlockedUsers)) {
	$buttons = "<input title='".$app_strings['LBL_DUPLICATE_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_DUPLICATE_BUTTON_KEY']."' class='crmButton small create'"
		." onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value=true; this.form.return_id.value='"
		.vtlib_purify($_REQUEST['record'])."';this.form.action.value='EditView'\" type='submit' name='Duplicate' value='".$app_strings['LBL_DUPLICATE_BUTTON_LABEL']."'>";
	$smarty->assign('DUPLICATE_BUTTON', $buttons);

	//done so that only the admin user can see the customize tab button
	if ($_REQUEST['record'] != $current_user->id) {
		$buttons = "<input title='".$app_strings['LBL_DELETE_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_DELETE_BUTTON_KEY']
			."' class='classBtn' onclick=\"deleteUser('$focus->id')\" type='button' name='Delete' value='  ".$app_strings['LBL_DELETE_BUTTON_LABEL']."  '>";
		$smarty->assign('DELETE_BUTTON', $buttons);
	}
}

if (is_admin($current_user)) {
	$smarty->assign('IS_ADMIN', true);
} else {
	$smarty->assign('IS_ADMIN', false);
}

$lead_tables = array('vtiger_users','vtiger_user2role');
$tabid = getTabid('Users');
$validationData = getDBValidationData($lead_tables, $tabid);
$data = split_validationdataArray($validationData);

if ($current_user->id == $_REQUEST['record'] || is_admin($current_user) == true) {
	$smarty->assign('VALIDATION_DATA_FIELDNAME', $data['fieldname']);
	$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', $data['datatype']);
	$smarty->assign('VALIDATION_DATA_FIELDLABEL', $data['fieldlabel']);
	$smarty->assign('MODULE', 'Users');
	$smarty->assign('cbodUserBlocked', in_array($focus->user_name, $cbodBlockedUsers));
	$smarty->assign('CURRENT_USERID', $current_user->id);
	$HomeValues = $focus->getHomeStuffOrder($focus->id);
	$smarty->assign('TAGCLOUDVIEW', $HomeValues['Tag Cloud']);
	$smarty->assign('SHOWTAGAS', getTranslatedString($HomeValues['showtagas'], 'Users'));
	unset($HomeValues['Tag Cloud'], $HomeValues['showtagas']);
	$smarty->assign('HOMEORDER', $HomeValues);
	$blocks = getBlocks($currentModule, 'detail_view', '', $focus->column_fields);
	$smarty->assign('BLOCKS', $blocks);
	$smarty->assign('USERNAME', getFullNameFromArray('Users', $focus->column_fields));
	$smarty->assign('HOUR_FORMAT', $focus->hour_format);
	$smarty->assign('START_HOUR', $focus->start_hour);
	coreBOS_Session::set('Users_FORM_TOKEN', rand(5, 2000) * rand(2, 7));
	$smarty->assign('FORM_TOKEN', $_SESSION['Users_FORM_TOKEN']);
	$smarty->assign('USE_ASTERISK', get_use_asterisk($current_user->id));

	if ($current_user->mustChangePassword()) {
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERR_MUST_CHANGE_PASSWORD', 'Users'));
		$smarty->assign('mustChangePassword', 1);
	} else {
		$smarty->assign('mustChangePassword', 0);
	}
	if (isset($_REQUEST['error_string'])) {
		$smarty->assign('ERROR_MESSAGE', vtlib_purify($_REQUEST['error_string']));
	}

	$smarty->assign('view', null);
	$smarty->display('UserDetailView.tpl');
} else {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
}
?>