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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

class CBSelectcbMap extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('bmapid', 'bmapid_display');
	}

	public function doTask(&$recordid) {
		global $adb;
		$maprs = $adb->pquery('select 1
			from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and cbmapid=?', array($this->bmapid));
		if ($maprs && $adb->num_rows($maprs)>0) {
			require_once 'modules/cbMap/cbMap.php';
			require_once 'modules/cbMap/processmap/processMap.php';
			$focus = new cbMap();
			$focus->id = $this->bmapid;
			$focus->mode = '';
			$focus->retrieve_entity_info($this->bmapid, 'cbMap');
			$contentok = processcbMap::isXML(htmlspecialchars_decode($focus->column_fields['content']));
			if ($contentok !== true) {
				return false;
			}
			$return = $focus->RecordAccessControl();
			list($mod,$rec) = explode('x', $recordid);
			$return->setRelatedRecordID($rec);
			return $return;
		} else {
			return false;
		}
	}
}
?>