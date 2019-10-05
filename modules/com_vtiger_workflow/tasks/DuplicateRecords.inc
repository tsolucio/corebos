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
 *  Module       : Duplicate Related Record functionality
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'include/utils/duplicate.php';

class DuplicateRecords extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('bmapid','bmapid_display');
	}

	public function doTask(&$recordid) {
		list($cto, $cto_id) = explode('x', $recordid->getId());
		if (!empty($cto_id) && !empty($this->bmapid)) {
			$module = getSalesEntityType($cto_id);
			$duplicatedfromfields = $recordid->getData();
			$duplicatedfrom = (isset($duplicatedfromfields['isduplicatedfromrecordid']) ? $duplicatedfromfields['isduplicatedfromrecordid'] : 0);
			if (!empty($duplicatedfrom)) {
				if (strpos($duplicatedfrom, 'x')>0) {
					list($wsdup, $duplicatedfrom) = explode('x', $duplicatedfrom);
				}
				duplicateRecordRelations($module, $cto_id, $duplicatedfrom, $this->bmapid);
			} else {
				duplicaterec($module, $cto_id, $this->bmapid);
			}
		}
	}
}
?>