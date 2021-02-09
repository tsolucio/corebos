<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';
require_once 'modules/cbMap/cbMap.php';
require_once 'include/events/include.inc';

class CBUpsertTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('bmapid', 'bmapid_display');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg, $from_wf, $currentModule;
		$from_wf = true;
		$logbg->debug('> CBUpsertTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$taskQueue = new VTTaskQueue($adb);
		$moduleName = $entity->getModuleName();
		$context = $entity->WorkflowContext;
		if (empty($currentModule) || $currentModule!=$moduleName) {
			$currentModule = $moduleName;
		}
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$logbg->debug("Module: $moduleName, Record: $entityId");
		if ($context != '') {
			$context_data = json_decode($context['context'], true);
			$ws_map_response = $context['ws_map_response'];
			$map_fields = array();
			foreach ($ws_map_response as $key => $value) {
				if ($key != 'message' && $key != 'data') {
					$field_map = $value['field'];
					$context_map = str_replace('wsctx_', '', $value['context']);
					if (strpos($field_map, '.') !== false) {
						$field = explode('.', $field_map);
						$related_modulename = $field[0];
						$fieldname = $field[1];
						$related_field = '';
						if (count($field) == 3) {
							$related_field = $field[2];
						}
						$fields_data = $adb->pquery('SELECT * FROM vtiger_field WHERE columnname=?', array($fieldname));
						$tabId = $adb->query_result($fields_data, 0, 'tabid');
						$uitype = $adb->query_result($fields_data, 0, 'uitype');
						$module = vtlib_getModuleNameById($tabId);
						if (!array_key_exists($module, $map_fields)) {
							$map_fields[$module] = array();
						}
						array_push($map_fields[$module], array(
							'field' => $fieldname,
							'context' => $context_map,
							'uitype' => $uitype,
							'related_modulename' => $uitype == '10' ? $related_modulename : '',
							'related_field' => $related_field
						));
					}
				}
			}
			//match fields from map with values in ws response
			foreach ($context_data as $key) {
				$map_records = array();
				foreach ($map_fields as $module => $fld) {
					$crmid = coreBOS_Rule::evaluate($this->bmapid, $key);
					$crmid = '';
					$flds = array_map(function ($k) use ($module, $key) {
						$field = $k['field'];
						$context_field = $k['context'];
						$uitype = $k['uitype'];
						$related_modulename = $k['related_modulename'];
						$related_field = $k['related_field'];
						return array(
							$module,
							$field,
							$key[$context_field],
							$uitype,
							$related_modulename,
							$related_field,
						);
					}, $fld);
					$records = $this->groupArrayByKey($flds, 0);
					if (empty($crmid)) {
						$this->upsertData($records, 'doCreate');
					} else {
						$this->upsertData($records, 'doUpdate', $crmid);
					}
				}
			}
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< CBUpsertTask');
		die;
	}

	public function upsertData($data, $action, $crmid = 0) {
		global $logbg, $adb;
		$logbg->debug('> upsertData');
		foreach ($data as $module => $val) {
			include_once "modules/$module/$module.php";
			$focus = new $module();
			if ($action == 'doCreate') {
				$focus->modue = '';
			} elseif ($action == 'doUpdate') {
				$focus->retrieve_entity_info($crmid, $module);
				$focus->id = $crmid;
				$focus->modue = 'edit';
			}
			for ($i=0; $i<count($val); $i++) {
				$field = $val[$i][1];
				$value = $val[$i][2];
				$uitype = $val[$i][3];
				$relMod = $val[$i][4];
				$relFld = $val[$i][5]; //add it only if uitype 10 values isn't crmid
				if ($uitype == '10') {
					//get recordid if another value is given
					if (!isRecordExists($value) && $relFld != '') {
						include_once "modules/$relMod/$relMod.php";
						$relFocus = new $relMod();
						$table_name = $relFocus->table_name;
						$table_index = $relFocus->table_index;
						$result = $adb->pquery("SELECT $table_index FROM $table_name INNER JOIN vtiger_crmentity ON crmid=$table_index WHERE deleted=0 AND $relFld=?", array($value));
						$value = $adb->query_result($result, 0, $table_index);
					}
				}
				$focus->column_fields[$field] = $value;
			}
			$focus->save($module);
		}
		$logbg->debug('< upsertData');
	}

	public function groupArrayByKey($array, $key) {
		global $logbg;
		$logbg->debug('> groupArrayByKey');
		$result = array();
		foreach ($array as $element) {
			$result[$element[$key]][] = $element;
		}
		$logbg->debug('< groupArrayByKey');
		return $result;
	}
}
?>