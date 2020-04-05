<?php
/***********************************************************************************
 * Copyright 2019 Spike Associates  --  This file is a part of coreBOS.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/
include_once 'include/Webservices/QueryParser.php';
include_once 'include/Webservices/VtigerModuleOperation.php';
include_once 'include/DatabaseUtil.php';

function showqueryfromwsdoquery($query, $user) {
	global $adb, $log;

	$webserviceObject = VtigerWebserviceObject::fromQuery($adb, $query);
	$types = vtws_listtypes(null, $user);
	if (!in_array($webserviceObject->getEntityName(), $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$query = trim($query, ';').';';
	$sql = trim($handler->wsVTQL2SQL($query, $meta, $queryRelatedModules), ';');
	$rdo = array('sql' => $sql);
	if (stripos($sql, ' LIMIT ') > 0) {
		$q = substr($sql, 0, stripos($sql, ' LIMIT ')).' limit 1';
	} else {
		$q = $sql.' limit 1';
	}
	$rs = $adb->query($q);
	if ($rs) {
		$rdo['status'] = 'OK';
		$rdo['msg'] = getTranslatedString('SQLTESTOK', 'cbQuestion');
	} else {
		$rdo['status'] = 'NOK';
		$rdo['msg'] = getTranslatedString('SQLTESTNOK', 'cbQuestion').' '.$adb->getErrorMsg();
	}
	return $rdo;
}