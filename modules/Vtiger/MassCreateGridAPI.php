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
global $current_user;
Vtiger_Request::validateRequest();
$op = vtlib_purify($_REQUEST['method']);
switch ($op) {
	case 'MassCreate':
		$newData = array();
		$searchon = array();
		$module = vtlib_purify($_REQUEST['moduleName']);
		$data = vtlib_purify($_REQUEST['data']);
		$data = json_decode($data, true);
		foreach ($data as $row) {
			unset($row['_attributes']);
			$currentRow = array();
			foreach ($row as $field => $value) {
				if (!is_array($value)) {
					if ($field == 'smownerid') {
						$value = '19x'.$value;
						$field = 'assigned_user_id';
						unset($row['smownerid']);
					} else {
						$searchon[] = $field;
					}
					$currentRow[$field] = $value;
				}
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
		$ActiveColumns = vtlib_purify($_REQUEST['ActiveColumns']);
		$ActiveColumns = json_decode($ActiveColumns, true);
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><deletethis/>');
		$map = $xml->addChild('map');
		$originmodule = $map->addChild('originmodule');
		$originname = $originmodule->addChild('originname', $moduleName);
		$popup = $map->addChild('popup');
		$columns = $popup->addChild('columns');
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
			$tabid = vtyiicpng_getWSEntityId('cbMap');
			$element = array(
				'id' => $tabid.$mapid,
				'content' => trim($map),
				'mapname' => $mapName,
				'assigned_user_id' => '19x'.$current_user->id
			);
			$response = vtws_update($element, $current_user);
		}
		break;
	default:
		$response = '';
		break;
}
echo json_encode($response);
?>
