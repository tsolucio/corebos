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

global $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

//the user might belong to multiple groups

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

$smarty->assign('CHANGE_PW_BUTTON', '');
if ((is_admin($current_user) || $_REQUEST['record'] == $current_user->id)
		&& isset($default_user_name)
		&& $default_user_name == $focus->user_name
		&& isset($lock_default_user_name)
		&& $lock_default_user_name
) {
	$buttons = "<button title='".$app_strings['LBL_EDIT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_EDIT_BUTTON_KEY']."' class='slds-button slds-button_neutral'"
		." onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.return_id.value='$focus->id'; "
		."this.form.action.value='EditView';\" type='submit' name='Edit'>  ".$app_strings['LBL_EDIT_BUTTON_LABEL']."  </button>";
	$smarty->assign('EDIT_BUTTON', $buttons);
} elseif ((is_admin($current_user) && !in_array($focus->user_name, $cbodBlockedUsers)) || $_REQUEST['record'] == $current_user->id) {
	$buttons = "<button title='".$app_strings['LBL_EDIT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_EDIT_BUTTON_KEY']."' class='slds-button slds-button_neutral'"
		." onclick=\"this.form.return_module.value='Users'; this.form.return_action.value='DetailView'; this.form.return_id.value='$focus->id'; "
		."this.form.action.value='EditView';\" type='submit' name='Edit'>  ".$app_strings['LBL_EDIT_BUTTON_LABEL']."  </button>";
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
				."' class='crmButton password small' onclick='loadPassword(".vtlib_purify($_REQUEST['record']).")' type='button' "
				."name='password' value='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_LABEL']."'>";
			break;
	}
	$smarty->assign('CHANGE_PW_BUTTON', $buttons);
}
if (is_admin($current_user) && !in_array($focus->user_name, $cbodBlockedUsers)) {
	$buttons = "<a title='".$app_strings['LBL_DUPLICATE_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_DUPLICATE_BUTTON_KEY']."' role='menuitem'"
		." href='javascript:void(0);'"
		." onclick=\"window.forms['DetailView'].return_module.value='Users'; window.forms['DetailView'].return_action.value='DetailView';"
		." window.forms['DetailView'].isDuplicate.value=true; window.forms['DetailView'].return_id.value='".vtlib_purify($_REQUEST['record'])
		."';window.forms['DetailView'].action.value='EditView'; window.forms['DetailView'].submit();\" name='Duplicate'>"
		."<span class='slds-truncate' title='".$app_strings['LBL_DUPLICATE_BUTTON_LABEL']."'><span>".$app_strings['LBL_DUPLICATE_BUTTON_LABEL'].'</span></span></a>';
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

if ($current_user->id == $_REQUEST['record'] || is_admin($current_user)) {
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
		$errormessageclass = isset($_REQUEST['error_msgclass']) ? vtlib_purify($_REQUEST['error_msgclass']) : '';
		$smarty->assign('ERROR_MESSAGE_CLASS', $errormessageclass);
		$smarty->assign('ERROR_MESSAGE', vtlib_purify($_REQUEST['error_string']));
	}
	// Gather the custom link information to display
	include_once 'vtlib/Vtiger/Link.php';
	$customlink_params = array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
	$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType($tabid, array('DETAILVIEWBASIC'), $customlink_params, null, $focus->id));
	$smarty->assign('view', null);
	$smarty->display('UserDetailView.tpl');
} else {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
}
?>