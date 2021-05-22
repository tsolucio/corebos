<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/QueryParser.php';

$vtwsQueryHandler = '';

function vtws_query($q, $user, $emptyCache = false) {
	global $log, $adb, $vtwsQueryHandler;
	static $vtws_query_cache = array();
	if ($emptyCache) {
		foreach ($vtws_query_cache as $cacheinfo) {
			$cacheinfo['handler']->emptyCache();
		}
		$vtws_query_cache = array();
		VtigerWebserviceObject::emptyCache();
		return array();
	}
	// Cache the instance for re-use
	$moduleRegex = "/[fF][rR][Oo][Mm]\s+([^\s;]+)/";
	$moduleName = '';
	if (preg_match($moduleRegex, $q, $m)) {
		$moduleName = trim($m[1]);
	}

	if (!isset($vtws_query_cache[$moduleName]['webserviceobject'])) {
		$webserviceObject = VtigerWebserviceObject::fromQuery($adb, $q);
		$vtws_query_cache[$moduleName]['webserviceobject'] = $webserviceObject;
	} else {
		$webserviceObject = $vtws_query_cache[$moduleName]['webserviceobject'];
	}

	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	// Cache the instance for re-use
	if (!isset($vtws_query_cache[$moduleName]['handler'])) {
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$vtws_query_cache[$moduleName]['handler'] = $handler;
	} else {
		$handler = $vtws_query_cache[$moduleName]['handler'];
	}

	// Cache the instance for re-use
	if (!isset($vtws_query_cache[$moduleName]['meta'])) {
		$meta = $handler->getMeta();
		$vtws_query_cache[$moduleName]['meta'] = $meta;
	} else {
		$meta = $vtws_query_cache[$moduleName]['meta'];
	}

	$types = vtws_listtypes(null, $user);
	if ($webserviceObject->getEntityName() != 'Users') {
		if (!in_array($webserviceObject->getEntityName(), $types['types'])) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
		}
		if (!$meta->hasReadAccess()) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read is denied');
		}
	}
	VTWS_PreserveGlobal::flush();
	$vtwsQueryHandler = $handler;
	return $handler->query($q);
}

function vtwsQueryWithTotal($q, $user) {
	global $vtwsQueryHandler;
	return array(
		'wsresult' => vtws_query($q, $user),
		'wsmoreinfo' => array(
			'totalrows' => $vtwsQueryHandler->getQueryTotalRows()
		),
	);
}
?>