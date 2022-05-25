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
require_once 'modules/cbMap/cbMap.php';
require_once 'include/events/include.inc';

class CBUpsertTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('field_value_mapping', 'bmapid', 'bmapid_display', 'upsert_module');
	}

	public function doTask(&$entity) {
		global $current_user, $logbg, $from_wf, $currentModule;
		$from_wf = true;
		$logbg->debug('> CBUpsertTask');
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = json_decode($this->field_value_mapping, true);
		}
		$logbg->debug('field mapping', $fieldValueMapping);
		if (!empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			$util = new VTWorkflowUtils();
			$util->adminUser();
			$moduleName = $entity->getModuleName();
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
			if (empty($entity->WorkflowContext['upsert_data'])) {
				$entity->WorkflowContext['upsert_data'] = array($focus->column_fields);
			}
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$_REQUEST['ajxaction'] = 'Workflow';
			$upsert_data = $entity->WorkflowContext['upsert_data'];
			if (!is_array($entity->WorkflowContext['upsert_data'])) {
				$upsert_data = json_decode($entity->WorkflowContext['upsert_data'], true);
			}
			$relmodule = $this->upsert_module;
			$moduleHandlerrel = vtws_getModuleHandlerFromName($relmodule, Users::getActiveAdminUser());
			$handlerMetarel = $moduleHandlerrel->getMeta();
			$moduleFieldsrel = $handlerMetarel->getModuleFields();
			$loopContext = $entity->WorkflowContext;
			foreach ($upsert_data as $key) {
				$entity->WorkflowContext = $loopContext;
				$fieldValue = array();
				$entity->WorkflowContext['current_upsert_row'] = $key;
				foreach ($fieldValueMapping as $fieldInfo) {
					$fieldName = $fieldInfo['fieldname'];
					$fieldValueType = $fieldInfo['valuetype'];
					$fieldValue1 = trim($fieldInfo['value']);
					$fieldValue[$fieldName]=$util->fieldvaluebytype($moduleFieldsrel, $fieldValueType, $fieldValue1, $fieldName, $focus, $entity, $handlerMeta);
				}
				$crmid = 0; // no map > we create
				if (!empty($bmapid)) {
					$mapValues = $fieldValue;
					if (empty($mapValues['record_id'])) {
						$mapValues['record_id'] = $recordId;
					}
					$crmid = coreBOS_Rule::evaluate($bmapid, $mapValues);
				}
				if (!empty($entity->WorkflowContext['linkmodeid'])) {
					$fieldValue['linkmodeid'] = $entity->WorkflowContext['linkmodeid'];
				}
				if (empty($crmid)) {
					$crmid = $this->upsertData($fieldValue, $relmodule, 'doCreate');
				} else {
					if ($crmid < 0) {
						continue;
					}
					$this->upsertData($fieldValue, $relmodule, 'doUpdate', $crmid);
				}
				$loopContext['upserted_crmids'][]= $crmid;
			}
			$entity->WorkflowContext = $loopContext;
			$util->revertUser();
			$_REQUEST['ajxaction'] = $hold_ajxaction;
		}
		$from_wf = false;
		$logbg->debug('< CBUpsertTask');
	}

	public function upsertData($data, $relmodule, $action, $crmid = 0) {
		global $logbg, $current_user, $currentModule;
		if (strpos($crmid, 'x')>0) {
			list($void, $crmid) = explode('x', $crmid); // suppport WS ID
		}
		$logbg->debug('> upsertData: '.$relmodule.' - '.$action);
		$logbg->debug('data', $data);
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
		if (!empty($focusrel->column_fields['linkmodeid'])) {
			$focusrel->linkmodeid = $focusrel->column_fields['linkmodeid'];
			$focusrel->linkmodemodule = getSalesEntityType($focusrel->column_fields['linkmodeid']);
			$_REQUEST['createmode'] = 'link';
			unset($focusrel->column_fields['linkmodeid']);
		}
		require 'modules/com_vtiger_workflow/tasks/processAttachments.php';
		$focusrel->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel->column_fields, $handlerMeta);
		$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
		$_REQUEST['ajxaction'] = 'Workflow';
		if ($relmodule == $currentModule) {
			$focusrel->saveentity($relmodule);
		} else {
			$focusrel->save($relmodule);
		}
		$_REQUEST['ajxaction'] = $hold_ajxaction;
		unset($_REQUEST['createmode']);
		if (!empty($wsAttachments)) {
			foreach ($wsAttachments as $file) {
				@unlink($file);
			}
		}
		$logbg->debug('< upsertData');
		return $focusrel->id;
	}
}
?>