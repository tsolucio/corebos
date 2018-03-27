<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *****************************************************>***************************/
require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';
require_once 'include/logging.php';
require_once 'include/utils/utils.php';
require_once 'modules/Reports/Reports.php';

global $app_strings, $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');

$log = LoggerManager::getLogger('report_type');
global $currentModule, $image_path, $theme, $current_user;

$report_std_filter = new vtigerCRM_Smarty;
$report_std_filter->assign('MOD', $mod_strings);
$report_std_filter->assign('APP', $app_strings);
$report_std_filter->assign('IMAGE_PATH', $image_path);
$report_std_filter->assign('DATEFORMAT', $current_user->date_format);
$report_std_filter->assign('JS_DATEFORMAT', parse_calendardate($app_strings['NTC_DATE_FORMAT']));

$roleid = $current_user->column_fields['roleid'];
$user_array = getAllUserName();
asort($user_array);
$userIdStr = '';
$userNameStr = '';
$m=0;
foreach ($user_array as $userid => $username) {
	if ($userid!=$current_user->id) {
		if ($m!=0) {
			$userIdStr .= ",";
			$userNameStr .= ",";
		}
		$userIdStr .="'".$userid."'";
		$userNameStr .="'".addslashes(decode_html($username))."'";
		$m++;
	}
}

$user_groups = getAllGroupName();
asort($user_groups);
$groupIdStr = "";
$groupNameStr = "";
$l=0;
foreach ($user_groups as $grpid => $groupname) {
	if ($l!=0) {
		$groupIdStr .= ",";
		$groupNameStr .= ",";
	}
	$groupIdStr .= "'".$grpid."'";
	$groupNameStr .= "'".addslashes(decode_html($groupname))."'";
	$l++;
}
if (isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$reportid = vtlib_purify($_REQUEST['record']);
	$report_std_filter->assign('VISIBLECRITERIA', getVisibleCriteria($recordid, false));
	$report_std_filter->assign('MEMBER', getShareInfo($recordid));
} else {
	$report_std_filter->assign('VISIBLECRITERIA', getVisibleCriteria('', false));
}
$report_std_filter->assign('GROUPNAMESTR', $groupNameStr);
$report_std_filter->assign('USERNAMESTR', $userNameStr);
$report_std_filter->assign('GROUPIDSTR', $groupIdStr);
$report_std_filter->assign('USERIDSTR', $userIdStr);

//include("modules/Reports/StandardFilter.php");
//include("modules/Reports/AdvancedFilter.php");

$report_std_filter->display('ReportSharing.tpl');
?>
