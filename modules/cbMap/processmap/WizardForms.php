<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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
 *  Module       : Business Mappings:: Wizard Step View Mapping
 *  Version      : 1.0
 *************************************************************************************************
 * The accepted format is:
<map>
<module>Contacts</module>
<rows>
	<row>
		<name>Informazioni Contatto</name>
		<fields>
			<field>firstname</field>
			<field>lastname</field>
		</fields>
	</row>
	<row>
		<fields>
			<field>assistant</field>
			<field>assistantphone</field>
			<field>cf_2191</field>
		</fields>
	</row>
	<row>
		<fields>
			<field>template_language</field>
		</fields>
	</row>
</rows>
</map>
*************************************************************************************************/

class WizardForms extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml) || empty($xml->rows) || empty($xml->module)) {
			return array();
		}
		$this->mapping['module'] = (isset($xml->module) ? (string)$xml->module : '');
		$this->detailModule = $this->mapping['module'];
		$rows = array();
		foreach ($xml->rows->row as $r) {
			$row = array();
			foreach ($r->fields->field as $field) {
				$row[] = $this->getFieldInfo((string)$field);
			}
			if (empty((string)$r->name)) {
				$rows[] = $row;
			} else {
				$rows[(string)$r->name] = $row;
			}
		}
		$this->mapping['rows'] = $rows;
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