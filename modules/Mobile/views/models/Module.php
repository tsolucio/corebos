<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/ModuleRecord.php';

class crmtogo_UI_ModuleModel {
	public $data;

	public function initData($moduleData) {
		$this->data = $moduleData;
	}

	public function id() {
		return $this->data['id'];
	}

	public function name() {
		return $this->data['name'];
	}

	public function active() {
		return $this->data['active'];
	}

	public function label() {
		return $this->data['label'];
	}

	public static function buildModelsFromResponse($modules) {
		$instances = array();
		foreach ($modules as $moduleData) {
			$instance = new self();
			$instance->initData($moduleData);
			$instances[] = $instance;
		}
		return $instances;
	}
}
?>