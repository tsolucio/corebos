<?php
/*************************************************************************************************
 * Copyright 2023 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: MasterGrid
 *  Version      : 1.0
 *************************************************************************************************
 * The accepted format is:
<map>
<module>Quotes</module>
<relatedfield>account_id</relatedfield>
<fields>
	<field>
		<name>subject</name>
		<readonly>1</readonly>
	</field>
	<field>
		<name>quotestage</name>
		<readonly>0</readonly>
	</field>
	<field>
		<name>potential_id</name>
	</field>
	...
</fields>
</map>
 *************************************************************************************************/
include_once 'include/Webservices/DescribeObject.php';

class cbMasterGrid extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml) || empty($xml->module) || empty($xml->fields) || empty($xml->relatedfield)) {
			return array();
		}
		$this->mapping['module'] = (string)$xml->module;
		$this->mapping['relatedfield'] = (string)$xml->relatedfield;
		$this->detailModule = $this->mapping['module'];
		$fields = (array)$xml->fields;
		foreach ($fields as $field) {
			foreach ($field as $fld) {
				$finfo = $this->getFieldInfo((string)$fld->name);
				if (isset($fld->readonly)) {
					$finfo['editable'] = !boolval((string)$fld->readonly);
				}
				$this->mapping['fields'][] = $finfo;
			}
		}
		return $this->mapping;
	}

	private function getFieldInfo($fieldname) {
		global $current_user;
		if (!isset($this->fieldsinfo[$this->detailModule])) {
			$wsfieldsinfo = vtws_describe($this->detailModule, $current_user);
			$this->fieldsinfo[$this->detailModule] = $wsfieldsinfo['fields'];
			$tabid = getTabid($this->detailModule);
			foreach ($this->fieldsinfo[$this->detailModule] as $key => $finfo) {
				$this->fieldsinfo[$this->detailModule][$key]['fieldid'] = getFieldid($tabid, $finfo['name']);
				$this->fieldsinfo[$this->detailModule][$key]['columnname'] = getColumnnameByFieldname($tabid, $finfo['name']);
			}
		}
		$ret = array_search($fieldname, array_column($this->fieldsinfo[$this->detailModule], 'name'));
		if (isset($this->fieldsinfo[$this->detailModule][$ret]['uitype']) && $this->fieldsinfo[$this->detailModule][$ret]['uitype']==10) {
			$refmod = $this->fieldsinfo[$this->detailModule][$ret]['type']['refersTo'][0];
			$rmod = CRMEntity::getInstance($refmod);
			$WSCodeID = vtws_getEntityId($refmod);
			$this->fieldsinfo[$this->detailModule][$ret]['searchin'] = $refmod;
			$this->fieldsinfo[$this->detailModule][$ret]['searchby'] = $refmod.$rmod->list_link_field;
			$this->fieldsinfo[$this->detailModule][$ret]['searchwsid'] = $WSCodeID;
		}
		return $this->fieldsinfo[$this->detailModule][$ret];
	}
}
?>
