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
	return $arr[0] || $arr[1];
}
function __cb_and($arr) {
	return $arr[0] && $arr[1];
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
	global $adb;
	$existsrelated = false;
	$relatedmodule = $params[0];
	$env = $params[3];
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
		global $GetRelatedList_ReturnOnlyQuery;
		$holdValue = $GetRelatedList_ReturnOnlyQuery;
		$GetRelatedList_ReturnOnlyQuery = true;
		$relationData = call_user_func_array(array($moduleInstance, $relationInfo['name']), $relfunp);
		if (!isset($relationData['query'])) {
			// OPERATIONNOTSUPPORTED
			return false;
		}
		$GetRelatedList_ReturnOnlyQuery = $holdValue;
		$relmod = Vtiger_Module::getInstance($relatedmodule);
		$fld = Vtiger_Field::getInstance($params[1], $relmod);
		if (!$fld) {
			// FIELD NOT FOUND
			return false;
		}
		$query = mkXQuery($relationData['query'], '1');
		$query = stripTailCommandsFromQuery($query).' AND '.$fld->table.'.'.$fld->column.'=? LIMIT 1';
		$result = $adb->pquery($query, array($params[2]));
		if ($result) {
			$existsrelated = ($adb->num_rows($result) > 0);
		}
	}
	return $existsrelated;
}