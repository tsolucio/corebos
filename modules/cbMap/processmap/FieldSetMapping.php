<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Field Set Mapping
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <module>
  <name>ModuleName</name>
  <fields>
	<field>
	  <name>fieldname</name>
	  <info>anything you need</info>
	</field>
  .....
  </fields>
  </module>
  ....
 </map>
 *************************************************************************************************/

class FieldSetMapping extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (isset($xml->module)) {
			foreach ($xml->module as $v) {
				$mname = (String)$v->name;
				$this->mapping[$mname] = array();
				foreach ($v->fields->field as $fv) {
					$this->mapping[$mname][] = array(
						'name' => (String)$fv->name,
						'info' => (String)$fv->info,
					);
				}
			}
		}
	}

	public function getFieldSet() {
		return $this->mapping;
	}

	/**
	* param $module
	*/
	public function getFieldSetModule($module) {
		return $this->mapping[$module];
	}
}
?>
