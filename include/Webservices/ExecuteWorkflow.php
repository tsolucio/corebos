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
	return cbwsExecuteWorkflowWithContext($workflow, $entities, '[]', $user);
}

/*
 * Execute a workflow against a list of CRMIDs with a given context
 * workflow: name or ID of the workflow to execute
 * entities: json encoded array of webservice CRMIDs
 * context: json encoded array of context variables
 */
function cbwsExecuteWorkflowWithContext($workflow, $entities, $context, $user) {
	global $adb;
	$result = $adb->pquery('select * from com_vtiger_workflows where (workflow_id=? or summary=?) and active=?', array($workflow, $workflow, 'true'));
	if (!$result || $adb->num_rows($result)==0) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: workflow');
	}
	$crmids = json_decode($entities, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid entities parameter: '.json_last_error_msg());
	}
	$ctx = json_decode($context, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid context parameter: '.json_last_error_msg());
	}
	$entityCache = new VTEntityCache($user);
	$wfs = new VTWorkflowManager($adb);
	$workflows = $wfs->getWorkflowsForResult($result);
	$workflow = reset($workflows);
	$workflow_mod = $workflow->moduleName; // it return module from workflow
	$types = vtws_listtypes(null, $user);
	if (!in_array($workflow_mod, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied for module');
	}
	$errortasks = array();
	foreach ($crmids as $crmid) {
		$entityData = $entityCache->forId($crmid);
		$modPrefix = $entityData->getModuleName(); // it return module from webservice
		if ($workflow_mod == $modPrefix) { // compare workflow module with webservice module to execute
			if ($workflow->isCompletedForRecord($crmid) || isPermitted($workflow_mod, 'DetailView', $crmid)=='no' || isPermitted($workflow_mod, 'Save', $crmid)=='no') {
				$errortasks[$crmid] = "Permission to access $crmid is denied or workflow already applied";
				continue;
			}
			if ($workflow->evaluate($entityCache, $entityData->getId())) {
				try {
					if ($workflow->activeWorkflow()) {
						$workflow->performTasks($entityData, $ctx, true);
					}
					if (VTWorkflowManager::$ONCE == $workflow->executionCondition) {
						$workflow->markAsCompletedForRecord($crmid);
					}
				} catch (WebServiceException $e) {
					$errortasks[$crmid] = $e->getMessage();
				}
			}
		} else {
			$errortasks[$crmid] = "Workflow and record ($crmid) modules do not match";
		}
	}
	if (!empty($errortasks)) {
		throw new WebServiceException(WebServiceErrorCode::$WORKFLOW_TASK_FAILED, json_encode($errortasks));
	}
	return true;
}
?>