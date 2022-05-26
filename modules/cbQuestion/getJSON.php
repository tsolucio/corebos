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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'modules/cbQuestion/cbQuestion.php';
require_once 'include/ListView/ListView.php';
require_once 'include/ListView/ListViewJSON.php';

if (isset($_REQUEST['page'])) {
	$page = vtlib_purify($_REQUEST['page']);
} else {
	$page = 1;
}
if (isset($_REQUEST['sortColumn'])) {
	$order_by = vtlib_purify($_REQUEST['sortColumn']);
	if (isset($focus->list_fields_name[$order_by])) {
		$order_by = $focus->list_fields_name[$order_by];
	} else {
		$order_by = $focus->default_order_by;
	}
} else {
}
if (!isset($_REQUEST['sortAscending'])) {
	$sorder = '';
} elseif ($_REQUEST['sortAscending']=='true') {
	$sorder = 'ASC';
} else {
	$sorder = 'DESC';
}
if (!empty($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage'])) {
	$rowsperpage = (int) vtlib_purify($_REQUEST['perPage']);
} else {
	$rowsperpage = GlobalVariable::getVariable('MasterDetail_Pagination', 40);
}
$from = ($page-1)*$rowsperpage;
$limit = " limit $from,$rowsperpage";
$qid = $_REQUEST['qid'];
$recordid = $_REQUEST['contextid'];
if (!empty($recordid)) {
	$ctxtmodule = getSalesEntityType($recordid);
	$params = array(
		'$RECORD$' => $recordid,
		'$MODULE$' => $ctxtmodule,
		'$USERID$' => $current_user->id,
	);
	$ent = CRMEntity::getInstance($ctxtmodule);
	$ent->id = $recordid;
	$ent->retrieve_entity_info($recordid, $ctxtmodule, false, true);
	foreach ($ent->column_fields as $fname => $fvalue) {
		$params['$'.$fname.'$'] = $fvalue;
	}
} else {
	$params = [];
}
$q = stripTailCommandsFromQuery(rtrim(cbQuestion::getSQL($qid, $params), ';'), false).$limit;
$grid = new GridListView('cbQuestion');
$grid->currentPage = $page;
$index = 'index';
$properties = (array) cbQuestion::getQuestionProperties($qid);
$list_fields = array();
foreach ($properties as $property) {
	if (isset($property->cbcolumnname)) {
		$list_fields[$property->name] = array($index=>$property->cbcolumnname);
	} else {
		$list_fields[$property->name] = array($index=>$property->name);
	}
}
$entries_list = $grid->gridTableBasedEntries($q, $list_fields, $index);
echo json_encode($entries_list);