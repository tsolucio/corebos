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

function getListViewJSON($currentModule, $entries = 20, $orderBy = 'DESC', $sortColumn = '', $currentPage = 1, $searchUrl = '', $searchtype = 'Basic') {
	global $app_strings, $mod_strings, $current_user, $adb;
	include_once 'include/utils/utils.php';
	include_once 'modules/Tooltip/TooltipUtils.php';
	require_once "modules/$currentModule/$currentModule.php";
	$category = getParentTab();
	$profileid = fetchUserProfileId($current_user->id);
	$lastPage = vtlib_purify($_REQUEST['lastPage']);
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

	coreBOS_Session::set($currentModule.'_Order_By', $order_by);
	coreBOS_Session::set($currentModule.'_Sort_Order', $sorder);

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
			if ($value != '') {
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

	if ((isset($searchUrl) && $searchUrl != '')) {
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

	if (isset($where) && $where != '') {
		coreBOS_Session::set('export_where', $where);
	} else {
		coreBOS_Session::delete('export_where');
	}

	// Sorting
	if (!empty($order_by)) {
		$list_query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.$sorder;
	}
	$list_result = $adb->pquery($list_query, array());
	$count_result = $adb->query('SELECT FOUND_ROWS();');
	$noofrows = $adb->query_result($count_result, 0, 0);

	$start = coreBOS_Session::get('lvs^'.$currentModule.'^'.$viewid.'^start', 1);
	if ($currentPage == 1) {
		if ($lastPage == 1) {
			coreBOS_Session::set('lvs^'.$currentModule.'^'.$viewid.'^start', (int)$lastPage);
		} else {
			if ($start > 1) {
				coreBOS_Session::set('lvs^'.$currentModule.'^'.$viewid.'^start', (int)$start);
			} else {
				coreBOS_Session::set('lvs^'.$currentModule.'^'.$viewid.'^start', 1);
			}
		}
	} else {
		coreBOS_Session::set('lvs^'.$currentModule.'^'.$viewid.'^start', (int)$currentPage);
	}
	$currentPage = coreBOS_Session::get('lvs^'.$currentModule.'^'.$viewid.'^start');
	$limit = ($currentPage-1) * $entries;
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
	$tabid = getTabid($currentModule);
	$actionPermission = getTabsActionPermission($profileid)[$tabid];
	$delete = true;
	$edit = true;
	if ($actionPermission[1]) {
		$edit = false;
	}
	if ($actionPermission[2]) {
		$delete = false;
	}
	$listview_header_search['action'] = $app_strings['LBL_ACTION'];
	$listview_header_arr = array();
	foreach ($listview_header_search as $fName => $fValue) {
		$fieldType = getUItypeByFieldName($currentModule, $fName);
		$tabid = getTabid($currentModule);
		$tooltip = ToolTipExists($fName, $tabid);
		if ($fieldType == '15') {
			$picklistValues = vtlib_getPicklistValues($fName);
			$lv_arr = array(
				'fieldname' => $fName,
				'fieldvalue' => $fValue,
				'uitype' => $fieldType,
				'picklist' => $picklistValues,
				'tooltip' => $tooltip,
				'edit' => $edit,
			);
		} elseif ($fieldType == '52' || $fieldType == '53') {
			$users = get_user_array();
			$lv_arr = array(
				'fieldname' => $fName,
				'fieldvalue' => $fValue,
				'uitype' => $fieldType,
				'picklist' => $users,
				'tooltip' => $tooltip,
				'edit' => $edit,
			);
		} else {
			$lv_arr = array(
				'fieldname' => $fName,
				'fieldvalue' => $fValue,
				'uitype' => $fieldType,
				'tooltip' => $tooltip,
				'edit' => $edit,
			);
		}
		array_push($listview_header_arr, $lv_arr);
	}
	if ($currentModule == 'cbCalendar') {
		require_once 'modules/Calendar4You/Calendar4You.php';
		$focus = new Calendar4You();
		$focus->GetDefPermission($current_user);
	}
	$Colorizer = false;
	if (vtlib_isModuleActive('Colorizer')) {
		$Colorizer = true;
	}
	$data = array();
	$linkfield = array();
	$result = $adb->pquery($list_query, array());
	while ($result && $row = $adb->fetch_array($result)) {
		$rows = array();
		$linkRow = array();
		foreach ($row as $fieldName => $fieldValue) {
			if (!is_numeric($fieldName)) {
				$fieldnameSql = $adb->pquery('SELECT fieldname FROM vtiger_field WHERE columnname=? AND tabid=?', array(
					$fieldName,
					$tabid
				));
				$fieldName = $adb->query_result($fieldnameSql, 0, 0);
				//check field uitypes
				$fieldType = getUItypeByFieldName($currentModule, $fieldName);
				if ($fieldType == '10') {
					//get value
					$parent_module = getSalesEntityType($fieldValue);
					$valueTitle = getTranslatedString($parent_module, $parent_module);
					$displayValueArray = getEntityName($parent_module, $fieldValue);
					$field10Value = '';
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $k => $value) {
							$field10Value = $value;
						}
					}
					$rows[$fieldName] = $field10Value;
					$linkRow[$fieldName] = array($parent_module, $fieldValue, $field10Value);
				} elseif ($fieldType == '14' || ($fieldType == '2' && ($fieldName == 'time_start' || $fieldName == 'time_end'))) {
					$date = new DateTimeField($fieldValue);
					$rows[$fieldName] = $date->getDisplayTime($current_user);
				} elseif ($fieldType == '5' || $fieldType == '6' || $fieldType == '23') {
					$date = new DateTimeField($fieldValue);
					$rows[$fieldName] = $date->getDisplayDate($current_user);
				} elseif ($fieldType == '50') {
					$date = new DateTimeField($fieldValue);
					$rows[$fieldName] = $date->getDisplayDateTimeValue($current_user);
				} elseif ($fieldType == '56') {
					if ($fieldValue == 1) {
						$rows[$fieldName] = getTranslatedString('yes', $currentModule);
					} elseif ($fieldValue == 0) {
						$rows[$fieldName] = getTranslatedString('no', $currentModule);
					} else {
						$rows[$fieldName] = '--';
					}
				} elseif ($fieldType == '71' || $fieldType == '72' || $fieldType == '7' || $fieldType == '9') {
					$currencyField = new CurrencyField($fieldValue);
					$rows[$fieldName] = $currencyField->getDisplayValue($current_user, true);
				} else {
					if ($fieldName) {
						$rows[$fieldName] = $fieldValue;
					}
				}
			}
			if (GlobalVariable::getVariable('Application_ListView_Record_Change_Indicator', 1, $currentModule)) {
				$isModified = false;
				if (!$focus->isViewed($row[$entityidfield])) {
					$isModified = true;
				}
			}
			$Actions = array();
			if ($currentModule == 'cbCalendar' && $focus->CheckPermissions('EDIT', $row[$entityidfield])) {
				$evstatus = $row['eventstatus'];
				if (!($evstatus == 'Deferred' || $evstatus == 'Completed' || $evstatus == 'Held' || $evstatus == '')) {
					if ($row['activitytype'] == 'Task') {
						$evt_status = 'Completed';
					} else {
						$evt_status = 'Held';
					}
					$Actions = array(
						'status' => $evt_status,
					);
				}
			}
			$rows['action'] = array(
				'edit' => $edit,
				'delete' => $delete,
				'isModified' => $isModified,
				'cbCalendar' => $Actions,
			);
			$rows['assigned_user_id'] = isset($row['smownerid']) ? getUserFullName($row['smownerid']) : '';
			$rows['recordid'] = $row[$entityidfield];
			$rows['reference'] = $fieldname;
			$rows['relatedRows'] = $linkRow;
		}
		if ($Colorizer) {
			$className = enableColorizer($row, $tabid);
			$rows['_attributes'] = $className;
		}
		array_push($data, $rows);
	}
	if ($result && $sql_error != true) {
		if ($noofrows>0) {
			$res = array(
				'data' => array(
					'contents' => $data,
					'pagination' => array(
						'page' => (int)$currentPage,
						'totalCount' => (int)$noofrows,
					),
				),
				'entityfield' => $entityidfield,
				'headers' => $listview_header_arr,
				'customview' => $customViewarr,
				'export_where' => $where,
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
				'headers' => $listview_header_arr,
				'customview' => $customViewarr,
				'export_where' => $where,
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
			'headers' => $listview_header_arr,
			'customview' => $customViewarr,
			'export_where' => $where,
			'result' => false,
			'message' => getTranslatedString('NoData', $currentModule),
		);
	}

	return array('data'=>$res, 'headers'=>$listview_header_arr);
}

function updateDataListView() {
	global $current_user;
	$modulename = vtlib_purify($_REQUEST['modulename']);
	$value = vtlib_purify($_REQUEST['value']);
	$columnName = vtlib_purify($_REQUEST['columnName']);
	$recordid = vtlib_purify($_REQUEST['recordid']);
	$moduleHandler = vtws_getModuleHandlerFromName($modulename, $current_user);
	$handlerMeta = $moduleHandler->getMeta();
	$tablename = getTableNameForField($modulename, $columnName);
	$focus = new $modulename;
	$focus->id = $recordid;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($recordid, $modulename);
	$focus->column_fields[$columnName] = $value;
	$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $handlerMeta);
	$focus->saveentity($modulename);
}

function enableColorizer($row, $tabid) {
	require_once 'modules/Colorizer/functions/processConditions.php';
	global $adb, $currentModule;
	if ($currentModule == 'Utilities') {
		$currentModule = vtlib_purify($_REQUEST['formodule']);
	}
	$classNames = array();
	foreach ($row as $fieldName => $fieldValue) {
		if (!is_numeric($fieldName)) {
			if ($fieldName == 'smownerid') {
				$fieldName = 'assigned_user_id';
			}
			$sql = $adb->pquery('SELECT * FROM vtiger_colorizer WHERE field=? AND tabid=?', array(
				$fieldName,
				$tabid
			));
			$numOfRows = $adb->num_rows($sql);
			if ($numOfRows > 0) {
				$condition = $adb->query_result($sql, 0, 'condition');
				$additional = $adb->query_result($sql, 0, 'additional');
				$className = $adb->query_result($sql, 0, 'classname');
				$additional = json_decode($additional, true);
				$condition = json_decode($condition, true);
				if (!empty($condition)) {
					$conditionRes = array();
					foreach ($condition as $key => $value) {
						$field = $value['field'];
						$not = $value['not'] == 1 ? true : false;
						$condition = $value['condition'];
						$parameter = isset($value['parameter']) ? $value['parameter'][0] : '';
						$fieldType = getUItypeByFieldName($currentModule, $field);
						if ($fieldType == '10' && ($row[$field] != 0 || $row[$field] != null)) {
							$parent_module = getSalesEntityType($row[$field]);
							if ($parent_module != '') {
								$displayValueArray = getEntityName($parent_module, $row[$field]);
								$field10Value = '';
								if (!empty($displayValueArray)) {
									$field10Value = $displayValueArray[$row[$field]];
									if ($field != $fieldName) {
										$row[$field] = $field10Value;
									} else {
										$fieldValue = $field10Value;
									}
								}
							}
						}
						if ($field != $fieldName) {
							if ($field == 'assigned_user_id') {
								$row[$field] = getUserFullName($row[$field]);
							}
							if (!isset($row[$field])) {
								$field = getColumnnameByFieldname($tabid, $field);
							}
							$res = processConditions($condition, $field, $not, $parameter, $row[$field]);
							array_push($conditionRes, $res);
						} else {
							if ($field == 'assigned_user_id') {
								$fieldValue = getUserFullName($fieldValue);
							}
							$res = processConditions($condition, $field, $not, $parameter, $fieldValue);
							array_push($conditionRes, $res);
						}
					}
					if ($additional['listviewrow'] == 0) {
						if (!in_array(false, $conditionRes)) {
							$fields = $fieldName.'::'.$className;
							array_push($classNames, $fields);
						}
					} else {
						if (!in_array(false, $conditionRes)) {
							$fields = array(
								'row' => array($className)
							);
							array_push($classNames, $fields);
						}
					}
				} else {
					if ($additional['listviewrow'] == 0) {
						$fields = $fieldName.'::'.$className;
						array_push($classNames, $fields);
					} else {
						$fields = array(
							'row' => array($className)
						);
						array_push($classNames, $fields);
					}
				}
			}
		}
	}
	$rows = array(
		'className'=> array(
			'row' => array()
		)
	);
	$columns = array();
	foreach ($classNames as $c => $name) {
		if (isset($name['row'])) {
			$columns['className']['row'] = $name['row'];
		} else {
			list($fName, $class) = explode('::', $name);
			$columns['className']['column'][$fName] = array($class);
		}
	}
	return $columns;
}
?>