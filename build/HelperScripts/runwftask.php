<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

ini_set('include_path', ini_get('include_path'). PATH_SEPARATOR . '../..');

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once "config.inc.php";
require_once 'include/database/PearDatabase.php';
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
require_once 'modules/Emails/mail.php';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/include.inc';

global $current_user, $adb;
$util = new VTWorkflowUtils();
$adminUser = $util->adminUser();
$current_user = $adminUser;
$tm = new VTTaskManager($adb);
$taskId = 41;
$entityId = '12x136';
$task = $tm->retrieveTask($taskId);
if (!empty($task)) {
	list($moduleId, $crmId) = explode('x', $entityId);
	$query = "select deleted from vtiger_crmentity where crmid={$crmId}";
	$res = $adb->query($query);
	if ($adb->num_rows($res) == 0 || $adb->query_result($res, 0, 0)) {
		echo "Deleted Record\n";
	} else {
		//error_reporting(E_ALL);ini_set('display_errors','on');
		$entity = new VTWorkflowEntity($adminUser, $entityId);
		$task->doTask($entity);
	}
} else {
	echo "Invalid task\n";
}

?>