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
	private $mapid = 0;
	
	function __construct($params) {
		foreach ($params as $key) {
			$this->params[$key['name']] = $key['value'];
		}
		if (isset($this->params['module'])) {
			$this->module = $this->params['module'];
		}
		if (isset($this->params['step'])) {
			$this->step = $this->params['step'];
		}
		if (isset($this->params['mapid'])) {
			$this->mapid = $this->params['mapid'];
		}
		if (isset($this->params['groupby'])) {
			$this->groupby = $this->params['groupby'];
		}
	}

	public function Init() {
		require_once 'modules/'.$this->module.'/'.$this->module.'.php';
		global $smarty;
		$smarty->assign('formodule', $this->module);
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
			$smarty->assign('Columns', json_encode($columns));
			$smarty->assign('relatedmodules', $this->RelatedFields());
			$smarty->assign('step', $this->step);
			$smarty->assign('entitynames', getEntityFieldNames($this->module));
			$smarty->assign('WizardMode', 'Create_PurchaseOrder');
			return $smarty->fetch('Smarty/templates/Components/WizardListView.tpl');
		}
		$smarty->assign('GroupBy', $this->groupby);
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

class WizardListView {

	private $module;
	
	function __construct($module) {
		$this->module = $module;
	}

	public function Grid() {
		global $current_user, $adb;
		$page = $_REQUEST['page'];
		$perPage = $_REQUEST['perPage'];
		$forids = isset($_REQUEST['forids']) ? json_decode($_REQUEST['forids'], true) : array();
		$forfield = isset($_REQUEST['forfield']) ? $_REQUEST['forfield'] : array();
		$qg = new QueryGenerator($this->module, $current_user);
		$qg->setFields(array('*'));
		if (!empty($forids)) {
			foreach ($forids as $id) {
				$qg->addReferenceModuleFieldCondition($forfield['relmodule'], $forfield['fieldname'], 'id', $id, 'e', 'or');
			}
		}
		$sql = $qg->getQuery();
		$limit = ($page-1) * $perPage;
		//count all records
		$countsql = mkCountQuery($sql);
		$rs = $adb->query($countsql);
		$count = $rs->fields['count'];
		$sql .= ' LIMIT '.$limit.','.$perPage;
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

	public function MassCreate() {
		if ($this->module == 'PurchaseOrder') {
			return $this->CreatePO();	
		}
	}

	public function CreatePO() {
		require_once 'include/Webservices/MassCreate.php';
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
		$response = MassCreate($target, $current_user);
		if (!empty($response['failed_creates'])) {
			return false;
		}
		return true;
	}
}