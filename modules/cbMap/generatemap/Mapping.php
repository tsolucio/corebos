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
 *  Module       : Business Mappings:: Module to Module Mapping
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

class Mapping extends generatecbMap {

	public function generateMap($arguments) {
		global $adb, $current_user;
		$mapping=$this->convertMap2Array();
		$ofields = $arguments[0];
		$tfields = $arguments[1];
		foreach ($mapping['fields'] as $targetfield => $sourcefields) {
			$value = '';
			$delim = (isset($sourcefields['delimiter']) ? $sourcefields['delimiter'] : '');
			foreach ($sourcefields['merge'] as $pos => $fieldinfo) {
				$idx = array_keys($fieldinfo);
				if (strtoupper($idx[0])=='CONST') {
					$const = array_pop($fieldinfo);
					$value.= $const.$delim;
				} else {
					$fieldname = array_pop($fieldinfo);
					$value.= $ofields[$fieldname].$delim;
				}
			}
			$value = rtrim($value, $delim);
			$tfields[$targetfield] = $value;
		}
		var_dump($tfields);
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=$target_fields=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		$mapping['target'] = (String)$xml->targetmodule->targetname;
		foreach ($xml->fields->field as $k => $v) {
			$fieldname = (String)$v->fieldname;
			if (!empty($v->value)) {
				$target_fields[$fieldname]['value'] = (String)$v->value;
			}
			$allmergeFields=array();
			foreach ($v->Orgfields->Orgfield as $key => $value) {
				$allmergeFields[]=array((String)$value->OrgfieldID=>(String)$value->OrgfieldName);
			}
			if (isset($v->Orgfields->delimiter)) {
				$target_fields[$fieldname]['delimiter']=(String)$v->Orgfields->delimiter;
			}
			$target_fields[$fieldname]['merge']=$allmergeFields;
		}
		$mapping['fields'] = $target_fields;
		return $mapping;
	}
}
?>