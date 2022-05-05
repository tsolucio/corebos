<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'Smarty_setup.php';
require_once 'include/Webservices/MassCreate.php';
require_once 'include/Webservices/Update.php';
require_once 'include/Webservices/Create.php';
require_once 'include/ListView/ListViewJSON.php';
global $current_user;
Vtiger_Request::validateRequest();
$op = vtlib_purify($_REQUEST['method']);
$cbMapTabid = vtws_getEntityId('cbMap');
$UsersTabid = vtws_getEntityId('Users');
switch ($op) {
	case 'MassCreate':
		$newData = array();
		$searchon = array();
		$mapName = vtlib_purify($_REQUEST['mapName']);
		$module = vtlib_purify($_REQUEST['moduleName']);
		$data = vtlib_purify($_REQUEST['data']);
		$data = json_decode($data, true);
		$grid = new GridListView($module);
		$grid->tabid = getTabid($module);
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$MassUpsert = $cbMap->MassUpsertGridView();
			$match = $MassUpsert->getMatchFields();
		}
		foreach ($data as $row) {
			unset($row['_attributes']);
			$currentRow = array();
			foreach ($row as $field => $value) {
				if (is_numeric($field)) {
					continue;
				}
				$fieldName = $grid->getFieldNameByColumn($field);
				if (!$fieldName) {
					continue;
				}
				$fieldType = getUItypeByFieldName($module, $fieldName);
				if (!is_array($value)) {
					if ($field == 'smownerid') {
						$value = $UsersTabid.'x'.$value;
						$field = 'assigned_user_id';
						unset($row['smownerid']);
					} else {
						$searchon[] = $fieldName;
					}
					if ($fieldType == Field_Metadata::UITYPE_RECORD_RELATION) {
						$relMods = $grid->findRelatedModule($fieldName);
						if (!empty($relMods)) {
							foreach ($relMods as $mod) {
								$reference_field = getEntityFieldNames($mod);
								if (is_array($reference_field['fieldname'])) {
									$id = getEntityId($mod, $value);
								} else {
									$id = __cb_getidof(array(
										$mod, $reference_field['fieldname'], $value
									));
								}
								$value = 0;
								if ($id > 0) {
									$tabid = vtws_getEntityId($mod);
									$value = $tabid.'x'.$id;
									break;
								}
							}
							$field = $fieldName;
						}
					}
					$currentRow[$fieldName] = $value;
				}
			}
			if (isset($match)) {
				$searchon = $match;
			}
			$newData[] = array(
				'elementType' => $module,
				'referenceId' => '',
				'searchon' => implode(',', $searchon),
				'element' => $currentRow
			);
		}
		$response = MassCreate($newData, $current_user);
		break;
	case 'SaveMap':
		$moduleName = vtlib_purify($_REQUEST['moduleName']);
		$mapName = vtlib_purify($_REQUEST['mapName']);
		$match = vtlib_purify($_REQUEST['match']);
		$ActiveColumns = vtlib_purify($_REQUEST['ActiveColumns']);
		$ActiveColumns = json_decode($ActiveColumns, true);
		$match = json_decode($match, true);
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><deletethis/>');
		$map = $xml->addChild('map');
		$originmodule = $map->addChild('originmodule');
		$originname = $originmodule->addChild('originname', $moduleName);
		if (!empty($match)) {
			$matchblock = $map->addChild('match');
			foreach ($match as $field) {
				$matchblock->addChild('field', $field['name']);
			}
		}
		$columns = $map->addChild('columns');
		foreach ($ActiveColumns as $key) {
			$field = $columns->addChild('field');
			$name = $field->addChild('name', $key['name']);
		}
		$dom = dom_import_simplexml($xml)->ownerDocument;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		$map = str_replace(
			array(
				'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<deletethis>',
				'</deletethis>',
			),
			'',
			$dom->saveXML()
		);
		$mapid = __cb_getidof(array(
			'cbMap', 'mapname', $mapName
		));
		$response = array();
		if ($mapid > 0) {
			$element = array(
				'id' => $cbMapTabid.'x'.$mapid,
				'content' => trim($map),
				'mapname' => $mapName,
				'assigned_user_id' => $UsersTabid.'x'.$current_user->id
			);
			$response = vtws_update($element, $current_user);
		} else {
			//create a new map
			$element = array(
				'content' => trim($map),
				'mapname' => $mapName,
				'maptype' => 'MassUpsertGridView',
				'targetname' => $moduleName,
				'assigned_user_id' => '19x'.$current_user->id
			);
			$response = vtws_create('cbMap', $element, $current_user);
		}
		break;
	case 'SaveMapFromModule':
		$MapID = vtlib_purify($_REQUEST['MapID']);
		$fields = vtlib_purify($_REQUEST['fields']);
		$match = vtlib_purify($_REQUEST['match']);
		$fields = json_decode($fields, true);
		$match = json_decode($match, true);
		$mapName = vtlib_purify($_REQUEST['mapName']);
		$moduleName = vtlib_purify($_REQUEST['moduleName']);
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><deletethis/>');
		$map = $xml->addChild('map');
		$originmodule = $map->addChild('originmodule');
		$originname = $originmodule->addChild('originname', $moduleName);
		if (!empty($match)) {
			$matchblock = $map->addChild('match');
			foreach ($match as $field) {
				$matchblock->addChild('field', $field);
			}
		}
		$columns = $map->addChild('columns');
		foreach ($fields as $field) {
			$fld = $columns->addChild('field');
			$name = $fld->addChild('name', $field);
		}
		$dom = dom_import_simplexml($xml)->ownerDocument;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		$map = str_replace(
			array(
				'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<deletethis>',
				'</deletethis>',
			),
			'',
			$dom->saveXML()
		);
		$element = array(
			'id' => $cbMapTabid.'x'.$MapID,
			'content' => trim($map),
			'mapname' => $mapName,
			'assigned_user_id' => $UsersTabid.'x'.$current_user->id
		);
		$response = vtws_update($element, $current_user);
		break;
	default:
		$response = '';
		break;
}
echo json_encode($response);
?>
