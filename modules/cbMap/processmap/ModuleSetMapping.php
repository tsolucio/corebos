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
 *  Module       : Business Mappings:: Module Set Mapping
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
	 <modules>
		 <module>ModuleName</module>
		 <module>ModuleName2</module>
		 .....
	 </modules>
 </map>
 *************************************************************************************************/
class ModuleSetMapping extends processcbMap {

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$module_set = array();
		if (isset($xml->modules)) {
			foreach ($xml->modules->module as $v) {
				$module_set[] = (string)$v;
			}
		}
		return $module_set;
	}

	public function getFullModuleSet() {
		return $this->convertMap2Array();
	}
}
?>