<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_extendSession($SessionManagerClass = 'SessionManager') {
	global $adb,$API_VERSION,$application_unique_key;
	if (isset($_SESSION['authenticated_user_id']) && $_SESSION['app_unique_key'] == $application_unique_key) {
		$userId = $_SESSION['authenticated_user_id'];
		if (gettype($SessionManagerClass)=='string') {
			$sessionManager = new $SessionManagerClass();
		} else {
			$sessionManager = $SessionManagerClass;
		}
		$sessionManager->set('authenticatedUserId', $userId);
		$crmObject = VtigerWebserviceObject::fromName($adb, 'Users');
		return array(
			'sessionName' => $sessionManager->getSessionId(),
			'userId' => vtws_getId($crmObject->getEntityId(), $userId),
			'version' => $API_VERSION,
			'vtigerVersion' => vtws_getVtigerVersion(),
		);
	} else {
		throw new WebServiceException(WebServiceErrorCode::$AUTHFAILURE, 'Authentication Failed');
	}
}
?>