<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/../lib/StringDiff.php';
include_once __DIR__ . '/ModTracker_Field.php';

class ModTracker_Detail {
	public $id;
	public $name;
	public $prevalue;
	public $postvalue;

	public $parent;
	public $fieldInstance;

	private $_prevalueLabel = false;
	private $_postvalueLabel = false;
	private $_fieldLabel = false;

	public function __construct($parent) {
		$this->parent = $parent;
	}

	public function getModuleName() {
		return $this->parent->module;
	}

	public function getModuleId() {
		return $this->parent->getTabid();
	}

	public function getRecordId() {
		return $this->parent->crmid;
	}

	public function getFieldName() {
		return $this->name;
	}

	public function getDisplayLabelForPreValue() {
		if ($this->_prevalueLabel === false) {
			$this->_prevalueLabel = $this->fieldInstance->getDisplayLabel($this->prevalue);
		}
		return $this->_prevalueLabel;
	}

	public function getDisplayLabelForPostValue() {
		if ($this->_postvalueLabel === false) {
			$this->_postvalueLabel = $this->fieldInstance->getDisplayLabel($this->postvalue);
		}
		return $this->_postvalueLabel;
	}

	public function initialize($valuemap) {
		$this->id = $valuemap['id'];
		$this->name = $valuemap['fieldname'];
		if ($this->parent->module=='Products' && substr($this->name, 0, 10)=='deltaimage') {
			$this->name='imagename';
		}
		$this->prevalue = $valuemap['prevalue'];
		$this->postvalue =$valuemap['postvalue'];
		$this->fieldInstance = new ModTracker_Field($this);
		$this->fieldInstance->initialize();
	}

	public function isViewPermitted() {
		// Check if the logged in user has access to the field
		global $current_user;
		if ($this->parent->module=='Products' && substr($this->name, 0, 10)=='deltaimage') {
			return true;
		}
		return (getFieldVisibilityPermission($this->parent->module, $current_user->id, $this->name) == '0');
	}

	public function diffHighlight() {
		return StringDiff::toHTML($this->prevalue, $this->postvalue);
	}

	public function getDisplayName() {
		if ($this->_fieldLabel === false) {
			$this->_fieldLabel = $this->fieldInstance->getFieldLabel();
		}
		return getTranslatedString($this->_fieldLabel, $this->parent->module);
	}

	public static function listAll($parent) {
		global $adb;
		$instances = array();
		$result = $adb->pquery('SELECT * FROM vtiger_modtracker_detail WHERE id=?', array($parent->id));
		if ($adb->num_rows($result)) {
			while ($rowmap = $adb->fetch_array($result)) {
				$instance = new self($parent);
				$instance->initialize($rowmap);
				// Pick the records which has view access
				if ($instance->isViewPermitted()) {
					$instances[] = $instance;
				}
			}
		}
		return $instances;
	}

	public function getModTrackerField() {
		$modTrackerFieldInstance = new ModTracker_Field();
		$modTrackerFieldInstance->initialize($this);
	}
}
?>
