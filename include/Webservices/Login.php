<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_login($username, $pwd) {
	$user = new Users();
	$userId = $user->retrieve_user_id($username);

	if (empty($userId)) {
		throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'Given user cannot be found');
	}
	$token = vtws_getActiveToken($userId);
	if ($token == null) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDTOKEN, 'Specified token is invalid or expired');
	}

	$accessKey = vtws_getUserAccessKey($userId);
	if ($accessKey == null) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSKEYUNDEFINED, 'Access key for the user is undefined');
	}

	$accessCrypt = md5($token.$accessKey);
	if (!hash_equals($accessCrypt, $pwd)) {
		$userpass = vtws_getUserPasswordFromInput($token, $pwd);
		$user->column_fields['user_name']=$username;
		if ($userpass['token']!=$token || !$user->doLogin($userpass['password'])) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, 'Invalid username or password');
		}
	}
	$user = $user->retrieveCurrentUserInfoFromFile($userId);
	if ($user->status != 'Inactive') {
		cbEventHandler::do_action('corebos.audit.authenticate', array($userId, 'Users', 'Authenticate', $userId, date('Y-m-d H:i:s'), 'webservice'));
		// Recording the login info
		require_once 'modules/Users/LoginHistory.php';
		$loghistory=new LoginHistory();
		$loghistory->user_login($username, Vtiger_Request::get_ip(), date('Y/m/d H:i:s'));
		return $user;
	}
	throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'Given user is inactive');
}

function vtws_getActiveToken($userId) {
	global $adb;
	$result = $adb->pquery('select token from vtiger_ws_userauthtoken where userid=? and expiretime>=?', array($userId, time()));
	if ($result != null && isset($result) && $adb->num_rows($result)>0) {
		return $adb->query_result($result, 0, 'token');
	}
	return null;
}

function vtws_getUserAccessKey($userId) {
	global $adb;
	$result = $adb->pquery('select accesskey from vtiger_users where id=?', array($userId));
	if ($result != null && isset($result) && $adb->num_rows($result)>0) {
		return $adb->query_result($result, 0, 'accesskey');
	}
	return null;
}

function vtws_getUserPasswordFromInput($token, $pwd) {
	$utoken = substr($pwd, 0, strlen($token));
	$upass = substr($pwd, strlen($token));
	return array('token' => $utoken, 'password' => $upass);
}
?>
