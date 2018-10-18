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

	global $root_directory;
	$types = vtws_listtypes(null, $user);
	if (!in_array($elementType, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation is denied");
	}

	global $log, $adb;
	if (!empty($element['relations'])) {
		$relations=$element['relations'];
		unset($element['relations']);
	}

	if (!empty($element['attachments'])) {
		foreach ($element['attachments'] as $fieldname => $attachment) {
			$filepath = $root_directory.'cache/'.$attachment['name'];
			file_put_contents($filepath, base64_decode($attachment['content']));
			$_FILES[$fieldname] = array(
				'name' => $attachment['name'],
				'type' => $attachment['type'],
				'tmp_name' => $filepath,
				'error' => 0,
				'size' => $attachment['size']
			);
		}
		unset($element['attachments']);
	}

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
	if ($meta->hasWriteAccess() !== true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
	}

	$referenceFields = $meta->getReferenceFieldDetails();
	foreach ($referenceFields as $fieldName => $details) {
		if (!empty($element[$fieldName])) {
			$ids = vtws_getIdComponents($element[$fieldName]);
			$elemTypeId = $ids[0];
			$referenceObject = VtigerWebserviceObject::fromId($adb, $elemTypeId);
			if (!in_array($referenceObject->getEntityName(), $details)) {
				throw new WebServiceException(WebServiceErrorCode::$REFERENCEINVALID, "Invalid reference specified for $fieldName");
			}
			if ($referenceObject->getEntityName() == 'Users') {
				if (!$meta->hasAssignPrivilege($element[$fieldName])) {
					throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Cannot assign record to the given user');
				}
			}
			if (!in_array($referenceObject->getEntityName(), $types['types']) && $referenceObject->getEntityName() != 'Users') {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to access reference type is denied' . $referenceObject->getEntityName());
			}
		} elseif (isset($element[$fieldName]) && $element[$fieldName] !== null) {
			unset($element[$fieldName]);
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
		if (in_array($elementType, getInventoryModules()) && (is_array($element['pdoInformation']))) {
			include 'include/Webservices/ProductLines.php';
		} else {
			$_REQUEST['action'] = $elementType.'Ajax';
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
			vtws_internal_setrelation($newrecid, $modname, $relations);
		}
		VTWS_PreserveGlobal::flush();
		if (!empty($_FILES)) {
			foreach ($_FILES as $field => $file) {
				unlink($file['tmp_name']);
			}
		}
		return $entity;
	} else {
		if (!empty($_FILES)) {
			foreach ($_FILES as $field => $file) {
				unlink($file['tmp_name']);
			}
		}
		return null;
	}
}
?>