<?php
/***********************************************************************************
 * Copyright 2012-2018 JPL TSolucio, S.L.  --  This file is a part of coreBOS
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
/*
 * Apply current application validations to an array of field values for the module
 * param: context: a JSON object of values to validate
 *        context must contain
 *           a key 'record' which must be a valid ID for the module if we are editing
 *           a key 'module' which is the module to validate the values against
 *        for inventory modules the 'pdoInformation' array format must be sent
 *  {"module":"Accounts","record":"","cbcustominfo1":"","cbcustominfo2":"","accountname":"nom e x","account_no":"AUTO GEN ON SAVE","website":"","phone":"","tickersymbol":"","fax":"","account_name":"","account_id":"","otherphone":"","employees":"22","email1":"","email2":"","ownership":"","industry":"Apparel","rating":"--None--","accounttype":"","siccode":"","emailoptout":false,"annual_revenue":"0","assigntype":"U","assigned_user_id":"1","assigned_group_id":"3","notify_owner":false,"cf_718":"","cf_719":"0","cf_720":"0","cf_721":"0","cf_722":"","cf_723":"","cf_724":"","cf_725":"","cf_726":false,"cf_727":"","cf_728":"","cf_729":"one","cf_730":"oneone","cf_731":"oneoneone","cf_732[]":"","bill_street":"","ship_street":"","bill_pobox":"","ship_pobox":"","bill_city":"","ship_city":"","bill_state":"","ship_state":"","bill_code":"","ship_code":"","bill_country":"","ship_country":"","description":""}
 */
function cbwsValidateInformation($context, $user) {
	global $log,$adb;
	if (empty($context)) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter');
	}
	$screen_values = json_decode($context, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter');
	}
	if (empty($screen_values['module']) || !isset($screen_values['record'])) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter');
	}
	$types = vtws_listtypes(null, $user);
	if (!in_array($screen_values['module'], $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}
	$_REQUEST['module'] = $screen_values['module'];
	$_REQUEST['record'] = $screen_values['record'];
	$_REQUEST['structure'] = $context;
	if (!empty($screen_values['record'])) {
		if (strpos($screen_values['record'], 'x')===false) {
			$wsrecord = vtws_getEntityId($screen_values['module']).'x'.$screen_values['record'];
		} else {
			$wsrecord = $_REQUEST['record'];
			list($wsid, $_REQUEST['record']) = explode('x', $_REQUEST['record']);
		}
		list($moduleId, $elementId) = vtws_getIdComponents($wsrecord);
		$webserviceObject = VtigerWebserviceObject::fromId($adb, $moduleId);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();

		require_once $handlerPath;

		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$meta = $handler->getMeta();
		if ($_REQUEST['module'] !== $webserviceObject->getEntityName()) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
		}
		if (!$meta->hasPermission(EntityMeta::$UPDATE, $wsrecord)) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
		}
		if (!$meta->exists($elementId)) {
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
		}
	}
	// Product line support
	$elementType = $_REQUEST['module'];
	if (in_array($elementType, getInventoryModules()) && isset($element['pdoInformation']) && (is_array($element['pdoInformation']))) {
		include_once 'include/Webservices/ProductLines.php';
	}
	include_once 'modules/cbMap/processmap/Validations.php';
	$validation = Validations::processAllValidationsFor($_REQUEST['module']);
	VTWS_PreserveGlobal::flush();
	return $validation;
}
