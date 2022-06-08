<?php
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  ********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'Smarty_setup.php';
global $app_strings, $currentModule, $current_user;
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
if ($current_user->is_admin != 'on') {
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
	$smarty->assign('ERROR_MESSAGE', $app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'><br>".$app_strings['LBL_GO_BACK'].'<br><br>');
	$smarty->display('applicationmessage.tpl');
	die();
}

$log = LoggerManager::getLogger('user_list');

global $mod_strings,$adb, $theme, $current_language;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$mod_strings = return_module_language($current_language, 'Users');
$focus = new Users();
$no_of_users=UserCount();

//Display the mail send status
$smarty = new vtigerCRM_Smarty;
if (!empty($_REQUEST['mail_error'])) {
	require_once 'modules/Emails/mail.php';
	$error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $app_strings['LBL_MAIL_NOT_SENT_TO_USER']. ' ' . vtlib_purify($_REQUEST['user']). '. ' .$app_strings['LBL_PLS_CHECK_EMAIL_N_SERVER'];
	$smarty->assign('ERROR_MSG', $app_strings['LBL_MAIL_SEND_STATUS'].' <b><font class="warning">'.$error_msg.'</font></b>');
}

$list_query = getListQuery('Users');

$userid = array();
$blockedusers = $cbodBlockedUsers;
$blockedusers[] = 'admin';
$userid_Query = 'SELECT id,user_name FROM vtiger_users WHERE user_name IN ('.generateQuestionMarks($blockedusers).')';
$total = "SELECT COUNT(id) as users FROM vtiger_users";
$result = $adb->pquery($total, array());
$total_users = $adb->query_result($result, 0, 'users');
$admin ="SELECT COUNT(id) as users FROM vtiger_users WHERE is_admin = ?";
$admin_result = $adb->pquery($admin, array('on'));
$total_admin = $adb->query_result($admin_result, 0, 'users');
$active_user ="SELECT COUNT(id) as users FROM vtiger_users WHERE status = ?";
$active_result = $adb->pquery($active_user, array('Active'));
$total_active = $adb->query_result($active_result, 0, 'users');
$inactive_user ="SELECT COUNT(id) as users FROM vtiger_users WHERE status = ?";
$inactive_result = $adb->pquery($inactive_user, array('Inactive'));
$inactive = $adb->query_result($inactive_result, 0, 'users');

$users = $adb->pquery($userid_Query, array($blockedusers));
$norows = $adb->num_rows($users);
if ($norows  > 0) {
	for ($i=0; $i<$norows; $i++) {
		$id = $adb->query_result($users, $i, 'id');
		$userid[$id] = $adb->query_result($users, $i, 'user_name');
	}
}
$smarty->assign('USERNODELETE', $userid);
$smarty->assign('TOTALUSERS', $total_users);
$smarty->assign('TOTALADMIN', $total_admin);
$smarty->assign('TOTALACTIVE', $total_active);
$smarty->assign('TOTALINACTIVE', $inactive);

$userid_noedit = array();
if (count($cbodBlockedUsers)>0) {
	$userid_noedit_Query = 'SELECT id,user_name FROM vtiger_users WHERE user_name IN ('.generateQuestionMarks($cbodBlockedUsers).')';
	$users_noedit = $adb->pquery($userid_noedit_Query, array($cbodBlockedUsers));
	$norows_noedit = $adb->num_rows($users_noedit);
	if ($norows_noedit > 0) {
		for ($i=0; $i<$norows_noedit; $i++) {
			$id = $adb->query_result($users_noedit, $i, 'id');
			if ($current_user->id != $id) {
				$userid_noedit[$id] = $adb->query_result($users_noedit, $i, 'user_name');
			}
		}
	}
}
$smarty->assign('USERNOEDIT', $userid_noedit);

if (!empty($_REQUEST['sorder'])) {
	$sorder = $adb->sql_escape_string($_REQUEST['sorder']);
} else {
	$sorder = $focus->getSortOrder();
}
coreBOS_Session::set('USERS_SORT_ORDER', $sorder);

if (!empty($_REQUEST['order_by'])) {
	$order_by = $adb->sql_escape_string($_REQUEST['order_by']);
} else {
	$order_by = $focus->getOrderBy();
}
coreBOS_Session::set('USERS_ORDER_BY', $order_by);

if (empty($_SESSION['lvs'][$currentModule])) {
	coreBOS_Session::delete('lvs');
	$modObj = new ListViewSession();
	$modObj->sorder = $sorder;
	$modObj->sortby = $order_by;
	coreBOS_Session::set('lvs^'.$currentModule, get_object_vars($modObj));
}

if (!empty($order_by)) {
	$list_query .= ' ORDER BY '.$order_by.' '.$sorder;
}

if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
	$count_result = $adb->query(mkCountQuery($list_query));
	$noofrows = $adb->query_result($count_result, 0, 'count');
} else {
	$noofrows = null;
}
$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, '', $queryMode);

$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);

$limit_start_rec = ($start-1) * $list_max_entries_per_page;
$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec, $noofrows);

$adminStatusFilterValueQuery = 'select distinct is_admin from vtiger_users';
$userStatusFilterValueQuery = 'select distinct status from vtiger_users';
$adminStatusValues = $adb->pquery($adminStatusFilterValueQuery, array());
$userStatusValues = $adb->pquery($userStatusFilterValueQuery, array());
$noadminstatusrows = $adb->num_rows($adminStatusValues);
$nouserstatusrows = $adb->num_rows($userStatusValues);
$default_admin_status_value_filters ='';

if ($noadminstatusrows > 0) {
	for ($i=0; $i < $noadminstatusrows; $i++) {
		$status = $adb->query_result($adminStatusValues, $i, 'is_admin');
		if ($status == 'on') {
			$lbl_trans_key = 'LBL_ON';
		} else {
			$lbl_trans_key = 'LBL_OFF';
		}
		$default_admin_status_value_filters = $default_admin_status_value_filters.'<option value='.$status.'>'.getTranslatedString($lbl_trans_key, 'Users').'</option>';
	}
}

$default_user_status_value_filters ='';

if ($nouserstatusrows > 0) {
	for ($i=0; $i < $nouserstatusrows; $i++) {
		$status = $adb->query_result($userStatusValues, $i, 'status');
		if ($status == 'Active') {
			$lbl_trans_key = 'LBL_ACTIVE';
		} else {
			$lbl_trans_key = 'LBL_INACTIVE';
		}
		$default_user_status_value_filters = $default_user_status_value_filters.'<option value='.$status.'>'.getTranslatedString($lbl_trans_key, 'Users').'</option>';
	}
	$default_user_status_value_filters = $default_user_status_value_filters.'<option value="loggedin">'.getTranslatedString('LOGGED IN', 'Users').'</option>';
}

if (isset($_REQUEST['error_string'])) {
	$errormessageclass = isset($_REQUEST['error_msgclass']) ? vtlib_purify($_REQUEST['error_msgclass']) : '';
	$smarty->assign('ERROR_MESSAGE_CLASS', $errormessageclass);
	$smarty->assign('ERROR_MESSAGE', vtlib_purify($_REQUEST['error_string']));
}
$smarty->assign('recordListRange', $recordListRangeMsg);
$url_string = '';
$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, 'Users', 'index', '');
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('CURRENT_USERID', $current_user->id);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('LIST_ADMIN_STATUS', $default_admin_status_value_filters);
$smarty->assign('LIST_USER_STATUS', $default_user_status_value_filters);
$smarty->assign('LIST_FIELDS', $focus->list_fields_names);
$smarty->assign('LIST_HEADER', $focus->getUserListHeader());
$smarty->assign('PAGE_START_RECORD', $limit_start_rec);
$smarty->assign('NAVIGATION', $navigationOutput);
$smarty->assign('USER_IMAGES', getUserImageNames());
if (!empty($_REQUEST['ajax'])) {
	$smarty->display('UserListViewContents.tpl');
} else {
	$smarty->display('UserListView.tpl');
}
?>