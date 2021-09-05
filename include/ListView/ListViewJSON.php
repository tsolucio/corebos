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

function getListViewJSON($currentModule, $tabid, $entries = 20, $orderBy = 'DESC', $sortColumn = '', $currentPage = 1, $searchUrl = '', $searchtype = 'Basic') {
	global $current_user, $adb;
	include_once 'modules/Tooltip/TooltipUtils.php';
	require_once "modules/$currentModule/$currentModule.php";
	$lastPage = isset($_REQUEST['lastPage']) ? vtlib_purify($_REQUEST['lastPage']) : '';
	if ($currentModule == 'Utilities') {
		$currentModule = vtlib_purify($_REQUEST['formodule']);
	}
	$viewid = isset($_SESSION['lvs'][$currentModule]) ? $_SESSION['lvs'][$currentModule]['viewname'] : 0;
	$focus = new $currentModule();
	$focus->initSortbyField($currentModule);
	$url_string = '';
	if ($sortColumn != '') {
		$order_by = $sortColumn;
	} else {
		$order_by = $focus->getOrderBy();
	}

	coreBOS_Session::set($currentModule.'_Order_By', $order_by);

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
	$search_mode = false;
	if ($searchtype == 'Basic' && $searchUrl != '') {
		$search = explode('&', $searchUrl);
		foreach ($search as $value) {
			if ($value != '') {
				$arg = explode('=', $value)[0];
				$val = explode('=', urldecode($value))[1];
				$_search[$arg] = $val;
			}
		}
		$_search['action'] = $currentModule.'Ajax';
		$_search['module'] = $currentModule;
		$_search['search'] = 'true';
		$search_mode = true;
	} elseif ($searchtype == 'Advanced' && $searchUrl != '') {
		$search = explode('&', $searchUrl);
		$_search['advft_criteria'] = urldecode(explode('=', $search[1])[1]);
		$_search['advft_criteria_groups'] = urldecode(explode('=', $search[2])[1]);
		$_search['searchtype'] = explode('=', $search[3])[1];
		$_search['action'] = $currentModule.'Ajax';
		$_search['module'] = $currentModule;
		$_search['query'] = 'true';
		$_search['search'] = 'true';
		$search_mode = true;
	}

	if ((isset($searchUrl) && $searchUrl != '')) {
		$queryGenerator->addUserSearchConditions($_search);
	}

	if (!empty($order_by)) {
		$queryGenerator->addWhereField($order_by);
	}
	if (isset($_REQUEST['isRecycleModule'])) {
		$rbfields = $queryGenerator->getFields();
		if (!in_array('modifiedtime', $rbfields)) {
			// Recycle Bin List view always shows modifiedtime
			$rbfields[] = 'modifiedtime';
			$queryGenerator->setFields($rbfields);
		}
		if (!in_array('modifiedby', $rbfields)) {
			// Recycle Bin List view always shows modifiedby
			$rbfields[] = 'modifiedby';
			$queryGenerator->setFields($rbfields);
		}
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
		$list_query.=' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.coreBOS_Session::get($currentModule.'_Sort_Order');
	}

	if (isset($_REQUEST['isRecycleModule'])) {
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias($currentModule, true);
		$list_query = preg_replace("/$crmEntityTable.deleted\s*=\s*0/i", $crmEntityTable.'.deleted = 1', $list_query);
	}
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
	$reference_field = getEntityFieldNames($currentModule);
	//add action in header
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
	try {
		$result = $adb->pquery($list_query, array());
		$count_result = $adb->query(mkXQuery(stripTailCommandsFromQuery($list_query, false), 'count(*) AS count'));
		$noofrows = $adb->query_result($count_result, 0, 0);
		$rowCount = $adb->num_rows($result);
		$listviewcolumns = $adb->getFieldsArray($result);
	} catch (Exception $e) {
		$sql_error = true;
	}
	$field_types = array();
	foreach ($listviewcolumns as $fName) {
		$fieldnameSql = $adb->pquery('SELECT fieldname, uitype FROM vtiger_field WHERE columnname=? AND tabid=?', array($fName, $tabid));
		if (!$fieldnameSql || $adb->num_rows($fieldnameSql)==0) {
			$field_types[] = array(
				'columnname' => $fName,
				'fieldname' => $fName,
				'fieldtype' => '',
			);
			continue;
		}
		$fieldName = $adb->query_result($fieldnameSql, 0, 0);
		$fieldType = $adb->query_result($fieldnameSql, 0, 1);
		$field_types[] = array(
			'columnname' => $fName,
			'fieldname' => $fieldName,
			'fieldtype' => $fieldType,
		);
	}
	for ($i=0; $i < $rowCount; $i++) {
		$rows = array();
		$colorizer_row = array();
		$linkRow = array();
		foreach ($field_types as $val) {
			$columnName = $val['columnname'];
			$fieldName = $val['fieldname'];
			$fieldType = $val['fieldtype'];
			$fieldValue = $adb->query_result($result, $i, $columnName);
			$recordID = $adb->query_result($result, $i, $entityidfield);
			$smownerid = $adb->query_result($result, $i, 'smownerid');
			$colorizer_row[$fieldName] = $fieldValue;
			if ($fieldValue == '' || $fieldValue == null) {
				$rows[$fieldName] = '';
				continue;
			}
			//check field uitypes
			if ($fieldType == '10') {
				//get value
				$field10Value = '';
				if ($fieldValue != 0 || $fieldValue != null || $fieldValue != '') {
					$parent_module = getSalesEntityType($fieldValue);
					$displayValueArray = getEntityName($parent_module, $fieldValue);
					if (!empty($displayValueArray)) {
						$field10Value = $displayValueArray[$fieldValue];
					}
					$linkRow[$fieldName] = array($parent_module, $fieldValue, $field10Value);
				}
				$rows[$fieldName] = $field10Value;
			} elseif ($fieldType == '14' || ($fieldType == '2' && ($fieldName == 'time_start' || $fieldName == 'time_end'))) {
				$date = new DateTimeField($fieldValue);
				$rows[$fieldName] = $date->getDisplayTime($current_user);
			} elseif ($fieldType == '5' || $fieldType == '6' || $fieldType == '23') {
				$date = new DateTimeField($fieldValue);
				$rows[$fieldName] = $date->getDisplayDate($current_user);
			} elseif ($fieldType == '50' || $fieldType == '70') {
				$date = new DateTimeField($fieldValue);
				$value = $date->getDisplayDate();
				if ($fieldValue != '0000-00-00' && $fieldValue != '0000-00-00 00:00') {
					$value .= ' ' . $date->getDisplayTime();
					$user_format = ($current_user->hour_format=='24' ? '24' : '12');
					if ($user_format != '24') {
						$curr_time = DateTimeField::formatUserTimeString($value, '12');
						$time_format = substr($curr_time, -2);
						$curr_time = substr($curr_time, 0, 5);
						list($dt,$tm) = explode(' ', $value);
						$value = $dt . ' ' . $curr_time . $time_format;
					}
				} elseif ($value == '0000-00-00' || $value == '0000-00-00 00:00') {
					$value = '';
				}
				$rows[$fieldName] = $value;
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
				if ($fieldType == '72' || $fieldType == '71') {
					if ($fieldName == 'unit_price') {
						$currencyId = getProductBaseCurrency($recordID, $currentModule);
						$cursym_convrate = getCurrencySymbolandCRate($currencyId);
						$currencySymbol = $cursym_convrate['symbol'];
					} else {
						$currencyInfo = getInventoryCurrencyInfo($currentModule, $recordID);
						$currencySymbol = $currencyInfo['currency_symbol'];
					}
					$currencyValue = CurrencyField::convertToUserFormat($fieldValue, null, true);
					$value = CurrencyField::appendCurrencySymbol($currencyValue, $currencySymbol);
				} else {
					$value = CurrencyField::convertToUserFormat($fieldValue);
				}
				$rows[$fieldName] = $value;
			} elseif ($fieldType == '27') {
				if ($fieldValue == 'I') {
					$rows[$fieldName] = getTranslatedString('LBL_INTERNAL', $currentModule);
				} elseif ($fieldValue == 'E') {
					$rows[$fieldName] = getTranslatedString('LBL_EXTERNAL', $currentModule);
				} else {
					$rows[$fieldName] = '--';
				}
			} elseif ($fieldName == 'modifiedby') {
					$rows[$fieldName] = getUserFullName($fieldValue);
			} else {
				if ($fieldName) {
					$rows[$fieldName] = textlength_check(getTranslatedString($fieldValue, $currentModule));
				}
			}
			$rows['uitype_'.$fieldName] = $fieldType;
			if ($currentModule == 'Documents') {
				$fileattach = 'select attachmentsid from vtiger_seattachmentsrel where crmid = ?';
				$res = $adb->pquery($fileattach, array($recordID));
				$fileid = $adb->query_result($res, 0, 'attachmentsid');
				$rows['fileid'] = $fileid;
			}
			$rows['assigned_user_id'] = isset($smownerid) ? getUserFullName($smownerid) : '';
			$rows['recordid'] = $recordID;
			$rows['reference_field'] = $reference_field['fieldname'];
			$rows['relatedRows'] = $linkRow;
		}
		if ($Colorizer) {
			$className = enableColorizer($colorizer_row, $tabid);
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
				'export_where' => $where,
				'result' => true,
				'message' => '',
				'search_mode' => $search_mode,
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
				'export_where' => $where,
				'result' => false,
				'message' => getTranslatedString('NoData', $currentModule),
				'search_mode' => $search_mode,
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
			'export_where' => $where,
			'result' => false,
			'message' => getTranslatedString('NoData', $currentModule),
			'search_mode' => $search_mode,
		);
	}

	return array('data'=>$res);
}

function getListViewHeaders($currentModule, $tabid) {
	global $app_strings, $current_user, $adb;
	include_once 'modules/Tooltip/TooltipUtils.php';
	require_once "modules/$currentModule/$currentModule.php";
	$profileid = getUserProfile($current_user->id);
	$profileid = reset($profileid);
	if ($currentModule == 'Utilities') {
		$currentModule = vtlib_purify($_REQUEST['formodule']);
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
	//add action in header
	$actionPermission = getTabsActionPermission($profileid)[$tabid];
	$edit = true;
	if ($actionPermission[1]) {
		$edit = false;
	}
	$controller = new ListViewController($adb, $current_user, $queryGenerator);
	$listview_header_search = $controller->getBasicSearchFieldInfoList();
	if (isset($_REQUEST['isRecycleModule'])) {
		$rbfields = $queryGenerator->getFields();
		if (!in_array('modifiedtime', $rbfields)) {
			$listview_header_search['modifiedtime'] = getTranslatedString('Modified Time', 'RecycleBin');
		}
		if (!in_array('modifiedby', $rbfields)) {
			$listview_header_search['modifiedby'] = getTranslatedString('Last Modified By', 'RecycleBin');
		}
	}
	$listview_header_search['cblvactioncolumn'] = $app_strings['LBL_ACTION'];
	$listview_header_arr = array();
	foreach ($listview_header_search as $fName => $fValue) {
		$fieldType = getUItypeByFieldName($currentModule, $fName);
		$tooltip = ToolTipExists($fName, $tabid);
		if ($fieldType == '15' || $fieldType == '16') {
			$picklistValues = vtlib_getPicklistValues($fName);
			$picklistEditor = array_map(function ($val) use ($currentModule) {
				return array(
					'label' => getTranslatedString($val, $currentModule),
					'value' => $val
				);
			}, $picklistValues);
			$lv_arr = array(
				'fieldname' => $fName,
				'fieldvalue' => $fValue,
				'uitype' => $fieldType,
				'picklist' => $picklistEditor,
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
	return array(
		'headers' => $listview_header_arr,
		'customview' => $customViewarr,
		'result' => true,
	);
}

function getRecordActions($module, $recordId) {
	global $adb, $current_user;
	if ($module == '') {
		return true;
	}
	$queryGenerator = new QueryGenerator($module, $current_user);
	$lvc = new ListViewController($adb, $current_user, $queryGenerator);
	$wfs = new VTWorkflowManager($adb);
	$actions = array();
	if (isPermitted($module, 'EditView', $recordId) == 'yes') {
		$racbr = $wfs->getRACRuleForRecord($module, $recordId);
		if (!$racbr || $racbr->hasListViewPermissionTo('edit')) {
			$edit_link = $lvc->getListViewEditLink($module, $recordId);
			$actions['edit'] = array(
				'edit' => true,
				'link' => $edit_link,
			);
		}
	} else {
		$actions['edit'] = array(
			'edit' => false,
			'link' => '',
		);
	}
	if (isPermitted($module, 'Delete', $recordId) == 'yes') {
		$racbr = $wfs->getRACRuleForRecord($module, $recordId);
		if (!$racbr || $racbr->hasListViewPermissionTo('delete')) {
			$del_link = $lvc->getListViewDeleteLink($module, $recordId);
			$actions['delete'] = array(
				'delete' => true,
				'link' => $del_link,
			);
		}
	} else {
		$actions['delete'] = array(
			'delete' => false,
			'link' => '',
		);
	}
	$focus = new $module();
	$App_LV_Record = GlobalVariable::getVariable('Application_ListView_Record_Change_Indicator', 1, $module);
	if ($App_LV_Record && method_exists($focus, 'isViewed')) {
		if (!$focus->isViewed($recordId)) {
			$actions['view'] = array(
				'view' => true,
			);
		} else {
			$actions['view'] = array(
				'view' => false,
			);
		}
	} else {
		$actions['view'] = array(
			'view' => false,
		);
	}
	if ($module == 'cbCalendar') {
		require_once 'modules/Calendar4You/Calendar4You.php';
		$focus = new Calendar4You();
		$focus->GetDefPermission($current_user);
		if ($focus->CheckPermissions('EDIT', $recordId)) {
			$focus = new $module();
			$focus->retrieve_entity_info($recordId, 'cbCalendar');
			$evstatus = $focus->column_fields['eventstatus'];
			$activitytype = $focus->column_fields['activitytype'];
			if (!($evstatus == 'Deferred' || $evstatus == 'Completed' || $evstatus == 'Held' || $evstatus == '')) {
				if ($activitytype == 'Task') {
					$evt_status = 'Completed';
				} else {
					$evt_status = 'Held';
				}
				$actions['calendar'] = array(
					'status' => $evt_status,
				);
			}
		}
	}
	return $actions;
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
					foreach ($condition as $value) {
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
	$columns = array();
	foreach ($classNames as $name) {
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
