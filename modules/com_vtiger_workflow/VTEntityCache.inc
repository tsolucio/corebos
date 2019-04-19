<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/VTEntityDelta.php';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';

class VTWorkflowEntity {
	private $isDeleted = false;

	public function __construct($user, $id, $deleted = false, $queue = false) {
		$this->moduleName = null;
		$this->id = $id;
		$this->user = $user;
		$this->isDeleted=$deleted;
		$this->isqueue=$queue;
		if ($deleted) {
			$data = vtws_retrieve_deleted($id, $user);
		} else {
			$data = vtws_retrieve($id, $user);
		}
		if (!is_null($data)) {
			foreach ($data as $key => $value) {
				if (is_string($value)) {
					$data[$key] = html_entity_decode($value, ENT_QUOTES, 'utf-8');
				}
			}
		}
		$this->data = $data;
	}

	/**
	 * Get the data from the entity object as an array.
	 *
	 * @return An array representation of the module data.
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Get the entity id.
	 *
	 * @return The entity id.
	 */
	public function getId() {
		return $this->data['id'];
	}

	/**
	 * Get the name of the module represented by the entity data object.
	 *
	 * @return The module name.
	 */
	public function getModuleName() {
		if ($this->moduleName==null) {
			global $adb;
			$wsId = $this->data['id'];
			$parts = explode('x', $wsId);
			$result = $adb->pquery('select name from vtiger_ws_entity where id=?', array($parts[0]));
			$rowData = $adb->raw_query_result_rowdata($result, 0);
			$this->moduleName = $rowData['name'];
		}
		return $this->moduleName;
	}

	public function get($fieldName) {
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $fieldName, $matches);
		if (count($matches)>0) {
			global $current_user;
			$ct = new VTSimpleTemplate($fieldName);
			$entityCache = new VTEntityCache($current_user);
			$info = $ct->render($entityCache, $this->getId());
			return $info;
		}
		return (isset($this->data[$fieldName]) ? $this->data[$fieldName] : null);
	}

	public function set($fieldName, $value) {
		$this->data[$fieldName] = $value;
	}

	public function save() {
		vtws_update($this->data, $this->user);
	}

	public function isNew() {
		$wsId = $this->data['id'];
		$parts = explode('x', $wsId);
		$recordId = $parts[1];
		$entityDelta = new VTEntityDelta();
		$oldEntity = $entityDelta->getOldEntity($this->moduleName, $recordId);
		return ($oldEntity == null);
	}

	public function isDeleted() {
		return $this->isDeleted;
	}
}

class VTEntityCache {

	public function __construct($user) {
		$this->user = $user;
		$this->cache = array();
	}

	public function forId($id) {
		if (!isset($this->cache[$id]) || $this->cache[$id]==null) {
			$deleted=!isRecordExists($id);
			$data  = new VTWorkflowEntity($this->user, $id, $deleted);
			$this->cache[$id] = $data;
		}
		return $this->cache[$id];
	}
}
?>