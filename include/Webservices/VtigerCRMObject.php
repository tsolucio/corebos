<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VtigerCRMObject {

	private $moduleName;
	private $moduleId;
	private $instance;

	public function __construct($moduleCredential, $isId = false) {
		if ($isId) {
			$this->moduleId = $moduleCredential;
			$this->moduleName = $this->getObjectTypeName($this->moduleId);
		} else {
			$this->moduleName = $moduleCredential;
			$this->moduleId = $this->getObjectTypeId($this->moduleName);
		}
		$this->instance = null;
		$this->getInstance();
	}

	public function getModuleName() {
		return $this->moduleName;
	}

	public function getModuleId() {
		return $this->moduleId;
	}

	public function getInstance() {
		if ($this->instance == null) {
			$this->instance = $this->getModuleClassInstance($this->moduleName);
		}
		return $this->instance;
	}

	public function getObjectId() {
		if ($this->instance==null) {
			$this->getInstance();
		}
		return $this->instance->id;
	}

	public function setObjectId($id) {
		if ($this->instance==null) {
			$this->getInstance();
		}
		$this->instance->id = $id;
	}

	public function titleCase($str) {
		$first = substr($str, 0, 1);
		return strtoupper($first).substr($str, 1);
	}

	private function getObjectTypeId($objectName) {
		$tid = getTabid($objectName);

		if ($tid === false) {
			global $adb;
			$params = array($objectName);
			$result = $adb->pquery('select tabid from vtiger_tab where name=?;', $params);
			$data1 = $adb->fetchByAssoc($result, 1, false);
			$tid = $data1['tabid'];
		}
		return $tid;
	}

	private function getModuleClassInstance($moduleName) {
		return CRMEntity::getInstance($moduleName);
	}

	private function getObjectTypeName($moduleId) {
		return getTabModuleName($moduleId);
	}

	private function getTabName() {
		return $this->getModuleName();
	}

	public function read($id, $deleted = false) {
		global $adb;
		$error = false;
		$adb->startTransaction();
		try {
			$this->instance->retrieve_entity_info($id, $this->moduleName, $deleted);
			$error = $adb->hasFailedTransaction();
		} catch (\Throwable $th) {
			$error = true;
		}
		$adb->completeTransaction();
		return !$error;
	}

	public function create($element) {
		global $adb;

		$error = false;
		$this->instance->column_fields = array_merge($this->instance->column_fields, $element);

		$adb->startTransaction();
		try {
			$this->instance->save($this->getTabName());
			$error = $adb->hasFailedTransaction();
		} catch (\Throwable $th) {
			$error = true;
		}
		$adb->completeTransaction();
		return !$error;
	}

	public function update($element) {
		global $adb;
		$error = false;

		$this->instance->column_fields = array_merge($this->instance->column_fields, $element);

		$adb->startTransaction();
		$this->instance->mode = 'edit';
		try {
			$this->instance->save($this->getTabName());
			$error = $adb->hasFailedTransaction();
		} catch (\Throwable $th) {
			$error = true;
		}
		$adb->completeTransaction();
		return !$error;
	}

	public function delete($id) {
		global $adb;
		$error = false;
		$adb->startTransaction();
		try {
			DeleteEntity($this->getTabName(), $this->getTabName(), $this->instance, $id, 0);
			$error = $adb->hasFailedTransaction();
		} catch (\Throwable $th) {
			$error = true;
		}
		$adb->completeTransaction();
		return !$error;
	}

	public function getFields() {
		return $this->instance->column_fields;
	}

	/* this method just checks if a record exists and is not deleted, it does not obligate that it be the same entity type of the object instantiation */
	public function exists($id) {
		global $adb;
		$module = $this->getModuleName();
		$mod = CRMEntity::getInstance($module);
		$result = $adb->pquery('select 1 from '.$mod->crmentityTable.' where crmid=? and deleted=0 limit 1', array($id));
		return ($result && $adb->num_rows($result)>0);
	}

	public function getSEType($id) {
		$seType = getSalesEntityType($id);
		if (empty($seType)) {
			return null;
		} else {
			return $seType;
		}
	}
}
?>
