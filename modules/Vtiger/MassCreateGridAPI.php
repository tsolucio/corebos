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
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mapName, cbMap::getMapIdByName($mapName), $currentModule);
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$MassUpsert = $cbMap->MassUpsertGridView();
			$ActiveColumns = $MassUpsert->getColumns();
			$match = $MassUpsert->getMatchFields();
		}
		$idx = 0;
		$currentRowActive = array();
		foreach ($data as $row) {
			unset($row['_attributes']);
			$currentRow = array();
			//use this to identify failed creates
			$currentRow['rowKey'] = $row['rowKey'];
			foreach ($row as $field => $value) {
				if (is_numeric($field)) {
					continue;
				}
				$fieldName = $grid->getFieldNameByColumn($field);
				//check if fieldname is in active colums
				if (empty($currentRowActive)) {
					foreach ($ActiveColumns as $key) {
						$currentRowActive[] = $grid->getFieldNameByColumn(array_values($key)[0]);
					}
				}
				if (!in_array($fieldName, $currentRowActive)) {
					continue;
				}
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
							$currentActiveModule = '';
							foreach ($relMods as $relMod) {
								foreach ($ActiveColumns as $label => $val) {
									foreach ($val as $table => $fldvalue) {
										if ($field == $fldvalue && isset($val['relatedModule'])) {
											$currentActiveModule = $val['relatedModule'];
											break;
										}
									}
								}
								if (count($relMods) > 1 && $currentActiveModule != $relMod) {
									continue;
								}
								$reference_field = getEntityFieldNames($relMod);
								$handler = vtws_getModuleHandlerFromName($relMod, $current_user);
								$meta = $handler->getMeta();
								$mandatoryFieldsList = $meta->getMandatoryFields();
								//create/update related modules with currentModule
								if (count($mandatoryFieldsList)) {
									$element = array();
									foreach ($mandatoryFieldsList as $field) {
										$element[$field] = $value;
										if ($field == 'assigned_user_id') {
											$element[$field] = $UsersTabid.'x'.$current_user->id;
										}
										if (isset($row[$relMod.'.'.$field])) {
											$element[$field] = $row[$relMod.'.'.$field];
										}
									}
									//use this to identify failed creates
									$element['rowKey'] = $row['rowKey'];
								}
								if (is_string($reference_field['fieldname'])) {
									$reference_field['fieldname'] = (array)$reference_field['fieldname'];
								}
								$tabid = getTabid($relMod);
								//find fieldnames for searchon paramter
								$searchonFields = array();
								foreach ($reference_field['fieldname'] as $field) {
									$cachedFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, $field);
									if ($cachedFields) {
										$field = $cachedFields['fieldname'];
									}
									if (isset($row[$relMod.'.'.$field])) {
										$element[$field] = $row[$relMod.'.'.$field];
									}
									$searchonFields[] = $field;
								}
								$newData[] = array(
									'elementType' => $relMod,
									'referenceId' => 'rel_entity_'.$fieldName.'_'.$idx,
									'searchon' => implode(',', $searchonFields),
									'element' => $element
								);
							}
							$field = $fieldName;
							$value = '@{rel_entity_'.$fieldName.'_'.$idx.'}';
							$idx++;
						}
					}
					$currentRow[$fieldName] = $value;
				}
			}
			if (isset($match)) {
				if (is_string($match)) {
					$match = (array)$match;
				}
				$searchon = $match;
			}
			//find fieldnames for searchon paramter
			$tabid = getTabid($module);
			$searchonFields = array();
			foreach ($searchon as $field) {
				$cachedFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, $field);
				if ($cachedFields) {
					$field = $cachedFields['fieldname'];
				}
				$searchonFields[] = $field;
			}
			$newData[] = array(
				'elementType' => $module,
				'referenceId' => '',
				'searchon' => implode(',', $searchonFields),
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
			if ($key['uitype'] == Field_Metadata::UITYPE_RECORD_RELATION) {
				$fields = lookupMandatoryFields($key);
				if (empty($fields)) {
					continue;
				}
				if (is_string($fields)) {
					//block save if mandatory fields in related modules are uitype10.
					echo json_encode($fields);
					exit;
				}
			}
			$field = $columns->addChild('field');
			$field->addChild('name', $key['name']);
			if (isset($key['activeModule'])) {
				$field->addChild('relatedModule', $key['activeModule']);
			}
			if (isset($fields) && $key['uitype'] == Field_Metadata::UITYPE_RECORD_RELATION) {
				$fields = array_unique($fields);
				foreach ($fields as $module => $fld) {
					if (!empty($fld)) {
						foreach ($fld as $name) {
							if ($name == 'assigned_user_id') {
								continue;
							}
							$relfield = $columns->addChild('field');
							$relfield->addChild('name', $module.'.'.$name);
						}
					}
				}
			}
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
				'assigned_user_id' => $UsersTabid.'x'.$current_user->id
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
		$tabid = getTabid($moduleName);
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
			$cachedFields = VTCacheUtils::lookupFieldInfoByColumn($tabid, $field);
			if (!$cachedFields) {
				$grid = new GridListView($moduleName);
				$grid->tabid = $tabid;
				$cachedFields = $grid->getFieldNameByColumn($field, 'array');
			}
			$fieldType = getUItypeByFieldName($moduleName, $cachedFields['fieldname']);
			if ($fieldType == Field_Metadata::UITYPE_RECORD_RELATION) {
				$fieldInfo = lookupMandatoryFields($cachedFields);
				if (empty($fieldInfo)) {
					continue;
				}
				if (is_string($fieldInfo)) {
					//block save if mandatory fields in related modules are uitype10.
					echo json_encode($fieldInfo);
					exit;
				}
			}
			$fld = $columns->addChild('field');
			$name = $fld->addChild('name', $field);
			if (isset($fieldInfo) && $fieldType == Field_Metadata::UITYPE_RECORD_RELATION) {
				$fieldInfo = array_unique($fieldInfo);
				foreach ($fieldInfo as $module => $flds) {
					if (!empty($flds)) {
						foreach ($flds as $name) {
							if ($name == 'assigned_user_id') {
								continue;
							}
							$fieldIns = $columns->addChild('field');
							$fieldIns->addChild('name', $module.'.'.$name);
						}
					}
				}
			}
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

function lookupMandatoryFields($key) {
	global $current_user;
	$currentModule = vtlib_getModuleNameById($key['tabid']);
	$grid = new GridListView($currentModule);
	$grid->tabid = getTabid($currentModule);
	$modules = $grid->findRelatedModule($key['fieldname']);
	if (isset($key['activeModule']) && $key['activeModule']) {
		$modules = array($key['activeModule']);
	}
	if (is_array($modules) && count($modules) == 1) {
		$reference_field = getEntityFieldNames($modules[0]);
		$handler = vtws_getModuleHandlerFromName($modules[0], $current_user);
		$meta = $handler->getMeta();
		$mandatoryFieldsList = $meta->getMandatoryFields();
		if (is_string($reference_field['fieldname'])) {
			$reference_field['fieldname'] = (array)$reference_field['fieldname'];
		}
		//no filter so we support modules with more then 1 reference field
		$filteredData = array();
		if (count($reference_field['fieldname']) > 1) {
			$mergeData = array_merge($mandatoryFieldsList, $reference_field['fieldname']);
			$tabid = getTabid($modules[0]);
			foreach ($mergeData as $field) {
				$column = getColumnnameByFieldname($tabid, $field);
				$fieldType = getUItype($modules[0], $column);
				if ($fieldType == Field_Metadata::UITYPE_RECORD_RELATION) {
					return getTranslatedString('LBL_UITYPE10_NOTALLOWED');
				}
				if ($field == 'assigned_user_id') {
					continue;
				}
				if (!in_array($field, $filteredData)) {
					$filteredData[] = $field;
				}
			}
		} else {
			$mergeData = array_merge(array_diff($mandatoryFieldsList, $reference_field['fieldname']), array_diff($reference_field['fieldname'], $mandatoryFieldsList));
			$tabid = getTabid($modules[0]);
			foreach ($mergeData as $field) {
				$column = getColumnnameByFieldname($tabid, $field);
				$fieldType = getUItype($modules[0], $column);
				if ($fieldType == Field_Metadata::UITYPE_RECORD_RELATION) {
					return getTranslatedString('LBL_UITYPE10_NOTALLOWED');
				}
				if ($field == 'assigned_user_id') {
					continue;
				}
				if (!in_array($field, $filteredData) && !in_array($field, $reference_field['fieldname'])) {
					$filteredData[] = $field;
				}
			}
		}
		return array(
			$modules[0] => $filteredData
		);
	}
	return array();
}
echo json_encode($response);
?>
