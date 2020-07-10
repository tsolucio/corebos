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
/**
 * Check if module exists in crm
 * @param {string} modulename
 */
function checkForModule($modulename) {
	global $adb, $current_user;
	$sql = 'SELECT * FROM vtiger_tab WHERE name=?';
	$data = $adb->pquery($sql, array($modulename));
	if ($adb->num_rows($data) > 0) {
		return 1;
	}
	return 0;
}
/**
 * Load all created/on progress modules
 * @param {number} page
 * @param {number} perPage
 */
function loadModules($page, $perPage) {
	global $adb, $current_user, $mod_strings;
	$limit = ($page-1) * $perPage;
	$limitSql = ' LIMIT '.$limit.','.$perPage;
	$list_query = 'SELECT vtiger_modulebuilder.modulebuilder_name as modulebuilder_name, mb.date as date, mb.completed as completed, vtiger_modulebuilder.modulebuilderid as moduleid
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
/**
 * Load all blocks in step 3
 */
function loadBlocks() {
	global $adb, $current_user;
	$moduleid = $_COOKIE['ModuleBuilderID'];
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
/**
 * Load all fields in step 4
 */
function loadFields() {
	global $adb, $current_user;
	$moduleid = $_COOKIE['ModuleBuilderID'];
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
/**
 * Get values to autocomplete inputs
 * @param {string} query
 */
function autocompleteName($query) {
	global $adb, $current_user;
	if ($query == '' || strlen($query) < 2) {
		return array();
	}
	$functionSql = $adb->pquery('SELECT DISTINCT name FROM vtiger_relatedlists WHERE name LIKE "%'.$query.'%" LIMIT 5', array());
	$name = array();
	for ($i=0; $i < $adb->num_rows($functionSql); $i++) {
		$nameArr = array();
		$nameVal = $adb->query_result($functionSql, $i, 'name');
		$nameArr['name'] = $nameVal;
		array_push($name, $nameArr);
	}
	return $name;
}
/**
 * Get values to autocomplete inputs
 * @param {string} query
 */
function autocompleteModule($query) {
	global $adb, $current_user;
	if ($query == '' || strlen($query) < 2) {
		return array();
	}
	$functionSql = $adb->pquery('SELECT DISTINCT name FROM vtiger_tab WHERE name LIKE "%'.$query.'%" LIMIT 5', array());
	$module = array();
	for ($i=0; $i < $adb->num_rows($functionSql); $i++) {
		$moduleArr = array();
		$moduleVal = $adb->query_result($functionSql, $i, 'name');
		$moduleArr['name'] = $moduleVal;
		array_push($module, $moduleArr);
	}
	return $module;
}
/**
 * Load all saved values on back step
 * @param {number} step
 */
function loadValues($step, $moduleId) {
	global $adb;
	$moduleid = $moduleId == 0 ? vtlib_purify($_COOKIE['ModuleBuilderID']) : $moduleId;
	$cookie_name = "ModuleBuilderID";
	$cookie_value = $moduleid;
	setcookie($cookie_name, $cookie_value, time() + ((86400 * 30) * 7), "/");
	if ($step == 1) {
		$modSql = $adb->pquery('SELECT * FROM vtiger_modulebuilder WHERE modulebuilderid=? AND status=?', array(
			$moduleid,
			'active'
		));
		$module = array();
		$module['name'] = $adb->query_result($modSql, 0, 'modulebuilder_name');
		$module['label'] = $adb->query_result($modSql, 0, 'modulebuilder_label');
		$module['parent'] = $adb->query_result($modSql, 0, 'modulebuilder_parent');
		$module['icon'] = $adb->query_result($modSql, 0, 'icon');
		return $module;
	} elseif ($step == 2) {
		$blockSql = $adb->pquery('SELECT * FROM vtiger_modulebuilder_blocks WHERE moduleid=?', array(
			$moduleid
		));
		$block = array();
		for ($i=0; $i < $adb->num_rows($blockSql); $i++) {
			$blockArr = array();
			$blocksid = $adb->query_result($blockSql, $i, 'blocksid');
			$blocks_label = $adb->query_result($blockSql, $i, 'blocks_label');
			$blockArr['blocksid'] = $blocksid;
			$blockArr['blocks_label'] = $blocks_label;
			array_push($block, $blockArr);
		}
		return $block;
	} elseif ($step == 3) {
		$fieldsdb = $adb->pquery('SELECT * FROM `vtiger_modulebuilder_fields` WHERE moduleid=?', array(
			$moduleid
		));
		$fieldlst = array();
		for ($i=0; $i < $adb->num_rows($fieldsdb); $i++) {
			$fieldsArr = array();
			$fieldsid = $adb->query_result($fieldsdb, $i, 'fieldsid');
			$fieldname = $adb->query_result($fieldsdb, $i, 'fieldname');
			$columnname = $adb->query_result($fieldsdb, $i, 'columnname');
			$fieldlabel = $adb->query_result($fieldsdb, $i, 'fieldlabel');
			$uitype = $adb->query_result($fieldsdb, $i, 'uitype');
			$entityidentifier = $adb->query_result($fieldsdb, $i, 'entityidentifier');
			$relatedmodules = $adb->query_result($fieldsdb, $i, 'relatedmodules');
			$sequence = $adb->query_result($fieldsdb, $i, 'sequence');
			$presence = $adb->query_result($fieldsdb, $i, 'presence');
			$typeofdata = $adb->query_result($fieldsdb, $i, 'typeofdata');
			$displaytype = $adb->query_result($fieldsdb, $i, 'displaytype');
			$masseditable = $adb->query_result($fieldsdb, $i, 'masseditable');
			$quickcreate = $adb->query_result($fieldsdb, $i, 'quickcreate');
			$fieldsArr['fieldsid'] = $fieldsid;
			$fieldsArr['fieldname'] = $fieldname;
			$fieldsArr['columnname'] = $columnname;
			$fieldsArr['fieldlabel'] = $fieldlabel;
			$fieldsArr['entityidentifier'] = $entityidentifier;
			$fieldsArr['relatedmodules'] = $relatedmodules;
			$fieldsArr['sequence'] = $sequence;
			$fieldsArr['uitype'] = $uitype;
			$fieldsArr['presence'] = $presence;
			$fieldsArr['typeofdata'] = $typeofdata;
			$fieldsArr['displaytype'] = $displaytype;
			$fieldsArr['masseditable'] = $masseditable;
			$fieldsArr['quickcreate'] = $quickcreate;
			array_push($fieldlst, $fieldsArr);
		}
		return $fieldlst;
	} elseif ($step == 4) {
		$viewSql = $adb->pquery('SELECT * FROM vtiger_modulebuilder_customview WHERE moduleid=?', array(
			$moduleid
		));
		$view = array();
		for ($i=0; $i < $adb->num_rows($viewSql); $i++) {
			$viewArr = array();
			$customviewid = $adb->query_result($viewSql, $i, 'customviewid');
			$viewname = $adb->query_result($viewSql, $i, 'viewname');
			$setdefault = $adb->query_result($viewSql, $i, 'setdefault');
			$fields = $adb->query_result($viewSql, $i, 'fields');
			$fields = explode(',', $fields);
			//get fields
			$fieldArr = array();
			foreach ($fields as $key => $value) {
				$fieldSql = $adb->pquery('SELECT fieldname FROM vtiger_modulebuilder_fields WHERE fieldsid=?', array($value));
				$fieldname = $adb->query_result($fieldSql, 0, 'fieldname');
				array_push($fieldArr, $fieldname);
			}
			$viewArr['customviewid'] = $customviewid;
			$viewArr['viewname'] = $viewname;
			$viewArr['setdefault'] = $setdefault;
			$viewArr['fields'] = $fieldArr;
			array_push($view, $viewArr);
		}
		return $view;
	}
}
/**
 * Remove an existing block
 * @param {number} blockid
 */
function removeBlock($blockid) {
	global $adb;
	$delete = $adb->pquery('delete from vtiger_modulebuilder_blocks where blocksid=?', array($blockid));
	if ($delete) {
		return true;
	}
	return false;
}
/**
 * Remove an existing field
 * @param {number} fieldsid
 */
function removeField($fieldsid) {
	global $adb;
	$delete = $adb->pquery('delete from vtiger_modulebuilder_fields where fieldsid=?', array($fieldsid));
	if ($delete) {
		return true;
	}
	return false;
}
/**
 * Load default blocks in step 2
 */
function loadDefaultBlocks() {
	global $adb;
	$moduleid = vtlib_purify($_COOKIE['ModuleBuilderID']);
	$blockSql = $adb->pquery('SELECT * FROM vtiger_modulebuilder_blocks WHERE moduleid=? AND blocks_label=?', array(
		$moduleid,
		'LBL_DESCRIPTION_INFORMATION'
	));
	if ($adb->num_rows($blockSql) == 0) {
		return 'load';
	}
	return false;
}

function loadTemplate() {
	global $adb;
	$moduleid = vtlib_purify($_COOKIE['ModuleBuilderID']);
	$moduleInfo = loadValues(1, $moduleid);
	$blockInfo = loadValues(2, $moduleid);
	$viewsInfo = loadValues(4, $moduleid);
	return array(
		'info' => $moduleInfo,
		'blocks' => $blockInfo,
		'fields' => array(),
		'views' => $viewsInfo,
		'lists' => array()
	);
}
?>