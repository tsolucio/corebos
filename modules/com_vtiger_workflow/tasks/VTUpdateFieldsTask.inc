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
		$breaks = array('<br />','<br>','<br/>');
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
			$fieldValueMapping = json_decode($this->field_value_mapping,true);
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
				$fieldtype = $fieldInstance->getFieldDataType();
				if ($fieldValueType == 'fieldname' && !preg_match('/\((\w+) : \(([_\w]+)\) (.+)\)/', $fieldValue)) {
					if ($fieldtype === 'currency' or $fieldtype === 'double') {
						$focus->column_fields[$fieldValue] = $focus->adjustCurrencyField($fieldValue,$focus->column_fields[$fieldValue],$handlerMeta->getTabId());
					}
					$fieldValue = $focus->column_fields[$fieldValue];
				} elseif ($fieldValueType == 'expression' || ($fieldValueType == 'fieldname' && preg_match('/\((\w+) : \(([_\w]+)\) (.+)\)/', $fieldValue))) {
					require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
					$fieldValue = preg_replace('/<br(\s+)?\/?>/i', ' ', $fieldValue);
					if (trim($fieldValue)=='') {
						$fieldValue = '';
					} else {
						$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($fieldValue)));
						$expression = $parser->expression();
						$exprEvaluater = new VTFieldExpressionEvaluater($expression);
						$fieldValue = $exprEvaluater->evaluate($entity);
					}
				} else {
					if ($fieldtype === 'currency' or $fieldtype === 'double') {
						$focus->column_fields[$fieldValue] = $focus->adjustCurrencyField($fieldValue,$focus->column_fields[$fieldValue],$handlerMeta->getTabId());
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
					if (in_array($fieldInstance->getUIType(), array(19,20,21))) {
						$fieldValue = str_ireplace($breaks, "\n", $fieldValue);
					}
				}

				if ($fieldInstance->getFieldDataType() === 'owner') {
					$userId = getUserId_Ol($fieldValue);
					$groupId = getGrpId($fieldValue);

					if ($userId == 0 && $groupId == 0) {
						$fieldValue = $focus->column_fields[$fieldName];
					} else {
						$userEntityId=vtws_getEntityId('Users').'x';
						$groupEntityId=vtws_getEntityId('Groups').'x';
						$fieldValue = ($userId == 0) ? $groupEntityId.$groupId : $userEntityId.$userId;
					}
				}

				$focus->column_fields[$fieldName] = $fieldValue;
				$entity->data[$fieldName] = $fieldValue;
			}
			$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields,$handlerMeta);
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$_REQUEST['ajxaction'] = 'Workflow';
			$logbg->debug('field values presave: '.print_r($focus->column_fields,true));
			$focus->saveentity($moduleName);
			$_REQUEST['ajxaction'] = $hold_ajxaction;
			$util->revertUser();
			$logbg->debug('field values postsave: '.print_r($focus->column_fields,true));
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->info('Exiting workflow task VTUpdateFieldsTask');
	}

}

?>