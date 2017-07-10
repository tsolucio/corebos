<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : coreBOS Rule
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

class coreBOS_Rule {

	public static $supportedBusinessMaps = array('Condition Query','Condition Expression');

	static public function evaluate($conditionid,$contextid) {
		global $log,$adb,$current_user;
		// check that $contextid is correct
		$webserviceObject = VtigerWebserviceObject::fromId($adb,$contextid);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();

		require_once $handlerPath;

		$handler = new $handlerClass($webserviceObject,$current_user,$adb,$log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($contextid);
		if (!$meta->hasPermission(EntityMeta::$RETRIEVE,$contextid)) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to read given object is denied");
		}

		$idComponents = vtws_getIdComponents($contextid);
		if (!$meta->exists($idComponents[1])) {
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND,"Record you are trying to access is not found");
		}

		// check that cbmapid is correct and load it
		if (preg_match('/^[0-9]+x[0-9]+$/',$conditionid)) {
			list($cbmapws,$conditionid) = explode('x',$conditionid);
		}
		if (is_numeric($conditionid)) {
			$cbmap = cbMap::getMapByID($conditionid);
		} else {
			$cbmap = cbMap::getMapByName($conditionid);
		}
		if (empty($cbmap) or !in_array($cbmap->column_fields['maptype'],self::$supportedBusinessMaps)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_BUSINESSMAP,"Valid Business Map not found.");
		}

		// do calculation
		switch ($cbmap->column_fields['maptype']) {
			case 'Condition Query':
				$ruleinfo = $cbmap->ConditionQuery($idComponents[1]);
				break;
			case 'Condition Expression':
			default:
				$ruleinfo = $cbmap->ConditionExpression($contextid);
				break;
		}
		return $ruleinfo;
	}

}