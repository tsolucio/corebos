<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/Update.php';
require_once 'modules/Users/Users.php';

/**
 * @param string $id
 */
function changeAccessKey($id, $user) {
	vtws_preserveGlobal('current_user', $user);
	if (strpos($id, 'x')>0) {
		$idComponents = vtws_getIdComponents($id);
	} else {
		$idComponents = array(vtws_getEntityId('Users'), $id);
	}
	if ($idComponents[1] == $user->id || is_admin($user)) {
		if (!Users::is_ActiveUserID($idComponents[1])) {
			VTWS_PreserveGlobal::flush();
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSER, vtws_getWebserviceTranslatedString('LBL_'.WebServiceErrorCode::$INVALIDUSER));
		}
		$user = new Users();
		$user->retrieveCurrentUserInfoFromFile($idComponents[1]);
		$user->createAccessKey();
		return array(
			'message' => 'Changed Access Key successfully. Save your new Access Key, you will not see it again.',
			'accesskey' => getSingleFieldValue('vtiger_users', 'accesskey', 'id', $idComponents[1]),
		);
	} else {
		VTWS_PreserveGlobal::flush();
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'You do not have permission to change the Access Key.');
	}
}
?>