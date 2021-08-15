<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************ */
require_once 'include/Webservices/SetRelation.php';

function vtws_create($elementType, $element, $user) {
	static $vtws_create_cache = array();

	$types = vtws_listtypes(null, $user);
	if (!in_array($elementType, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	global $log, $adb;
	if (!empty($element['relations'])) {
		$relations=$element['relations'];
		unset($element['relations']);
	}
	require 'include/Webservices/processAttachments.php';

	// Cache the instance for re-use
	if (!isset($vtws_create_cache[$elementType]['webserviceobject'])) {
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $elementType);
		$vtws_create_cache[$elementType]['webserviceobject'] = $webserviceObject;
	} else {
		$webserviceObject = $vtws_create_cache[$elementType]['webserviceobject'];
	}

	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	if (!$meta->hasCreateAccess()) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
	}

	$referenceFields = $meta->getReferenceFieldDetails();
	$referenceFields['assigned_user_id'] = array('Users', 'Groups');
	foreach ($referenceFields as $fieldName => $details) {
		if (!empty($element[$fieldName])) {
			$element[$fieldName] = vtws_getWSID($element[$fieldName]);
			$ids = vtws_getIdComponents($element[$fieldName]);
			$elemTypeId = $ids[0];
			$referenceObject = VtigerWebserviceObject::fromId($adb, $elemTypeId);
			if (!in_array($referenceObject->getEntityName(), $details)) {
				throw new WebServiceException(WebServiceErrorCode::$REFERENCEINVALID, "Invalid reference specified for $fieldName");
			}
			if ($referenceObject->getEntityName() == 'Users' && !$meta->hasAssignPrivilege($element[$fieldName])) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Cannot assign record to the given user');
			}
			if (!in_array($referenceObject->getEntityName(), $types['types']) && $referenceObject->getEntityName() != 'Users') {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to access reference type is denied' . $referenceObject->getEntityName());
			}
		} elseif (isset($element[$fieldName]) && $element[$fieldName] !== null) {
			unset($element[$fieldName]);
		}
	}

	foreach ($meta->getModuleFields() as $fieldName => $webserviceField) {
		$dval = $webserviceField->getDefault();
		if (!isset($element[$fieldName]) && !empty($dval)) {
			$element[$fieldName] = $dval;
		}
	}

	if ($meta->hasMandatoryFields($element)) {
		$ownerFields = $meta->getOwnerFields();
		if (is_array($ownerFields)) {
			foreach ($ownerFields as $ownerField) {
				if (isset($element[$ownerField]) && $element[$ownerField] !== null && !$meta->hasAssignPrivilege($element[$ownerField])) {
					throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Cannot assign record to the given user');
				}
			}
		}
		// Product line support
		$hrequest = $_REQUEST;
		if (in_array($elementType, getInventoryModules())) {
			if (!empty($element['pdoInformation']) && is_array($element['pdoInformation'])) {
				include 'include/Webservices/ProductLines.php';
			} else {
				$_REQUEST['action'] = $elementType.'Ajax';
			}
		}
		if ($elementType == 'HelpDesk') {
			//Added to construct the update log for Ticket history
			$colflds = $element;
			list($void, $colflds['assigned_user_id']) = explode('x', $colflds['assigned_user_id']);
			$grp_name = fetchGroupName($colflds['assigned_user_id']);
			$assigntype = ($grp_name != '') ? 'T' : 'U';
			$updlog = HelpDesk::getUpdateLogCreateMessage($colflds, $grp_name, $assigntype);
		}
		$entity = $handler->create($elementType, $element);
		if ($elementType == 'HelpDesk') {
			list($wsid,$newrecid) = vtws_getIdComponents($entity['id']);
			$adb->pquery('update vtiger_troubletickets set update_log=? where ticketid=?', array($updlog, $newrecid));
		}
		// Establish relations
		if (!empty($relations)) {
			list($wsid,$newrecid) = vtws_getIdComponents($entity['id']);
			$modname = $meta->getEntityName();
			if (is_string($relations)) { // they sent a comma-separated list
				$relations = explode(',', $relations);
			}
			vtws_internal_setrelation($newrecid, $modname, $relations, $types);
		}
		VTWS_PreserveGlobal::flush();
		$_REQUEST = $hrequest;
		if (!empty($wsAttachments)) {
			foreach ($wsAttachments as $file) {
				@unlink($file);
			}
		}
		return $entity;
	} else {
		if (!empty($wsAttachments)) {
			foreach ($wsAttachments as $file) {
				@unlink($file);
			}
		}
		return null;
	}
}
?>