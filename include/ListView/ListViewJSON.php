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

class GridListView {

	public $tabid;
	public $entries = 20;
	public $orderBy = 'DESC';
	public $sortColumn = '';
	public $currentPage = '';
	public $searchUrl = '';
	public $searchtype = 'Basic';

	public function __construct($module) {
		$this->module = $module;
		if ($this->module == 'Utilities') {
			$this->module = vtlib_purify($_REQUEST['formodule']);
		}
		$entityField = getEntityField($this->module);
		$this->entityidfield = $entityField['entityid'];
	}

	public function Show() {
		global $current_user, $adb;
		include_once 'modules/Tooltip/TooltipUtils.php';
		require_once "modules/$this->module/$this->module.php";
		$sql_error = false;
		$lastPage = isset($_REQUEST['lastPage']) ? vtlib_purify($_REQUEST['lastPage']) : '';
		$viewid = isset($_SESSION['lvs'][$this->module]) ? $_SESSION['lvs'][$this->module]['viewname'] : 0;
		$focus = new $this->module();
		$focus->initSortbyField($this->module);
		if ($this->sortColumn != '') {
			$order_by = $this->sortColumn;
		} else {
			$order_by = $focus->getOrderBy();
		}
		$queryGenerator = new QueryGenerator($this->module, $current_user);
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
		if ($this->searchtype == 'Basic' && $this->searchUrl != '') {
			$this->searchUrl = urldecode($this->searchUrl);
			$search = explode('&', $this->searchUrl);
			foreach ($search as $value) {
				if (!empty($value)) {
					$param = explode('=', $value);
					$searchCriteria[$param[0]] = $param[1];
				}
			}
			$searchCriteria['action'] = $this->module.'Ajax';
			$searchCriteria['module'] = $this->module;
			$searchCriteria['search'] = 'true';
			$search_mode = true;
		} elseif (($this->searchtype == 'Advanced' || $this->searchtype == 'advance') && $this->searchUrl != '') {
			$this->searchUrl = urldecode($this->searchUrl);
			$search = explode('&', $this->searchUrl);
			if ($this->searchtype == 'advance') {
				$searchCriteria['advft_criteria'] = $this->searchUrl;
				$searchCriteria['advft_criteria_groups'] = vtlib_purify($_REQUEST['advft_criteria_groups']);
				$searchCriteria['searchtype'] = $this->searchtype;
			} else {
				foreach ($search as $value) {
					if (!empty($value)) {
						$param = explode('=', $value);
						$searchCriteria[$param[0]] = $param[1];
					}
				}
			}
			$searchCriteria['action'] = $this->module.'Ajax';
			$searchCriteria['module'] = $this->module;
			$searchCriteria['query'] = 'true';
			$searchCriteria['search'] = 'true';
			$search_mode = true;
		}
		if ((isset($this->searchUrl) && $this->searchUrl != '')) {
			$queryGenerator->addUserSearchConditions($searchCriteria);
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
			$list_query.=' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.$this->orderBy;
		}
		if (isset($_REQUEST['isRecycleModule'])) {
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias($this->module, true);
			$list_query = preg_replace("/$crmEntityTable.deleted\s*=\s*0/i", $crmEntityTable.'.deleted = 1', $list_query);
		}
		if (!isset($_REQUEST['fromInstance'])) {
			$this->currentPage = $lastPage;
		}
		if (isset($this->entries) && !empty($this->entries)) {
			$limit = ($this->currentPage-1) * $this->entries;
			$list_query .= ' LIMIT '.$limit.','.$this->entries;
		}
		//add action in header
		if ($this->module == 'cbCalendar') {
			require_once 'modules/Calendar4You/Calendar4You.php';
			$focus = new Calendar4You();
			$focus->GetDefPermission($current_user);
		}
		try {
			$result = $adb->pquery($list_query, array());
			$count_result = $adb->query(mkXQuery(stripTailCommandsFromQuery($list_query, false), 'count(*) AS count'));
			$noofrows = $adb->query_result($count_result, 0, 0);
			$rowCount = $adb->num_rows($result);
			$listviewcolumns = $adb->getFieldsArray($result);
		} catch (Exception $e) {
			$sql_error = true;
		}
		$folderid = '';
		$data = array();
		$field_types = $this->ListViewColumns($listviewcolumns);
		if (isset($_REQUEST['folderid']) && $this->module == 'Documents') {
			$folderid = vtlib_purify($_REQUEST['folderid']);
			//totalCount to be fixed
			if ($folderid != '__empty__') {
				$whereClause = $queryGenerator->getWhereClause();
				$data = $this->TreeStructure($field_types, $folderid, 'DocumentFolders', 'parentfolder', $whereClause);
			}
		} else {
			$data = $this->processResults($result, $field_types);
		}
		if ($result && $sql_error != true) {
			if ($noofrows>0) {
				$res = array(
					'data' => array(
						'contents' => $data,
						'pagination' => array(
							'page' => (int)$this->currentPage,
							'totalCount' => (int)$noofrows,
						),
					),
					'entityfield' => $this->entityidfield,
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
					'entityfield' => $this->entityidfield,
					'export_where' => $where,
					'result' => false,
					'message' => getTranslatedString('NoData', $this->module),
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
				'entityfield' => $this->entityidfield,
				'export_where' => $where,
				'result' => false,
				'message' => getTranslatedString('NoData', $this->currentModule),
				'search_mode' => $search_mode,
			);
		}
		$res['query'] = $list_query;
		return array('data'=>$res);
	}

	public function Headers() {
		global $app_strings, $current_user, $adb;
		include_once 'modules/Tooltip/TooltipUtils.php';
		require_once "modules/$this->module/$this->module.php";
		$profileid = getUserProfile($current_user->id);
		$profileid = reset($profileid);
		$isRecycleModule = isset($_REQUEST['isRecycleModule']) ? vtlib_purify($_REQUEST['isRecycleModule']): '';
		$customView = new CustomView($this->module);
		$viewid = coreBOS_Session::get('lvs^'.$this->module.'^viewname');
		if (isset($_REQUEST['viewname']) && !empty($_REQUEST['viewname'])) {
			$viewid = vtlib_purify($_REQUEST['viewname']);
		} elseif (empty($viewid)) {
			$viewid = $customView->getViewId($this->module);
		}
		coreBOS_Session::set('lvs^'.$this->module.'^viewname', $viewid);
		$viewinfo = $customView->getCustomViewByCvid($viewid);
		$statusdetails = $customView->isPermittedChangeStatus($viewinfo['status'], $viewid);
		$cv = array(
			'viewid' => $viewid,
			'viewinfo' => $viewinfo,
			'edit_permit' => $customView->isPermittedCustomView($viewid, 'EditView', $this->module),
			'delete_permit' => $customView->isPermittedCustomView($viewid, 'Delete', $this->module),
			'customview_html' => $customView->getCustomViewCombo($viewid),
			'setpublic' => $statusdetails
		);
		$queryGenerator = new QueryGenerator($this->module, $current_user);
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
		$actionPermission = getTabsActionPermission($profileid)[$this->tabid];
		$edit = true;
		if ($actionPermission[1] || $isRecycleModule == 'true') {
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
		$findRelatedModule = '';
		foreach ($listview_header_search as $fName => $fValue) {
			$fieldType = getUItypeByFieldName($this->module, $fName);
			if ($fieldType == '10') {
				$findRelatedModule = $this->findRelatedModule($fName);
			}
			$tooltip = ToolTipExists($fName, $this->tabid);
			if ($fieldType == '15' || $fieldType == '16') {
				$picklistValues = vtlib_getPicklistValues($fName);
				$picklistEditor = array_map(function ($val) {
					return array(
						'label' => getTranslatedString($val, $this->module),
						'value' => $val
					);
				}, $picklistValues);
				$lv_arr = array(
					'fieldname' => $fName,
					'fieldvalue' => html_entity_decode($fValue),
					'uitype' => $fieldType,
					'picklist' => $picklistEditor,
					'tooltip' => $tooltip,
					'edit' => $edit,
				);
			} elseif ($fieldType == '52' || $fieldType == '53') {
				$users = get_user_array();
				$lv_arr = array(
					'fieldname' => $fName,
					'fieldvalue' => html_entity_decode($fValue),
					'uitype' => $fieldType,
					'picklist' => $users,
					'tooltip' => $tooltip,
					'edit' => $edit,
				);
			} elseif ($fieldType == '10') {
				$lv_arr = array(
					'fieldname' => $fName,
					'fieldvalue' => html_entity_decode($fValue),
					'uitype' => $fieldType,
					'tooltip' => $tooltip,
					'edit' => $edit,
					'relatedModule' => $findRelatedModule
				);
			} else {
				$lv_arr = array(
					'fieldname' => $fName,
					'fieldvalue' => html_entity_decode($fValue),
					'uitype' => $fieldType,
					'tooltip' => $tooltip,
					'edit' => $edit,
				);
			}
			array_push($listview_header_arr, $lv_arr);
		}
		$folders = array();
		if ($this->module == 'Documents') {
			$folders = $this->findDocumentFolders();
		}
		return array(
			'headers' => $listview_header_arr,
			'customview' => $cv,
			'result' => true,
			'folders' => $folders //for Documents module
		);
	}

	public function findRelatedModule($fName) {
		global $adb;
		$rs = $adb->pquery('select fieldid, tabid from vtiger_field where fieldname=? and tabid=?', array($fName, $this->tabid));
		$noofrows = $adb->num_rows($rs);
		if ($noofrows > 0) {
			$modules = array();
			for ($i=0; $i < $noofrows; $i++) {
				$fieldid = $adb->query_result($rs, $i, 'fieldid');
				$tabid = $adb->query_result($rs, $i, 'tabid');
				$rel = $adb->pquery('select * from vtiger_fieldmodulerel where fieldid=? and module=?', array(
					$fieldid, $this->module
				));
				$noofrows = $adb->num_rows($rel);
				if ($noofrows > 0) {
					for ($j=0; $j<$noofrows; $j++) {
						$modules[] = $adb->query_result($rel, $j, 'relmodule');
					}
				}
			}
			return $modules;
		}
		return false;
	}

	public function processResults($result, $field_types, $parentid = 0) {
		global $adb, $current_user;
		$Colorizer = false;
		$ids = array();
		if (vtlib_isModuleActive('Colorizer')) {
			$Colorizer = true;
		}
		if (isset($_REQUEST['searchFullDocuments']) && !empty($_REQUEST['searchFullDocuments'])) {
			$ids = $this->SearchFullDocuments($_REQUEST['searchFullDocuments']);
		}
		$data = array();
		$reference_field = getEntityFieldNames($this->module);
		$columnnameVal = $this->getFieldNameByColumn($reference_field['fieldname']);
		$rowCount = $adb->num_rows($result);
		for ($i=0; $i < $rowCount; $i++) {
			$rows = array();
			$colorizer_row = array();
			$linkRow = array();
			$AutocompleteFields = array();
			foreach ($field_types as $val) {
				$columnName = $val['columnname'];
				$fieldName = $val['fieldname'];
				$fieldType = $val['fieldtype'];
				$fieldValue = $adb->query_result($result, $i, $columnName);
				$recordID = $adb->query_result($result, $i, $this->entityidfield);
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
						$rows[$fieldName] = getTranslatedString('yes', $this->module);
					} elseif ($fieldValue == 0) {
						$rows[$fieldName] = getTranslatedString('no', $this->module);
					} else {
						$rows[$fieldName] = '--';
					}
				} elseif ($fieldType == '71' || $fieldType == '72' || $fieldType == '7' || $fieldType == '9') {
					$currencyField = new CurrencyField($fieldValue);
					if ($fieldType == '72' || $fieldType == '71') {
						if ($fieldName == 'unit_price') {
							$currencyId = getProductBaseCurrency($recordID, $this->module);
							$cursym_convrate = getCurrencySymbolandCRate($currencyId);
							$currencySymbol = $cursym_convrate['symbol'];
						} else {
							$currencyInfo = getInventoryCurrencyInfo($this->module, $recordID);
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
						$rows[$fieldName] = getTranslatedString('LBL_INTERNAL', $this->module);
					} elseif ($fieldValue == 'E') {
						$rows[$fieldName] = getTranslatedString('LBL_EXTERNAL', $this->module);
					} else {
						$rows[$fieldName] = '--';
					}
				} elseif ($fieldName == 'modifiedby') {
						$rows[$fieldName] = getUserFullName($fieldValue);
				} elseif ($fieldType == '1024') {
					if (!empty($fieldValue)) {
						$fieldValue = implode(', ', array_map('getRoleName', explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $fieldValue)));
					}
					$rows[$fieldName] = textlength_check($fieldValue);
				} elseif ($fieldType == '1025') {
					$field1025Value = array();
					$fieldValue = explode(' |##| ', $fieldValue);
					if (!empty($fieldValue)) {
						$parent_module = getSalesEntityType($fieldValue[0]);
						foreach ($fieldValue as $id) {
							$displayValueArray = getEntityName($parent_module, $id);
							if (!empty($displayValueArray)) {
								$field1025Value[] = '<a href="index.php?module='.$parent_module.'&action=DetailView&record='.$id.'">'.$displayValueArray[$id].'</a>';
							}
							$AutocompleteFields[] = array(
								$parent_module, $fieldName, $displayValueArray[$id], $id
							);
						}
					}
					$rows[$fieldName] = implode(',', $field1025Value);
				} else {
					if ($fieldName) {
						$rows[$fieldName] = textlength_check($fieldValue);
					}
				}
				$rows['uitype_'.$fieldName] = $fieldType;
				if ($this->module == 'Documents') {
					$fileattach = 'select attachmentsid from vtiger_seattachmentsrel where crmid = ?';
					$res = $adb->pquery($fileattach, array($recordID));
					$fileid = $adb->query_result($res, 0, 'attachmentsid');
					$rows['fileid'] = $fileid;
				}
				$group_array = get_group_array();
				$assigned_user_id = isset($smownerid) ? getUserFullName($smownerid) : false;
				if (!$assigned_user_id) {
					$assigned_user_id = $group_array[$smownerid];
				}
				$rows['assigned_user_id'] = $assigned_user_id;
				$rows['recordid'] = $recordID;
				$rows['reference_field'] = array(
					'columnname' => $reference_field['fieldname'],
					'fieldname' => $columnnameVal
				);
				$rows['relatedRows'] = $linkRow;
				$rows['autocompleteFields'] = $AutocompleteFields;
				if ($this->module == 'Documents') {
					$rows['parent'] = $parentid;
				}
			}
			if ($Colorizer) {
				$className = $this->enableColorizer($colorizer_row);
				$rows['_attributes'] = $className;
			}
			if (!$ids && isset($_REQUEST['searchFullDocuments']) && !empty($_REQUEST['searchFullDocuments'])) {
				continue;
			} elseif (isset($_REQUEST['searchFullDocuments']) && !empty($_REQUEST['searchFullDocuments']) && is_array($ids) && !in_array($rows['recordid'], $ids)) {
				continue;
			}
			array_push($data, $rows);
		}
		return $data;
	}

	public function getFieldNameByColumn($columnname, $return = '') {
		global $adb;
		if (is_array($columnname)) {
			$columnname = $columnname[0];
		}
		$rs = $adb->pquery('select * from vtiger_field where columnname=? and tabid=?', array(
			$columnname, $this->tabid
		));
		if ($adb->num_rows($rs) == 1) {
			if ($return == 'array') {
				return $rs->FetchRow();
			}
			return $adb->query_result($rs, 0, 'fieldname');
		}
		return false;
	}

	public function findChilds($records_list, $parentId, $field_types, $whereClause = '') {
		$tree = array();
		foreach ($records_list as $list) {
			if ($list['parent'] == $parentId) {
				$children = $this->findChilds($records_list, $list['id'], $field_types, $whereClause);
				$data = $this->getDocuments($list['id'], $field_types, $whereClause);
				if (!$children && empty($data) && isset($_REQUEST['searchtype']) && !empty($_REQUEST['searchtype'])) {
					continue;
				}
				if ($children) {
					$tmpChildren = $children;
					if (!empty($data)) {
						$children = array();
						$children = array_merge($data[0], $tmpChildren);
					}
					$list['_children'] = $children;
					unset($tmpChildren);
				} else {
					if (!empty($data)) {
						$list['_children'] = $data[0];
					}
				}
				$tree[] = $list;
			}
		}
		return $tree;
	}

	public function TreeStructure($field_types, $id, $currentModule, $referenceField, $whereClause = '') {
		global $adb;
		$records_list = array();
		$encountered_records = array($id);
		$treeAttr = array(
			$currentModule, $field_types[0]['fieldname']
		);
		$focus = new $currentModule();
		$records_list = $focus->getParentRecords($id, $records_list, $encountered_records, $referenceField, $currentModule, $treeAttr);
		$parent = $records_list[0]['id'];
		$records_list = array();
		$records_list = $focus->getChildRecords($id, $records_list, 0, $referenceField, $currentModule, $treeAttr);
		$parents = $this->getDocuments($parent, $field_types, $whereClause);
		$note_no = getEntityName($currentModule, $parent);
		$top_parents = array();
		if (isset($parents[0])) {
			$top_parents = $parents[0];
		}
		$parents = $this->findChilds($records_list, $parent, $field_types, $whereClause);
		return array_merge($top_parents, $parents);
	}

	public function getDocuments($id, $field_types, $whereClause = '') {
		global $adb;
		if (!empty($whereClause)) {
			$whereClause = ' and '.str_replace('WHERE', '', $whereClause);
		}
		$query = 'select distinct vtiger_notes.*, vtiger_crmentity.*, vtiger_notescf.* from vtiger_notes
			inner join vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_notes.notesid
			inner join vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid=vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid=vtiger_crmentity.crmid)
			inner join vtiger_notescf ON vtiger_notescf.notesid = vtiger_notes.notesid
			left join vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			left join vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?) '.$whereClause;
		$rs = $adb->pquery($query, array($id, $id));
		$numOfRows = $adb->num_rows($rs);
		$data = array();
		if ($numOfRows > 0) {
			$listviewcolumns = $adb->getFieldsArray($rs);
			$data[] = $this->processResults($rs, $field_types, $id);
		}
		return $data;
	}

	public function ListViewColumns($listviewcolumns) {
		global $adb;
		$field_types = array();
		foreach ($listviewcolumns as $fName) {
			$fieldnameSql = $adb->pquery('SELECT fieldname, uitype FROM vtiger_field WHERE columnname=? AND tabid=?', array($fName, $this->tabid));
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
		return $field_types;
	}

	public function Actions($recordId) {
		global $adb, $current_user;
		if ($this->module == '' || $this->module == 'RecycleBin') {
			return true;
		}
		$queryGenerator = new QueryGenerator($this->module, $current_user);
		$lvc = new ListViewController($adb, $current_user, $queryGenerator);
		$wfs = new VTWorkflowManager($adb);
		$actions = array();
		if (isPermitted($this->module, 'EditView', $recordId) == 'yes') {
			$racbr = $wfs->getRACRuleForRecord($this->module, $recordId);
			if (!$racbr || $racbr->hasListViewPermissionTo('edit')) {
				$edit_link = $lvc->getListViewEditLink($this->module, $recordId);
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
		if (isPermitted($this->module, 'Delete', $recordId) == 'yes') {
			$racbr = $wfs->getRACRuleForRecord($this->module, $recordId);
			if (!$racbr || $racbr->hasListViewPermissionTo('delete')) {
				$del_link = $lvc->getListViewDeleteLink($this->module, $recordId);
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
		$focus = new $this->module();
		$App_LV_Record = GlobalVariable::getVariable('Application_ListView_Record_Change_Indicator', 1, $this->module);
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
		if ($this->module == 'cbCalendar') {
			require_once 'modules/Calendar4You/Calendar4You.php';
			$focus = new Calendar4You();
			$focus->GetDefPermission($current_user);
			if ($focus->CheckPermissions('EDIT', $recordId)) {
				$focus = new cbCalendar();
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

	public function Update() {
		global $current_user;
		$value = vtlib_purify($_REQUEST['value']);
		$columnName = vtlib_purify($_REQUEST['columnName']);
		$recordid = vtlib_purify($_REQUEST['recordid']);
		$moduleHandler = vtws_getModuleHandlerFromName($this->module, $current_user);
		$handlerMeta = $moduleHandler->getMeta();
		$tablename = getTableNameForField($this->module, $columnName);
		$focus = new $this->module();
		$focus->id = $recordid;
		$focus->mode = 'edit';
		$focus->retrieve_entity_info($recordid, $this->module);
		$focus->column_fields[$columnName] = $value;
		$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $handlerMeta);
		$focus->save($this->module);
	}

	public function enableColorizer($row) {
		require_once 'modules/Colorizer/functions/processConditions.php';
		global $adb;
		$classNames = array();
		foreach ($row as $fieldName => $fieldValue) {
			if (!is_numeric($fieldName)) {
				if ($fieldName == 'smownerid') {
					$fieldName = 'assigned_user_id';
				}
				$sql = $adb->pquery('SELECT * FROM vtiger_colorizer WHERE field=? AND tabid=?', array(
					$fieldName,
					$this->tabid
				));
				$numOfRows = $adb->num_rows($sql);
				if ($numOfRows > 0) {
					for ($i=0; $i < $numOfRows; $i++) {
						$condition = html_entity_decode($adb->query_result($sql, $i, 'condition'));
						$additional = html_entity_decode($adb->query_result($sql, $i, 'additional'));
						$className = $adb->query_result($sql, $i, 'classname');
						$additional = json_decode($additional, true);
						$condition = json_decode($condition, true);
						if (!empty($condition)) {
							$conditionRes = array();
							foreach ($condition as $value) {
								$field = $value['field'];
								$not = $value['not'] == 1 ? true : false;
								$condition = $value['condition'];
								$parameter = isset($value['parameter']) ? $value['parameter'][0] : '';
								$fieldType = getUItypeByFieldName($this->module, $field);
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

	public function findDocumentFolders() {
		require_once 'modules/DocumentFolders/DocumentFolders.php';
		global $current_user, $adb;
		$focus = new DocumentFolders();
		$referenceField = 'parentfolder';
		$queryGenerator = new QueryGenerator('DocumentFolders', $current_user);
		$queryGenerator->setFields(array('id','foldername'));
		if (!isset($_REQUEST['folders'])) {
			$queryGenerator->addCondition('parentfolder', '', 'e');
		}
		$list_query = $queryGenerator->getQuery();
		$result = $adb->pquery($list_query.' order by vtiger_documentfolders.sequence', array());
		$foldercount = $adb->num_rows($result);
		$folders = array();
		$records_list = array();
		if ($foldercount > 0) {
			for ($i=0; $i<$foldercount; $i++) {
				$id = $adb->query_result($result, $i, 'documentfoldersid');
				$foldername = $adb->query_result($result, $i, 'foldername');
				$folders[] = array($id, $foldername);
			}
		}
		if (empty($folders)) {
			$folders[] = 1;
		}
		return $folders;
	}

	public function SearchFullDocuments($text) {
		global $adb;
		$this->DocumentSearch = true;
		$result = $adb->pquery('select * from vtiger_documentsearchinfo where text LIKE ?', array(
			'%'.$text.'%'
		));
		if ($adb->num_rows($result) > 0) {
			$ids = array();
			while ($row = $result->FetchRow()) {
				$ids[] = $row['documentid'];
			}
			return $ids;
		}
		return false;
	}

	public function gridTableBasedEntries($q, $columns, $table_name, $column_format = []) {
		global $adb;
		$result = $adb->pquery($q, array());
		$count_result = $adb->query(mkXQuery(stripTailCommandsFromQuery($q, false), 'count(*) AS count'));
		$noofrows = $adb->query_result($count_result, 0, 0);
		$data  = array();
		for ($i=0; $i < $adb->num_rows($result); $i++) {
			$currentRow = array();
			foreach ($columns as $label => $col) {
				$currentRow[$label] = $adb->query_result($result, $i, $col[$table_name]);
				if (!empty($column_format[$col[$table_name]])) {
					$currentRow[$label] = $column_format[$col[$table_name]]($currentRow[$label],$currentRow);
				}
			}
			array_push($data, $currentRow);
		}
		return array(
			'data' => array(
				'contents' => $data,
				'pagination' => array(
					'page' => (int)$this->currentPage,
					'totalCount' => (int)$noofrows,
				),
			),
			'result' => true,
		);
	}
}
?>