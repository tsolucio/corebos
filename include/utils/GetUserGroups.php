<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/** Class to retrieve all the Parent Groups of the specified Group */
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/GetParentGroups.php';

class GetUserGroups {

	public $user_groups=array();

	/** to get all the parent groups of the specified group
	 * @param integer Group ID
	 * @return array updates the parent group in the varibale $parent_groups of the class
	 */
	public function getAllUserGroups($userid) {
		global $adb, $log;
		$log->debug('> getAllUserGroups '.$userid);
		$result = $adb->pquery('select groupid from vtiger_users2group where userid=?', array($userid));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_group_id=$adb->query_result($result, $i, 'groupid');
			if (!in_array($now_group_id, $this->user_groups)) {
				$this->user_groups[]=$now_group_id;
			}
		}

		//Setting the User Role
		$userRole = fetchUserRole($userid);
		$result = $adb->pquery('select groupid from vtiger_group2role where roleid=?', array($userRole));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_group_id=$adb->query_result($result, $i, 'groupid');
			if (!in_array($now_group_id, $this->user_groups)) {
				$this->user_groups[]=$now_group_id;
			}
		}

		$parentRoles=getParentRole($userRole);
		$parentRolelist= array();
		foreach ($parentRoles as $par_rol_id) {
			$parentRolelist[] = $par_rol_id;
		}
		$parentRolelist[] = $userRole;
		$result = $adb->pquery('select groupid from vtiger_group2rs where roleandsubid in ('. generateQuestionMarks($parentRolelist) .')', array($parentRolelist));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$now_group_id=$adb->query_result($result, $i, 'groupid');
			if (!in_array($now_group_id, $this->user_groups)) {
				$this->user_groups[]=$now_group_id;
			}
		}
		foreach ($this->user_groups as $grp_id) {
			$focus = new GetParentGroups();
			$focus->getAllParentGroups($grp_id);
			foreach ($focus->parent_groups as $par_grp_id) {
				if (!in_array($par_grp_id, $this->user_groups)) {
					$this->user_groups[]=$par_grp_id;
				}
			}
		}
		$log->debug('< getAllUserGroups');
	}
}
?>
