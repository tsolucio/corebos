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

function getListViewJSON($currentModule, $entries = 20, $orderBy = 'desc', $sortColumn = '', $currenPage = 1, $searchUrl = '', $searchtype = 'Basic') {
	global $app_strings, $mod_strings, $current_user, $adb;
	include_once 'include/utils/utils.php';
	require_once "modules/$currentModule/$currentModule.php";
	$category = getParentTab();
	if ($currentModule == 'Utilities') {
		$currentModule = vtlib_purify($_REQUEST['formodule']);
	}
	$focus = new $currentModule();
	$focus->initSortbyField($currentModule);
	$url_string = '';
	$sorder = $orderBy;
	if ($sortColumn != '') {
		$order_by = $sortColumn;
	} else {
		$order_by = $focus->getOrderBy();
	}
	$customViewarr = array();
	$customView = new CustomView($currentModule);
	$viewid = $customView->getViewId($currentModule);
	$customview_html = $customView->getCustomViewCombo($viewid);
	$viewinfo = $customView->getCustomViewByCvid($viewid);
	// Approving or Denying status-public by the admin in CustomView
	$statusdetails = $customView->isPermittedChangeStatus($viewinfo['status'], $viewid);
	// To check if a user is able to edit/delete a CustomView
	$edit_permit = $customView->isPermittedCustomView($viewid, 'EditView', $currentModule);
	$delete_permit = $customView->isPermittedCustomView($viewid, 'Delete', $currentModule);
	$customViewarr['viewid'] = $viewid;
	$customViewarr['viewinfo'] = $viewinfo;
	$customViewarr['edit_permit'] = $edit_permit;
	$customViewarr['delete_permit'] = $delete_permit;
	$customViewarr['customview_html'] = $customview_html;
	$customViewarr['category'] = $category;

	$sql_error = false;

	$queryGenerator = new QueryGenerator($currentModule, $current_user);
	try {
		if ($viewid != '0') {
			$queryGenerator->initForCustomViewById($viewid);
		} else {
			$queryGenerator->initForDefaultCustomView();
		}
	} catch (Exception $e) {
		$sql_error = true;
	}

	if ($searchtype == 'Basic' && $searchUrl != '') {
		$search = explode('&', $searchUrl);
		foreach ($search as $key => $value) {
			if($value != '') {
				$arg = explode('=', $value)[0];
				$val = explode('=', $value)[1];
				$_search[$arg] = $val;
			}
		}
		$_search['action'] = $currentModule.'Ajax';
		$_search['module'] = $currentModule;
		$_search['search'] = 'true';
	} elseif ($searchtype == 'Advanced' && $searchUrl != '') {
		$search = explode('&', $searchUrl);
		$advft_criteria = explode('=', $search[1])[1];
		$_search['advft_criteria'] = urldecode(explode('=', $search[1])[1]);
		$_search['advft_criteria_groups'] = urldecode(explode('=', $search[2])[1]);
		$_search['searchtype'] = explode('=', $search[3])[1];
		$_search['action'] = $currentModule.'Ajax';
		$_search['module'] = $currentModule;
		$_search['query'] = 'true';
		$_search['search'] = 'true';
	}

	if (isset($searchUrl) && $searchUrl != '') {
		$queryGenerator->addUserSearchConditions($_search);
	}

	if (!empty($order_by)) {
		$queryGenerator->addWhereField($order_by);
	}
	$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.before', $queryGenerator);
	$list_query = $queryGenerator->getQuery();
	$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.after', $queryGenerator);
	$list_query = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.query', $list_query);
	$where = $queryGenerator->getConditionalWhere();
	// Sorting
	if (!empty($order_by)) {
		$list_query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.$sorder;
	}
	$list_result = $adb->pquery($list_query, array());
	$count_result = $adb->query('SELECT FOUND_ROWS();');
	$noofrows = $adb->query_result($count_result, 0, 0);

	$limit = ($currenPage-1) * $entries;
	$list_query .= ' LIMIT '.$limit.','.$entries;
	//get entityfieldid
	$entityField = getEntityField($currentModule);
	$entityidfield = $entityField['entityid'];
	$tablename = $entityField['tablename'];
	$fieldname = $focus->list_link_field;
	try {
		$list_query = 'SELECT SQL_CALC_FOUND_ROWS '.$tablename.'.'.$entityidfield.','.substr($list_query, 6);
	} catch (Exception $e) {
		$sql_error = true;
	}
	$controller = new ListViewController($adb, $current_user, $queryGenerator);
	$listview_header_search = $controller->getBasicSearchFieldInfoList();
	//add action in header
	$listview_header_search['action'] = $app_strings['LBL_ACTION'];
	$data = array();
	$linkfield = array();
	$result = $adb->pquery($list_query, array());

	while ($result && $row = $adb->fetch_array($result)) {
		$rows = array();
		$linkRow = array();
		foreach ($row as $key => $fieldValue) {
			if (!is_numeric($key)) {
				//check field uitypes
				$fieldType = getUItypeByFieldName($currentModule, $key);
				if ($fieldType == '10') {
					//get value
					$parent_module = getSalesEntityType($fieldValue);
					$valueTitle = getTranslatedString($parent_module, $parent_module);
					$displayValueArray = getEntityName($parent_module, $fieldValue);
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $k => $value) {
							$field10Value = $value;
						}
					}
					$rows[$key] = $field10Value;
					$linkRow[$key] = array($parent_module, $fieldValue, $field10Value);
				} else {
					$rows[$key] = $fieldValue;
				}
			}
			$rows['action'] = '';
			$rows['assigned_user_id'] = isset($row['smownerid']) ? getUserFullName($row['smownerid']) : '';
			$rows['recordid'] = $row[$entityidfield];
			$rows['reference'] = $fieldname;
			$rows['relatedRows'] = $linkRow;
		}
		array_push($data, $rows);
	}
	if ($result && $sql_error != true) {
		if ($noofrows>0) {
			$res = array(
				'data' => array(
					'contents' => $data,
					'pagination' => array(
						'page' => (int)$currenPage,
						'totalCount' => (int)$noofrows,
					),
				),
				'entityfield' => $entityidfield,
				'headers' => $listview_header_search,
				'customview' => $customViewarr,
				'result' => true,
				'message' => '',
			);
		} else {
			$res = array(
				'data' => array(
					'contents' =>  array(),
					'pagination' => array(
						'page' => 1,
						'totalCount' => 0,
					),
				),
				'entityfield' => $entityidfield,
				'headers' => $listview_header_search,
				'customview' => $customViewarr,
				'result' => false,
				'message' => getTranslatedString('NoData', $currentModule),
			);
		}
	} else {
		$res = array(
			'data' => array(
				'contents' =>  array(),
				'pagination' => array(
					'page' => 1,
					'totalCount' => 0,
				),
			),
			'entityfield' => $entityidfield,
			'headers' => $listview_header_search,
			'customview' => $customViewarr,
			'result' => false,
			'message' => getTranslatedString('NoData', $currentModule),
		);
	}

	return array('data'=>$res, 'headers'=>$listview_header_search);
}
?>