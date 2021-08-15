<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'include/Webservices/CustomerPortalWS.php';
include_once 'include/Webservices/getRecordImages.php';

function vtws_update($element, $user) {
	global $log,$adb;
	if (empty($element['id'])) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}
	$element['id'] = vtws_getWSID($element['id']);
	$idList = vtws_getIdComponents($element['id']);
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $idList[0]);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($element['id']);
	require 'include/Webservices/processAttachments.php';

	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}

	if (!$meta->hasPermission(EntityMeta::$UPDATE, $element['id'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	if (!$meta->exists($idList[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	if ($meta->hasWriteAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
	}

	$referenceFields = $meta->getReferenceFieldDetails();
	$referenceFields['assigned_user_id'] = array('Users', 'Groups');
	foreach ($referenceFields as $fieldName => $details) {
		if (isset($element[$fieldName]) && strlen($element[$fieldName]) > 0) {
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
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to access reference type is denied '.$referenceObject->getEntityName());
			}
		} elseif (isset($element[$fieldName]) && $element[$fieldName] !== null) {
			unset($element[$fieldName]);
		}
	}

	$meta->hasMandatoryFields($element);

	$ownerFields = $meta->getOwnerFields();
	if (is_array($ownerFields)) {
		foreach ($ownerFields as $ownerField) {
			if (isset($element[$ownerField]) && $element[$ownerField]!==null && !$meta->hasAssignPrivilege($element[$ownerField])) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Cannot assign record to the given user');
			}
		}
	}
	// Product line support
	$hrequest = $_REQUEST;
	if (in_array($entityName, getInventoryModules())) {
		if (!empty($element['pdoInformation']) && is_array($element['pdoInformation'])) {
			$elementType = $entityName;
			$elementCRMID = $idList[1];
			include 'include/Webservices/ProductLines.php';
		} else {
			$_REQUEST['action'] = $entityName.'Ajax';
		}
	}
	if ($entityName == 'HelpDesk') {
		//Added to construct the update log for Ticket history
		$colflds = $element;
		list($void, $colflds['assigned_user_id']) = explode('x', $colflds['assigned_user_id']);
		$updlog = HelpDesk::getUpdateLogEditMessage($idList[1], $colflds, 'U');
	}
	$entity = $handler->update($element);
	if ($entityName == 'HelpDesk') {
		$adb->pquery('update vtiger_troubletickets set update_log=? where ticketid=?', array($updlog, $idList[1]));
	}
	VTWS_PreserveGlobal::flush();
	$_REQUEST = $hrequest;
	if (!empty($wsAttachments)) {
		foreach ($wsAttachments as $file) {
			@unlink($file);
		}
	}
	// Dereference WSIDs
	$r = $meta->getReferenceFieldDetails();
	$listofrelfields = array();
	if (!empty($entity['assigned_user_id'])) {
		$r['assigned_user_id'] = array('Users');
		$listofrelfields[] = $entity['assigned_user_id'];
	}
	foreach ($r as $relfield => $mods) {
		if (!empty($entity[$relfield])) {
			$listofrelfields[] = $entity[$relfield];
		}
	}
	if (!empty($listofrelfields)) {
		if ($entityName=='Emails' && $entity['parent_id']!='') {
			unset($listofrelfields['parent_id'], $r['parent_id']);
		}
		$deref = unserialize(vtws_getReferenceValue(serialize($listofrelfields), $user));
		foreach ($r as $relfield => $mods) {
			if (!empty($entity[$relfield]) && !empty($deref[$entity[$relfield]])) {
				$entity[$relfield.'ename'] = $deref[$entity[$relfield]];
			}
		}
		if ($entityName=='Emails' && $entity['parent_id']!='') {
			$entity['parent_idename'] = unserialize(vtws_getReferenceValue(serialize(array($entity['parent_id'])), $user));
		}
	}
	// Add attachment information
	$imgs = $meta->getImageFields();
	if (!empty($imgs)) {
		$imginfo = cbws_getrecordimageinfo($element['id'], $user);
		if ($imginfo['results']>0) {
			foreach ($imgs as $img) {
				if (!empty($entity[$img])) {
					$entity[$img.'imageinfo'] = $imginfo['images'][$img];
				}
			}
		}
	}
	return $entity;
}
?>
