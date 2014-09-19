<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../lib/StringDiff.php';
include_once dirname(__FILE__) . '/ModTracker_Field.php';

class ModTracker_Detail {
	var $id;
	var $name;
	var $prevalue;
	var $postvalue;

	var $parent;
	var $fieldInstance;
	
	var $_prevalueLabel = false;
	var $_postvalueLabel = false;
	var $_fieldLabel = false;

	function __construct($parent) {
		$this->parent = $parent;
	}

	function getModuleName() {
		return $this->parent->module;
	}

	function getModuleId() {
		return $this->parent->getTabid();
	}

	function getRecordId() {
		return $this->parent->crmid;
	}

	function getFieldName() {
		return $this->name;
	}
	
	function getDisplayLabelForPreValue() {
		if($this->_prevalueLabel === false) {
			$this->_prevalueLabel = $this->fieldInstance->getDisplayLabel($this->prevalue);
		}
		return $this->_prevalueLabel;
	}

	function getDisplayLabelForPostValue() {
		if($this->_postvalueLabel === false) {
			$this->_postvalueLabel = $this->fieldInstance->getDisplayLabel($this->postvalue);
		}
		return $this->_postvalueLabel;
	}
	
	function initialize($valuemap) {
		$this->id = $valuemap['id'];
		$this->name = $valuemap['fieldname'];
		$this->prevalue = $valuemap['prevalue'];
		$this->postvalue =$valuemap['postvalue'];
		$this->fieldInstance = new ModTracker_Field($this);
		$this->fieldInstance->initialize();
	}
	
	function isViewPermitted() {
		// Check if the logged in user has access to the field
		global $current_user;
		return (getFieldVisibilityPermission($this->parent->module, $current_user->id, $this->name) == '0');
	}

	function diffHighlight() {
		return StringDiff::toHTML($this->prevalue, $this->postvalue);
	}

	function getDisplayName() {
		if($this->_fieldLabel === false) {
			$this->_fieldLabel = $this->fieldInstance->getFieldLabel();
		}
		return getTranslatedString($this->_fieldLabel, $this->parent->module);
	}

	static function listAll($parent) {
		global $adb, $log;
		$instances = Array();
		$result = $adb->pquery('SELECT * FROM vtiger_modtracker_detail WHERE id=?', Array($parent->id));
		if($adb->num_rows($result)) {
			while($rowmap = $adb->fetch_array($result)) {
				$instance = new self($parent);
				$instance->initialize($rowmap);
				// Pick the records which has view access
				if($instance->isViewPermitted()) {
					$instances[] = $instance;
				}
			}
		}
		return $instances;
	}

	function getModTrackerField() {
		$modTrackerFieldInstance = new ModTracker_Field();
		$modTrackerFieldInstance->initialize($this);
		
	}
}
?>
