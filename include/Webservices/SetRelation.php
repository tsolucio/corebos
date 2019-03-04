<?php
/***********************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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
require_once 'include/Webservices/Query.php';

function vtws_setrelation($relateThisId, $withTheseIds, $user) {
	global $log,$adb;
	list($moduleId, $elementId) = vtws_getIdComponents($relateThisId);
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $moduleId);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$moduleName = $meta->getObjectEntityName($relateThisId);

	$types = vtws_listtypes(null, $user);
	if (!in_array($moduleName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	if ($moduleName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}

	if (!$meta->hasPermission(EntityMeta::$UPDATE, $relateThisId)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	if (!$meta->exists($elementId)) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	if ($meta->hasWriteAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
	}

	vtws_internal_setrelation($elementId, $moduleName, $withTheseIds);
	VTWS_PreserveGlobal::flush();
	return true;
}

function vtws_internal_setrelation($elementId, $moduleName, $withTheseIds) {
	global $adb;
	$withTheseIds = (array)$withTheseIds;
	$focus = CRMEntity::getInstance($moduleName);
	foreach ($withTheseIds as $withThisId) {
		list($withModuleId, $withElementId) = vtws_getIdComponents($withThisId);
		$rsmodname = $adb->pquery('select name from vtiger_ws_entity where id=?', array($withModuleId));
		$withModuleName = $adb->query_result($rsmodname, 0, 0);
		relateEntities($focus, $moduleName, $elementId, $withModuleName, $withElementId);
	}
}

