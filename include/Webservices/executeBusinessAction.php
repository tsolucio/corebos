<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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
*************************************************************************************************/

function executeBusinessAction($businessactionid, $context, $user) {
	global $currentModule, $adb, $log;
	$businessactionid = vtws_getWSID($businessactionid);
	$context = json_decode($context, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid context parameter: '.json_last_error_msg());
	}
	$wscrmid = empty($context['ID']) ? (empty($context['RECORDID']) ?  (empty($context['RECORD']) ? 0 : $context['RECORD']) : $context['RECORDID']) : $context['ID'];
	if ($wscrmid==0) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context (no ID)');
	}
	if (strpos($wscrmid, 'x')===false) {
		if (!is_numeric($wscrmid)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context (invalid ID)');
		}
		$ctx_MODULE = getSalesEntityType($wscrmid);
		if (empty($ctx_MODULE)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context (invalid ID)');
		}
		$wscrmid = vtws_getEntityId($ctx_MODULE).'x'.$wscrmid;
		if (empty($context['MODULE'])) {
			$context['MODULE'] = $ctx_MODULE;
			$context['module'] = $ctx_MODULE;
		}
	}
	list($wsid, $crmid) = explode('x', $wscrmid);
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $wscrmid);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($wscrmid);
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}

	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $wscrmid)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	if (!$meta->exists($crmid)) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	$context['ID'] = $context['RECORDID'] = $context['RECORD'] = $crmid;
	$context['id'] = $context['recordid'] = $context['record'] = $crmid;
	if (empty($context['MODULE'])) {
		$context['MODULE'] = $context['module'] = getSalesEntityType($crmid);
	}
	$currentModule = $context['module'];
	if (!isset($context['MODE'])) {
		$context['MODE'] = 'edit';
		$context['mode'] = 'edit';
	}
	//$context['FIELDS']
	$businessAction = CRMEntity::getInstance('BusinessActions');
	list($bawsid, $baid) = explode('x', $businessactionid);
	$businessAction->retrieve_entity_info($baid, 'BusinessActions', false, true, true);
	$ba = $businessAction->column_fields;
	if (strpos(Field_Metadata::MULTIPICKLIST_SEPARATOR.$ba['module_list'].Field_Metadata::MULTIPICKLIST_SEPARATOR, Field_Metadata::MULTIPICKLIST_SEPARATOR.$context['module'].Field_Metadata::MULTIPICKLIST_SEPARATOR)===false) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDMODULE, 'Module not supported by action');
	}
	$strtemplate = new Vtiger_StringTemplate();
	foreach ($context as $key => $value) {
		$strtemplate->assign($key, $value);
	}
	$ba['linkurl'] = $strtemplate->merge($ba['linkurl']);
	$ba['businessactionsid'] = $businessactionid;
	$ba['elementtype_action'] = $ba['linktype'];
	$ba['status'] = $ba['active'];
	$lnk = BusinessActions::convertToObject(BusinessActions::IGNORE_MODULE, $ba);
	if (preg_match("/^block:\/\/(.*)/", $ba['linkurl'], $matches)) {
		return vtlib_process_widget($lnk, $context);
	} else {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: business action (only block detail view widgets supported)');
	}
}