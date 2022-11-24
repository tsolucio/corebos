<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
  PUSH ALONG FLOW WEB SERVICE
 *************************************************************************************************/
require_once 'modules/cbProcessFlow/cbProcessFlow.php';

function cbwsPushAlongFlow($pflowid, $contextid, $user) {
	global $adb, $log;
	vtws_preserveGlobal('current_language', $user->language);
	vtws_preserveGlobal('current_user', $user);
	if (substr($contextid, 0, 1)=='{' && substr($contextid, -1)=='}') { // it is a json string of values
		$context = json_decode($contextid, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid Process Flow context');
		}
		$contextid = $context['record'];
	} else {
		$context = '';
	}
	$pflowid = vtws_getWSID($pflowid);
	$contextid = vtws_getWSID($contextid);
	if (empty($pflowid)) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Process Flow ID specified is incorrect');
	}
	if (empty($contextid)) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Context ID specified is incorrect');
	}
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $pflowid);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($pflowid);
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied: insufficient access to Process Flow');
	}
	if ($entityName !== $webserviceObject->getEntityName() || $entityName!='cbProcessFlow') {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect or not a Process Flow record');
	}

	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $pflowid)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given Process Flow object is denied');
	}

	$pfComponents = vtws_getIdComponents($pflowid);
	if (!$meta->exists($pfComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Process Flow Record you are trying to access is not found');
	}
	////
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $contextid);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($contextid);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied: insufficient access to Context');
	}
	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect or not a Context record');
	}

	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $contextid)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given Context object is denied');
	}

	$ctxfComponents = vtws_getIdComponents($contextid);
	if (!$meta->exists($ctxfComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Context Record you are trying to access is not found');
	}

	$ret = array(
		'graph' => getTranslatedString('LBL_NO_DATA'),
		'functionname' => '',
		'fieldname' => '',
		'fieldtype' => '',
		'module' => '',
		'record' => '',
	);
	$recid = $ctxfComponents[1];
	$pflowid = $pfComponents[1];
	$rs = $adb->pquery(
		'select pffield, pfcondition
		from vtiger_cbprocessflow
		inner join vtiger_crmentity on crmid=cbprocessflowid
		where deleted=0 and cbprocessflowid=?',
		array($pflowid)
	);
	if (!$rs || $adb->num_rows($rs)==0) {
		return $ret;
	}
	$pfcondition = $rs->fields['pfcondition'];
	if (!empty($pfcondition) && !coreBOS_Rule::evaluate($pfcondition, $recid)) {
		return $ret;
	}
	$pffield = $rs->fields['pffield'];
	$queryGenerator = new QueryGenerator($entityName, $user);
	$queryGenerator->setFields(array($pffield));
	$queryGenerator->addCondition('id', $recid, 'e', $queryGenerator::$AND);
	$query = $queryGenerator->getQuery();
	$rs = $adb->query($query);
	$pfcolumn = getColumnnameByFieldname(getTabId($entityName), $pffield);
	$fromstate = $rs->fields[$pfcolumn];
	$graph = cbProcessFlow::getDestinationStatesGraph($pflowid, $fromstate, $recid, true, $context);
	if ($graph=='') {
		return $ret;
	}
	$mod = Vtiger_Module::getInstance($entityName);
	$fld = Vtiger_Field::getInstance($pffield, $mod);
	$ret['graph'] = $graph;
	$ret['functionname'] = 'processflowmoveto'.$pflowid;
	$ret['fieldname'] = $fld->name;
	$ret['fieldtype'] = $fld->uitype;
	$ret['module'] = $entityName;
	$ret['record'] = $contextid;
	VTWS_PreserveGlobal::restore('current_language');
	return $ret;
}
