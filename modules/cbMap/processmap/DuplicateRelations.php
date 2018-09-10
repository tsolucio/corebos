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
 *  Module       : Business Mappings:: Duplicate Relations
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
The accepted format is:
<map>
	<originmodule>
		<originname>Contacts</originname> {optional}
	</originmodule>
	<relatedmodules>
		<relatedmodule>
			<module></module>
			<relation>m:m</relation> {optional}
			<condition></condition>	{optional}
		</relatedmodule>
		...
		<relatedmodule>
			<module></module>
			<relation>1:1</relation>
		</relatedmodule>
	</relatedmodules>
	<DuplicateDirectRelations>false</DuplicateDirectRelations> Allowed values: true, false
</map>
*************************************************************************************************/

require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';

class DuplicateRelations extends processcbMap {
	private $mapping = array();
	private $modulename = '';
	private $moduleid = 0;

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function getCompleteMapping() {
		return $this->mapping;
	}

	public function getRelatedModules() {
		if (isset($this->mapping["relatedmodules"])) {
			return $this->mapping["relatedmodules"];
		}
		return array();
	}

	public function DuplicateDirectRelations() {
		return filter_var($this->mapping['DuplicateDirectRelations'], FILTER_VALIDATE_BOOLEAN);
	}

	public function getOriginModuleName() {
		return $this->mapping['originname'];
	}

	public function getOriginModuleId() {
		return $this->mapping['originid'];
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=$target_fields=array();
		$mapping['originid'] = (String)$xml->originmodule->originid;
		$mapping['originname'] = (String)$xml->originmodule->originname;
		$mapping['DuplicateDirectRelations'] = (String)$xml->DuplicateDirectRelations;

		$relativemodules = array();
		foreach ($xml->relatedmodules->relatedmodule as $r) {
			$relativemodules[ (string)$r->module ] = array(
				'relation' => (string)$r->relation,
				'condition' => (string)$r->condition,
			);
		}
		$mapping["relatedmodules"] = $relativemodules;

		$this->mapping = $mapping;
	}
}
?>