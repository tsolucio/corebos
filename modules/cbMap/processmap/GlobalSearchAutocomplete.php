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
 *  Module       : Business Mappings:: Global Search Autocomplete
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
  <mincharstosearch>3</mincharstosearch>
  <maxresults>10</maxresults>
  <searchin>
	<module>
	  <name>Potentials</name>
	  <searchfields>field1,field2,...,fieldn</searchfields>
	  <searchcondition>startswith|contains</searchcondition>
	  <showfields>field1,field2,...,fieldn</showfields>
	</module>
   ...
  </searchin>
</map>

 *************************************************************************************************/

class GlobalSearchAutocomplete extends processcbMap {

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping = array();
		$mapping['mincharstosearch'] = (isset($xml->mincharstosearch) ? (Integer)$xml->mincharstosearch : 3);
		$mapping['maxresults'] = (
			isset($xml->maxresults) ? (Integer)$xml->maxresults : GlobalVariable::getVariable('Application_Global_Search_Autocomplete_Limit', 15)
		);
		$searchin = array();
		foreach ($xml->searchin->module as $k => $v) {
			$searchfields = explode(',', (String)$v->searchfields);
			$searchfields = array_map('trim', $searchfields);
			$showfields = explode(',', (String)$v->showfields);
			$showfields = array_map('trim', $showfields);
			$searchin[(String)$v->name] = array(
				'searchfields' => $searchfields,
				'showfields' => $showfields,
				'searchcondition' => (isset($v->searchcondition) ? (String)$v->searchcondition : 'startswith'),
			);
		}
		$mapping['searchin'] = $searchin;
		return $mapping;
	}
}
?>