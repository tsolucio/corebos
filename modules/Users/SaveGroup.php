<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
global $adb, $mod_strings;

$groupName = vtlib_purify(trim($_REQUEST['groupName']));
$mode = vtlib_purify($_REQUEST['mode']);

if (isset($_REQUEST['dup_check']) && $_REQUEST['dup_check']!='') {
	if ($mode != 'edit') {
		$query = 'select groupname from vtiger_groups where groupname=?';
		$params = array($groupName);
	} else {
		$groupid = vtlib_purify($_REQUEST['groupid']);
		$query = 'select groupname from vtiger_groups where groupname=? and groupid !=?';
		$params = array($groupName, $groupid);
	}
	$result = $adb->pquery($query, $params);

	$user_result = $adb->pquery('SELECT user_name FROM vtiger_users WHERE user_name =?', array($groupName));

	if ($adb->num_rows($result) > 0) {
		echo $mod_strings['LBL_GROUPNAME_EXIST'];
		die;
	} elseif ($adb->num_rows($user_result) > 0) {
		echo $mod_strings['LBL_USERNAME_EXIST'];
		die;
	} else {
		echo 'SUCCESS';
		die;
	}
}

/** returns the group members in an formatted array
  * @param $member_array -- member_array:: Type varchar
  * @returns $groupMemberArray:: Type varchar
	$groupMemberArray['groups'] -- gives the array of sub groups members ;
	$groupMemberArray['roles'] -- gives the array of roles present in the group ;
	$groupMemberArray['rs'] -- gives the array of roles & subordinates present in the group ;
	$groupMemberArray['users'] -- gives the array of roles & subordinates present in the group;
*/
function constructGroupMemberArray($member_array) {
	$groupMemberArray=array();
	$roleArray=array();
	$roleSubordinateArray=array();
	$groupArray=array();
	$userArray=array();

	foreach ($member_array as $member) {
		$memSubArray=explode('::', $member);
		if ($memSubArray[0] == 'groups') {
			$groupArray[]=$memSubArray[1];
		}
		if ($memSubArray[0] == 'roles') {
			$roleArray[]=$memSubArray[1];
		}
		if ($memSubArray[0] == 'rs') {
			$roleSubordinateArray[]=$memSubArray[1];
		}
		if ($memSubArray[0] == 'users') {
			$userArray[]=$memSubArray[1];
		}
	}

	$groupMemberArray['groups']=$groupArray;
	$groupMemberArray['roles']=$roleArray;
	$groupMemberArray['rs']=$roleSubordinateArray;
	$groupMemberArray['users']=$userArray;

	return $groupMemberArray;
}

if (isset($_REQUEST['returnaction']) && $_REQUEST['returnaction'] != '') {
	$returnaction = urlencode(vtlib_purify($_REQUEST['returnaction'])).'&roleid='.urlencode(vtlib_purify($_REQUEST['roleid']));
} else {
	$returnaction='GroupDetailView';
}

$description = vtlib_purify($_REQUEST['description']);
//Inserting values into Role Table
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') {
	$groupId = vtlib_purify($_REQUEST['groupId']);
	$selected_col_string = vtlib_purify($_REQUEST['selectedColumnsString']);
	$member_array = explode(';', $selected_col_string);
	$groupMemberArray=constructGroupMemberArray($member_array);
	updateGroup($groupId, $groupName, $groupMemberArray, $description);
} elseif (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'create') {
	$selected_col_string = vtlib_purify($_REQUEST['selectedColumnsString']);
	$member_array = explode(';', $selected_col_string);
	$groupMemberArray=constructGroupMemberArray($member_array);
	$groupId=createGroup($groupName, $groupMemberArray, $description);
}

header("Location: index.php?action=$returnaction&module=Settings&groupId=".urlencode(vtlib_purify($groupId)));
?>