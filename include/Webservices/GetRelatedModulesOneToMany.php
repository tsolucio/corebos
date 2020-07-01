<?php
/***********************************************************************************
 * Copyright 2020 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
 * You can copy, adapt and distribute the work under the 'Attribution-NonCommercial-ShareAlike'
 * Vizsage Public License (the 'License'). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  'AS IS' BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/
require_once 'include/Webservices/Utils.php';

/* Given a module, get all the one to many related modules */
function GetRelatedModulesOneToMany($module, $user) {
	global $adb, $log;
	// pickup meta data of module
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $module);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	require_once $handlerPath;
	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$mainModule = $meta->getTabName();  // normalize module name
	// check modules
	if (!$meta->isModuleEntity()) {
		throw new WebServiceException('INVALID_MODULE', "Given module ($module) cannot be found");
	}

	// check permission on module
	$entityName = $meta->getEntityName();
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation on module ($mainModule) is denied");
	}
	$result = $adb->pquery(
		'SELECT relmodule,fieldname
			from vtiger_fieldmodulerel
			join vtiger_field on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid
			where module=? and relmodule in (select name from vtiger_tab where presence=0)',
		array($module)
	);
	$modules=array();
	while ($rel = $result->fetchRow()) {
		$modules[] = array(
			'label' => getTranslatedString($rel['relmodule'], $rel['relmodule']),
			'name' => $rel['relmodule'],
			'field' => $rel['fieldname'],
		);
	}
	return $modules;
}
