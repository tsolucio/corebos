<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Extended Field Information Mapping
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
  <originmodule>
	<originname>ServiceContracts</originname>
  </originmodule>
  <fields>
	<field>
	  <fieldname>description</fieldname>
	  <features>
		<feature>
		  <name>RTE</name>
		  <value>1</value>
		</feature>
	  </features>
	</field>
  </fields>
</map>

<map>
  <originmodule>
	<originname>ServiceContracts</originname>
  </originmodule>
  <fields>
	<field>
	  <fieldname>autocompletefieldx</fieldname>
	  <features>
		<feature>
		  <name>showfields</name>
		  <value>field1,field2,...,fieldn</value>
		</feature>
		<feature>
		  <name>searchfields</name>
		  <value>field1,field2,...,fieldn</value>
		</feature>
	  </features>
	</field>
  </fields>
</map>

 *************************************************************************************************/

class FieldInfo extends processcbMap {

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$mapping = array();
		$mapping['origin'] = (string)$xml->originmodule->originname;
		$target_fields = array();
		foreach ($xml->fields->field as $v) {
			$fieldname = (string)$v->fieldname;
			$features = array();
			foreach ($v->features->feature as $feature) {
				if (isset($feature->value)) {
					$features[(string)$feature->name] = (string)$feature->value;
				} else {
					foreach ($feature->values->value as $single_value) {
						if (isset($single_value->module)) {
							$features[(string)$feature->name][(string)$single_value->module] = (string)$single_value->value;
						} else {
							$features[(string)$feature->name][] = isset($single_value->value) ? (string)$single_value->value : (string)$single_value;
						}
					}
				}
			}
			$target_fields[$fieldname] = $features;
		}
		$mapping['fields'] = $target_fields;
		return $mapping;
	}
}
?>