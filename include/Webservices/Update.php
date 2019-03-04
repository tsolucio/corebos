<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_update($element, $user) {
	global $log,$adb,$root_directory;
	$idList = vtws_getIdComponents($element['id']);
	if ((vtws_getEntityId('Calendar')==$idList[0] || vtws_getEntityId('Events')==$idList[0]) && getSalesEntityType($idList[1])=='cbCalendar') {
		$idList[0] = vtws_getEntityId('cbCalendar') . 'x' . $idList[1];
	}
	if (vtws_getEntityId('cbCalendar')==$idList[0] && getSalesEntityType($idList[1])=='Calendar') {
		$rs = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($idList[1]));
		if ($rs && $adb->num_rows($rs)==1) {
			if ($adb->query_result($rs, 0, 0)=='Task') {
				$idList[0] = vtws_getEntityId('Calendar') . 'x' . $idList[1];
			} else {
				$idList[0] = vtws_getEntityId('Events') . 'x' . $idList[1];
			}
		}
	}
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $idList[0]);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($element['id']);

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
	foreach ($referenceFields as $fieldName => $details) {
		if (isset($element[$fieldName]) && strlen($element[$fieldName]) > 0) {
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
	if (in_array($entityName, getInventoryModules()) && isset($element['pdoInformation']) && (is_array($element['pdoInformation']))) {
		$elementType = $entityName;
		include_once 'include/Webservices/ProductLines.php';
	} else {
		$_REQUEST['action'] = $entityName.'Ajax';
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
	if (!empty($_FILES)) {
		foreach ($_FILES as $file) {
			unlink($file['tmp_name']);
		}
	}
	return $entity;
}
?>
