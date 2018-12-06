<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

/*
 * function to aggregate a set of records related to a main record
 * @param array[0] aggregation operation: sum, min, max, avg, count, std, variance
 * @param array[1] RelatedModule
 * @param array[2] relatedFieldToAggregate
 * @param array[3] conditions: [field,op,value,glue],[...]
 *   => value will be evaluated against the main module record as an expression
 *   => may be empty
 *   => if main module and related module are different the relation condition will be added automatically
 * @param array[4] environment data, this is automatically added by the application
 */
function __cb_aggregation($arr) {
	global $adb;
	$query = __cb_aggregation_getQuery($arr, false);
	if (empty($query)) {
		return 0;
	} else {
		$rs = $adb->query($query);
		if ($rs) {
			$rdo = $adb->query_result($rs, 0, 'aggop');
			if (empty($rdo)) {
				return 0;
			} else {
				return $rdo;
			}
		} else {
			return 0;
		}
	}
}

/*
 * function to aggregate a set of records related to a main record
 * @param array[0] aggregation operation: sum, min, max, avg, count, std, variance
 * @param array[1] RelatedModule
 * @param array[2] relatedFieldsToAggregate with operations too
 * @param array[3] conditions: [field,op,value,glue],[...]
 *   => value will be evaluated against the main module record as an expression
 *   => may be empty
 *   => if main module and related module are different the relation condition will be added automatically
 * @param array[4] environment data, this is automatically added but the application
 */
function __cb_aggregation_operation($arr) {
	global $adb;
	$query = __cb_aggregation_getQuery($arr, true);
	if (empty($query)) {
		return 0;
	} else {
		$rs = $adb->query($query);
		if ($rs) {
			$rdo = $adb->query_result($rs, 0, 'aggop');
			if (empty($rdo)) {
				return 0;
			} else {
				return $rdo;
			}
		} else {
			return 0;
		}
	}
}

function __cb_aggregation_getQuery($arr, $userdefinedoperation = true) {
	global $adb, $GetRelatedList_ReturnOnlyQuery;
	$validoperations = array('sum', 'min', 'max', 'avg', 'count', 'std', 'variance', 'time_to_sec');
	$operation = strtolower($arr[0]);
	if (!in_array($operation, $validoperations)) {
		return 0;
	}
	$env = $arr[4];
	if (isset($env->moduleName)) {
		$mainmodule = $env->moduleName;
	} else {
		$mainmodule = $env->getModuleName();
	}
	$moduleId = getTabid($mainmodule);
	$data = $env->getData();
	$recordid = $data['id'];
	list($wsid,$crmid) = explode('x', $recordid);
	$relmodule = $arr[1];
	$relatedModuleId = getTabid($relmodule);
	if ($userdefinedoperation) {
		$relfields_operation = $arr[2];
	} else {
		$relatedmoduleInstance = Vtiger_Module::getInstance($relmodule);
		$relfield = $arr[2];
		$rfield = Vtiger_Field::getInstance($relfield, $relatedmoduleInstance);
		if (!$rfield) {
			return 0;
		}
	}

	$relationResult = $adb->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?', array($moduleId, $relatedModuleId));

	if ($relationResult && $adb->num_rows($relationResult)>0) {
		$relationInfo = $adb->fetch_array($relationResult);
		$moduleInstance = CRMEntity::getInstance($mainmodule);
		$params = array($crmid, $moduleId, $relatedModuleId);
		$GetRelatedList_ReturnOnlyQuery = true;
		$relationData = call_user_func_array(array($moduleInstance,$relationInfo['name']), $params);
		if (!isset($relationData['query'])) {
			return 0; // no query found
		}
		$query = $relationData['query'];
		$query = str_replace(array("\n", "\t", "\r"), ' ', $query);
		unset($GetRelatedList_ReturnOnlyQuery);
		if (!empty($arr[3])) {
			$query .= ' and ('.__cb_aggregation_getconditions($arr[3], $relmodule, $mainmodule, $crmid).')';
		}
	} elseif ($mainmodule==$relmodule) {
		$query = __cb_aggregation_queryonsamemodule($arr[3], $mainmodule, $relfield, $crmid);
	} else {
		return 0; // MODULES_NOT_RELATED
	}
	$qfrom = substr($query, stripos($query, ' from '));
	if ($userdefinedoperation) {
		$query = 'select '.$operation.'('.$relfields_operation.') as aggop '.$qfrom;
	} else {
		$query = 'select '.$operation.'('.$rfield->table.'.'.$rfield->column.') as aggop '.$qfrom;
	}
	return $query;
}

function __cb_aggregation_getconditions($conditions, $module, $mainmodule, $recordid) {
	global $current_user;
	$c = explode('],[', $conditions);
	array_walk($c, function (&$v, $k) {
		$v = trim($v, '[');
		$v = trim($v, ']');
	});
	$SQLGenerationMode = ($recordid=='::#');
	$entityId = vtws_getEntityId($mainmodule).'x'.$recordid;
	$entityCache = new VTEntityCache($current_user);
	$qg = new QueryGenerator($module, $current_user);
	$qg->setFields(array('id'));
	foreach ($c as $cond) {
		$cndparams = explode(',', $cond);
		if (!$SQLGenerationMode) {
			$ct = new VTSimpleTemplate($cndparams[2]);
			$value = $ct->render($entityCache, $entityId);
		} else {
			$value = $cndparams[2];
		}
		$qg->addCondition($cndparams[0], $value, $cndparams[1], $cndparams[3]);
	}
	$where = $qg->getWhereClause();
	return substr($where, stripos($where, 'where ')+6);
}

function __cb_aggregation_queryonsamemodule($conditions, $module, $relfield, $recordid) {
	global $current_user;
	$entityId = vtws_getEntityId($module).'x'.$recordid;
	$entityCache = new VTEntityCache($current_user);
	$qg = new QueryGenerator($module, $current_user);
	$qg->setFields(array($relfield));
	if (!empty($conditions)) {
		$c = explode('],[', $conditions);
		array_walk($c, function (&$v, $k) {
			$v = trim($v, '[');
			$v = trim($v, ']');
		});
		foreach ($c as $cond) {
			$cndparams = explode(',', $cond);
			$ct = new VTSimpleTemplate($cndparams[2]);
			$value = $ct->render($entityCache, $entityId);
			$qg->addCondition($cndparams[0], $value, $cndparams[1], $cndparams[3]);
		}
	}
	return $qg->getQuery();
}

function __cb_aggregate_time($arr) {
	$total_seconds = __cb_aggregation_operation(['sum', $arr[0], 'time_to_sec('. $arr[1] .')', $arr[2], $arr[3]]);
	$hours = floor($total_seconds / 3600);
	$minutes = floor((($total_seconds - ($hours * 3600)) / 60));
	$seconds = (($total_seconds - ($hours * 3600)) % 60);
	return sprintf('%03d', $hours) . ':' . sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
}

?>