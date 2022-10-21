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

class WizardView {

	private $step;
	private $module;
	private $groupby;
	private $actions = '';
	private $mode = 'ListView';
	private $mapid = 0;
	private $goback = 1;
	private $validate = true;

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
		if (isset($this->params['validate'])) {
			$this->validate = $this->params['validate'];
		}
		if (isset($this->params['back'])) {
			$this->goback = $this->params['back'];
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
			$query = '';
			if (isset($this->conditions['condition'])) {
				$query = $this->conditions['condition'];
			}
			$smarty->assign('WizardFilterBy', $query);
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

class WizardActions {

	private $module;

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
		if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
			$sql = vtlib_purify($_REQUEST['query']);
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
					foreach ($forids as $id) {
						$qg->addCondition('id', $id, 'e', 'or');
					}
				}
				if (!empty($newRecords)) {
					$step = vtlib_purify($_REQUEST['step']);
					foreach ($newRecords[$step-1] as $id) {
						$qg->addCondition('id', $id, 'e', 'or');
					}
				}
			} elseif ($step == '0' && $this->module == 'Products' && $mode == 'SELECTPRODUCT') {
				//specific use case
				if (!empty($newRecords)) {
					foreach ($newRecords[$step-1] as $id) {
						$qg->addCondition('id', $id, 'e', 'or');
					}
					$page = 1;
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
						foreach ($forids as $id) {
							$qg->addReferenceModuleFieldCondition($forfield['relmodule'], $forfield['fieldname'], 'id', $id, 'e', 'or');
						}
					}
				}
			}
			$sql = $qg->getQuery();
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

	public function MassCreate() {
		require_once 'include/Webservices/MassCreate.php';
		global $current_user;
		$subaction = isset($_REQUEST['subaction']) ? vtlib_purify($_REQUEST['subaction']) : '';
		$target = array();
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
		return false;
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
		global $current_user;
		$UsersTabid = vtws_getEntityId('Users');
		$data = json_decode($_REQUEST['data'], true);
		if (count($data) == 2) {
			$relmodule = $data[0]['relmodule'];
			$createmodule = $data[1]['createmodule'];
			$relfield = getFieldNameByFieldId(getRelatedFieldId($relmodule, $createmodule));
			if (!empty($relfield)) {
				$target = array();
				foreach ($data[1]['data'] as $row) {
					$row[$relfield] = vtws_getEntityId($relmodule).'x'.$data[0]['id'][0];
					$row['assigned_user_id'] = $UsersTabid.'x'.$current_user->id;
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

	public function Create_ProductComponent() {
		global $current_user;
		$UsersTabid = vtws_getEntityId('Users');
		$ProductsTabid = vtws_getEntityId('Products');
		$data = json_decode($_REQUEST['data'], true);
		$fromProduct = $data[0][0];
		$target = array();
		if (isset($data[1])) {
			foreach ($data[1] as $id) {
				$target[] = array(
					'elementType' => $this->module,
					'referenceId' => '',
					'searchon' => '',
					'element' => array(
						'frompdo' => $ProductsTabid.'x'.$fromProduct,
						'topdo' => $ProductsTabid.'x'.$id,
						'assigned_user_id' => $UsersTabid.'x'.$current_user->id
					)
				);
			}
		}
		return $target;
	}

	public function Create_PurchaseOrder() {
		global $current_user;
		$UsersTabid = vtws_getEntityId('Users');
		$VendorTabid = vtws_getEntityId('Vendors');
		$data = json_decode($_REQUEST['data'], true);
		$target = array();
		$element = array();
		foreach ($data as $id => $relids) {
			$vendorname = getEntityName('Vendors', $id);
			$target[] = array(
				'elementType' => $this->module,
				'referenceId' => 'entity_id_'.$id,
				'searchon' => '',
				'element' => array(
					'vendor_id' => $VendorTabid.'x'.$id,
					'subject' => 'Quotes by ('.$vendorname[$id].')',
					'postatus' => 'Created',
					'bill_street' => '-',
					'ship_street' => '-',
					'assigned_user_id' => $UsersTabid.'x'.$current_user->id
				)
			);
			foreach ($relids as $rid) {
				$target[] = array(
					'elementType' => 'InventoryDetails',
					'referenceId' => '',
					'searchon' => 'id',
					'element' => array(
						'related_to' => '@{entity_id_'.$id.'}',
						'id' => $rid,
					)
				);
			}
		}
		return $target;
	}
}