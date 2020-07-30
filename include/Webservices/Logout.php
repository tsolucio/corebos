<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_logout($sessionId, $user, $SessionManagerClass = 'SessionManager') {
	if (gettype($SessionManagerClass)=='string') {
		$sessionManager = new $SessionManagerClass();
	} else {
		$sessionManager = $SessionManagerClass;
	}
	if (!coreBOS_Session::isSessionStarted()) {
		$sessionManager->startSession($sessionId);
	}
	if (!isset($sessionId) || $sessionId=='' || !$sessionManager->isValid()) {
		return $sessionManager->getError();
	}
	cbEventHandler::do_action('corebos.logout', array($user, $sessionManager, 'webservice'));

	$sessionManager->destroy();
//	$sessionManager->setExpire(1);
	// Recording Logout Info
	require_once 'modules/Users/LoginHistory.php';
	$loghistory=new LoginHistory();
	$loghistory->user_logout($user->user_name, Vtiger_Request::get_ip(), date('Y/m/d H:i:s'));
	return array('message' => 'successfull');
}
?>
