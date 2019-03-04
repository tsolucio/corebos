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

class CBMassiveUpdateRelatedTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('field_value_mapping');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg, $from_wf;
		$from_wf = true;
		$logbg->debug('> CBMassiveUpdateRelatedTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$taskQueue = new VTTaskQueue($adb);
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$logbg->debug("Module: $moduleName, Record: $entityId");
		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, Users::getActiveAdminUser());
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
			$logbg->debug('field values pre: '.print_r($focus->column_fields, true));
			$logbg->debug('update info: '.print_r($fieldValueMapping, true));

			$hold_user = $current_user;
			$util->loggedInUser();
			if (is_null($current_user)) {
				$current_user = $hold_user; // make sure current_user is defined
			}
			$relmodule = array();
			$handlerMetarel[] = array();
			$fieldValue = array();
			$fieldmodule = array();

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
				if (!in_array($fldmod, $relmodule)) {
					$relmodule[] = $fldmod;
				}
				$moduleHandlerrel = vtws_getModuleHandlerFromName($module, Users::getActiveAdminUser());
				$handlerMetarel[$fldmod] = $moduleHandlerrel->getMeta();
				$moduleFieldsrel = $handlerMetarel[$fldmod]->getModuleFields();
				$fieldValue[$fldmod][$fieldName]=$util->fieldvaluebytype($moduleFieldsrel, $fieldValueType, $fieldValue1, $fieldName, $focus, $entity, $handlerMeta);
			}
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$_REQUEST['ajxaction'] = 'Workflow';
			if (count($relmodule)>0) {
				$focusrel[] = array();
				if (count($relmodule)>3 && $isqueue==false) {
					$delay = strtotime(' +5 minutes');
					$taskQueue->queueTask($this->id, $entityId, $delay);
					$util->revertUser();
					$from_wf = false;
					$_REQUEST['ajxaction'] = $hold_ajxaction;
					$logbg->debug('< CBMassiveUpdateRelatedTask: queued');
					return;
				} else {
					foreach ($relmodule as $mod) {
						$fieldmodule = explode('__', $mod);
						$relmodule = $fieldmodule[0];
						$relfield = $fieldmodule[1];
						$focusrel[$mod] = CRMEntity::getInstance($relmodule);
						$maintable = $focusrel[$mod]->table_name;
						$mainid = $focusrel[$mod]->table_index;
						$records=$adb->query("select $mainid from $maintable join vtiger_crmentity on crmid=$mainid where deleted=0 and $relfield='$recordId'");
						for ($nr=0; $nr<$adb->num_rows($records); $nr++) {
							$relId = $adb->query_result($records, $nr, '0');
							$focusrel[$mod]->id = $relId;
							$focusrel[$mod]->mode = 'edit';
							$focusrel[$mod]->retrieve_entity_info($relId, $relmodule, false, $from_wf);
							$focusrel[$mod]->clearSingletonSaveFields();
							$fval = $fieldValue[$mod];
							foreach ($fval as $key => $value) {
								$focusrel[$mod]->column_fields[$key]=$value;
							}
							$focusrel[$mod]->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel[$mod]->column_fields, $handlerMetarel[$mod]);
							$focusrel[$mod]->saveentity($relmodule);
						}
					}
				}
			}
			$_REQUEST['ajxaction'] = $hold_ajxaction;
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< CBMassiveUpdateRelatedTask');
	}
}
?>
