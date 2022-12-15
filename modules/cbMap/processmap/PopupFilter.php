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
 *  Module       : Business Mappings:: PopupFilter
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
	/////////////////////////////
	// example for cbCalendar: //
	/////////////////////////////
	<map>
		<modulename>cbCalendar</modulename>
		<field>
			<fieldname>rel_id</fieldname>
			<dependency>
				<modulename>Accounts</modulename>
				<advft_criteria>[{"groupid":1,"columnname":"Notify Owner","comparator":"e","value":"$sendnotification","columncondition":""}]</advft_criteria>
				<advft_criteria_groups>[]</advft_criteria_groups>
			</dependency>
			<dependency>
				<modulename>Leads</modulename>
				<advft_criteria>[{"groupid":1,"columnname":"Phone","comparator":"e","value":"414-661-9598","columncondition":"or"},{"groupid":1,"columnname":"Phone","comparator":"e","value":"925-647-3298","columncondition":""}]</advft_criteria>
				<advft_criteria_groups>[]</advft_criteria_groups>
			</dependency>
		</field>
	</map>

	/////////////////////////
	// example for Quotes: //
	/////////////////////////
	<map>
		<modulename>Quotes</modulename>
		<field>
			<fieldname>contact_id</fieldname>
			<modulename>Contacts</modulename>
			<advft_criteria>[{"groupid":1,"columnname":"First Name","comparator":"e","value":"Lina","columncondition":""}]</advft_criteria>
			<advft_criteria_groups>[]</advft_criteria_groups>
		</field>
	</map>

	///////////////////////////
	// example for Contacts: //
	///////////////////////////
	<map>
		<modulename>Contacts</modulename>
		<field>
			<fieldname>account_id</fieldname>
			<modulename>Accounts</modulename>
			<advft_criteria>[{"groupid":1,"columnname":"Billing City","comparator":"e","value":"Els Poblets","columncondition":""}]</advft_criteria>
			<advft_criteria_groups>[]</advft_criteria_groups>
		</field>
	</map>

	/////////////////////////////////////////////////////////////
	// example for getting values from inside uitype10 fields: //
	/////////////////////////////////////////////////////////////
	<map>
		<modulename>Contacts</modulename>
		<field>
			<fieldname>account_id</fieldname>
			<modulename>Accounts</modulename>
			<advft_criteria>[{"groupid":1,"columnname":"contact","comparator":"e","value":"$account_id->$account_id->$phone","columncondition":""}]</advft_criteria>
			<advft_criteria_groups>[]</advft_criteria_groups>
		</field>
	</map>
 *************************************************************************************************/

class PopupFilter extends processcbMap {

	public function processMap($arguments) {
		$xml = $this->getXMLContent();
		$xml = json_decode(json_encode($xml), true);
		$record = $arguments[0];
		$currentModule = $arguments[1];
		$res = [];

		// forcing a schema and changing column format inside the json
		$xml["field"] = isset($xml["field"]["fieldname"]) ? [$xml["field"]] : $xml["field"];
		foreach ($xml["field"] as &$field) {
			if (isset($field["dependency"])) {
				$field["dependency"] = isset($field["dependency"]["modulename"]) ? [$field["dependency"]] : $field["dependency"];
				foreach ($field["dependency"] as &$dependency) {
					$keyName = $field["fieldname"] . '#' . $dependency["modulename"];
					$res[$keyName]["advft_criteria"] = $this->changeJsonColumnNameFormat($dependency["advft_criteria"], $dependency["modulename"]);
					$res[$keyName]["advft_criteria_groups"] = json_decode($dependency["advft_criteria_groups"]);
				}
			} else {
				$res[$field["fieldname"]]["advft_criteria"] = $this->changeJsonColumnNameFormat($field["advft_criteria"], $field["modulename"]);
				$res[$field["fieldname"]]["advft_criteria_groups"] = json_decode($field["advft_criteria_groups"]);
			}
		}
		return json_encode($res);
	}

	/**
	 * Changes the format of the field
	 * for example: from "Phone" to "vtiger_leadaddress:phone:phone:Leads_Phone:V"
	 */
	public function changeJsonColumnNameFormat($jsonString, $module) {
		$json = json_decode($jsonString, true);
		$customView = new CustomView();
		foreach ($json as &$condition) {
			$condition["columnname"] = $customView->getFilterFieldDefinitionByNameOrLabel($condition["columnname"], $module);
		}
		return $json;
	}
}
?>
