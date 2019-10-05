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

class VTCreateEntityTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('entity_type', 'reference_field', 'field_value_mapping');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $from_wf, $logbg;
		$from_wf = true;
		$util = new VTWorkflowUtils();

		$util->adminUser();
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];

		$entityType = $this->entity_type;
		if (!vtlib_isModuleActive($entityType)) {
			$util->revertUser();
			return;
		}

		$fieldValueMapping = array();
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = json_decode($this->field_value_mapping, true);
		}

		if (!empty($entityType) && !empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			$logbg->debug('CreateEntityTask: create '.$entityType);
			require_once 'data/CRMEntity.php';
			require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';

			$focus = CRMEntity::getInstance($moduleName);
			$focus->id = $recordId;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($recordId, $moduleName);

			$newEntity = CRMEntity::getInstance($entityType);
			$newEntity->mode = '';
			if ($moduleName == $entityType) {
				$newEntity->column_fields = $focus->column_fields;
			}
			$newEntityData = VTEntityData::fromCRMEntity($newEntity);
			$entityModuleHandler = vtws_getModuleHandlerFromName($entityType, $current_user);
			$handlerMeta = $entityModuleHandler->getMeta();
			$ownerFields = $handlerMeta->getOwnerFields();
			$dtFields = $handlerMeta->getFieldListByType('datetime');
			$datetimeFields = array();
			foreach ($dtFields as $webserviceField) {
				$datetimeFields[] = $webserviceField->getFieldName();
			}
			unset($dtFields);
			$logbg->debug($focus->column_fields);
			$logbg->debug($fieldValueMapping);
			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
				$referenceModule = $fieldInfo['modulename'];
				$fieldValueType = $fieldInfo['valuetype'];
				$fieldValue = trim($fieldInfo['value']);

				if ($fieldValueType == 'fieldname' && !preg_match('/\((\w+) : \(([_\w]+)\) (.+)\)/', $fieldValue)) {
					if ($referenceModule == $entityType) {
						$fieldValue = $newEntity->column_fields[$fieldValue];
					} else {
						$fieldValue = ($fieldValue == 'id' ? $focus->column_fields['record_id'] : $focus->column_fields[$fieldValue]);
					}
				} elseif ($fieldValueType == 'expression' || ($fieldValueType == 'fieldname' && preg_match('/\((\w+) : \(([_\w]+)\) (.+)\)/', $fieldValue))) {
					$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($fieldValue)));
					$expression = $parser->expression();
					$exprEvaluater = new VTFieldExpressionEvaluater($expression);
					if ($referenceModule == $entityType) {
						$fieldValue = $exprEvaluater->evaluate($newEntityData);
					} else {
						$fieldValue = $exprEvaluater->evaluate($entity);
					}
				} elseif (preg_match('/([^:]+):boolean$/', $fieldValue, $match)) {
					$fieldValue = $match[1];
					if ($fieldValue == 'true') {
						$fieldValue = '1';
					} else {
						$fieldValue = '0';
					}
				}

				if (in_array($fieldName, $ownerFields) && !is_numeric($fieldValue)) {
					$userId = getUserId_Ol($fieldValue);
					$groupId = getGrpId($fieldValue);

					if ($userId == 0 && $groupId == 0) {
						$fieldValue = $focus->column_fields[$fieldName];
					} else {
						$fieldValue = ($userId == 0) ? $groupId : $userId;
					}
				}
				if (in_array($fieldName, $datetimeFields) && strpos($fieldValue, ' ')===false) {
					$rs = $adb->pquery('select start_hour from vtiger_users where id=?', array($current_user->id));
					$fieldValue = $fieldValue.' '.$adb->query_result($rs, 0, 'start_hour');
				}

				$newEntity->column_fields[$fieldName] = $fieldValue;
			}
			$newEntity->column_fields[$this->reference_field] = $focus->id;
			if (($moduleName=='Invoice' || $moduleName=='SalesOrder') && $entityType=='CobroPago' && empty($newEntity->column_fields['parent_id'])) {
				$newEntity->column_fields['parent_id'] = (
					empty($focus->column_fields['account_id']) ?
						$focus->column_fields['contact_id'] :
						$focus->column_fields['account_id']
				);
			}
			if (($moduleName=='Invoice' || $moduleName=='SalesOrder') && $entityType=='Potentials' && empty($newEntity->column_fields['related_to'])) {
				$newEntity->column_fields['related_to'] = (
					empty($focus->column_fields['account_id']) ?
						$focus->column_fields['contact_id'] :
						$focus->column_fields['account_id']
				);
			}
			if ($moduleName=='PurchaseOrder' && $entityType=='CobroPago' && empty($newEntity->column_fields['parent_id'])) {
				$newEntity->column_fields['parent_id'] = $focus->column_fields['vendor_id'];
			}

			$cbfromid = $recordId;
			$cbfrommodule = getSalesEntityType($cbfromid);
			$bmapname = $cbfrommodule.'2'.$entityType;
			$cbfrom = CRMEntity::getInstance($cbfrommodule);
			$cbfrom->retrieve_entity_info($cbfromid, $cbfrommodule);
			$cbMapidFromid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapidFromid) {
				$cbMapid = $cbMapidFromid;
			}
			if (!empty($cbMapid)) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$newEntity->column_fields = $cbMap->Mapping($cbfrom->column_fields, $newEntity->column_fields);
			}

			$newEntity->column_fields = DataTransform::sanitizeRetrieveEntityInfo($newEntity->column_fields, $handlerMeta);
			$logbg->debug($newEntity->column_fields);
			$newEntity->save($entityType);
			relateEntities($focus, $moduleName, $recordId, $entityType, $newEntity->id);
		} else {
			$logbg->debug('CreateEntityTask: cannot create entity due to missing information');
			$logbg->debug($entityType);
			$logbg->debug($fieldValueMapping);
		}
		$from_wf = false;
		$util->revertUser();
	}
}
?>
