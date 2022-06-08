<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/** Class to retrieve all the users present in a group */
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/GetParentGroups.php';

class GetGroupUsers {

	public $group_users=array();
	public $group_subgroups=array();

	/** to get all the users and groups of the specified group
	 * @param integer Group ID
	 * @return array users present in the group in the variable $parent_groups of the class
	 * @return array sub groups present in the group in the variable $group_subgroups of the class
	 */
	public function getAllUsersInGroup($groupid) {
		global $adb, $log;
		$log->debug('> getAllUsersInGroup '.$groupid);
		$result = $adb->pquery('select userid from vtiger_users2group where groupid=?', array($groupid));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_user_id=$adb->query_result($result, $i, 'userid');
			if (!in_array($now_user_id, $this->group_users)) {
				$this->group_users[]=$now_user_id;
			}
		}

		$result = $adb->pquery('select roleid from vtiger_group2role where groupid=?', array($groupid));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_role_id=$adb->query_result($result, $i, 'roleid');
			$now_role_users=array();
			$now_role_users=getRoleUsers($now_role_id);
			foreach ($now_role_users as $now_role_userid => $now_role_username) {
				if (! in_array($now_role_userid, $this->group_users)) {
					$this->group_users[]=$now_role_userid;
				}
			}
		}

		$result = $adb->pquery('select roleandsubid from vtiger_group2rs where groupid=?', array($groupid));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_rs_id=$adb->query_result($result, $i, 'roleandsubid');
			$now_rs_users=getRoleAndSubordinateUsers($now_rs_id);
			foreach ($now_rs_users as $now_rs_userid => $now_rs_username) {
				if (!in_array($now_rs_userid, $this->group_users)) {
					$this->group_users[]=$now_rs_userid;
				}
			}
		}
		$result = $adb->pquery('select containsgroupid from vtiger_group2grouprel where groupid=?', array($groupid));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_grp_id=$adb->query_result($result, $i, 'containsgroupid');

			$focus = new GetGroupUsers();
			$focus->getAllUsersInGroup($now_grp_id);
			$now_grp_users=$focus->group_users;
			if (!array_key_exists($now_grp_id, $this->group_subgroups)) {
				$this->group_subgroups[$now_grp_id]=$now_grp_users;
			}

			foreach ($focus->group_users as $temp_user_id) {
				if (!in_array($temp_user_id, $this->group_users)) {
					$this->group_users[]=$temp_user_id;
				}
			}

			foreach ($focus->group_subgroups as $temp_grp_id => $users_array) {
				if (!array_key_exists($temp_grp_id, $this->group_subgroups)) {
					$this->group_subgroups[$temp_grp_id]=$focus->group_users;
				}
			}
		}
		$log->debug('< getAllUsersInGroup');
	}
}
?>
