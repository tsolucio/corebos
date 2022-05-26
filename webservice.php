<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/** Workaround to enable capaturing relation query */
// include/RelatedListView.php checks for the existence of this
// function and proxies the call (if exists).
function GetRelatedList($module, $relatedmodule, $focus, $query, $button, $returnset, $id = '', $edit_val = '', $del_val = '') {
	return array('query' => $query);
}

require_once 'config.inc.php';
require_once 'include/utils/Session.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/State.php';
require_once 'include/Webservices/OperationManager.php';
require_once 'include/Webservices/SessionManager.php';
require_once 'include/logging.php';
checkFileAccessForInclusion("include/language/$default_language.lang.php");
require_once "include/language/$default_language.lang.php";
include_once 'include/integrations/saml/saml.php';

$API_VERSION = '0.22';
$adminid = Users::getActiveAdminId();
if (!GlobalVariable::getVariable('Webservice_Enabled', 1, 'Users', $adminid) || coreBOS_Settings::getSetting('cbSMActive', 0)) {
	echo 'Webservice - Service is not active';
	return;
}
// Full CORS support: preflight options call support
// Access-Control headers are received during OPTIONS requests
if (isset($_SERVER['REQUEST_METHOD'])) {
	$cors_enabled_domains = GlobalVariable::getVariable('Webservice_CORS_Enabled_Domains', '', 'Users', $adminid);
	if (isset($_SERVER['HTTP_ORIGIN']) && !empty($cors_enabled_domains)) {
		$parse = parse_url($_SERVER['HTTP_ORIGIN']);
		if ($cors_enabled_domains=='*' || strpos($cors_enabled_domains, $parse['host'])!==false) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
		}
	}
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	}
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	}
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		exit(0);
	}
}

global $seclog, $log;
$seclog = LoggerManager::getLogger('SECURITY');
$log = LoggerManager::getLogger('webservice');

function getRequestParamsArrayForOperation($operation) {
	global $operationInput;
	return $operationInput[$operation];
}

// some frameworks (namely angularjs and polymer) send information in application/json format, we try to adapt to those system with the next two if
if (empty($_REQUEST)) {
	$data = json_decode(file_get_contents('php://input'));
	if (is_object($data) && !empty($data->operation)) {
		$_POST = get_object_vars($data);  // only post is affected by this
		$_REQUEST = $_POST;
	}
}
$operation = vtws_getParameter($_REQUEST, 'operation');
$operation = strtolower($operation);
$format = vtws_getParameter($_REQUEST, 'format', 'json');
$sessionId = vtws_getParameter($_REQUEST, 'sessionName');
$mode = vtws_getParameter($_REQUEST, 'mode', '');

$sessionManager = new SessionManager();

$saml = new corebos_saml();
if ($saml->isActiveWS() && !empty($saml->samlclient) && ($mode!='' || ($operation=='logout' && !empty($sessionId)))) {
	$sessionManager->startSession();
	if (!empty($sessionManager->get('samlUserdata'))) {
		$saml->authenticateWS($sessionManager, $API_VERSION, $mode);
	} else {
		if ($operation=='logout' || $mode=='slo') {
			$saml->logoutWS($sessionId, $sessionManager->get('authenticatedUserId'));
		} elseif ($mode=='acs') {
			$saml->acs($sessionManager, $API_VERSION, $mode);
		} elseif ($mode=='metadata') {
			$saml->metadata();
		} else {
			$rturl = vtws_getParameter($_REQUEST, 'RTURL', '');
			$saml->login($mode.($rturl=='' ? '' : '&RTURL='.$rturl));
		}
	}
	die();
}

try {
	$operationManager = new OperationManager($adb, $operation, $format, $sessionManager);
} catch (WebServiceException $e) {
	$operationManager = new OperationManager($adb, 'getchallenge', 'json', null);
	writeErrorOutput($operationManager, $e);
	die();
}

try {
	if (!$sessionId || strcasecmp($sessionId, 'null')===0) {
		$sessionId = null;
	}

	$input = $operationManager->getOperationInput();
	$adoptSession = false;
	$sessionName = null;
	if (strcasecmp($operation, 'extendsession')===0) {
		if (isset($input['operation'])) {
			// Workaround fix for PHP 5.3.x: $_REQUEST doesn't have PHPSESSID
			$sessionName = coreBOS_Session::getSessionName();
			if (isset($_REQUEST[$sessionName])) {
				$sessionId = vtws_getParameter($_REQUEST, $sessionName);
			} elseif (isset($_COOKIE[$sessionName])) {
				$sessionId = vtws_getParameter($_COOKIE, $sessionName);
			} elseif (isset($_REQUEST['PHPSESSID'])) {
				$sessionId = vtws_getParameter($_REQUEST, 'PHPSESSID');
			} else {
				// NOTE: Need to evaluate for possible security issues
				$sessionId = vtws_getParameter($_COOKIE, 'PHPSESSID');
			}
			$adoptSession = true;
		} else {
			writeErrorOutput($operationManager, new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'Authentication required'));
			return;
		}
	}
	$sid = $sessionManager->startSession($sessionId, $adoptSession, $sessionName);

	if (!$sessionId && !$operationManager->isPreLoginOperation()) {
		writeErrorOutput($operationManager, new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'Authentication required'));
		return;
	}

	if (!$sid) {
		writeErrorOutput($operationManager, $sessionManager->getError());
		return;
	}

	$userid = $sessionManager->get('authenticatedUserId');
	if (!empty($userid)) {
		$seed_user = new Users();
		$current_user = $seed_user->retrieveCurrentUserInfoFromFile($userid);
		if (!empty($current_user->language)) {
			$app_strings = return_application_language($current_user->language);
		}
	} else {
		$current_user = null;
	}
	vtws_logcalls($_REQUEST);
	if (!empty($input['cbwsOptions'])) {
		$cbwsOptions = json_decode($input['cbwsOptions'], true);
		if (json_last_error() === JSON_ERROR_NONE) {
			$queableCommands = vtws_getQueableCommands();
			if (isset($cbwsOptions['launchfromqueue']) && $cbwsOptions['launchfromqueue']==1 && in_array($operation, $queableCommands)) {
				$delay = (empty($cbwsOptions['queuedelay']) ? 0 : (int)$cbwsOptions['queuedelay']);
				$cbmq = coreBOS_MQTM::getInstance();
				$opID = uniqid('', true);
				$msg = json_encode([
					'operation' => $operation,
					'operationTrackingID' => $opID,
					'format' => $format,
					'request' => $input,
					'sessionId' => $sessionId,
					'adoptSession' => $adoptSession,
					'sessionName' => $sessionName,
				]);
				$cbmq->sendMessage('wsOperationChannel', 'wsoperationqueue', 'wsoperationqueue', 'Data', '1:M', 0, Field_Metadata::FAR_FAR_AWAY_FROM_NOW, $delay, $userid, $msg);
				writeOutput($operationManager, ['operationTrackingID' => $opID]);
				exit(0);
			}
		}
		unset($cbwsOptions, $input['cbwsOptions']);
	}

	$operationInput = $operationManager->sanitizeOperation($input);
	$includes = $operationManager->getOperationIncludes();

	foreach ($includes as $ind => $path) {
		checkFileAccessForInclusion($path);
		require_once $path;
	}
	cbEventHandler::do_action('corebos.audit.action', array((isset($current_user) ? $current_user->id:0), 'Webservice', $operation, 0, date('Y-m-d H:i:s')));
	$rawOutput = $operationManager->runOperation($operationInput, $current_user);
	writeOutput($operationManager, $rawOutput);
} catch (WebServiceException $e) {
	writeErrorOutput($operationManager, $e);
} catch (Exception $e) {
	writeErrorOutput($operationManager, new WebServiceException(WebServiceErrorCode::$INTERNALERROR, 'Unknown Error while processing request'));
}
?>
