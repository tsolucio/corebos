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
require_once 'include/utils/utils.php';
$methodName = vtlib_purify($_REQUEST['methodName']);
if ($methodName == 'checkForModule') {
	$modulename = vtlib_purify($_REQUEST['modulename']);
	echo json_encode(checkForModule($modulename));
} elseif ($methodName == 'loadModules') {
	$page = vtlib_purify($_REQUEST['page']);
	$perPage = vtlib_purify($_REQUEST['perPage']);
	echo json_encode(loadModules($page, $perPage));
} elseif ($methodName == 'loadBlocks') {
	echo json_encode(loadBlocks());
} elseif ($methodName == 'loadFields') {
	echo json_encode(loadFields());
}

function checkForModule($modulename) {
	global $adb, $current_user;
	$sql = 'SELECT * FROM vtiger_tab WHERE name=?';
	$data = $adb->pquery($sql, array($modulename));
	if ($adb->num_rows($data) > 0) {
		return 1;
	}
	return 0;
}

function loadModules($page, $perPage) {
	global $adb, $current_user, $mod_strings;
	$limit = ($page-1) * $perPage;
	$limitSql = ' LIMIT '.$limit.','.$perPage;
	$list_query = 'SELECT vtiger_modulebuilder.modulebuilder_name as modulebuilder_name, mb.date as date, mb.completed as completed, mb.moduleid as moduleid
		FROM vtiger_modulebuilder_name as mb
		JOIN vtiger_modulebuilder ON mb.modulebuilderid=vtiger_modulebuilder.modulebuilderid 
		WHERE userid=?';
	$modulesSql = $adb->pquery($list_query, array($current_user->id));
	$numOfRows = $adb->num_rows($modulesSql);
	$modules = $adb->pquery($list_query.$limitSql, array($current_user->id));
	$moduleLists = array();
	for ($i=0; $i < $adb->num_rows($modules); $i++) {
		$modArr = array();
		$modulebuilder_name = $adb->query_result($modules, $i, 'modulebuilder_name');
		$date = $adb->query_result($modules, $i, 'date');
		$completed = $adb->query_result($modules, $i, 'completed');
		$moduleid = $adb->query_result($modules, $i, 'moduleid');
		$modArr['modulebuilder_name'] = $modulebuilder_name;
		$modArr['moduleid'] = $moduleid;
		$modArr['date'] = $date;
		if ($completed == 'Completed') {
			$modArr['completed'] = $mod_strings['LBL_MB_COMPLETED'];
			$modArr['export'] = 'Export';
		} else {
			$modArr['completed'] = $completed.'%';
			$modArr['export'] = 'Start editing';
		}
		array_push($moduleLists, $modArr);
	}
	if ($numOfRows > 0) {
		$entries_list = array(
			'data' => array(
				'contents' => $moduleLists,
				'pagination' => array(
					'page' => (int)$page,
					'totalCount' => (int)$numOfRows,
				),
			),
			'result' => true,
		);
	} else {
		$entries_list = array(
			'data' => array(
				'contents' => array(),
				'pagination' => array(
					'page' => 1,
					'totalCount' => 0,
				),
			),
			'result' => false,
		);
	}
	return $entries_list;
}

function loadBlocks() {
	global $adb, $current_user;
	$moduleid = $_COOKIE['moduleid'];
	$blocks = $adb->pquery('SELECT blocksid, blocks_label FROM vtiger_modulebuilder LEFT JOIN vtiger_modulebuilder_blocks ON modulebuilderid=moduleid WHERE status=? AND modulebuilderid=?', array(
		'active',
		$moduleid
	));
	$blockname = array();
	for ($i=0; $i < $adb->num_rows($blocks); $i++) {
		$blockArr = array();
		$blocksid = $adb->query_result($blocks, $i, 'blocksid');
		$blocks_label = $adb->query_result($blocks, $i, 'blocks_label');
		$blockArr['blocksid'] = $blocksid;
		$blockArr['blocks_label'] = $blocks_label;
		array_push($blockname, $blockArr);
	}
	return $blockname;
}

function loadFields() {
	global $adb, $current_user;
	$moduleid = $_COOKIE['moduleid'];
	$fieldSql = $adb->pquery('SELECT fieldsid, fieldname FROM vtiger_modulebuilder_fields WHERE moduleid=?', array(
		$moduleid
	));
	$fields = array();
	for ($i=0; $i < $adb->num_rows($fieldSql); $i++) {
		$fldArr = array();
		$fieldsid = $adb->query_result($fieldSql, $i, 'fieldsid');
		$fieldname = $adb->query_result($fieldSql, $i, 'fieldname');
		$fldArr['fieldsid'] = $fieldsid;
		$fldArr['fieldname'] = $fieldname;
		array_push($fields, $fldArr);
	}
	return $fields;
}
?>