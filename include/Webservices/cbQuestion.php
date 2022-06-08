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
include_once 'modules/cbQuestion/cbQuestion.php';

/*
 * Get answer from the question
 * qid: ID of the question
 */
function cbwsGetAnswer($qid, $params, $user) {
	global $adb, $log;
	$result_array = array();
	$qid = explode(',', $qid);
	if (is_string($params) && substr($params, 0, 1)=='{') {
		$params = json_decode($params, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$params = [];
		}
	}
	foreach ((array)$qid as $id) {
		$qwsid = vtws_getWSID($id);
		if ($qwsid===false || $qwsid=='0x0') {
			// we try to search it as a string
			$qrs = $adb->pquery(
				'select cbquestionid from vtiger_cbquestion inner join vtiger_crmentity on crmid=cbquestionid where deleted=0 and qname=?',
				array($id)
			);
			if ($qrs && $adb->num_rows($qrs)>0) {
				$qwsid = vtws_getEntityId('cbQuestion').'x'.$qrs->fields['cbquestionid'];
			}
		}
		$id = $qwsid;
		$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();

		require_once $handlerPath;

		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($id);
		$types = vtws_listtypes(null, $user);
		if ($entityName!='cbQuestion' || !in_array($entityName, $types['types'])) {
			$result_array[] = new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
			continue;
		}
		if ($meta->hasReadAccess()!==true) {
			$result_array[] = new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read is denied');
			continue;
		}
		if ($entityName !== $webserviceObject->getEntityName()) {
			$result_array[] = new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
			continue;
		}
		if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
			$result_array[] = new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
			continue;
		}
		$qidComponents = vtws_getIdComponents($id);
		if (!$meta->exists($qidComponents[1])) {
			$result_array[] = new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
			continue;
		}
		$result_array[] = cbQuestion::getAnswer($qidComponents[1], (empty($params[$id]) ? $params : $params[$id]));
	}
	if (count($result_array) == 1) {
		$return = reset($result_array);
		if (is_a($return, 'WebServiceException')) {
			throw $return;
		} else {
			return $return;
		}
	} else {
		return $result_array;
	}
}