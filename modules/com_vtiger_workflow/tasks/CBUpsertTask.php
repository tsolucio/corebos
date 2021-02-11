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
require_once 'modules/com_vtiger_workflow/expression_functions/application.php';

class CBUpsertTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('field_value_mapping', 'bmapid', 'bmapid_display');
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
		$bmapid = $this->bmapid;
		$logbg->debug("Module: $moduleName, Record: $entityId");
		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, Users::getActiveAdminUser());
		$handlerMeta = $moduleHandler->getMeta();
		$moduleFields = $handlerMeta->getModuleFields();
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = json_decode($this->field_value_mapping, true);
		}
		$logbg->debug("field mapping: ".print_r($fieldValueMapping, true));
		if (!empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			include_once 'data/CRMEntity.php';
			$focus = CRMEntity::getInstance($moduleName);
			$focus->id = $recordId;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($recordId, $moduleName, false, $from_wf);
			$focus->clearSingletonSaveFields();

			$hold_user = $current_user;
			$util->loggedInUser();
			if (is_null($current_user)) {
				$current_user = $hold_user; // make sure current_user is defined
			}
			$relmodule = array();
			$handlerMetarel[] = array();
			$fieldValue = array();
			$fieldmodule = array();
			if (empty($entity->WorkflowContext['upsert_data'])) {
				$entity->WorkflowContext['upsert_data'] = array($focus->column_fields);
			}
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$_REQUEST['ajxaction'] = 'Workflow';
			$upsert_data = json_decode($entity->WorkflowContext['upsert_data'], true);
			foreach ($upsert_data as $key) {
				$entity->WorkflowContext['current_upsert_row'] = $key;
				foreach ($fieldValueMapping as $fieldInfo) {
					$fieldName = $fieldInfo['fieldname'];
					$fieldType = '';
					$fldmod = '';
					$fieldValueType = $fieldInfo['valuetype'];
					$fieldValue1 = trim($fieldInfo['value']);
					if (array_key_exists('fieldmodule', $fieldInfo)) {
						$fldmod = trim($fieldInfo['fieldmodule']);
						$fieldmodule = explode('__', trim($fieldInfo['fieldmodule']));
					}
					$module = $fieldmodule[0];
					$moduleHandlerrel = vtws_getModuleHandlerFromName($module, Users::getActiveAdminUser());
					$handlerMetarel[$fldmod] = $moduleHandlerrel->getMeta();
					$moduleFieldsrel = $handlerMetarel[$fldmod]->getModuleFields();
					$fieldValue[$fldmod][$fieldName]=$util->fieldvaluebytype($moduleFieldsrel, $fieldValueType, $fieldValue1, $fieldName, $focus, $entity, $handlerMeta);
				}
				if ($fldmod != '') {
					$fieldmodule = explode('__', $fldmod);
					$relmodule = $fieldmodule[0];
					$relfield = $fieldmodule[1];
					$fval = $fieldValue[$fldmod];
					$crmid = coreBOS_Rule::evaluate($bmapid, $fval);
					if (empty($crmid)) {
						$this->upsertData($fval, $relmodule, $relfield, 'doCreate');
					} else {
						$this->upsertData($fval, $relmodule, $relfield, 'doUpdate', $crmid);
					}
					$util->revertUser();
					$_REQUEST['ajxaction'] = $hold_ajxaction;
				}
			}
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< CBUpsertTask');
	}

	public function upsertData($data, $relmodule, $relfield, $action, $crmid = 0) {
		global $logbg, $adb, $current_user;
		$logbg->debug('> upsertData');
		$moduleHandler = vtws_getModuleHandlerFromName($relmodule, $current_user);
		$handlerMeta = $moduleHandler->getMeta();
		$focusrel = CRMEntity::getInstance($relmodule);
		if ($action == 'doCreate') {
			$focusrel->mode = '';
		} else {
			$focusrel->retrieve_entity_info($crmid, $relmodule);
			$focusrel->id = $crmid;
			$focusrel->mode = 'edit';
		}
		foreach ($data as $key => $value) {
			$focusrel->column_fields[$key] = $value;
		}
		$focusrel->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel->column_fields, $handlerMeta);
		$focusrel->save($relmodule);
		$logbg->debug('< upsertData');
	}
}
?>