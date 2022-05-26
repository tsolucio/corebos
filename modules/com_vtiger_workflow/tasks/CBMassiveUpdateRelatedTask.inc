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
require_once 'modules/cbMap/cbMap.php';
require_once 'include/events/include.inc';


class CBMassiveUpdateRelatedTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('field_value_mapping','bmapid', 'bmapid_display', 'launchrelwf');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg, $from_wf, $currentModule;
		$from_wf = true;
		$logbg->debug('> CBMassiveUpdateRelatedTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$moduleName = $entity->getModuleName();
		$holdCM = $currentModule;
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
		$fieldValueMapping = array();
		if (!empty($bmapid)) {
			// Business Map
			$focusmap = new cbMap();
			$focusmap->id = $bmapid;
			$focusmap->mode = '';
			$focusmap->retrieve_entity_info($bmapid, 'cbMap');
			$businessMap = $focusmap->column_fields['maptype'];
		} else {
			$businessMap = '';
		}
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = json_decode($this->field_value_mapping, true);
		}
		$logbg->debug('field mapping', $fieldValueMapping);
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

			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
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
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$_REQUEST['ajxaction'] = 'Workflow';
			if ($fldmod!='') {
				$focusrel[] = array();
				$fieldmodule = explode('__', $fldmod);
				$relmodule = $fieldmodule[0];
				$relfield = $fieldmodule[1];
				$focusrel[$fldmod] = CRMEntity::getInstance($relmodule);
				$maintable = $focusrel[$fldmod]->table_name;
				$mainid = $focusrel[$fldmod]->table_index;
				$condition = '';
				if ($businessMap == 'Condition Query') {
					$ids = $focusmap->ConditionQuery($recordId);
					foreach ($ids as $recid) {
						$getids[] = $recid[$mainid];
					}
					if (count($getids)>0) {
						$condition = " and $mainid in (".implode(',', $getids).')';
					}
				}

				global $GetRelatedList_ReturnOnlyQuery;
				$holdValue = $GetRelatedList_ReturnOnlyQuery;
				$GetRelatedList_ReturnOnlyQuery = true;
				$rectabid = getTabid($moduleName);
				$relatedModuleId = getTabid($relmodule);
				$relationResult = $adb->pquery(
					'SELECT name FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? limit 1',
					array($rectabid, $relatedModuleId)
				);
				$relationInfo = $adb->fetch_array($relationResult);
				$params = array($recordId, $rectabid, $relatedModuleId);
				$logbg->debug(['rel func' => $relationInfo['name'], 'params' => $params]);
				if (empty($holdCM)) {
					$holdCM = $currentModule;
				}
				$currentModule = $moduleName;
				$relationData = call_user_func_array(array($focus, $relationInfo['name']), array_values($params));
				$GetRelatedList_ReturnOnlyQuery = $holdValue;
				$currentModule = $holdCM;
				if (isset($relationData['query'])) {
					$query = $relationData['query'];
					preg_match('/[\t ]from /i', $query, $matches, PREG_OFFSET_CAPTURE);
					$query = 'select vtiger_crmentity.crmid'.substr($query, $matches[0][1]).$condition;
					$rs = $adb->query($query);
					while ($row = $adb->fetch_array($rs)) {
						$relId = $row['crmid'];
						if ($businessMap == '' || ($businessMap == 'Condition Expression' && $focusmap->ConditionExpression(vtws_getEntityId($relmodule).'x'.$relId))) {
							$focusrel[$fldmod]->id = $relId;
							$focusrel[$fldmod]->mode = 'edit';
							$focusrel[$fldmod]->retrieve_entity_info($relId, $relmodule, false, $from_wf);
							$focusrel[$fldmod]->clearSingletonSaveFields();
							$fval = $fieldValue[$fldmod];
							foreach ($fval as $key => $value) {
								$focusrel[$fldmod]->column_fields[$key]=$value;
							}
							$focusrel[$fldmod]->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel[$fldmod]->column_fields, $handlerMetarel[$fldmod]);
							$logbg->debug("Saving: $relmodule", $focusrel[$fldmod]->column_fields);
							if ($this->launchrelwf) {
								$focusrel[$fldmod]->save($relmodule);
							} else {
								// Initialize Event trigger cache
								$em = new VTEventsManager($adb);
								$em->initTriggerCache();
								$entityData = VTEntityData::fromCRMEntity($focusrel[$fldmod]);
								$em->triggerEvent('corebos.beforesave.workflow', $entityData);
								$focusrel[$fldmod]->saveentity($relmodule);
								$em->triggerEvent('corebos.aftersave.workflow', $entityData);
								$em->triggerEvent('corebos.aftersave.workflow.final', $entityData);
							}
						}
					}
				}
			}
			$util->revertUser();
			$_REQUEST['ajxaction'] = $hold_ajxaction;
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< CBMassiveUpdateRelatedTask');
	}
}
?>
