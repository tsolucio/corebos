<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

require_once 'include/Webservices/upsert.php';

$mcProcessedReferences = array();
$mcRecords = array();
$mcModules = array();

function MassCreate($elements, $user) {
	global $mcProcessedReferences, $mcRecords, $mcModules, $adb, $log;
	$mcProcessedReferences = array();
	$mcRecords = array();
	$mcModules = array();
	$failedCreates = [];
	$successCreates = [];
	$countElements = count($elements);
	foreach ($elements as &$element) {
		mcProcessReference($element, $elements, $countElements);
	}

	$types = vtws_listtypes(null, $user);
	if ($mcModules && !empty($mcModules)) {
		foreach ($mcModules as $module) {
			if (!in_array($module, $types['types'])) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied on module'.$module);
			}
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $module);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
			$meta = $handler->getMeta();
			if ($meta->hasCreateAccess() !== true) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied on module '.$module);
			}
		}
	}

	foreach ($mcRecords as &$record) {
		foreach ($record['element'] as $key => $value) {
			if (strpos($value, '@{') !== false) {
				$reference = trim($value, '@{}');
				if (!empty($reference)) {
					$id = mcGetRecordId($mcRecords, $reference);
					$record['element'][$key] = $id;
				}
			}
		}
		try {
			$updatedfields = implode(',', array_keys($record['element'])); // all fields
			if (!empty($record['searchon'])) {
				$searchOn = $record['searchon'];
				if (!empty($record['condition'])) {
					$searchOn = [
						'searchon' => $record['searchon'],
						'condition' => $record['condition'],
					];
				}
			} else {
				$searchOn = $updatedfields;
			}
			$rec = vtws_upsert($record['elementType'], $record['element'], $searchOn, $updatedfields, $user);
			$record['id'] = $rec['id'];
			$successCreates[] = $rec;
		} catch (Exception $e) {
			$failedCreates[] = [
				'record' => $record,
				'code' => $e->getCode(),
				'message' => $e->getMessage()
			];
		}
	}

	return [
		'success_creates' => $successCreates,
		'failed_creates' => $failedCreates
	];
}

function mcGetRecordId($arr, $reference) {
	$id = '';
	foreach ($arr as $ar) {
		if ($ar['referenceId'] == $reference) {
			if (isset($ar['id'])) {
				$id = $ar['id'];
			}
			break;
		}
	}
	return $id;
}

function mcGetReferenceRecord(&$arr, $reference, $lastReferenceId, $countElements) {
	$array = array();
	$index = null;
	for ($x = 0; $x <= $countElements; $x++) {
		if (isset($arr[$x]) && $arr[$x]['referenceId'] == $reference) {
			if (!mcIsCyclicReference($arr[$x], $lastReferenceId)) {
				$array = $arr[$x];
				$index = $x;
				break;
			} else {
				throw new WebServiceException(WebServiceErrorCode::$REFERENCEINVALID, 'Invalid reference specified');
			}
		}
	}
	return array($index, $array);
}

function mcProcessReference($element, &$elements, $countElements) {
	global $mcProcessedReferences, $mcRecords, $mcModules;
	foreach ($element['element'] as $value) {
		if (strpos($value, '@{') !== false) {
			$reference = trim($value, '{@}');
			if (!empty($reference) && !in_array($reference, $mcProcessedReferences)) {
				$lastReferenceId = $element['referenceId'];
				list($index, $array) = mcGetReferenceRecord($elements, $reference, $lastReferenceId, $countElements);
				if ($index !== null && $array) {
					mcProcessReference($array, $elements, $countElements);
					unset($elements[$index]);
					$mcProcessedReferences[] = $reference;
				} else {
					throw new WebServiceException(WebServiceErrorCode::$REFERENCEINVALID, 'Invalid reference specified');
				}
			}
		}
	}
	if (!in_array($element['elementType'], $mcModules)) {
		$mcModules[] = $element['elementType'];
	}
	if (!mcInArray($element, $mcRecords)) {
		$mcRecords[] = $element;
	}
}

function mcInArray($needle, $arrays) {
	if ($arrays) {
		foreach ($arrays as $array) {
			if ($array === $needle) {
				return true;
			}
		}
	}
	return false;
}

function mcIsCyclicReference($array, $lastReferenceId) {
	foreach ($array['element'] as $value) {
		if (strpos($value, '@{') !== false) {
			$reference = trim($value, '{@}');
			if (!empty($reference) && $reference == $lastReferenceId) {
				return true;
			}
		}
	}
	return false;
}