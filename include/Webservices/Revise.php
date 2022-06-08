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

function vtws_revise($element, $user) {
	global $log, $adb;
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
		}
	}
	//check if the element has mandtory fields filled
	$meta->isUpdateMandatoryFields($element);

	$ownerFields = $meta->getOwnerFields();
	if (is_array($ownerFields)) {
		foreach ($ownerFields as $ownerField) {
			if (isset($element[$ownerField]) && $element[$ownerField]!==null && !$meta->hasAssignPrivilege($element[$ownerField])) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Cannot assign record to the given user');
			}
		}
	}
	//  Product line support
	$hrequest = $_REQUEST;
	if (in_array($entityName, getInventoryModules()) && isset($element['pdoInformation']) && is_array($element['pdoInformation'])) {
		$elementType = $entityName;
		$elementCRMID = $idList[1];
		include 'include/Webservices/ProductLines.php';
	} else {
		$_REQUEST['action'] = $entityName.'Ajax';
	}

	$entity = $handler->revise($element);
	VTWS_PreserveGlobal::flush();
	$_REQUEST = $hrequest;
	if (!empty($wsAttachments)) {
		foreach ($wsAttachments as $file) {
			if (file_exists($file)) {
				@unlink($file);
			}
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
		$deref = unserialize(vtws_getReferenceValue(serialize($listofrelfields), $user));
		foreach ($r as $relfield => $mods) {
			if (!empty($entity[$relfield]) && !empty($deref[$entity[$relfield]])) {
				$entity[$relfield.'ename'] = $deref[$entity[$relfield]];
			}
		}
	}
	// Add attachment information
	$imgs = $meta->getImageFields();
	if (count($imgs)>0) {
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
