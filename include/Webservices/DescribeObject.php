<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
function vtws_describe($elementType, $user) {
	include_once 'include/Webservices/GetFilterFields.php';
	include_once 'include/Webservices/getRelatedModules.php';
	global $log, $adb;
	$modules = explode(',', $elementType);
	$rdo = array();
	$types = vtws_listtypes(null, $user);
	foreach ($modules as $elementType) {
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $elementType);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		if (!in_array($elementType, $types['types']) && $elementType!='Users') {
			if (count($modules)==1) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
			} else {
				continue;
			}
		}
		$rdo[$elementType] = $handler->describe($elementType);
		$rdo[$elementType]['filterFields']=vtws_getfilterfields($elementType, $user);
		try {
			$rdo[$elementType]['relatedModules']=getRelatedModulesInfomation($elementType, $user);
		} catch (\Throwable $th) {
			$rdo[$elementType]['relatedModules']=array();
		}
	}
	VTWS_PreserveGlobal::flush();
	if (count($modules)==1) {
		return $rdo[$elementType];
	} else {
		return $rdo;
	}
}
?>