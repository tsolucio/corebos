<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Class to handle Caching Mechanism and re-use information.
 */
class VTCacheUtils {

	/** Generic information caching */
	public static $_cbcacheinfo_cache = array();
	public static function lookupCachedInformation($key) {
		if (isset(self::$_cbcacheinfo_cache[$key])) {
			return array(self::$_cbcacheinfo_cache[$key],true);
		}
		return array(false,false);
	}
	public static function updateCachedInformation($key, $value) {
		self::$_cbcacheinfo_cache[$key] = $value;
	}

	/** Tab information caching */
	public static $_tabidinfo_cache = array();
	public static function lookupTabid($module) {
		$flip_cache = array_flip(self::$_tabidinfo_cache);

		if (isset($flip_cache[$module])) {
			return $flip_cache[$module];
		}
		return false;
	}

	public static function lookupModulename($tabid) {
		if (isset(self::$_tabidinfo_cache[$tabid])) {
			return self::$_tabidinfo_cache[$tabid];
		}
		return false;
	}

	public static function updateTabidInfo($tabid, $module) {
		if (!empty($tabid) && !empty($module)) {
			self::$_tabidinfo_cache[$tabid] = $module;
		}
	}

	public static function emptyTabidInfo() {
		self::$_tabidinfo_cache = array();
	}

	/** All tab information caching */
	public static $_alltabrows_cache = false;
	public static function lookupAllTabsInfo() {
		return self::$_alltabrows_cache;
	}
	public static function updateAllTabsInfo($tabrows) {
		self::$_alltabrows_cache = $tabrows;
	}

	/** Block information caching */
	private static $_blocklabel_cache = array();
	public static function updateBlockLabelWithId($label, $id) {
		self::$_blocklabel_cache[$id] = $label;
	}
	public static function lookupBlockLabelWithId($id) {
		if (isset(self::$_blocklabel_cache[$id])) {
			return self::$_blocklabel_cache[$id];
		}
		return false;
	}

	/** Field information caching */
	public static $_fieldinfo_cache = array();
	public static function updateFieldInfo(
		$tabid,
		$fieldname,
		$fieldid,
		$fieldlabel,
		$columnname,
		$tablename,
		$uitype,
		$typeofdata,
		$presence
	) {
		self::$_fieldinfo_cache[$tabid][$fieldname] = array(
			'tabid'     => $tabid,
			'fieldid'   => $fieldid,
			'fieldname' => $fieldname,
			'fieldlabel'=> $fieldlabel,
			'columnname'=> $columnname,
			'tablename' => $tablename,
			'uitype'    => $uitype,
			'typeofdata'=> $typeofdata,
			'presence'  => $presence,
		);
	}
	public static function lookupFieldInfo($tabid, $fieldname) {
		if (isset(self::$_fieldinfo_cache[$tabid]) && isset(self::$_fieldinfo_cache[$tabid][$fieldname])) {
			return self::$_fieldinfo_cache[$tabid][$fieldname];
		}
		return false;
	}
	public static function lookupFieldInfo_Module($module, $presencein = array('0', '2')) {
		$tabid = getTabid($module);
		$modulefields = false;

		if (isset(self::$_fieldinfo_cache[$tabid])) {
			$modulefields = array();

			$fldcache = self::$_fieldinfo_cache[$tabid];
			foreach ($fldcache as $fieldinfo) {
				if (in_array($fieldinfo['presence'], $presencein)) {
					$modulefields[] = $fieldinfo;
				}
			}
		}
		return $modulefields;
	}

	public static function lookupFieldInfoByColumn($tabid, $columnname) {
		if (isset(self::$_fieldinfo_cache[$tabid])) {
			foreach (self::$_fieldinfo_cache[$tabid] as $fieldinfo) {
				if ($fieldinfo['columnname'] == $columnname) {
					return $fieldinfo;
				}
			}
		}
		return false;
	}

	/** Module active column fields caching */
	public static $_module_columnfields_cache = array();
	public static function updateModuleColumnFields($module, $column_fields) {
		self::$_module_columnfields_cache[$module] = $column_fields;
	}
	public static function lookupModuleColumnFields($module) {
		if (isset(self::$_module_columnfields_cache[$module])) {
			return self::$_module_columnfields_cache[$module];
		}
		return false;
	}

	/** User currency id caching */
	public static $_usercurrencyid_cache = array();
	public static function lookupUserCurrenyId($userid) {
		global $current_user;
		if (isset($current_user) && $current_user->id == $userid) {
			return array(
				'currencyid' => $current_user->column_fields['currency_id']
			);
		}

		if (isset(self::$_usercurrencyid_cache[$userid])) {
			return self::$_usercurrencyid_cache[$userid];
		}

		return false;
	}
	public static function updateUserCurrencyId($userid, $currencyid) {
		self::$_usercurrencyid_cache[$userid] = array(
			'currencyid' => $currencyid
		);
	}

	/** Currency information caching */
	public static $_currencyinfo_cache = array();
	public static function lookupCurrencyInfo($currencyid) {
		if (isset(self::$_currencyinfo_cache[$currencyid])) {
			return self::$_currencyinfo_cache[$currencyid];
		}
		return false;
	}
	public static function updateCurrencyInfo($currencyid, $name, $code, $symbol, $rate, $position) {
		self::$_currencyinfo_cache[$currencyid] = array(
			'currencyid' => $currencyid,
			'name'       => $name,
			'code'       => $code,
			'symbol'     => $symbol,
			'position'   => $position,
			'rate'       => $rate
		);
	}

	/** ProfileId information caching */
	public static $_userprofileid_cache = array();
	public static function updateUserProfileId($userid, $profileid) {
		self::$_userprofileid_cache[$userid] = $profileid;
	}
	public static function lookupUserProfileId($userid) {
		if (isset(self::$_userprofileid_cache[$userid])) {
			return self::$_userprofileid_cache[$userid];
		}
		return false;
	}

	/** Profile2Field information caching */
	public static $_profile2fieldpermissionlist_cache = array();
	public static function lookupProfile2FieldPermissionList($module, $profileid) {
		$pro2fld_perm = self::$_profile2fieldpermissionlist_cache;
		if (isset($pro2fld_perm[$module]) && isset($pro2fld_perm[$module][$profileid])) {
			return $pro2fld_perm[$module][$profileid];
		}
		return false;
	}
	public static function updateProfile2FieldPermissionList($module, $profileid, $value) {
		self::$_profile2fieldpermissionlist_cache[$module][$profileid] = $value;
	}

	/** Role information */
	public static $_subroles_roleid_cache = array();
	public static function lookupRoleSubordinates($roleid) {
		if (isset(self::$_subroles_roleid_cache[$roleid])) {
			return self::$_subroles_roleid_cache[$roleid];
		}
		return false;
	}
	public static function updateRoleSubordinates($roleid, $roles) {
		self::$_subroles_roleid_cache[$roleid] = $roles;
	}
	public static function clearRoleSubordinates($roleid = false) {
		if ($roleid === false) {
			self::$_subroles_roleid_cache = array();
		} elseif (isset(self::$_subroles_roleid_cache[$roleid])) {
			unset(self::$_subroles_roleid_cache[$roleid]);
		}
	}

	/** Related module information for Report */
	public static $_report_listofmodules_cache = false;
	public static function lookupReport_ListofModuleInfos() {
		return self::$_report_listofmodules_cache;
	}
	public static function updateReport_ListofModuleInfos($module_list, $related_modules) {
		if (self::$_report_listofmodules_cache === false) {
			self::$_report_listofmodules_cache = array(
				'module_list' => $module_list,
				'related_modules' => $related_modules
			);
		}
	}

	/** Report module information based on used. */
	public static $_reportmodule_infoperuser_cache = array();
	public static function lookupReport_Info($userid, $reportid) {
		if (isset(self::$_reportmodule_infoperuser_cache[$userid])) {
			if (isset(self::$_reportmodule_infoperuser_cache[$userid][$reportid])) {
				return self::$_reportmodule_infoperuser_cache[$userid][$reportid];
			}
		}
		return false;
	}

	public static $_map_listofmodules_cache = false;
	public static function lookupMap_ListofModuleInfos() {
		return self::$_map_listofmodules_cache;
	}
	public static function updateMap_ListofModuleInfos($module_list, $related_modules, $rel_fields) {
		if (self::$_map_listofmodules_cache === false) {
			self::$_map_listofmodules_cache = array(
				'module_list' => $module_list,
				'related_modules' => $related_modules,
				'rel_fields'=>$rel_fields
			);
		}
	}

	public static function updateReport_Info(
		$userid,
		$reportid,
		$primarymodule,
		$secondarymodules,
		$reporttype,
		$reportname,
		$description,
		$folderid,
		$owner,
		$cbreporttype
	) {
		if (!isset(self::$_reportmodule_infoperuser_cache[$userid])) {
			self::$_reportmodule_infoperuser_cache[$userid] = array();
		}
		if (!isset(self::$_reportmodule_infoperuser_cache[$userid][$reportid])) {
			self::$_reportmodule_infoperuser_cache[$userid][$reportid] = array (
				'reportid'        => $reportid,
				'primarymodule'   => $primarymodule,
				'secondarymodules'=> $secondarymodules,
				'reporttype'      => $reporttype,
				'reportname'      => $reportname,
				'description'     => $description,
				'folderid'        => $folderid,
				'owner'           => $owner,
				'cbreporttype'    => $cbreporttype
			);
		}
	}

	/** Report module sub-ordinate users information. */
	public static $_reportmodule_subordinateuserid_cache = array();
	public static function lookupReport_SubordinateUsers($reportid) {
		if (isset(self::$_reportmodule_subordinateuserid_cache[$reportid])) {
			return self::$_reportmodule_subordinateuserid_cache[$reportid];
		}
		return false;
	}
	public static function updateReport_SubordinateUsers($reportid, $userids) {
		self::$_reportmodule_subordinateuserid_cache[$reportid] = $userids;
	}

	/** Report module information based on used. */
	public static $_reportmodule_scheduledinfoperuser_cache = array();
	public static function lookupReport_ScheduledInfo($userid, $reportid) {

		if (isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid])) {
			if (isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid])) {
				return self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid];
			}
		}
		return false;
	}
	public static function updateReport_ScheduledInfo($userid, $reportid, $isScheduled, $scheduledFormat, $scheduledInterval, $scheduledRecipients, $scheduledTime) {
		if (!isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid])) {
			self::$_reportmodule_scheduledinfoperuser_cache[$userid] = array();
		}
		if (!isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid])) {
			self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid] = array (
				'reportid'				=> $reportid,
				'isScheduled'			=> $isScheduled,
				'scheduledFormat'		=> $scheduledFormat,
				'scheduledInterval'		=> $scheduledInterval,
				'scheduledRecipients'	=> $scheduledRecipients,
				'scheduledTime'			=> $scheduledTime,
			);
		}
	}

	/** Role Related Users information */
	public static $_role_related_users_cache = array();
	public static function lookupRole_RelatedUsers($roleid) {
		if (isset(self::$_role_related_users_cache[$roleid])) {
			return self::$_role_related_users_cache[$roleid];
		}
		return false;
	}
	public static function updateRole_RelatedUsers($roleid, $users) {
		self::$_role_related_users_cache[$roleid] = $users;
	}
}
?>
