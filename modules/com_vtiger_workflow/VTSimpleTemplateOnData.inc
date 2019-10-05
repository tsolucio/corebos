<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';

/**
 * This class renders a text template with data passed in as an array
 * It is identical to VTSimpleTemplate except that it does not expect a record to exist,
 * it just uses the information it has.
 * if one of the fields is a reference field it will search for that record.
 */
class VTSimpleTemplateOnData extends VTSimpleTemplate {

	public function render($entityCache, $modulename, $data = array()) {
		$this->cache = $entityCache;
		$this->parent = $this;
		$this->data = $data;
		$this->moduleName = $modulename;
		return $this->parseTemplate();
	}

	public function getData() {
		return $this->data;
	}

	public function get($fieldName) {
		return isset($this->data[$fieldName]) ? $this->data[$fieldName] : '';
	}

	public function getId() {
		if (isset($this->data['record_id']) && is_numeric($this->data['record_id'])) {
			$id = $this->data['record_id'];
		} elseif (isset($this->data['id']) && is_numeric($this->data['id'])) {
			$id = $this->data['id'];
		} else {
			$id = '0x0';
		}
		return $id;
	}

	public function getModuleName() {
		return $this->moduleName;
	}
}
?>
