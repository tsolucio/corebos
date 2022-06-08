<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

function __cb_is_numeric($arr) {
	return is_numeric($arr[0]);
}

function __cb_is_string($arr) {
	return is_string($arr[0]);
}

function __cb_or($arr) {
	$res = false;
	for ($x=0; $x < count($arr); $x++) {
		$res = $res || $arr[$x];
		if ($res) {
			break;
		}
	}
	return $res;
}
function __cb_and($arr) {
	$res = true;
	for ($x=0; $x < count($arr); $x++) {
		$res = $res && $arr[$x];
		if (!$res) {
			break;
		}
	}
	return $res;
}
function __cb_not($arr) {
	return !($arr[0]);
}

function __cb_regex($arr) {
	if (count($arr)!=2) {
		return false;
	}
	$arr[0] = '/'.trim($arr[0], '/').'/';
	return preg_match($arr[0], $arr[1])==1;
}

function __cb_exists($arr) {
	global $current_user, $adb;
	$env = $arr[2];
	$data = $env->getData();
	$recordid = $data['id'];
	$qg = new QueryGenerator($env->getModuleName(), $current_user);
	$qg->addCondition($arr[0], $arr[1], 'e');
	list($mid, $crmid) = explode('x', $recordid);
	$qg->addCondition('id', $crmid, 'n', $qg::$AND);
	$qe = 'SELECT EXISTS(SELECT 1 '.$qg->getFromClause().$qg->getWhereClause().')';
	$rs = $adb->query($qe);
	if ($rs) {
		$ex = $adb->query_result($rs, 0, 0);
		return ($ex == '1');
	} else {
		return false;
	}
}

function __cb_existsrelated($params) {
	return __cb_relatedevaluations('existsrelated', $params);
}

function __cb_allrelatedare($params) {
	return __cb_relatedevaluations('allrelatedare', $params);
}

function __cb_allrelatedarethesame($params) {
	return __cb_relatedevaluations('allrelatedarethesame', $params);
}

function __cb_relatedevaluations($evaluation, $params) {
	global $adb;
	$return = false;
	$relatedmodule = $params[0];
	if (is_string($params[3])) {
		$conditions = $params[3];
		$env = $params[4];
	} else {
		$conditions = '';
		$env = $params[3];
	}
	$data = $env->getData();
	$recordid = $data['id'];
	$module = $env->getModuleName();
	if (!empty($relatedmodule) && !empty($recordid) && !empty($module)) {
		list($mid, $crmid) = explode('x', $recordid);
		$moduleId = getTabid($module);
		$relatedModuleId = getTabid($relatedmodule);
		$moduleInstance = CRMEntity::getInstance($module);
		$relationResult = $adb->pquery(
			'SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?',
			array($moduleId, $relatedModuleId)
		);

		if (!$relationResult || !$adb->num_rows($relationResult)) {
			// MODULES_NOT_RELATED
			return false;
		}

		$relationInfo = $adb->fetch_array($relationResult);
		$relfunp = array($crmid, $moduleId, $relatedModuleId);
		global $GetRelatedList_ReturnOnlyQuery, $currentModule;
		$holdValue = $GetRelatedList_ReturnOnlyQuery;
		$GetRelatedList_ReturnOnlyQuery = true;
		$holdCM = $currentModule;
		$currentModule = $module;
		$relationData = call_user_func_array(array($moduleInstance, $relationInfo['name']), array_values($relfunp));
		$currentModule = $holdCM;
		$GetRelatedList_ReturnOnlyQuery = $holdValue;
		if (!isset($relationData['query'])) {
			// OPERATIONNOTSUPPORTED
			return false;
		}
		$relmod = Vtiger_Module::getInstance($relatedmodule);
		$fld = Vtiger_Field::getInstance($params[1], $relmod);
		if (!$fld) {
			// FIELD NOT FOUND
			return false;
		}
		$query = mkXQuery($relationData['query'], '1');
		if ($conditions!='') {
			$conditions = ' AND ('.__cb_aggregation_getconditions($conditions, $relatedmodule, $module, $crmid).')';
		}
		switch ($evaluation) {
			case 'existsrelated':
				$query = stripTailCommandsFromQuery($query).' AND ('.$fld->table.'.'.$fld->column.'=? '.$conditions.') LIMIT 1';
				$result = $adb->pquery($query, array($params[2]));
				if ($result) {
					$return = ($adb->num_rows($result) > 0);
				}
				break;
			case 'allrelatedare':
				$query = stripTailCommandsFromQuery($query).' AND ('.$fld->table.'.'.$fld->column.'!=? '.$conditions.') LIMIT 1';
				$result = $adb->pquery($query, array($params[2]));
				if ($result) {
					$return = ($adb->num_rows($result) == 0);
				}
				break;
			case 'allrelatedarethesame':
				$query = mkXQuery($relationData['query'], $fld->table.'.'.$fld->column);
				$query = stripTailCommandsFromQuery($query).$conditions.' GROUP BY '.$fld->table.'.'.$fld->column. ' LIMIT 2';
				$result = $adb->pquery($query, array());
				if ($result) {
					if ($adb->num_rows($result)==2) {
						$return = false;
					} elseif ($adb->num_rows($result)==1) {
						if ($params[2]=='') {
							$return = true;
						} else {
							$return = ($adb->query_result($result, 0, 0) == $params[2]);
						}
					} else {
						$return = true;
					}
				}
				break;
			default:
				return false;
		}
	}
	return $return;
}

function __cb_min($values) {
	if (count($values) != 0) {
		return min($values);
	}
	return false;
}

function __cb_max($values) {
	if (count($values) != 0) {
		return max($values);
	}
	return false;
}
