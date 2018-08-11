<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once __DIR__ . '/Block.php';

class crmtogo_UI_ModuleRecordModel {
	private $_id;
	private $_blocks = array();

	public function initData($recordData) {
		$this->data = $recordData;
		if (isset($recordData['blocks'])) {
			$blocks = crmtogo_UI_BlockModel::buildModelsFromResponse($recordData['blocks']);
			foreach ($blocks as $block) {
				$this->_blocks[$block->label()] = $block;
			}
		}
	}

	public function setId($newId) {
		$this->_id = $newId;
	}

	public function id() {
		return $this->data['id'];
	}

	public function label() {
		return $this->data['label'];
	}

	public function blocks() {
		return $this->_blocks;
	}

	public static function buildModelFromResponse($recordData) {
		$instance = new self();
		$instance->initData($recordData);
		return $instance;
	}

	public static function buildModelsFromResponse($records) {
		$instances = array();
		foreach ($records as $recordData) {
			$instance = new self();
			$instance->initData($recordData);
			$instances[] = $instance;
		}
		return $instances;
	}

	public function initCreateData($recordData) {
		$this->data = $recordData;
		if (isset($recordData['blocks'])) {
			$blocks = crmtogo_UI_BlockModel::buildCreateModel($recordData['blocks']);
			foreach ($blocks as $block) {
				$this->_blocks[$block->label()] = $block;
			}
		}
	}

	public static function buildModel($recordData) {
		$instance = new self();
		$instance->initCreateData($recordData);
		return $instance;
	}
}
?>