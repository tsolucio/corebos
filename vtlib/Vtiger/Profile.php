<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Utils.php');

/**
 * Provides API to work with vtiger CRM Profile
 * @package vtlib
 */
class Vtiger_Profile {

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	static function log($message, $delimit=true) {
		Vtiger_Utils::Log($message, $delimit);
	}

	/**
	 * Initialize profile setup for Field
	 * @param Vtiger_Field Instance of the field
	 * @access private
	 */
	static function initForField($fieldInstance) {
		global $adb;

		// Allow field access to all
		$adb->pquery("INSERT INTO vtiger_def_org_field (tabid, fieldid, visible, readonly) VALUES(?,?,?,?)",
			Array($fieldInstance->getModuleId(), $fieldInstance->id, '0', '0'));

		$profileids = self::getAllIds();
		foreach($profileids as $profileid) {
			$adb->pquery("INSERT INTO vtiger_profile2field (profileid, tabid, fieldid, visible, readonly) VALUES(?,?,?,?,?)",
				Array($profileid, $fieldInstance->getModuleId(), $fieldInstance->id, '0', '0'));
		}
	}

	/**
	 * Delete profile information related with field.
	 * @param Vtiger_Field Instance of the field
	 * @access private
	 */
	static function deleteForField($fieldInstance) {
		global $adb;

		$adb->pquery("DELETE FROM vtiger_def_org_field WHERE fieldid=?", Array($fieldInstance->id));
		$adb->pquery("DELETE FROM vtiger_profile2field WHERE fieldid=?", Array($fieldInstance->id));
	}

	/**
	 * Get all the existing profile ids
	 * @access private
	 */
	static function getAllIds() {
		global $adb;
		$profileids = Array();
		$result = $adb->pquery('SELECT profileid FROM vtiger_profile', array());
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$profileids[] = $adb->query_result($result, $index, 'profileid');
		}
		return $profileids;
	}

	/**
	 * Initialize profile setup for the module
	 * @param Vtiger_Module Instance of module
	 * @access private
	 */
	static function initForModule($moduleInstance) {
		global $adb;

		$actionids = Array();
		$result = $adb->pquery("SELECT actionid from vtiger_actionmapping WHERE actionname IN 
			(?,?,?,?,?)", array('Save','EditView','Delete','index','DetailView'));
		/* 
		 * NOTE: Other actionname (actionid >= 5) is considered as utility (tools) for a profile.
		 * Gather all the actionid for associating to profile.
		 */
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$actionids[] = $adb->query_result($result, $index, 'actionid');
		}

		$profileids = self::getAllIds();		

		foreach($profileids as $profileid) {			
			$adb->pquery("INSERT INTO vtiger_profile2tab (profileid, tabid, permissions) VALUES (?,?,?)",
				Array($profileid, $moduleInstance->id, 0));

			if($moduleInstance->isentitytype) {
				foreach($actionids as $actionid) {
					$adb->pquery(
						"INSERT INTO vtiger_profile2standardpermissions (profileid, tabid, Operation, permissions) VALUES(?,?,?,?)",
						Array($profileid, $moduleInstance->id, $actionid, 0));
				}
			}
		}
		self::log("Initializing module permissions ... DONE");
	}

	/**
	 * Delete profile setup of the module
	 * @param Vtiger_Module Instance of module
	 * @access private
	 */
	static function deleteForModule($moduleInstance) {
		global $adb;
		$adb->pquery("DELETE FROM vtiger_profile2tab WHERE tabid=?", Array($moduleInstance->id));
		$adb->pquery("DELETE FROM vtiger_profile2standardpermissions WHERE tabid=?", Array($moduleInstance->id));
	}
}
?>
