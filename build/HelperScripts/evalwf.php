<?php
require 'build/cbHeader.inc';
require_once("include/HTTP_Session/Session.php");
require_once 'include/Webservices/Utils.php';
require_once("modules/Users/Users.php");
require_once("include/Webservices/State.php");
require_once("include/Webservices/OperationManager.php");
require_once("include/Webservices/SessionManager.php");
require_once("include/Zend/Json.php");
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once('include/logging.php');
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once "include/language/$default_language.lang.php";
require_once 'include/Webservices/Retrieve.php';
require_once('include/Webservices/Update.php');
require_once('modules/Emails/mail.php');
require_once('include/events/SqlResultIterator.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowManager.inc');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once 'modules/com_vtiger_workflow/include.inc';
global $currentModule; $currentModule = 'HelpDesk';
$util = new VTWorkflowUtils();
$adminUser = $util->adminUser();
$entityCache = new VTEntityCache($adminUser);
$wfs = new VTWorkflowManager($adb);
$result = $adb->query("select workflow_id, module_name, summary, test, execution_condition, type
						from com_vtiger_workflows where workflow_id=9");
$workflows = $wfs->getWorkflowsForResult($result);
$workflow = $workflows[9];
$entityId = '17x111';
$entityData = $entityCache->forId($entityId);
$eval = $workflow->evaluate($entityCache, $entityId);
var_dump($eval);

require 'build/cbFooter.inc';
?>
