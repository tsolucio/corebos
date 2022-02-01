<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM.
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
*************************************************************************************************
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/cbMap/cbRule.php';

/**
 * coreBOS Rule Evaluation
 * @param integer conditionid: ID of the map
 * @param array context: variables for the rule evaluation
 * @return mixed result of the evaluation of the rule
 */
function cbws_cbRule($conditionid, $context, $user) {
	global $adb, $log;
	$mapid = vtws_getWSID($conditionid);
	if ($mapid===false || $mapid=='0x0') {
		// we try to search it as a string
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbMap');
		$maprs = $adb->pquery(
			'select cbmapid from vtiger_cbmap inner join '.$crmEntityTable.' on crmid=cbmapid where deleted=0 and mapname=?',
			array($conditionid)
		);
		if ($maprs && $adb->num_rows($maprs)>0) {
			$mapid = vtws_getEntityId('cbMap').'x'.$maprs->fields['cbmapid'];
		}
	}
	$conditionid = $mapid;
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $conditionid);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($conditionid);
	$types = vtws_listtypes(null, $user);
	if ($entityName!='cbMap' || !in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}
	if ($meta->hasReadAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read is denied');
	}
	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}
	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $conditionid)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}
	$idComponents = vtws_getIdComponents($conditionid);
	if (!$meta->exists($idComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}
	if (is_string($context) && substr($context, 0, 1)=='{') {
		$context = json_decode($context, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Invalid Rule context');
		}
	}
	return coreBOS_Rule::evaluate($idComponents[1], $context);
}