<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : Adam Heinz
 *************************************************************************************************/

class UserPrivileges {
	const GLOBAL_READ = 1;
	const GLOBAL_WRITE = 2;

	const ACTION_DELETE = 3;

	const SHARING_READONLY = 0;
	const SHARING_READWRITE = 1;
	const SHARING_READWRITEDELETE = 2;
	const SHARING_PRIVATE = 3;

	private $parent_role_seq = null;
	private $profiles = null;
	private $profileGlobalPermission = null;
	private $profileTabsPermission = null;
	private $profileActionPermission = null;
	private $groups = array();
	private $subordinate_roles = null;
	private $parent_roles = null;
	private $subordinate_roles_users = null;
	private $defaultOrgSharingPermission = null;
	private $nosharingarray = array('ROLE'=>array(),'GROUP'=>array());

	public function __construct($userid) {
		global $cbodUserPrivilegesStorage;
		if ($cbodUserPrivilegesStorage == 'file') {
			$this->loadUserPrivilegesFile($userid);
		} elseif ($cbodUserPrivilegesStorage == 'db') {
			$this->loadUserPrivilegesDB($userid);
		}
	}

	/**
	 * Load User Privileges from the file
	 *
	 * @param int $userid
	 * @return void
	 */
	private function loadUserPrivilegesFile($userid, $withot_sharing = false) {
		checkFileAccessForInclusion('user_privileges/user_privileges_' . $userid . '.php');
		require "user_privileges/user_privileges_$userid.php";
		$this->is_admin = (bool) $is_admin;
		if ($this->is_admin) {
			$current_user_roles = 'H1';
			$current_user_profiles = array(1);
		}
		$this->roles = $current_user_roles;
		if (!$this->is_admin) {
			$this->parent_role_seq = $current_user_parent_role_seq;
			$this->profiles = $current_user_profiles;
			$this->profileGlobalPermission = $profileGlobalPermission;
			$this->profileTabsPermission = $profileTabsPermission;
			$this->profileActionPermission = $profileActionPermission;
			$this->groups = $current_user_groups;
			$this->subordinate_roles = $subordinate_roles;
			$this->parent_roles = $parent_roles;
			$this->subordinate_roles_users = $subordinate_roles_users;
			if (!$withot_sharing) {
				$this->loadSharingPrivilegesFile($userid);
			}
		}
		$this->user_info = $user_info;
	}

	/**
	 * Load Sharing Privileges from the file
	 *
	 * @param int $userid
	 * @return void
	 */
	private function loadSharingPrivilegesFile($userid) {
		checkFileAccessForInclusion('user_privileges/sharing_privileges_' . $userid . '.php');
		require "user_privileges/sharing_privileges_$userid.php";
		$this->defaultOrgSharingPermission = (empty($defaultOrgSharingPermission) ? [] : $defaultOrgSharingPermission);
		$this->related_module_share = (empty($related_module_share) ? [] : $related_module_share);
		$ignore = array('GLOBALS', 'argv', 'argc', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', 'userid', 'defaultOrgSharingPermission', 'related_module_share');
		foreach (get_defined_vars() as $var => $val) {
			if (!in_array($var, $ignore) && preg_match('/.+_share_\w+_permission/', $var)) {
				$this->$var = $val;
			}
		}
	}

	/**
	 * Load User Privileges from the database
	 *
	 * @param int $userid
	 * @return void
	 */
	private function loadUserPrivilegesDB($userid, $withot_sharing = false) {
		global $adb;

		$query = $adb->pquery('SELECT user_data FROM user_privileges WHERE userid=?', array($userid));
		$result = $adb->query_result($query, 0, 0);

		$user_data = json_decode($result, true);

		$this->is_admin = (bool)$user_data['is_admin'];
		$this->roles = isset($user_data['current_user_roles']) ? $user_data['current_user_roles'] : '';

		if (!$this->is_admin) {
			$this->parent_role_seq = $user_data['current_user_parent_role_seq'];
			$this->profiles = $user_data['current_user_profiles'];
			$this->profileGlobalPermission = $user_data['profileGlobalPermission'];
			$this->profileTabsPermission = $user_data['profileTabsPermission'];
			$this->profileActionPermission = $user_data['profileActionPermission'];
			$this->groups = $user_data['current_user_groups'];
			$this->subordinate_roles = $user_data['subordinate_roles'];
			$this->parent_roles = $user_data['parent_roles'];
			$this->subordinate_roles_users = $user_data['subordinate_roles_users'];
			if (!$withot_sharing) {
				$this->loadSharingPrivilegesDB($userid);
			}
		}
		$this->user_info = $user_data['user_info'];
	}

	/**
	 * Load Sharing Privileges from the database
	 *
	 * @param int $userid
	 * @return void
	 */
	private function loadSharingPrivilegesDB($userid) {
		global $adb;

		$query = $adb->pquery('SELECT sharing_data FROM sharing_privileges WHERE userid=?', array($userid));

		if ($adb->num_rows($query) != 1) {
			return;
		}

		$sharing_data = json_decode($adb->query_result($query, 0, 0), true);

		foreach ($sharing_data as $key => $data) {
			$this->$key = $data;
		}
	}

	/**
	 * @return array of integer group IDs
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * Ternary return value:
	 * null - has permission for all records
	 * 0 - has permission for module, but record permission must be checked
	 * 1 - does not have permission
	 * @deprecated 5.4.0
	 * @return mixed
	 */
	public function getModulePermission($tabid, $actionid) {
		return (isset($this->profileActionPermission[$tabid][$actionid]) ? $this->profileActionPermission[$tabid][$actionid] : null);
	}

	/**
	 * @return int - SHARING_* const
	 */
	public function getModuleSharingPermission($tabid) {
		return (isset($this->defaultOrgSharingPermission[$tabid]) ? $this->defaultOrgSharingPermission[$tabid] : null);
	}

	/**
	 * @param string module name
	 * @param string read|write|delete
	 * @return array of module sharing rules
	 */
	public function getModuleSharingRules($tabname, $permission) {
		$varname = $tabname . '_share_' . $permission . '_permission';
		return (empty($this->$varname) ? $this->nosharingarray : $this->$varname);
	}

	/**
	 * @return array(string)
	 */
	public function getParentRoles() {
		return $this->parent_roles;
	}

	/**
	 * @return string
	 */
	public function getParentRoleSequence() {
		return $this->parent_role_seq;
	}

	/**
	 * @return array(int)
	 */
	public function getProfiles() {
		return (isset($this->profiles) ? $this->profiles : array());
	}

	/**
	 * @param $parentname - parent module name
	 * @param $tabname - module name
	 * @param $permission - read|write|delete
	 */
	public function getRelatedModuleSharingRules($parentname, $tabname, $permission) {
		$varname = $parentname . '_' . $tabname . '_share_' . $permission . '_permission';
		return $this->$varname;
	}

	/**
	 * @return array(int)
	 */
	public function getRelatedSharedModules($tabid) {
		return (isset($this->related_module_share[$tabid]) ? $this->related_module_share[$tabid] : '');
	}

	/**
	 * @return array(string)
	 */
	public function getSubordinateRoles() {
		return $this->subordinate_roles;
	}

	/**
	 * @return array(string => array(int))
	 */
	public function getSubordinateRoles2Users() {
		return (isset($this->subordinate_roles_users) ? $this->subordinate_roles_users : array());
	}

	/**
	 * @return array(string => string)
	 */
	public function getUserInfo($field = null) {
		if ($field) {
			return $this->user_info[$field];
		} else {
			return $this->user_info;
		}
	}

	public function getprofileTabsPermission() {
		return $this->profileTabsPermission;
	}

	public function hasGlobalReadPermission() {
		return $this->isAdmin()
			|| (0 == $this->profileGlobalPermission[self::GLOBAL_READ])
			|| (0 == $this->profileGlobalPermission[self::GLOBAL_WRITE]);
	}

	public function hasGlobalWritePermission() {
		return $this->isAdmin()
			|| (0 == $this->profileGlobalPermission[self::GLOBAL_WRITE]);
	}

	public function hasGlobalViewPermission() {
		return ($this->isAdmin() || (0 == $this->profileGlobalPermission[self::GLOBAL_READ]));
	}

	public function hasGroups() {
		return count($this->groups) > 0;
	}

	public function hasModuleAccess($tabid) {
		return ($this->isAdmin() || (!is_null($tabid) && (isset($this->profileTabsPermission[$tabid]) && 0 == $this->profileTabsPermission[$tabid])));
	}

	public function hasModulePermission($tabid, $actionid) {
		return (0 == $this->profileActionPermission[$tabid][$actionid]);
	}

	public function hasModuleReadSharing($tabid) {
		return (self::SHARING_PRIVATE != (isset($this->defaultOrgSharingPermission[$tabid]) ? $this->defaultOrgSharingPermission[$tabid] : self::SHARING_READWRITEDELETE));
	}

	public function hasModuleWriteSharing($tabid) {
		$sharing = (empty($this->defaultOrgSharingPermission[$tabid]) ? self::SHARING_PRIVATE : $this->defaultOrgSharingPermission[$tabid]);
		return (self::SHARING_PRIVATE != $sharing) && (self::SHARING_READONLY != $sharing);
	}

	public function isAdmin() {
		return $this->is_admin;
	}

	public function getRoles() {
		return $this->roles;
	}

	public static function hasPrivileges($userId, $is_admin = true) {
		global $adb, $cbodUserPrivilegesStorage;
		if ($cbodUserPrivilegesStorage == 'db') {
			$query = $adb->pquery(
				"SELECT count(*) FROM user_privileges WHERE userid = ?",
				array($userId)
			);
			$result = $adb->query_result($query, 0, 0);

			if ($is_admin == "off" || !$is_admin) {
				$query = $adb->pquery(
					'SELECT count(*) FROM sharing_privileges WHERE userid = ?',
					array($userId)
				);
				$sharing_result = $adb->query_result($query, 0, 0);
				return (($result == 1) && ($sharing_result == 1));
			}

			return ($result == 1);
		} else {
			return file_exists("user_privileges/sharing_privileges_{$userId}.php");
		}
	}

	public static function privsWithoutSharing($userId) {
		global $cbodUserPrivilegesStorage;
		$instance = new self($userId);
		if ($cbodUserPrivilegesStorage == 'file') {
			$instance->loadUserPrivilegesFile($userId, true);
		} elseif ($cbodUserPrivilegesStorage == 'db') {
			$instance->loadUserPrivilegesDB($userId, true);
		}
		return $instance;
	}
}
