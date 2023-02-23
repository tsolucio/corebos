<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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
require_once 'include/ListView/GridUtils.php';
require_once 'modules/Vtiger/Wizard/WizardCustomFunctions.php';

class WizardView {

	private $step;
	private $module;
	private $groupby;
	private $actions = '';
	private $mode = 'ListView';
	private $mapid = 0;
	private $goback = 1;
	private $validate = true;
	private $required_action = '';
	private $custom_function = '';
	private $confirmstep = '';
	private $confirmmsg = '';
	private $reset_wizard = true;

	public $conditions = '';

	public function __construct($params) {
		foreach ($params as $key) {
			$this->params[$key['name']] = $key['value'];
		}
		if (isset($this->params['module'])) {
			$this->module = $this->params['module'];
		}
		if (isset($this->params['step'])) {
			$this->step = $this->params['step'];
		}
		//mapid for columns to show in grid
		if (isset($this->params['mapid'])) {
			$this->mapid = $this->params['mapid'];
		}
		//group by selected rows by "this" field
		if (isset($this->params['groupby'])) {
			$this->groupby = $this->params['groupby'];
		}
		//Show different views in wizard: ListView, MassCreate
		if (isset($this->params['mode'])) {
			$this->mode = $this->params['mode'];
		}
		//Action type that we apply in "Next" or "Finish"
		if (isset($this->params['actions'])) {
			$this->actions = $this->params['actions'];
		}
		//validate before we go in the next step 0 | 0
		if (isset($this->params['validate'])) {
			$this->validate = $this->params['validate'];
		}
		//do not allow to go back 0 | 1
		if (isset($this->params['back'])) {
			$this->goback = $this->params['back'];
		}
		//duplicate
		if (isset($this->params['required_action'])) {
			$this->required_action = $this->params['required_action'];
		}
		//call a function custom from backend
		if (isset($this->params['custom_function'])) {
			$this->custom_function = $this->params['custom_function'];
		}
		//reset wizard on finish action or start as it is
		if (isset($this->params['reset_wizard'])) {
			$this->reset_wizard = $this->params['reset_wizard'];
		}
		//show or hide save button for each step
		if (isset($this->params['save_action'])) {
			$this->save_action = $this->params['save_action'];
		}
		if (isset($this->params['filter_context'])) {
			$this->filtercontext = $this->params['filter_context'];
		}
		//start: ask user to confirm the step before proceed
		if (isset($this->params['confirm_step'])) {
			$this->confirmstep = $this->params['confirm_step'];
		}
		if (isset($this->params['confirm_message'])) {
			$this->confirmmsg = $this->params['confirm_message'];
		}
		//end confirm
	}

	public function Init() {
		require_once 'modules/'.$this->module.'/'.$this->module.'.php';
		global $smarty;
		$smarty->assign('formodule', $this->module);
		$smarty->assign('GroupBy', $this->groupby);
		$WizardSuboperation = $smarty->getTemplateVars('WizardSuboperation');
		//suboperation support into steps
		if (!empty($WizardSuboperation)) {
			if ($this->mapid != 0) {
				$columns = self::RenderListViewColumns();
				$smarty->assign('Columns', $columns);
			}
			return $smarty->fetch('Smarty/templates/Components/Wizard/'.$WizardSuboperation.'.tpl');
		}
		//GLOBAL: Form Template
		if ($smarty->getTemplateVars('wizardOperation') == 'FORMTEMPLATE') {
			$Rows = array();
			if ($this->mapid != 0) {
				$cbMap = cbMap::getMapByID($this->mapid);
				$Rows = $cbMap->WizardForms();
			}
			$smarty->assign('Rows', isset($Rows['rows']) ? $Rows['rows'] : array());
			$smarty->assign('ModuleName', $Rows['module']);
			return $smarty->fetch('Smarty/templates/Components/Wizard/WizardFormTemplate.tpl');
		}
		//GLOBAL: ListView, MassCreate
		if ($this->mapid != 0) {
			$columns = self::RenderListViewColumns();
			$smarty->assign('Columns', $columns);
			$smarty->assign('relatedmodules', $this->RelatedFields());
			$smarty->assign('entitynames', getEntityFieldNames($this->module));
			$smarty->assign('WizardMode', $this->mode);
			//ListView, Create_{ModuleName}, MassCreate_{ModuleName}
			$smarty->assign('WizardActions', $this->actions);
			$smarty->assign('WizardValidate', $this->validate);
			$smarty->assign('WizardGoBack', $this->goback);
			$smarty->assign('WizardRequiredAction', $this->required_action);
			$smarty->assign('WizardCustomFunction', $this->custom_function);
			$smarty->assign('WizardModuleEditor', $this->module);
			$smarty->assign('WizardConfirmStep', array(
				'confirm' => boolval($this->confirmstep),
				'message' => $this->confirmmsg
			));
			$smarty->assign('WizardContext', isset($this->filtercontext) ? $this->filtercontext : '');
			$WizardSaveAction = false;
			if (!empty($this->save_action)) {
				$WizardSaveAction = boolval($this->save_action);
			}
			$smarty->assign('WizardSaveAction', $WizardSaveAction);
			$smarty->assign('ResetWizard', $this->reset_wizard);
			$condition = '';
			if (isset($this->conditions['condition'])) {
				$condition = $this->conditions['condition'];
			}
			$query = '';
			if (isset($this->conditions['query'])) {
				$query = $this->conditions['query'];
			}
			$smarty->assign('WizardFilterBy', $condition);
			$smarty->assign('WizardConditionQuery', $query);
			return $smarty->fetch('Smarty/templates/Components/Wizard/WizardListView.tpl');
		}
	}

	private function RenderListViewColumns() {
		$cbMap = cbMap::getMapByID($this->mapid);
		$fieldlist = $cbMap->MassUpsertGridView();
		$fields = $fieldlist->getColumns();
		foreach ($fields as $label => $name) {
			$fieldname = array_values($name)[0];
			if ($fieldname == 'smownerid') {
				$fieldname = 'assigned_user_id';
			}
			$columns[] = array(
				'header' => $label,
				'name' => $fieldname,
				'uitype' => getUItype($this->module, $fieldname)
			);
		}
		return $columns;
	}

	public function RelatedFields() {
		global $adb;
		$rs = $adb->pquery('select vtiger_field.fieldid, fieldname, module, columnname, relmodule from vtiger_fieldmodulerel inner join vtiger_field on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid where module=?', array(
			$this->module
		));
		$relfields = array();
		while ($row = $rs->FetchRow()) {
			$relfields[] = array(
				'module' => $row['module'],
				'relmodule' => $row['relmodule'],
				'fieldid' => $row['fieldid'],
				'fieldname' => $row['fieldname'],
				'columnname' => $row['columnname'],
			);
		}
		return $relfields;
	}
}

class WizardActions extends WizardCustomFunctions {

	public $module;

	public function __construct($module = '') {
		$this->module = $module;
	}

	public function Grid() {
		require_once 'modules/'.$this->module.'/'.$this->module.'.php';
		global $current_user, $adb;
		$page = vtlib_purify($_REQUEST['page']);
		$perPage = vtlib_purify($_REQUEST['perPage']);
		$mode = isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';
		$showdata = isset($_REQUEST['showdata']) ? vtlib_purify($_REQUEST['showdata']) : false;
		$step = isset($_REQUEST['step']) ? intval($_REQUEST['step']) : '';
		$filtergrid = isset($_REQUEST['filtergrid']) ? vtlib_purify($_REQUEST['filtergrid']) : false;
		$conditionquery = isset($_REQUEST['conditionquery']) ? vtlib_purify($_REQUEST['conditionquery']) : false;
		$parentid = isset($_REQUEST['parentid']) ? vtlib_purify($_REQUEST['parentid']) : 0;
		$currentid = isset($_REQUEST['currentid']) ? vtlib_purify($_REQUEST['currentid']) : 0;
		$required_action = isset($_REQUEST['required_action']) ? intval($_REQUEST['required_action']) : '';
		$context = isset($_REQUEST['context']) ? $_REQUEST['context'] : '';
		$filterFromContext = isset($_REQUEST['filterFromContext']) ? json_decode($_REQUEST['filterFromContext'], true) : '';
		if ($step > 0 && !$showdata && !$filtergrid) {
			return json_encode(array(
				'data' => array(
					'contents' =>  array(),
					'pagination' => array(
						'page' => 1,
						'totalCount' => 0,
					),
				),
				'result' => false
			));
		}
		if (isset($_REQUEST['query']) && !empty($_REQUEST['query']) && !$filtergrid) {
			$sql = vtlib_purify($_REQUEST['query']);
			$ctxConds = '';
			if (!empty($filterFromContext)) {
				foreach ($filterFromContext as $condX) {
					$fldName = $condX['match'];
					if (!isset($context[$condX['find']])) {
						continue;
					}
					$value = $context[$condX['find']];
					if (isset($condX['function'])) {
						$function = $condX['function'];
						if (strpos($condX['function'], ':') !== false) {
							list($function, $delimiter, $elem) = explode(':', $condX['function']);
						}
						switch ($function) {
							case 'explode':
								if (strpos($condX['function'], ':') !== false) {
									$value = explode($delimiter, $context[$condX['find']]);
									$value = $value[$elem];
								}
								break;
							default:
								break;
						}
					}
					$op = ' = ';
					$val = $value;
					if (isset($condX['operator'])) {
						switch ($condX['operator']) {
							case 'c':
								$op = ' LIKE ';
								$val = '%'.$value.'%';
								break;
							default:
								break;
						}
					}
					$ctxConds .= $adb->convert2Sql(' and '.$condX['match'].' '.$op.' ? ', array($val));
				}
			}
			$sql .= $ctxConds;
		} else {
			$forids = isset($_REQUEST['forids']) ? json_decode($_REQUEST['forids'], true) : array();
			$forfield = isset($_REQUEST['forfield']) ? $_REQUEST['forfield'] : array();
			$filterrows = isset($_REQUEST['filterrows']) ? $_REQUEST['filterrows'] : false;
			$qg = new QueryGenerator($this->module, $current_user);
			$qg->setFields(array('*'));
			$newRecords = coreBOS_Session::get('DuplicatedRecords');
			if ($filterrows) {
				//filter records for the next step based on some givend ids
				if (!empty($forids)) {
					$qg->startGroup();
					foreach ($forids as $id) {
						$qg->addCondition('id', $id, 'e', 'or');
					}
					$qg->endGroup();
				}
				if (!empty($newRecords)) {
					$step = vtlib_purify($_REQUEST['step']);
					$qg->startGroup();
					foreach ($newRecords[$step-1] as $id) {
						$qg->addCondition('id', $id, 'e', 'or');
					}
					$qg->endGroup();
				}
			} elseif ($required_action == 'duplicate' && $this->module == 'Products' && $mode == 'SELECTPRODUCT') {
				//specific use case
				if (!empty($newRecords)) {
					$qg->startGroup();
					foreach ($newRecords[$step-1] as $id) {
						$qg->addCondition('id', $id, 'e', 'or');
					}
					$qg->endGroup();
					$page = 1;
				}
			} elseif ($filtergrid) {
				$forColumn = vtlib_purify($_REQUEST['forColumn']);
				$value = vtlib_purify($_REQUEST['value']);
				$operator = vtlib_purify($_REQUEST['operator']);
				$qg->addCondition($forColumn, $value, $operator);
				$q = isset($_REQUEST['query']) ? $_REQUEST['query'] : '';
				if (!empty($q)) {
					$wc = explode('WHERE', $q);
				}
			} else {
				if (!empty($forids)) {
					if (!is_array($forfield) && strpos($forfield, '.') !== false) {
						//get record relations between 3 modules
						list($relmodule, $module, $fieldname) = explode('.', $forfield);
						$forids = $forids[0];
						$focus = CRMEntity::getInstance($this->module);
						$focus->retrieve_entity_info($forids, $this->module);
						$focus->id = $forids;
						if (isset($focus->column_fields[$fieldname]) && !empty($focus->column_fields[$fieldname])) {
							$relfield = getFieldNameByFieldId(getRelatedFieldId($module, $relmodule));
							$qg = new QueryGenerator($relmodule, $current_user);
							$qg->setFields(array('*'));
							$qg->addReferenceModuleFieldCondition($module, $relfield, 'id', $focus->column_fields[$fieldname], 'e', 'or');
							$this->module = $relmodule;
						}
					} else {
						//filter records for the next step based on some givend ids
						$qg->startGroup();
						foreach ($forids as $id) {
							$qg->addReferenceModuleFieldCondition($forfield['relmodule'], $forfield['fieldname'], 'id', $id, 'e', 'or');
						}
						$qg->endGroup();
					}
				}
			}
			$sql = $qg->getQuery();
			if (!empty($wc)) {
				$sql .= ' and '.end($wc);
			}
			if ($parentid > 0 && !empty($conditionquery)) {
				$sql = $adb->convert2Sql($sql.' '.$conditionquery, array($parentid));
			}
			if ($currentid > 0 && !empty($conditionquery)) {
				$sql = $adb->convert2Sql($sql.' '.$conditionquery, array($currentid));
			}
		}
		$limit = ($page-1) * $perPage;
		//count all records
		$countsql = mkCountQuery($sql);
		$countsql = htmlspecialchars_decode($countsql);
		$rs = $adb->query($countsql);
		$count = $rs->fields['count'];
		$sql .= ' LIMIT '.$limit.','.$perPage;
		$sql = htmlspecialchars_decode($sql);
		$rs = $adb->query($sql);
		$data = array();
		$columns = array();
		$tabid = getTabid($this->module);
		$focus = new $this->module;
		$table_index = $focus->table_index;
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($this->module);
		while ($row = $rs->FetchRow()) {
			$data[] = self::processRows($row, $cachedModuleFields, $this->module, $table_index);
		}
		return json_encode(
			array(
				'data' => array(
					'contents' => $data,
					'pagination' => array(
						'page' => (int)$page,
						'totalCount' => (int)$count,
					),
				),
				'result' => true,
			)
		);
	}

	private function processRows($row, $cachedModuleFields, $module, $table_index) {
		$crow = array();
		$fieldinfo = array();
		foreach ($row as $field => $value) {
			if (is_numeric($field)) {
				continue;
			}
			if ($field == 'smownerid') {
				$field = 'assigned_user_id';
			}
			$fieldinfo[$field] = self::FieldInfo($cachedModuleFields, $field);
			if (!isset($fieldinfo[$field])) {
				continue;
			}
			$gridvalue = getDataGridValue($module, $row[$table_index], $fieldinfo[$field], $value, 'Wizard');
			$crow[$field] = $gridvalue[0];
			$crow['id'] = $row[$table_index];
			$crow[$fieldinfo[$field]['name'].'_raw'] = $value;
			$crow['__modulename'] = $module;
		}
		return $crow;
	}

	private function FieldInfo($cachedModuleFields, $field) {
		$fieldinfo = array();
		foreach ($cachedModuleFields as $key) {
			if ($key['fieldname'] == $field) {
				$fieldinfo = array(
					'fieldtype' => 'corebos',
					'fieldinfo' => [
						'name' => $field,
						'uitype' => $key['uitype'],
					],
					'name' => $field,
					'uitype' => $key['uitype'],
					'fieldid' => $key['fieldid']
				);
			}
		}
		return $fieldinfo;
	}

	public function HandleRequest() {
		$subaction = isset($_REQUEST['subaction']) ? vtlib_purify($_REQUEST['subaction']) : '';
		$response = false;
		if (!empty($subaction)) {
			$response = $this->$subaction();
		}
		return $response;
	}

	public function Mapping() {
		global $currentModule;
		$data = json_decode($_REQUEST['data'], true);
		$mapping = array();
		$cbmap = cbMap::getMapByName('Wizard_'.$data[0]['module'].'_Mapping');
		if (!$cbmap) {
			return array(
				'data' => array(
					'contents' => array(),
					'pagination' => array(
						'page' => 1,
						'totalCount' => 0,
					),
				),
				'result' => true,
			);
		}
		$origin = CRMEntity::getInstance($data[0]['module']);
		$origin->retrieve_entity_info($data[0]['id'][0], $data[0]['module']);
		$target = CRMEntity::getInstance($data[1]['module']);
		foreach ($data[1]['id'] as $id) {
			$target->retrieve_entity_info($id, $data[1]['module']);
			$mapping[] = $cbmap->Mapping($target->column_fields, $origin->column_fields);
		}
		return array(
			'data' => array(
				'contents' => $this->FormatValues($mapping, $data[0]['module']),
				'pagination' => array(
					'page' => 1,
					'totalCount' => count($data[1]['id']),
				),
			),
			'result' => true,
		);
	}

	private function FormatValues($data, $module) {
		$arr = array();
		$formodule = isset($_REQUEST['formodule']) ? vtlib_purify($_REQUEST['formodule']) : '';
		if (!empty($formodule)) {
			$lv = new GridListView($formodule);
			$lv->tabid = getTabid($formodule);
		}
		foreach ($data as $values) {
			$tmparr = $values;
			foreach ($values as $fld => $val) {
				if ($val == 0) {
					continue;
				}
				$uitype = getUItypeByFieldName($module, $fld);
				if ($uitype != Field_Metadata::UITYPE_RECORD_RELATION) {
					//check for uitype 10 in related modules
					if (isset($lv)) {
						$relmod = $lv->findRelatedModule($fld);
						if (empty($relmod)) {
							continue;
						}
					} else {
						continue;
					}
				}
				if (!isset($relmod[0])) {
					$setype = getSalesEntityType($val);
				} else {
					$setype = $relmod[0];
				}
				$displayValue = getEntityName($setype, $val);
				$tmparr[$fld.'_raw'] = $tmparr[$fld];
				$tmparr[$fld] = $displayValue[$val];
			}
			$arr[] = $tmparr;
		}
		return $arr;
	}

	public function MassCreate($target = array()) {
		require_once 'include/Webservices/MassCreate.php';
		global $current_user;
		$subaction = isset($_REQUEST['subaction']) ? vtlib_purify($_REQUEST['subaction']) : '';
		if (!empty($subaction)) {
			$target = $this->$subaction();
		}
		if ($target == '__MassCreateSuccess__') {
			return true;
		}
		if (!empty($target)) {
			$response = MassCreate($target, $current_user);
			if (isset($response['wssuccess']) && !$response['wssuccess']) {
				return false;
			}
			$res = array();
			foreach ($response['success_creates'] as $key) {
				$id = explode('x', $key['id'])[1];
				if (isset($_REQUEST['step'])) {
					$step = vtlib_purify($_REQUEST['step']);
					coreBOS_Session::set('DuplicatedRecords^'.$step.'^'.$id, $id);
				}
				$res[] = $id;
			}
			return $res;
		}
		return 'no_create';
	}

	public function DeleteSession() {
		return coreBOS_Session::delete('DuplicatedRecords');
	}

	public function GetSession() {
		return coreBOS_Session::get('DuplicatedRecords');
	}

	public function DeleteRecords() {
		$data = json_decode($_REQUEST['data'], true);
		$id = $data['recordid'];
		$module = $data['modulename'];
		$focus = CRMEntity::getInstance($module);
		$focus->retrieve_entity_info($id, $module);
		list($delerror, $errormessage) = $focus->preDeleteCheck();
		if (!$delerror) {
			$focus->trash($module, $id);
			return true;
		}
		return false;
	}

	public function CreateRecords() {
		require_once 'include/Webservices/Create.php';
		global $current_user, $adb;
		$UsersTabid = vtws_getEntityId('Users');
		$data = json_decode($_REQUEST['data'], true);
		if (count($data) == 2) {
			$relmodule = $data[0]['relmodule'];
			$relatedRows = $data[0]['relatedRows'];
			$createmodule = $data[1]['createmodule'];
			$relfield = getFieldNameByFieldId(getRelatedFieldId($relmodule, $createmodule));
			if (!empty($relfield)) {
				$target = array();
				foreach ($data[1]['data'] as $row) {
					$row[$relfield] = vtws_getEntityId($relmodule).'x'.$data[0]['id'][0];
					$row['assigned_user_id'] = $UsersTabid.'x'.$current_user->id;
					if (!empty($relatedRows)) {
						foreach ($relatedRows as $relid) {
							$relModule = getSalesEntityType($relid);
							$fieldrs = $adb->pquery('select fieldid from vtiger_fieldmodulerel where module=? and relmodule=?', array(
								$createmodule, $relModule
							));
							if ($adb->num_rows($fieldrs) == 0) {
								continue;
							}
							$fieldnamers = $adb->pquery('select fieldname from vtiger_field where fieldid=?', array(
								$adb->query_result($fieldrs, 0, 'fieldid')
							));
							$fieldname = $adb->query_result($fieldnamers, 0, 'fieldname');
							$row[$fieldname] = $relid;
						}
					}
					vtws_create($createmodule, $row, $current_user);
				}
				return '__MassCreateSuccess__';
			}
		}
		return false;
	}

	public function Duplicate() {
		global $current_user;
		$data = json_decode($_REQUEST['data'], true);
		if (!empty($data)) {
			$recordid = $data['recordid'][0];
			$modulename = $data['modulename'];
			try {
				$entityModuleHandler = vtws_getModuleHandlerFromName($modulename, $current_user);
				$handlerMeta = $entityModuleHandler->getMeta();
				$oldRecord = CRMEntity::getInstance($modulename);
				$oldRecord->retrieve_entity_info($recordid, $modulename);
				$newRecord = CRMEntity::getInstance($modulename);
				$newRecord->mode = '';
				foreach ($oldRecord->column_fields as $key => $value) {
					$newRecord->column_fields[$key] = $value;
				}
				if (isset($newRecord->column_fields['_generated'])) {
					$newRecord->column_fields['_generated'] = 1;
				}
				$newRecord->column_fields = DataTransform::sanitizeRetrieveEntityInfo($newRecord->column_fields, $handlerMeta);
				$newRecord->saveentity($modulename);
				$step = $data['step'] - 1;
				coreBOS_Session::set('DuplicatedRecords^'.$step.'^'.$newRecord->id, $newRecord->id);
				$newRecord->column_fields['id'] = $newRecord->id;
				return $newRecord->column_fields;
			} catch (Throwable $e) {
				return 0;
			}
		}
	}

	public function CreateForm() {
		global $current_user;
		require_once 'modules/Vtiger/ExecuteFunctionsfromphp.php';
		$data = json_decode($_REQUEST['data'], true);
		$row = $data['data'];
		$modulename = $data['modulename'];
		$recordid = $data['recordid'];
		$row = json_decode($row, true);
		$focus = CRMEntity::getInstance($modulename);
		if ($recordid > 0) {
			$focus->id = $recordid;
			$focus->mode = 'edit';
		} else {
			$focus->mode = '';
		}
		foreach ($row as $k) {
			$focus->column_fields[$k['key']] = $k['value'];
		}
		$vals = executefunctionsvalidate('ValidationLoad', $modulename, json_encode($focus->column_fields));
		if ($vals != '%%%OK%%%') {
			return false;
		}
		$handler = vtws_getModuleHandlerFromName($modulename, $current_user);
		$meta = $handler->getMeta();
		$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);
		$focus->saveentity($modulename);
		return $focus->id;
	}

	public function GetEvents() {
		global $adb;
		$data = json_decode($_REQUEST['data'], true);
		$sql = $adb->pquery('select * from vtiger_activity
			inner join vtiger_crmentity on crmid=activityid where deleted=0 and rel_id=?', array($data['recordid']));
		$noOfRows = $adb->num_rows($sql);
		if ($noOfRows > 0) {
			$res = array();
			for ($i=0; $i < $noOfRows; $i++) { 
				$res[] = array(
					'activityid' => $adb->query_result($sql, $i, 'activityid'),
					'subject' => $adb->query_result($sql, $i, 'subject'),
					'date_start' => $adb->query_result($sql, $i, 'date_start'),
					'due_date' => $adb->query_result($sql, $i, 'due_date'),
					'time_start' => $adb->query_result($sql, $i, 'time_start'),
					'time_end' => $adb->query_result($sql, $i, 'time_end'),
					'activitytype' => $adb->query_result($sql, $i, 'activitytype'),
				);
			}
			return $res;
		}
		return array();
	}

	public function UpdateEvent() {
		require_once 'modules/cbCalendar/cbCalendar.php';
		global $current_user;
		$data = json_decode($_REQUEST['data'], true);
		$start = explode(' ', $data['dateStart']);
		$end = explode(' ', $data['dateEnd']);
		$focus = new cbCalendar();
		$focus->id = $data['eventId'];
		$focus->mode = 'edit';
		$focus->retrieve_entity_info($data['eventId'], 'cbCalendar');
		$focus->column_fields['dtstart'] = $data['dateStart'];
		$focus->column_fields['dtend'] = $data['dateEnd'];
		$handler = vtws_getModuleHandlerFromName('cbCalendar', $current_user);
		$meta = $handler->getMeta();
		$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);
		$focus->saveentity('cbCalendar');
	}

	public function TreeView() {
		global $current_user, $adb;
		$data = array();
		$fieldinfo = array();
		$childmodule = vtlib_purify($_REQUEST['child']);
		$parentid = array_unique($_REQUEST['parentid']);
		$parentmodule = getSalesEntityType($parentid[0]);
		$focus = CRMEntity::getInstance($parentmodule);
		$relfocus = CRMEntity::getInstance($childmodule);
		$relatedField = getFieldNameByFieldId(getRelatedFieldId($parentmodule, $childmodule));
		$qg = new QueryGenerator($childmodule, $current_user);
		$qg->setFields(array('*'));
		$cachedModuleChild = VTCacheUtils::lookupFieldInfo_Module($childmodule);
		$cachedModuleParent = VTCacheUtils::lookupFieldInfo_Module($parentmodule);
		foreach ($parentid as $id) {
			$entity = getEntityName($parentmodule, $id);
			$focus->retrieve_entity_info($id, $parentmodule);
			$focus->column_fields = self::processRows($focus->column_fields, $cachedModuleParent, $parentmodule, $focus->table_index);
			$focus->column_fields['parentaction'] = $entity[$id];
			$qg->addReferenceModuleFieldCondition($parentmodule, $relatedField, 'id', $id, 'e');
			$sql = $qg->getQuery();
			$result = $adb->pquery($sql, array());
			if ($adb->num_rows($result) > 0) {
				$crow = array();
				while ($row = $result->FetchRow()) {
					$crow[] = self::processRows($row, $cachedModuleChild, $childmodule, $relfocus->table_index);
				}
				$focus->column_fields['_children'] = $crow;
			}
			$focus->column_fields['_attributes'] = array(
				'expanded' => true
			);
			$data[] = $focus->column_fields;
		}
		return array(
			'data' => array(
				'contents' => $data,
				'pagination' => array(
					'page' => (int)1,
					'totalCount' => (int)0,
				),
			),
			'result' => true,
		);
	}
}