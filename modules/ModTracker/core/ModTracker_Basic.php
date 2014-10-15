<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************** */
include_once dirname(__FILE__) . '/ModTracker_Detail.php';

class ModTracker_Basic {

	var $id;
	var $crmid;
	var $module;
	var $whodid;
	var $changedon;

	function __construct() {

	}

	function exists() {
		return!empty($this->id);
	}

	function initialize($valuemap) {
		$this->id = $valuemap['id'];
		$this->crmid = $valuemap['crmid'];
		$this->module = $valuemap['module'];
		$this->whodid = $valuemap['whodid'];
		$this->changedon = $valuemap['changedon'];
	}

	function getTabid() {
		return getTabid($this->module);
	}

	function getDisplayName() {
		if (!isset($this->_entityName)) {
			$entityName = getEntityName($this->module, array($this->crmid));
			$this->_entityName = $entityName[$this->crmid];
		}
		return $this->_entityName;
	}

	function getViewLink() {
		if (!isset($this->_viewlink)) {
			$entityName = $this->getDisplayName();
			$this->_viewlink = "<a href='index.php?module=$this->module&action=DetailView&record=$this->crmid'>" . $entityName . "</a>";
		}
		return $this->_viewlink;
	}

	function getModifiedOn() {
		$changedOn = new DateTimeField($this->changedon);
		return $changedOn->getDisplayDateTimeValue();
	}

	function getModifiedByLabel() {
		global $current_user, $currentModule;
		if (isset($current_user) && $current_user->id == $this->whodid) {
			return getFullNameFromArray('Users', $current_user->column_fields);
		}
		return getUserFullName($this->whodid);
	}

	function getDetails() {
		return ModTracker_Detail::listAll($this);
	}

	function isViewPermitted() {
		global $current_user;
		if (isset($current_user) && is_admin($current_user))
			return true;
		// Does current user has access to view the record that was tracked?
		if ($this->module == 'Events') {
			$moduleName = 'Calendar';
		} else {
			$moduleName = $this->module;
		}
		return (isPermitted($moduleName, 'DetailView', $this->crmid) == "yes");
	}

	static function getById($id) {
		global $adb;
		$instance = false;
		$result = $adb->pquery('SELECT * FROM vtiger_modtracker_basic WHERE id=?', Array($id));
		if ($adb->num_rows($result)) {
			$rowmap = $adb->fetch_array($result);
			$instance = new self();
			$instance->initialize($rowmap);
		}
		return $instance;
	}

	static function getByCRMId($crmid, $atpoint) {
		global $adb, $current_user, $log;
		$instance = false;

		// Avoid SQL Injection attacks
		$purifiedAtPoint = $adb->sql_escape_string($atpoint);

		$result = $adb->pquery("SELECT * FROM vtiger_modtracker_basic WHERE crmid=? ORDER BY changedon DESC LIMIT $purifiedAtPoint, 1",
						array($crmid));

		if ($adb->num_rows($result)) {
			$rowmap = $adb->fetch_array($result);
			$instance = new self();
			$instance->initialize($rowmap);
		}
		return $instance;
	}

	static function listAll($module=false, $asc=true) {
		global $adb;
		$instances = Array();
		$result = false;

		if ($module) {
			if ($asc)
				$result = $adb->pquery('SELECT * FROM vtiger_modtracker_basic
				WHERE module=? ORDER BY id', Array($module));
			else
				$result = $adb->pquery('SELECT * FROM vtiger_modtracker_basic
				WHERE module=? ORDER BY id DESC', Array($module));
		}

		if ($result && $adb->num_rows($result)) {
			for ($index = 0; $index < $adb->num_rows($result); ++$index) {
				$rowmap = $adb->fetch_array($result);
				$instance = new self();
				$instance->initialize($rowmap);
				$instances[] = $instance;
			}
		}
		return $instances;
	}

}

?>
