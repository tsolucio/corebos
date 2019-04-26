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
 *  Module       : Business Mappings:: IOMap
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
 <input>
   <fields>
	<field>
	 <fieldname>recordid</fieldname>
	</field>
	<field>
	 <fieldname>originmodule</fieldname>
	</field>
   </fields>
 </input>
 <output>
   <fields>
	<field>
	 <fieldname>targetaction</fieldname>
	</field>
   </fields>
 </output>
</map>
 *************************************************************************************************/

require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';

class IOMap extends processcbMap {
	private $mapping = array();
	private $input = array();
	private $output = array();

	public function processMap($arguments) {
		$this->convertMap2Array();
		return $this;
	}

	public function getCompleteMapping() {
		return $this->mapping;
	}

	public function readInputFields() {
		if (isset($this->mapping['input']['fields'])) {
			return $this->mapping['input']['fields'];
		}
		return array();
	}

	public function readOutputFields() {
		if (isset($this->mapping['output']['fields'])) {
			return $this->mapping['output']['fields'];
		}
		return array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=array();
		$mapping['input'] = array();
		$mapping['input']['fields']=array();
		foreach ($xml->input->fields->field as $k => $v) {
			$fieldname[]= isset($v->fieldname) ? (String)$v->fieldname : '';
		}
		$mapping['input']['fields']=$fieldname;
		$mapping['output']=array();
		$mapping['output']['fields']=array();
		foreach ($xml->output->fields->field as $k1 => $v1) {
			$fieldname1[]= isset($v1->fieldname) ? (String)$v1->fieldname : '';
		}
		$mapping['output']['fields']=$fieldname1;
		$this->mapping = $mapping;
	}
}
?>