<?php
/***********************************************************************************
 * Copyright 2018 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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

function getBusinessActions($view, $module, $id, $linktype, $user) {
	global $adb, $log, $current_user;

	$tabid = getTabid($module);
	$type = explode(',', $linktype);
	$action = vtlib_purify($view);
	$parameters = ['MODULE' => $module, 'ACTION' => $action];
	$recordId = null;

	//check if the user has access to the specified module
	$types = vtws_listtypes(null, $current_user);
	if (!in_array($module, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	if ($view == 'ListView') {
		$parameters['CATEGORY'] = getParentTab();
	} else {
		if (!empty($id)) {
			$idComponents = vtws_getIdComponents($id);

			$parameters['RECORD'] = $idComponents[1];

			$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$meta = $handler->getMeta();

			if ($meta->hasReadAccess()!==true) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
			}
			if ($module !== $webserviceObject->getEntityName()) {
				throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
			}
			if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
			}
			if (!$meta->exists($idComponents[1])) {
				throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
			}
		}
	}

	$businessActions = Vtiger_Link::getAllByType($tabid, $type, $parameters, $current_user->id, $recordId);
	return json_encode($businessActions);
}
