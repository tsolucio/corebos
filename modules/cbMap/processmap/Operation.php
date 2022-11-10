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
 *  Module       : Business Mappings:: Operation View Mapping
 *  Version      : 1.0
 *************************************************************************************************
 * The accepted format is:
<map>
	<module></module>
	<filter>
		<businessquestions>
			<id></id>
			...
		</businessquestions>
	</filter>
	<actions>
		<businessactions>
			<id></id>
			...
		</businessactions>
	</actions>
<map>
*************************************************************************************************/

class Operation extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml) || empty($xml->filter) || empty($xml->actions)) {
			return array();
		}
		$this->mapping['module'] = (string)$xml->module;
		foreach ($xml->filter->businessquestions as $key) {
			$this->mapping['filters'] = (array)$key->id;
		}
		foreach ($xml->actions->businessactions as $key) {
			$this->mapping['actions'] = (array)$key->id;
		}
		return $this->mapping;
	}
}
?>
