<?php
/*************************************************************************************************
 * Copyright 2020 Spike, JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Generic Information Map
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
	<information>
		<infotype>Holidays</infotype>
		<value>2020-04-01</value>
		<value>2020-04-11</value>
		<value>2020-05-11</value>
		<value>2020-05-16</value>
		<value>2020-05-18</value>
	</information>
</map>
 *************************************************************************************************/

require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';

class InformationMap extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function readInformationValue() {
		if (isset($this->mapping['information']['value'])) {
			return $this->mapping['information']['value'];
		}
		return array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$mapping_arr=array();
		$mapping_arr['information'] = array();
		$mapping_arr['information']['infotype']=array();
		$mapping_arr['information']['value']=array();
		foreach ($xml->information->value as $v) {
			$date = (array)$v[0];
			$value[]= isset($date[0]) ? (string)$date[0] : '';
		}
		foreach ($xml->information->infotype as $v) {
			$info = (array)$v[0];
			$infotypeinfotype[] = isset($info[0]) ? (string)$info[0] : '';
		}
		$mapping_arr['information']['infotype']=$infotypeinfotype;
		$mapping_arr['information']['value']=$value;
		$this->mapping = $mapping_arr;
	}
}
?>