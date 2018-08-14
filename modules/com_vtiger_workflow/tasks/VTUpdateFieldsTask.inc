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
require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';

class VTUpdateFieldsTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('field_value_mapping', 'launchrelwf');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg,$from_wf;
		$from_wf = true;
		$logbg->info('Entering workflow task VTUpdateFieldsTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$taskQueue = new VTTaskQueue($adb);
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
			$logbg->debug('field values pre: '.print_r($focus->column_fields, true));
			$logbg->debug('update info: '.print_r($fieldValueMapping, true));

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
					if (!in_array($fieldmodule, $relmodule) && $setype==$fieldmodule) {
						$relmodule[]=$fieldmodule;
						$focusrel[$fieldmodule] = CRMEntity::getInstance($fieldmodule);
						$focusrel[$fieldmodule]->id = $relId;
						$focusrel[$fieldmodule]->mode = 'edit';
						$focusrel[$fieldmodule]->retrieve_entity_info($relId, $fieldmodule);
						$focusrel[$fieldmodule]->clearSingletonSaveFields();
					}
					if (count($relmodule)>3 && $isqueue==false) {
						$delay = strtotime(' +5 minutes');
						$taskQueue->queueTask($this->id, $entityId, $delay);
						return;
					}
					$moduleHandlerrel = vtws_getModuleHandlerFromName($fieldmodule, $current_user);
					$handlerMetarel[$fieldmodule] = $moduleHandlerrel->getMeta();
					$moduleFieldsrel = $handlerMetarel[$fieldmodule]->getModuleFields();
					$fieldValue=$util->fieldvaluebytype($moduleFieldsrel, $fieldValueType, $fieldValue, $fldrelname, $focus, $entity, $handlerMetarel[$fieldmodule]);
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
			$_REQUEST['ajxaction'] = 'Workflow';
			$logbg->debug('field values presave: '.print_r($focus->column_fields, true));
			$focus->saveentity($moduleName);
			if (count($relmodule)>0 && $relId!='' && $relId!=0) {
				foreach ($relmodule as $mod) {
					$focusrel[$mod]->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel[$mod]->column_fields, $handlerMetarel[$mod]);
					if ($this->launchrelwf) {
						$focusrel[$mod]->save($mod);
					} else {
						$focusrel[$mod]->saveentity($mod);
					}
				}
			}
			$_REQUEST['ajxaction'] = $hold_ajxaction;
			$util->revertUser();
			$logbg->debug('field values postsave: '.print_r($focus->column_fields, true));
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->info('Exiting workflow task VTUpdateFieldsTask');
	}
}
?>