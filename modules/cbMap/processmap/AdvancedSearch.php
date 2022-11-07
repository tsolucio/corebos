<?php
 /*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: AdvancedSearch
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
    <map>
        <modulename>todo</modulename>
        <group>
            <conditions>
                <condition>
                    <fieldname>subject</fieldname>
                    <operator>eq</operator>
                </condition>
                <condition>
                    <join>and</join>
                    <fieldname>desciption</fieldname>
                    <operator>eq</operator>
                </condition>
            </conditions>
        </group>
        <group>
            <groupjoin>and</groupjoin>
            <conditions>
                <condition>
                    <fieldname>End Date</fieldname>
                    <operator>less than</operator>
                </condition>
            </conditions>
        </group>
    </map>
 *************************************************************************************************/

class AdvancedSearch extends processcbMap {

	public function processMap($module) {
		$map = $this->getMap();
		$parsedxml = $this->getXMLContent();
        $xmlString = htmlspecialchars_decode($map->column_fields["content"]);

        // checking the module
        $module = (string)$module[0];
        $moduleInXml = (string)$parsedxml->modulename;
        if ($module != $moduleInXml) {
            return "WRONG_MODULE";
        }

        // convert $parsedxml into an associative array
        $parsedxml = json_decode(json_encode((array)$parsedxml), true);

        // convert fields into the right format
        $customView = new CustomView();
        if(isset($parsedxml["group"]["conditions"])) {
            if (isset($parsedxml["group"]["conditions"]["condition"]["fieldname"])) {
                $parsedxml["group"]["conditions"]["condition"]["fieldname"] = $customView->getFilterFieldDefinitionByNameOrLabel($parsedxml["group"]["conditions"]["condition"]["fieldname"], $module);
            } else {
                foreach ($parsedxml["group"]["conditions"]["condition"] as &$value) {
                    $value["fieldname"] = $customView->getFilterFieldDefinitionByNameOrLabel($value["fieldname"], $module);
                }
            }
        } else {
            foreach ($parsedxml["group"] as &$value) {
                if (isset($value["conditions"]["condition"]["fieldname"])) {
                    $value["conditions"]["condition"]["fieldname"] = $customView->getFilterFieldDefinitionByNameOrLabel($value["conditions"]["condition"]["fieldname"], $module);
                } else {
                    foreach ($value["conditions"]["condition"] as &$value2) {
                        $value2["fieldname"] = $customView->getFilterFieldDefinitionByNameOrLabel($value2["fieldname"], $module);
                    }
                }
            }
        }

		return $parsedxml;
	}
}
?>