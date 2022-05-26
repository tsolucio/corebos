<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

class VTUpdateFieldsTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('field_value_mapping', 'launchrelwf');
	}

	public function after_retrieve() {
		if (!isset($this->launchrelwf)) {
			$this->launchrelwf = 0;
		}
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg,$from_wf, $currentModule;
		$from_wf = true;
		$logbg->debug('> VTUpdateFieldsTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$logbg->debug("Module: $moduleName, Record: $entityId");
		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$handlerMeta = $moduleHandler->getMeta();
		$moduleFields = $handlerMeta->getModuleFields();
		$fieldValueMapping = array();
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = json_decode($this->field_value_mapping, true);
		}
		if (!empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			include_once 'data/CRMEntity.php';
			$focus = CRMEntity::getInstance($moduleName);
			$focus->id = $recordId;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($recordId, $moduleName);
			$focus->clearSingletonSaveFields();
			$logbg->debug('field values pre', $focus->column_fields);
			$logbg->debug('update info', $fieldValueMapping);

			$hold_user = $current_user;
			$util->loggedInUser();
			if (is_null($current_user)) {
				$current_user = $hold_user;  // make sure current_user is defined
			}
			$relmodule=array();
			$handlerMetarel[]=array();
			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
				$fieldmodule='';
				$fldrelname='';
				$fieldValueType = $fieldInfo['valuetype'];
				$fieldValue = trim($fieldInfo['value']);
				$focusrel[]=array();
				if (array_key_exists('fldmodule', $fieldInfo)) {
					$fieldmodule = trim($fieldInfo['fldmodule']);
				}
				if (array_key_exists('fldrelname', $fieldInfo)) {
					$fldrelname = trim($fieldInfo['fldrelname']);
					$relId=$focus->column_fields[$fieldName];
					$setype=getSalesEntityType($relId);
				}
				if ($fldrelname!='' && $relId!='' && $relId!=0) {
					if ($setype!=$fieldmodule) {
						continue; // uitype 10 with multiple modules and current value is not this mappings module
					}
					if (!in_array($fieldmodule, $relmodule)) {
						$relmodule[]=$fieldmodule;
						$focusrel[$fieldmodule] = CRMEntity::getInstance($fieldmodule);
						$focusrel[$fieldmodule]->id = $relId;
						$focusrel[$fieldmodule]->mode = 'edit';
						$focusrel[$fieldmodule]->retrieve_entity_info($relId, $fieldmodule, false, $from_wf);
						$focusrel[$fieldmodule]->clearSingletonSaveFields();
					}
					if (count($relmodule)>3 && !$isqueue) {
						$delay = 5*60; // 5 minutes from now
						$entityData->WorkflowContext['__WorkflowID'] = $entity->WorkflowID;
						$msg = array(
							'taskId' => $this->id,
							'entityId' => $entityId,
							'context' => $entity->WorkflowContext,
						);
						Workflow::pushWFTaskToQueue($entity->WorkflowID, $entity->WorkflowEvent, $entityId, $msg, $delay);
						return;
					}
					$moduleHandlerrel = vtws_getModuleHandlerFromName($fieldmodule, Users::getActiveAdminUser());
					$handlerMetarel[$fieldmodule] = $moduleHandlerrel->getMeta();
					$moduleFieldsrel = $handlerMetarel[$fieldmodule]->getModuleFields();
					$fieldValue=$util->fieldvaluebytype($moduleFieldsrel, $fieldValueType, $fieldValue, $fldrelname, $focus, $entity, $handlerMeta);
				} else {
					$fieldValue=$util->fieldvaluebytype($moduleFields, $fieldValueType, $fieldValue, $fieldName, $focus, $entity, $handlerMeta);
				}
				if ($fieldmodule=='') {
					$focus->column_fields[$fieldName] = $fieldValue;
					$entity->data[$fieldName] = $fieldValue;
				} else {
					$focusrel[$fieldmodule]->column_fields[$fldrelname]=$fieldValue;
				}
			}
			$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $handlerMeta);
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$holdCM = $currentModule;
			$_REQUEST['ajxaction'] = 'Workflow';
			$logbg->debug('field values presave', $focus->column_fields);
			$focus->saveentity($moduleName);
			if (!empty($relmodule) && !empty($relId)) {
				foreach ($relmodule as $mod) {
					$currentModule = $mod;
					$focusrel[$mod]->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel[$mod]->column_fields, $handlerMetarel[$mod]);
					if ($this->launchrelwf) {
						$focusrel[$mod]->save($mod);
					} else {
						$em = new VTEventsManager($adb);
						// Initialize Event trigger cache
						$em->initTriggerCache();
						$entityData = VTEntityData::fromCRMEntity($focusrel[$mod]);
						$em->triggerEvent('corebos.beforesave.workflow', $entityData);

						$focusrel[$mod]->saveentity($mod);

						$em->triggerEvent('corebos.aftersave.workflow', $entityData);
						$em->triggerEvent('corebos.aftersave.workflow.final', $entityData);
					}
				}
			}
			$_REQUEST['ajxaction'] = $hold_ajxaction;
			$currentModule = $holdCM;
			$util->revertUser();
			$logbg->debug('field values postsave', $focus->column_fields);
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< VTUpdateFieldsTask');
	}
}
?>