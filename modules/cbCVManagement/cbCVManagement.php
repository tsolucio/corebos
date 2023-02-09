<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'modules/CustomView/CustomView.php';

class cbCVManagement extends CRMEntity {
	public $table_name = 'vtiger_cbcvmanagement';
	public $table_index= 'cbcvmanagementid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-calibration', 'class' => 'slds-icon', 'icon'=>'calibration');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbcvmanagementcf', 'cbcvmanagementid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbcvmanagement', 'vtiger_cbcvmanagementcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbcvmanagement'   => 'cbcvmanagementid',
		'vtiger_cbcvmanagementcf' => 'cbcvmanagementid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array (
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbcvmno'=> array('cbcvmanagement' => 'cbcvmno'),
		'Name'=> array('cbcvmanagement' => 'cvid'),
		'cvrole'=>array('cbcvmanagement' => 'cvrole'),
		'Assigned To' => array('crmentity' => 'smownerid'),
		'default_setting'=>array('cbcvmanagement' => 'default_setting'),
		'Preference'=>array('cbcvmanagement' => 'preference'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Globalno'=> 'cbcvmno',
		'Name'=> 'cvid',
		'cvrole'=>'cvrole',
		'Assigned To' => 'assigned_user_id',
		'default_setting'=>'default_setting',
		'Preference'=>'preference',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbcvmno';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Globalno'=> array('cbcvmanagement' => 'cbcvmno'),
		'Name'=> array('cbcvmanagement' => 'cvid'),
		'cvrole'=>array('cbcvmanagement' => 'cvrole'),
		'Assigned To' => array('crmentity' => 'smownerid'),
		'default_setting'=>array('cbcvmanagement' => 'default_setting'),
		'Preference'=>array('cbcvmanagement' => 'preference'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Globalno'=> 'cbcvmno',
		'Name'=> 'cvid',
		'cvrole'=>'cvrole',
		'Assigned To' => 'assigned_user_id',
		'default_setting'=>'default_setting',
		'Preference'=>'preference',
	);

	// For Popup window record selection
	public $popup_fields = array('cbcvmno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbcvmno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbcvmno';

	// Required Information for enabling Import feature
	public $required_fields = array('cbcvmno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbcvmno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cvid');

	private static $validationinfo = array();

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'cvm-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	/** returns the default Custom View ID for the given module and user applying escalation rules
	 * @param string the module we need the view for, will use current module if no value is given
	 * @param integer user for which we want to get the view, will use current user if no value is given
	 * @return integer custom view ID
	 *   search for mandatory default record
	 *   search for non-mandatory record that belongs to the role of the user
	 *   search for non-mandatory record assigned to the user
	 *   search for non-mandatory record assigned to any group of the user
	 *   search for non-mandatory record as default setting for the module
	 *   if no record is found then the default view will be set as per the database configuration (like it was before we had this functionality: one for all)
	 *   if no record is found then the ALL view for the module will be returned
	 *   if no record is found then a boolean false will be returned
	 */
	public static function getDefaultView($module = '', $cvuserid = '') {
		global $adb, $current_user, $currentModule;
		if (empty($module)) {
			$module = $currentModule;
		}
		if (empty($cvuserid) && !empty($current_user)) {
			$cvuserid = $current_user->id;
		}
		if (empty($cvuserid) || empty($module)) {
			return false;
		}
		self::$validationinfo = array();
		self::$validationinfo[] = "search for default CV on $module for user '$cvuserid'";
		$key = 'cvdcache'.$module.$cvuserid;
		list($value,$found) = VTCacheUtils::lookupCachedInformation($key);
		if ($found) {
			self::$validationinfo[] = 'default CV found in cache';
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for mandatory records';
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCVManagement');
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			where vtiger_crmentity.deleted=0 and mandatory='1' and cvdefault='1' and entitytype=? and cvretrieve='1' limit 1";
		$cvrs = $adb->pquery($cvsql, array($module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'mandatory records found';
			$value = $adb->query_result($cvrs, 0, 0);
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			where vtiger_crmentity.deleted=0 and cvdefault='1' and smownerid=? and entitytype=? and cvretrieve='1' limit 1";
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for user records';
		$cvrs = $adb->pquery($cvsql, array($cvuserid, $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'user records found';
			$value = $adb->query_result($cvrs, 0, 0);
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			left join vtiger_user2role on vtiger_user2role.userid=?
			where vtiger_crmentity.deleted=0 and cvdefault='1' and cvrole like concat('%', vtiger_user2role.roleid, '%') and entitytype=? and cvretrieve='1' limit 1";
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for role records';
		$cvrs = $adb->pquery($cvsql, array($cvuserid, $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'role records found';
			$value = $adb->query_result($cvrs, 0, 0);
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		require_once 'include/utils/GetUserGroups.php';
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($cvuserid);
		if (count($UserGroups->user_groups)>0) {
			$groups=implode(',', $UserGroups->user_groups);
			$cvsql = 'select vtiger_cbcvmanagement.cvid
				from vtiger_cbcvmanagement
				inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
				inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
				where vtiger_crmentity.deleted=0 and cvdefault='1' and smownerid in ($groups) and entitytype=? and cvretrieve='1' limit 1";
			self::$validationinfo[] = '---';
			self::$validationinfo[] = 'search for group records';
			$cvrs = $adb->pquery($cvsql, array($module));
			if ($cvrs && $adb->num_rows($cvrs)>0) {
				self::$validationinfo[] = 'group records found';
				$value = $adb->query_result($cvrs, 0, 0);
				VTCacheUtils::updateCachedInformation($key, $value);
				return $value;
			}
		} else {
			self::$validationinfo[] = 'no groups to search in';
		}
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			where vtiger_crmentity.deleted=0 and cvdefault='1' and default_setting='1' and module_list REGEXP ? and entitytype=? and cvretrieve='1' limit 1";
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for default setting record';
		$cvrs = $adb->pquery($cvsql, array(' *'.$module.' *', $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'default setting records found';
			$value = $adb->query_result($cvrs, 0, 0);
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		$cvsql = 'select vtiger_customview.cvid from vtiger_customview where (setdefault=1 or viewname=?) and entitytype=? order by setdefault desc limit 1';
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for CV default records';
		$cvrs = $adb->pquery($cvsql, array('All', $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'CV default records found';
			$value = $adb->query_result($cvrs, 0, 0);
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'no view found';
		return false;
	}

	/** returns all the Custom Views available for the given module and user applying escalation rules
	 * @param string the module we need the views for, will use current module if no value is given
	 * @param integer user for which we want to get the views, will use current user if no value is given
	 * @return mixed custom view IDs
	 *   search for mandatory default record
	 *   search for non-mandatory record that belongs to the role of the user
	 *   search for non-mandatory record assigned to the user
	 *   search for non-mandatory record assigned to any group of the user
	 *   search for non-mandatory record as default setting for the module
	 *   if no record is found then the ALL view for the module will be returned
	 *   if no record is found then an empty array will be returned
	 */
	public static function getAllViews($module = '', $cvuserid = '') {
		global $adb, $current_user, $currentModule;
		$allViews = array();
		if (empty($module)) {
			$module = $currentModule;
		}
		if (empty($cvuserid) && !empty($current_user)) {
			$cvuserid = $current_user->id;
		}
		if (empty($cvuserid) || empty($module)) {
			return $allViews;
		}
		self::$validationinfo = array();
		self::$validationinfo[] = "search for default CV on $module for user '$cvuserid'";
		$key = 'cvallcache'.$module.$cvuserid;
		list($value,$found) = VTCacheUtils::lookupCachedInformation($key);
		if ($found) {
			self::$validationinfo[] = 'default CV found in cache';
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for mandatory records';
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCVManagement');
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			where vtiger_crmentity.deleted=0 and mandatory='1' and entitytype=? and cvretrieve='1'";
		$cvrs = $adb->pquery($cvsql, array($module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'mandatory records found';
			while ($value = $adb->fetch_array($cvrs)) {
				self::$validationinfo[] = $value['cvid'];
				$allViews[] = $value['cvid'];
			}
		}
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			left join vtiger_user2role on vtiger_user2role.userid=?
			where vtiger_crmentity.deleted=0 and entitytype=? and cvretrieve='1'
			and (cvrole=vtiger_user2role.roleid or cvrole like concat(vtiger_user2role.roleid, ' %')
				or cvrole like concat('% ', vtiger_user2role.roleid, ' %') or cvrole like concat('% ', vtiger_user2role.roleid))";
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for role records';
		$cvrs = $adb->pquery($cvsql, array($cvuserid, $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'role records found';
			while ($value = $adb->fetch_array($cvrs)) {
				self::$validationinfo[] = $value['cvid'];
				$allViews[] = $value['cvid'];
			}
		}
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			where vtiger_crmentity.deleted=0 and smownerid=? and entitytype=? and cvretrieve='1'";
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for user records';
		$cvrs = $adb->pquery($cvsql, array($cvuserid, $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'user records found';
			while ($value = $adb->fetch_array($cvrs)) {
				self::$validationinfo[] = $value['cvid'];
				$allViews[] = $value['cvid'];
			}
		}
		require_once 'include/utils/GetUserGroups.php';
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($cvuserid);
		if (count($UserGroups->user_groups)>0) {
			$groups=implode(',', $UserGroups->user_groups);
			$cvsql = 'select vtiger_cbcvmanagement.cvid
				from vtiger_cbcvmanagement
				inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
				inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
				where vtiger_crmentity.deleted=0 and smownerid in ($groups) and entitytype=? and cvretrieve='1'";
			self::$validationinfo[] = '---';
			self::$validationinfo[] = 'search for group records';
			$cvrs = $adb->pquery($cvsql, array($module));
			if ($cvrs && $adb->num_rows($cvrs)>0) {
				self::$validationinfo[] = 'group records found';
				while ($value = $adb->fetch_array($cvrs)) {
					self::$validationinfo[] = $value['cvid'];
					$allViews[] = $value['cvid'];
				}
			}
		} else {
			self::$validationinfo[] = 'no groups to search in';
		}
		$cvsql = 'select vtiger_cbcvmanagement.cvid
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			inner join vtiger_customview on vtiger_customview.cvid=vtiger_cbcvmanagement.cvid
			where vtiger_crmentity.deleted=0 and default_setting='1' and module_list REGEXP ? and entitytype=? and cvretrieve='1'";
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for default setting record';
		$cvrs = $adb->pquery($cvsql, array(' *'.$module.' *', $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'default setting records found';
			while ($value = $adb->fetch_array($cvrs)) {
				self::$validationinfo[] = $value['cvid'];
				$allViews[] = $value['cvid'];
			}
		}
		$cvsql = 'select vtiger_customview.cvid from vtiger_customview where viewname=? and entitytype=?';
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for CV default records';
		$cvrs = $adb->pquery($cvsql, array('All', $module));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			self::$validationinfo[] = 'CV default records found';
			while ($value = $adb->fetch_array($cvrs)) {
				self::$validationinfo[] = $value['cvid'];
				$allViews[] = $value['cvid'];
			}
		}

		// push all public filters if the user is admin
		if ($current_user->id == 1) {
			$cvsql = 'SELECT vtiger_cbcvmanagement.cvid FROM `vtiger_cbcvmanagement` WHERE setpublic = 1 AND module_list = ?';
			$queryResult = $adb->pquery($cvsql, array($module));
			while ($row=$adb->fetch_array($queryResult)) {
				$allViews[] = $row['cvid'];
			}
		}

		// push all approved public filters
		$cvsql = "SELECT * FROM `vtiger_customview` WHERE status = 3 AND entitytype = ?";
		$queryResult = $adb->pquery($cvsql, array($module));
		while ($row=$adb->fetch_array($queryResult)) {
			$allViews[] = $row['cvid'];
		}

		VTCacheUtils::updateCachedInformation($key, $value);
		return array_unique($allViews);
	}

	public static function getApprovers($cvid) {
		global $adb;
		if (empty($cvid)) {
			return false;
		}
		$cvrs = $adb->pquery('select viewname, entitytype, userid, status from vtiger_customview where vtiger_customview.cvid=?', array($cvid));
		if (!$cvrs || $adb->num_rows($cvrs)==0) {
			return false;
		}
		$module = $adb->query_result($cvrs, 0, 'entitytype');
		$cvname = $adb->query_result($cvrs, 0, 'viewname');
		$cvuid = $adb->query_result($cvrs, 0, 'userid');
		$cvstatus = $adb->query_result($cvrs, 0, 'status');
	}

	private static function returnPermission($sql, $key) {
		global $adb;
		$cvrs = $adb->query($sql);
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			$vals = $adb->fetch_array($cvrs);
			$value = array(
				'C' => $vals['c'],
				'R' => $vals['r'],
				'U' => $vals['u'],
				'D' => $vals['d'],
				'A' => $vals['a'],
			);
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		} else {
			return false;
		}
	}

	/** returns set of permissions of a user upon a custom view depending on the different records
	 * @param integer the custom view identifier
	 * @param integer user for which we want to know the perimissions, will use current user if no value is given
	 * @return mixed
	 *  if $cvid or $cvuserid are empty or invalid it returns boolean false
	 *  else it returns array with CRUD and Approve permissions:
	 *   search for mandatory record that belongs to the user
	 *   search for mandatory record that belongs to the role of the user
	 *   search for mandatory record that belongs to any group the user is in
	 *   search for non-mandatory record that belongs to the user
	 *   search for non-mandatory record that belongs to the role of the user
	 *   search for non-mandatory record that belongs to any group the user is in
	 *   search for record set as default for the module of the custom view
	 *   if no permission record is found then
	 *       if the View belongs to the user:
	 *         - he will be able to do everything except approve
	 *         - he will be able to approve if he is administrator
	 *       if the View does not belong to the user:
	 *         - he will be able to create
	 *         - retrieve if the View is public and approved
	 *         - he will not be able to edit, delete nor approve
	 */
	public static function getPermission($cvid, $cvuserid = '') {
		global $adb, $current_user;
		if (empty($cvid)) {
			return false;
		}
		$cvrs = $adb->pquery('select viewname, entitytype, userid, status from vtiger_customview where vtiger_customview.cvid=?', array($cvid));
		if (!$cvrs || $adb->num_rows($cvrs)==0) {
			return false;
		}
		if (empty($cvuserid) && !empty($current_user)) {
			$cvuserid = $current_user->id;
		}
		if (empty($cvuserid)) {
			return false;
		}

		// giving all permissions if its the admin user
		if ($cvuserid == 1) {
			return array('C' => 1, 'R' => 1, 'U' => 1, 'D' => 1, 'A' => 1);
		}

		$module = $adb->query_result($cvrs, 0, 'entitytype');
		$cvname = $adb->query_result($cvrs, 0, 'viewname');
		$cvuid = $adb->query_result($cvrs, 0, 'userid');
		$cvstatus = $adb->query_result($cvrs, 0, 'status');
		self::$validationinfo = array();
		self::$validationinfo[] = "search for permission on '$cvname' ($module) for user '$cvuserid'";
		$key = 'cvcache'.$cvid.$cvuserid;
		list($value,$found) = VTCacheUtils::lookupCachedInformation($key);
		if ($found) {
			self::$validationinfo[] = 'cv permission found in cache';
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for mandatory/owner records';
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCVManagement');
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			where vtiger_crmentity.deleted=0 and cvdefault=? and smownerid=? and cvid=? and mandatory=? limit 1';
		$cvsql = $adb->convert2Sql($cvsql, array(0, $cvuserid, $cvid, 1));
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for mandatory/role records';
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			left join vtiger_user2role on vtiger_user2role.userid=?
			where vtiger_crmentity.deleted=0 and cvdefault=? and cvid=? and mandatory=?';
		$cvsql = $adb->convert2Sql($cvsql, array($cvuserid, 0, $cvid, 1))." and cvrole like concat('%', vtiger_user2role.roleid, '%') limit 1";
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		require_once 'include/utils/GetUserGroups.php';
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($cvuserid);
		if (count($UserGroups->user_groups)>0) {
			$groups=implode(',', $UserGroups->user_groups);
		} else {
			self::$validationinfo[] = 'no groups to search in';
			$groups='-1';
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for mandatory/group records';
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			where vtiger_crmentity.deleted=0 and cvdefault=? and smownerid in ($groups) and cvid=? and mandatory=? limit 1";
		$cvsql = $adb->convert2Sql($cvsql, array(0, $cvid, 1));
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for owner records';
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			where vtiger_crmentity.deleted=0 and cvdefault=? and smownerid=? and cvid=? limit 1';
		$cvsql = $adb->convert2Sql($cvsql, array(0, $cvuserid, $cvid));
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for role records';
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			left join vtiger_user2role on vtiger_user2role.userid=?
			where vtiger_crmentity.deleted=0 and cvdefault=? and cvid=?';
		$cvsql = $adb->convert2Sql($cvsql, array($cvuserid, 0, $cvid))." and cvrole like concat('%', vtiger_user2role.roleid, '%') limit 1";
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for group records';
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable." on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			where vtiger_crmentity.deleted=0 and cvdefault=? and smownerid in ($groups) and cvid=? limit 1";
		$cvsql = $adb->convert2Sql($cvsql, array(0, $cvid));
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'search for default records';
		$cvsql = 'select cvcreate as c, cvretrieve as r, cvupdate as u, cvdelete as d, cvapprove as a
			from vtiger_cbcvmanagement
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_cbcvmanagement.cbcvmanagementid
			where vtiger_crmentity.deleted=0 and cvdefault=? and module_list like ? and default_setting=? limit 1';
		$cvsql = $adb->convert2Sql($cvsql, array(0, '%'.$module.'%', 1));
		$value = self::returnPermission($cvsql, $key);
		if ($value) {
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = 'return default value';
		if ($cvuid==$cvuserid) {
			$value = array(
				'C' => 1,
				'R' => 1,
				'U' => 1,
				'D' => 1,
			);
		} else {
			$value = array(
				'C' => 1,
				'R' => ($cvstatus==3 ? 1 : 0), // public and approved
				'U' => 0,
				'D' => 0,
			);
		}
		$cvuser = CRMEntity::getInstance('Users');
		$cvuser->retrieve_entity_info($cvuserid, 'Users');
		$value['A'] = (is_admin($cvuser) ? 1 : 0);
		return $value;
	}

	public static function getValidationInfo() {
		return self::$validationinfo;
	}
}
?>
