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
	public $field_value_mapping;
	public $launchwf;
	public $launchrelwf;

	public function getFieldNames() {
		return array('field_value_mapping', 'launchwf', 'launchrelwf');
	}

	public function after_retrieve() {
		if (!isset($this->launchrelwf)) {
			$this->launchrelwf = 0;
		}
		if (!isset($this->launchwf)) {
			$this->launchwf = 0;
		}
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg, $from_wf, $currentModule;
		$logbg->debug('> UpdateFieldsTask');
		$from_wf = true;
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$logbg->debug("(UpdateFieldsTask) Module: $moduleName, Record: $entityId");
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
			$focus->retrieve_entity_info($recordId, $moduleName, false, $from_wf);
			$focus->clearSingletonSaveFields();
			$logbg->debug('(UpdateFieldsTask) field values pre', $focus->column_fields);
			$logbg->debug('(UpdateFieldsTask) update info', $fieldValueMapping);

			$hold_user = $current_user;
			$util->loggedInUser();
			if (is_null($current_user)) {
				$current_user = $hold_user; // make sure current_user is defined
			}
			$relmodule=array();
			$handlerMetarel[]=array();
			$focusrel[]=array();
			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
				$fieldinfo = VTCacheUtils::lookupFieldInfo(getTabid($moduleName), $fieldName);
				preg_match('/\((\w+) : \(([_\w]+)\) (.+)\)/', $fieldInfo['value'], $matches);
				$fieldmodule='';
				$fldrelname='';
				$fieldValueType = $fieldInfo['valuetype'];
				$fieldValue = trim($fieldInfo['value']);
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
					$fieldValue = str_replace(' ', '_', $fieldValue);
					$_REQUEST[$fieldName.'_hidden'] = $fieldValue; # cf_1234
					if ($fieldinfo['uitype']==69) {
						$_REQUEST['__cbisduplicatedfromrecordid'] = $focus->column_fields[$matches[1]];
					} else {
						$_REQUEST['__cbisduplicatedfromrecordid'] = '';
					}
				}
				if ($fieldmodule=='') {
					$focus->column_fields[$fieldName] = $fieldValue;
					$entity->data[$fieldName] = $fieldValue;
				} else {
					if (empty($focusrel[$fieldmodule]) && $relId!='' && $relId!=0 && !in_array($fieldmodule, $relmodule)) {
						$relmodule[]=$fieldmodule;
						$focusrel[$fieldmodule] = CRMEntity::getInstance($fieldmodule);
						$focusrel[$fieldmodule]->id = $relId;
						$focusrel[$fieldmodule]->mode = 'edit';
						$focusrel[$fieldmodule]->retrieve_entity_info($relId, $fieldmodule, false, $from_wf);
						$focusrel[$fieldmodule]->clearSingletonSaveFields();
					}
					if (!empty($focusrel[$fieldmodule])) {
						$focusrel[$fieldmodule]->column_fields[$fldrelname]=$fieldValue;
					}
				}
			}
			$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $handlerMeta);
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$holdCM = $currentModule;
			$_REQUEST['ajxaction'] = 'Workflow';
			$logbg->debug('(UpdateFieldsTask) field values presave', $focus->column_fields);
			if (isset($this->launchwf) && $this->launchwf) {
				$focus->save($moduleName);
			} else {
				$focus->saveentity($moduleName);
			}
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
			$logbg->debug('(UpdateFieldsTask) field values postsave', $focus->column_fields);
		} else {
			$logbg->debug('(UpdateFieldsTask) not called: fieldValueMapping is empty');
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< UpdateFieldsTask');
	}
}
?>
