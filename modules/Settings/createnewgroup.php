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

global $adb, $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$smarty = new vtigerCRM_Smarty;
$parentGroupArray=array();
if (isset($_REQUEST['groupId']) && $_REQUEST['groupId'] != '') {
	$mode = 'edit';
	$groupId=vtlib_purify($_REQUEST['groupId']);
	$groupInfo=getGroupInfo($groupId);
	require_once 'include/utils/GetParentGroups.php';
	$parGroups = new GetParentGroups();
	$parGroups->parent_groups[]=$groupId;
	$parGroups->getAllParentGroups($groupId);
	$parentGroupArray=$parGroups->parent_groups;
} else {
	$mode = 'create';
	$groupId = 0;
	$groupInfo = array(0=>'',1=>'');
	if (isset($_REQUEST['error']) && ($_REQUEST['error']=='true')) {
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_GROUP_NAME_ERROR', 'Users'));
		$smarty->display('applicationmessage.tpl');
		$groupInfo[] = vtlib_purify($_REQUEST['groupname']);
		$groupInfo[] = vtlib_purify($_REQUEST['desc']);
	}
}

//Constructing the Role Array
$roleDetails=getAllRoleDetails();
asort($roleDetails);
$roleIdStrArr=array();
$roleNameStrArr=array();
foreach ($roleDetails as $roleId => $roleInfo) {
	if ($roleId != 'H1') {
		$roleName=$roleInfo[0];
		$roleIdStrArr[] = "'".$roleId."'";
		$roleNameStrArr[] = "'".addslashes(decode_html($roleName))."'";
	}
}
$roleIdStr=implode(',', $roleIdStrArr);
$roleNameStr=implode(',', $roleNameStrArr);

//Constructing the User Array
$userDetails=getAllUserName();
asort($userDetails);
$userIdStrArr=array();
$userNameStrArr=array();
foreach ($userDetails as $userId => $userInfo) {
	$userIdStrArr[] = "'".$userId."'";
	$userNameStrArr[] = "'".addslashes(decode_html($userInfo))."'";
}
$userIdStr=implode(',', $userIdStrArr);
$userNameStr=implode(',', $userNameStrArr);

//Constructing the Group Array
$grpDetails=getAllGroupName();
asort($grpDetails);
$grpIdStrArr=array();
$grpNameStrArr=array();
foreach ($grpDetails as $grpId => $grpName) {
	if (!in_array($grpId, $parentGroupArray)) {
		$grpIdStrArr[] = "'".$grpId."'";
		$grpNameStrArr[] = "'".addslashes(decode_html($grpName))."'";
	}
}
$grpIdStr=implode(',', $grpIdStrArr);
$grpNameStr=implode(',', $grpNameStrArr);
$member = array();
if ($mode == 'edit') {
	$groupMemberArr=$groupInfo[2];
	foreach ($groupMemberArr as $memberType => $memberValue) {
		foreach ($memberValue as $memberId) {
			if ($memberType == 'groups') {
				$memberName=fetchGroupName($memberId);
				$memberDisplay='Group::';
			} elseif ($memberType == 'roles') {
				$memberName=getRoleName($memberId);
				$memberDisplay='Roles::';
			} elseif ($memberType == 'rs') {
				$memberName=getRoleName($memberId);
				$memberDisplay='RoleAndSubordinates::';
			} elseif ($memberType == 'users') {
				$memberName=getUserFullName($memberId);
				$memberDisplay='User::';
			}
			$member[]=$memberType.'::'.$memberId;
			$member[]=$memberDisplay.$memberName;
		}
	}
	$smarty->assign('MEMBER', array_chunk($member, 2));
} else {
	$smarty->assign('MEMBER', $member);
}
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('ROLEIDSTR', $roleIdStr);
$smarty->assign('ROLENAMESTR', $roleNameStr);
$smarty->assign('USERIDSTR', $userIdStr);
$smarty->assign('USERNAMESTR', $userNameStr);
$smarty->assign('GROUPIDSTR', $grpIdStr);
$smarty->assign('GROUPNAMESTR', $grpNameStr);
$smarty->assign('RETURN_ACTION', (isset($_REQUEST['returnaction']) ? vtlib_purify($_REQUEST['returnaction']) : ''));
$smarty->assign('GROUPID', $groupId);
$smarty->assign('MODE', $mode);
$smarty->assign('GROUPNAME', $groupInfo[0]);
$smarty->assign('DESCRIPTION', $groupInfo[1]);

$smarty->display('GroupEditView.tpl');
?>