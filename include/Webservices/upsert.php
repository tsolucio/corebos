<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Revise.php';

function vtws_upsert($elementType, $element, $searchOn, $updatedfields, $user) {
	global $adb;
	$searchFields = explode(',', $searchOn);
	array_walk(
		$searchFields,
		function (&$val, $idx) {
			$val = trim($val);
		}
	);
	$fields = explode(',', $updatedfields);
	array_walk(
		$fields,
		function (&$val, $idx) {
			$val = trim($val);
		}
	);
	$searchWithValues = [];

	//check if all the values that will we be used for comparison exist
	foreach ($searchFields as $searchField) {
		$searchField = trim($searchField);
		if (!isset($element[$searchField])) {
			throw new WebServiceException(WebServiceErrorCode::$SEARCH_VALUE_NOT_PROVIDED, "No value is provided for the search field: $searchField");
		}
		$searchWithValues[$searchField] = $element[$searchField];
	}
	$queryGenerator = new QueryGenerator($elementType, $user);
	$queryGenerator->setFields(['id']);

	//add condition to check for the record
	foreach ($searchWithValues as $fieldName => $fieldValue) {
		$queryGenerator->addCondition($fieldName, $fieldValue, 'e');
	}

	//get only one record of many possible records
	$query = $queryGenerator->getQuery(false, 1);
	$result = $adb->pquery($query, []);
	if ($adb->num_rows($result) == 0) {
		//remove id field if exists from input
		if (isset($element['id'])) {
			unset($element['id']);
		}
		$record = vtws_create($elementType, $element, $user);
	} else {
		$meta = $queryGenerator->getMeta($elementType);
		$baseTable = $meta->getEntityBaseTable();
		$moduleTableIndexList = $meta->getEntityTableIndexList();
		$baseTableIndex = $moduleTableIndexList[$baseTable];
		$crmId = $adb->query_result($result, 0, $baseTableIndex);
		//search for updatedfields
		foreach (array_keys($element) as $key) {
			if (!in_array($key, $fields)) {
				unset($element[$key]);
			}
		}
		$element['id'] = vtws_getEntityId($elementType).'x'.$crmId;
		$record = vtws_revise($element, $user);
	}
	return $record;
}