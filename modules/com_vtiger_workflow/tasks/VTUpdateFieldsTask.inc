<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');

class VTUpdateFieldsTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('field_value_mapping');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $default_charset, $log, $logbg,$from_wf;
		$from_wf = true;
		$logbg->info('Entering workflow task VTUpdateFieldsTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();

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
			$fieldValueMapping = Zend_Json::decode($this->field_value_mapping);
		}

		if (!empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			require_once('data/CRMEntity.php');
			$focus = CRMEntity::getInstance($moduleName);
			$focus->id = $recordId;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($recordId, $moduleName);
			$focus->clearSingletonSaveFields();
			$logbg->debug('field values pre: '.print_r($focus->column_fields,true));
			$logbg->debug('update info: '.print_r($fieldValueMapping,true));

			$hold_user = $current_user;
			$util->loggedInUser();
			if (is_null($current_user)) $current_user = $hold_user;  // make sure current_user is defined
			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
				$fieldType = '';
				$fieldValueType = $fieldInfo['valuetype'];
				$fieldValue = trim($fieldInfo['value']);

				$fieldInstance = $moduleFields[$fieldName];
				if ($fieldValueType == 'fieldname') {
					if ($fieldInstance->getFieldDataType() === 'currency') {
						$focus->column_fields[$fieldValue] = $focus->adjustCurrencyField($fieldValue,$focus->column_fields[$fieldValue]);
					}
					$fieldValue = $focus->column_fields[$fieldValue];
				} elseif ($fieldValueType == 'expression') {
					require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
					$fieldValue = preg_replace('/<br(\s+)?\/?>/i', ' ', $fieldValue);
					$fieldValue = str_replace("''", "' '", $fieldValue);
					if (trim($fieldValue)=='') {
						$fieldValue = '';
					} else {
						$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($fieldValue)));
						$expression = $parser->expression();
						$exprEvaluater = new VTFieldExpressionEvaluater($expression);
						$fieldValue = $exprEvaluater->evaluate($entity);
					}
				} else {
					if ($fieldInstance->getFieldDataType() === 'currency') {
						$focus->column_fields[$fieldValue] = $focus->adjustCurrencyField($fieldValue,$focus->column_fields[$fieldValue]);
					}
					if (preg_match('/([^:]+):boolean$/', $fieldValue, $match)) {
						$fieldValue = $match[1];
						if ($fieldValue == 'true') {
							$fieldValue = '1';
						} else {
							$fieldValue = '0';
						}
					}
					if ($fieldInstance->getFieldDataType() === 'date') {
						$date = new DateTimeField($fieldValue);
						$fieldValue = $date->getDisplayDate();
					}
				}

				if ($fieldInstance->getFieldDataType() === 'owner') {
					$userId = getUserId_Ol($fieldValue);
					$groupId = getGrpId($fieldValue);

					if ($userId == 0 && $groupId == 0) {
						$fieldValue = $focus->column_fields[$fieldName];
					} else {
						$fieldValue = ($userId == 0) ? $groupId : $userId;
					}
				}

				$focus->column_fields[$fieldName] = $fieldValue;
				$entity->data[$fieldName] = $fieldValue;
			}

			foreach ($focus->column_fields as $fieldName => $fieldValue) {
				$focus->column_fields[$fieldName] = html_entity_decode($fieldValue, ENT_QUOTES, $default_charset);
			}

			// Convert currency fields to User format before save
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $moduleName);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			$handler = new $handlerClass($webserviceObject,$current_user,$adb,$log);
			$meta = $handler->getMeta();
			$moduleFields = $meta->getModuleFields();
			foreach($moduleFields as $fieldName=>$fieldObj){
				if (in_array($moduleName, array('SalesOrder', 'PurchaseOrder', 'Invoice', 'Quotes')) && ($fieldName == 'hdnSubTotal' || $fieldName == 'hdnGrandTotal')) {
					continue;
				}
				if($fieldObj->getFieldDataType()=="currency"){
					if(!empty($focus->column_fields[$fieldName])){
						$focus->column_fields[$fieldName] = CurrencyField::convertToUserFormat($focus->column_fields[$fieldName],$current_user);
					}
				}
			}
			$hold_ajxaction = $_REQUEST['ajxaction'];
			$_REQUEST['ajxaction'] = 'Workflow';
			$logbg->debug('field values presave: '.print_r($focus->column_fields,true));
			$focus->saveentity($moduleName);
			$_REQUEST['ajxaction'] = $hold_ajxaction;
			$util->revertUser();
			$logbg->debug('field values post: '.print_r($focus->column_fields,true));
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->info('Exiting workflow task VTUpdateFieldsTask');
	}

}

?>