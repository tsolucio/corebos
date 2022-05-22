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
require_once 'config.inc.php';
require_once 'include/Webservices/Utils.php';
require_once 'modules/Users/Users.php';
require_once 'include/Webservices/State.php';
require_once 'include/Webservices/OperationManager.php';
require_once 'include/Webservices/SessionManager.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once 'include/Webservices/VtigerCRMObject.php';
require_once 'include/Webservices/VtigerCRMObjectMeta.php';
require_once 'include/Webservices/DataTransform.php';
require_once 'include/Webservices/WebServiceError.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'include/utils/Session.php';
require_once 'include/logging.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once "include/language/$default_language.lang.php";
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/Users/Users.php';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/include.inc';

/** Workaround to enable capaturing relation query */
// include/RelatedListView.php checks for the existence of this
// function and proxies the call (if exists).
function GetRelatedList($module, $relatedmodule, $focus, $query, $button, $returnset, $id = '', $edit_val = '', $del_val = '') {
	return array('query' => $query);
}

require_once 'config.inc.php';
checkFileAccessForInclusion("include/language/$default_language.lang.php");
require_once "include/language/$default_language.lang.php";

global $logbg, $current_user, $app_strings;
$logbg->debug('WS Cron:: Start processing WSOPerations from the queue');
$adb = PearDatabase::getInstance();
$cbmq = coreBOS_MQTM::getInstance();
$tm = new VTTaskManager($adb);
while ($msg = $cbmq->getMessage('wsOperationChannel', 'wsoperationqueue', 'wsoperationqueue')) {
	$msginfo = json_decode($msg['information'], true);
	$operation = $msginfo['operation'];
	$operationTrackingID = $msginfo['operationTrackingID'];
	$format = $msginfo['format'];
	$input = $msginfo['request'];
	$sessionId = $msginfo['sessionId'];
	$adoptSession = $msginfo['adoptSession'];
	$sessionName = $msginfo['sessionName'];
	$sessionManager = new SessionManager();
	$sessionManager->startSession($sessionId, $adoptSession, $sessionName);
	try {
		$operationManager = new OperationManager($adb, $operation, $format, $sessionManager);
	} catch (WebServiceException $e) {
		$input['WSERROR'] = $e->getMessage();
		vtws_logcalls($input);
		continue;
	}
	try {
		$operationInput = $operationManager->sanitizeOperation($input);
		$includes = $operationManager->getOperationIncludes();
		foreach ($includes as $ind => $path) {
			checkFileAccessForInclusion($path);
			require_once $path;
		}
		$seed_user = new Users();
		$current_user = $seed_user->retrieveCurrentUserInfoFromFile($msg['userid']);
		if (!empty($current_user->language)) {
			$app_strings = return_application_language($current_user->language);
		}
		cbEventHandler::do_action('corebos.audit.action', array((isset($current_user) ? $current_user->id:0), 'Webservice', $operation, 0, date('Y-m-d H:i:s')));
		$rawOutput = $operationManager->runOperation($operationInput, $current_user);
		$rdo = writeOutput($operationManager, $rawOutput, 'return', 'doNothing');
	} catch (WebServiceException $e) {
		$rdo = writeErrorOutput($operationManager, $e, 'return', 'doNothing');
	} catch (Exception $e) {
		$rdo = writeErrorOutput($operationManager, new WebServiceException(WebServiceErrorCode::$INTERNALERROR, 'Unknown Error while processing request'), 'return', 'doNothing');
	}
	coreBOS_Settings::setSetting($operationTrackingID, $rdo);
}
?>
