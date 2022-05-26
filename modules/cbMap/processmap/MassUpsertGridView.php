<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: MassUpsertGridView
 *  Version      : 1.0
 *************************************************************************************************
 * The accepted format is:
<map>
	<originmodule>
	  <originname>Accounts</originname>
	</originmodule>
	<grid>
		<match>
			<field>accountname</field>
			<field>parentid</field>
		</match>
		<columns>
			<field>
				<name>accountname</name>
			</field>
			<field>
				<name>parentid</name>
			</field>
			<field>
				<name>smownerid</name>
			</field>
			<field>
				<name>bill_city</name>
			</field>
			<field>
				<name>bill_code</name>
			</field>
			<field>
				<name>Contacts.firstname</name>
			</field>
		</columns>
	</grid>
</map>
 *************************************************************************************************/
require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';
require_once 'include/ListView/ListViewJSON.php';

class MassUpsertGridView extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function getColumns() {
		return $this->mapping['columns'];
	}

	public function getMatchFields() {
		return $this->mapping['match'];
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$this->mapping['match'] = array();
		$this->mapping['columns'] = array();
		$this->mapping['module'] = (string)$xml->originmodule->originname;
		if (isset($xml->match)) {
			$match = (array)$xml->match;
			$this->mapping['match'] = $match['field'];
		}
		if (isset($xml->columns)) {
			$columns = (array)$xml->columns;
			foreach ($columns['field'] as $field) {
				$module = '';
				$fName = (string)$field->name;
				$tabid = getTabid($this->mapping['module']);
				if (strpos($fName, '.') !== false) {
					list($module, $fName) = explode('.', $fName);
					$tabid = getTabid($module);
				}
				if ($fName == 'assigned_user_id') {
					continue;
				}
				$column = getColumnnameByFieldname($tabid, $fName);
				if ($column) {
					$fName = $column;
				}
				$cachedModuleFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, $fName);
				$table = str_replace('vtiger_', '', $cachedModuleFields['tablename']);
				$columnname = (string)$field->name;
				$field->name = $cachedModuleFields['fieldname'];
				if (!empty($module)) {
					$cachedModuleFields['fieldlabel'] = $cachedModuleFields['fieldlabel'].' ('.$module.')';
				}
				$label = $cachedModuleFields['fieldlabel'];
				$this->mapping['columns'][$label] = array(
					$table => $columnname
				);
				if (isset($field->relatedModule)) {
					$this->mapping['columns'][$label] = array(
						$table => $columnname,
						'relatedModule' => (string)$field->relatedModule
					);
				}
			}
		}
	}
}
?>
