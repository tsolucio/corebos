<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include 'config.inc.php';
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/Webservices/Utils.php';
require_once "modules/Users/Users.php";
require_once "include/Webservices/State.php";
require_once "include/Webservices/OperationManager.php";
require_once "include/Webservices/SessionManager.php";
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once "include/Webservices/VtigerCRMObject.php";
require_once "include/Webservices/VtigerCRMObjectMeta.php";
require_once "include/Webservices/DataTransform.php";
require_once "include/Webservices/WebServiceError.php";
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'include/logging.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once "include/language/$default_language.lang.php";
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Update.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/include.inc';

/*
 * Execute a workflow against a list of CRMIDs
 * workflow: name or ID of the workflow to execute
 * entities: json encoded array of webservice CRMIDs
 */
function cbwsExecuteWorkflow($workflow, $entities, $user) {
	global $adb;
	$result = $adb->pquery('select * from com_vtiger_workflows where workflow_id=? or summary=?', array($workflow, $workflow));
	if (!$result || $adb->num_rows($result)==0) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: workflow');
	}
	$crmids = json_decode($entities, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: entities');
	}
	$util = new VTWorkflowUtils();
	$entityCache = new VTEntityCache($user);
	$wfs = new VTWorkflowManager($adb);
	$workflows = $wfs->getWorkflowsForResult($result);
	$workflow = reset($workflows);
	foreach ($crmids as $crmid) {
		$entityData = $entityCache->forId($crmid);
		if ($workflow->evaluate($entityCache, $entityData->getId())) {
			if (VTWorkflowManager::$ONCE == $workflow->executionCondition) {
				$entity_id = vtws_getIdComponents($entityData->getId());
				$entity_id = $entity_id[1];
				$workflow->markAsCompletedForRecord($entity_id);
			}
			$workflow->performTasks($entityData);
		}
	}
	return true;
}
?>