<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L.  --  This file is a part of coreBOS
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

/**
 * Get result of processing a Business Map.
 * Most maps are supported by other web service end points. For example, Validations has it's own web service end point
 * and many other maps are just information to be used in the front end application. So, here, we have only a few map types
 * under the same umbrella where we can easily add others in the future if needed.
 * @param integer mapid ID of the map to process
 * @param array parameters set of required parameters for each type of map
 * @return array with the result of processing the map
 */
function cbwsProcessMap($mapid, $parameters, $user) {
	global $adb, $log;
	$bmapid = vtws_getWSID($mapid);
	if ($bmapid===false || $bmapid=='0x0') {
		// we try to search it as a string
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbMap');
		$maprs = $adb->pquery(
			'select cbmapid from vtiger_cbmap inner join '.$crmEntityTable.' on crmid=cbmapid where deleted=0 and mapname=?',
			array($mapid)
		);
		if ($maprs && $adb->num_rows($maprs)>0) {
			$bmapid = vtws_getEntityId('cbMap').'x'.$maprs->fields['cbmapid'];
		}
	}
	$mapid = $bmapid;
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $mapid);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($mapid);
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
	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $mapid)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}
	$mapidComponents = vtws_getIdComponents($mapid);
	if (!$meta->exists($mapidComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}
	$pmap = new cbwsProcessMapWorker($mapidComponents[1], $parameters);
	$pmap->checkPermissions($user); // this throws an exception if there are no permissions
	$pmap->setup();
	$result = $pmap->process();
	$pmap->teardown();
	return $result;
}

class cbwsProcessMapWorker {
	public $maptype;
	public $mapobj;
	public $parameters;
	public $mapmodule;

	public function __construct($mapid, $parameters) {
		$this->parameters = $parameters;
		$this->mapobj = CRMEntity::getInstance('cbMap');
		$this->mapobj->retrieve_entity_info($mapid, 'cbMap');
		$this->maptype = $this->mapobj->column_fields['maptype'];
	}

	public function checkPermissions($user) {
		switch ($this->maptype) {
			case 'Mapping':
				return $this->checkPermissionsMapping($user);
			default:
				return true;
				break;
		}
	}

	public function setup() {
		switch ($this->maptype) {
			case 'Mapping':
			default:
				break;
		}
		return true;
	}

	public function process() {
		switch ($this->maptype) {
			case 'Mapping':
				return $this->processMapping();
			case 'Detail View Layout Mapping':
				return $this->mapobj->DetailViewLayoutMapping();
			case 'ListColumns':
				return $this->mapobj->ListColumns()->getCompleteMapping();
			case 'ApplicationMenu':
				return $this->mapobj->ApplicationMenu($this->parameters);
			case 'FieldDependency':
				return $this->mapobj->FieldDependency();
			default:
				throw new WebServiceException(WebServiceErrorCode::$OPERATIONNOTSUPPORTED, 'business map type not supported');
				break;
		}
	}

	public function teardown() {
		switch ($this->maptype) {
			case 'Mapping':
			default:
				break;
		}
		return true;
	}

	public function processMapping() {
		if (strpos($this->parameters['infields']['record_id'], 'x')>0) {
			list($wsid, $crmid) = explode('x', $this->parameters['infields']['record_id']);
			$this->parameters['infields']['record_id'] = $crmid;
		} else {
			$crmid = $this->parameters['infields']['record_id'];
		}
		$focus = CRMEntity::getInstance($this->mapmodule);
		$focus->retrieve_entity_info($crmid, $this->mapmodule);
		if (empty($this->parameters['outfields'])) {
			$out = array();
		} elseif (count($this->parameters['outfields'])==1 && !empty($this->parameters['outfields']['record_id'])) {
			if (strpos($this->parameters['outfields']['record_id'], 'x')>0) {
				list($wsid, $crmidO) = explode('x', $this->parameters['outfields']['record_id']);
				$this->parameters['outfields']['record_id'] = $crmidO;
			} else {
				$crmidO = $this->parameters['outfields']['record_id'];
			}
			$moduleO = getSalesEntityType($crmidO);
			$focusO = CRMEntity::getInstance($moduleO);
			$focusO->retrieve_entity_info($crmidO, $moduleO);
			$out = $focusO->column_fields;
		} else {
			$out = $this->parameters['outfields'];
		}
		return $this->mapobj->Mapping($focus->column_fields, $out);
	}

	private function checkPermissionsMapping($user) {
		global $adb, $log;
		if (empty($this->parameters['infields']) || empty($this->parameters['infields']['record_id'])) {
			throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, 'infields parameter or record_id parameter missing');
		}
		$crmid = vtws_getWSID($this->parameters['infields']['record_id']);
		$webserviceObject = VtigerWebserviceObject::fromId($adb, $crmid);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($crmid);
		$this->mapmodule = $entityName;
		$types = vtws_listtypes(null, $user);
		if (!in_array($entityName, $types['types'])) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied: infields module');
		}
		if ($meta->hasReadAccess()!==true) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read is denied: infields module');
		}
		if ($entityName !== $webserviceObject->getEntityName()) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect: infields record');
		}
		if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $crmid)) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied: infields record');
		}
		$crmidComponents = vtws_getIdComponents($crmid);
		if (!$meta->exists($crmidComponents[1])) {
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found: infields record');
		}
		return true;
	}
}