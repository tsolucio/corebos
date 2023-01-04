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
	}

	public function Init() {
		require_once 'modules/'.$this->module.'/'.$this->module.'.php';
		global $smarty;
		$smarty->assign('formodule', $this->module);
		$smarty->assign('GroupBy', $this->groupby);
		if ($this->mapid != 0) {
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
			$smarty->assign('Columns', $columns);
			$smarty->assign('relatedmodules', $this->RelatedFields());
			$smarty->assign('step', $this->step);
			$smarty->assign('entitynames', getEntityFieldNames($this->module));
			$smarty->assign('WizardMode', $this->mode);
			//ListView, Create_{ModuleName}, MassCreate_{ModuleName}
			$smarty->assign('WizardActions', $this->actions);
			$smarty->assign('WizardValidate', $this->validate);
			$smarty->assign('WizardGoBack', $this->goback);
			$smarty->assign('WizardRequiredAction', $this->required_action);
			$smarty->assign('WizardCustomFunction', $this->custom_function);
			$smarty->assign('WizardModuleEditor', $this->module);
			$smarty->assign('WizardContext', $this->filtercontext);
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
			return $smarty->fetch('Smarty/templates/Components/WizardListView.tpl');
		}
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
		$step = isset($_REQUEST['step']) ? intval($_REQUEST['step']) : '';
		$filtergrid = isset($_REQUEST['filtergrid']) ? vtlib_purify($_REQUEST['filtergrid']) : false;
		$conditionquery = isset($_REQUEST['conditionquery']) ? vtlib_purify($_REQUEST['conditionquery']) : false;
		$parentid = isset($_REQUEST['parentid']) ? vtlib_purify($_REQUEST['parentid']) : 0;
		$currentid = isset($_REQUEST['currentid']) ? vtlib_purify($_REQUEST['currentid']) : 0;
		$required_action = isset($_REQUEST['required_action']) ? intval($_REQUEST['required_action']) : '';
		$context = isset($_REQUEST['context']) ? $_REQUEST['context'] : '';
		$filterFromContext = isset($_REQUEST['filterFromContext']) ? json_decode($_REQUEST['filterFromContext'], true) : '';
		if (isset($_REQUEST['query']) && !empty($_REQUEST['query']) && !$filtergrid) {
			$sql = vtlib_purify($_REQUEST['query']);
			$ctxConds = '';
			if (!empty($filterFromContext)) {
				foreach ($filterFromContext as $condX) {
					$fldName = $condX['match'];
					if (!isset($context[$condX['find']])) {
						continue;
					}
					$ctxConds .= $adb->convert2Sql(' and '.$condX['match'].' =? ', array($context[$condX['find']]));
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
		$fieldinfo = array();
		$tabid = getTabid($this->module);
		$focus = new $this->module;
		$table_index = $focus->table_index;
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($this->module);
		while ($row = $rs->FetchRow()) {
			$crow = array();
			foreach ($row as $field => $value) {
				if (is_numeric($field)) {
					continue;
				}
				if ($field == 'smownerid') {
					$field = 'assigned_user_id';
				}
				if (!isset($fieldinfo[$field])) {
					foreach ($cachedModuleFields as $key) {
						if ($key['fieldname'] == $field) {
							$fieldinfo[$field] = array(
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
				}
				if (!isset($fieldinfo[$field])) {
					continue;
				}
				$gridvalue = getDataGridValue($this->module, $row[$table_index], $fieldinfo[$field], $value, 'Wizard');
				$crow[$field] = $gridvalue[0];
				$crow['id'] = $row[$table_index];
				$crow[$fieldinfo[$field]['name'].'_raw'] = $value;
				$crow['__modulename'] = $this->module;
			}
			$data[] = $crow;
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
				'contents' => $mapping,
				'pagination' => array(
					'page' => 1,
					'totalCount' => count($data[1]['id']),
				),
			),
			'result' => true,
		);
	}

	public function MassCreate($target = array()) {
		require_once 'include/Webservices/MassCreate.php';
		global $current_user;
		$subaction = isset($_REQUEST['subaction']) ? vtlib_purify($_REQUEST['subaction']) : '';
		if (!empty($subaction)) {
			$target = $this->$subaction();
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
					$target[] = array(
						'elementType' => $createmodule,
						'referenceId' => '',
						'searchon' => '',
						'element' => $row
					);
				}
				return $target;
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
}