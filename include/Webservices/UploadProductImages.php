<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

//include 'vtlib/Vtiger/Module.php';
function cbws_uploadProductImages($recordID, $fileData, $user)
{

    global $log, $adb, $root_directory;
	require_once 'include/Webservices/getRecordImages.php';
	


    // How do we separate the product id from 4x 41838 ?
    // Separates into $wsid (module) and $crmid (crmID)
    list($wsid, $crmid) = explode('x', $recordID);

    // Check for maximum of 6 images? Not sure how best to do this
    // use function cbws_getrecordimageinfo($id, $user)
    
	// this breaks....
	 //$check = cbws_getrecordimageinfo($recordID, $user); 
	
	$log->debug("Entering into add uploadProductImages($wsid,$crmid) method.");
	
    // Another is in Utils.php
    // Separates into $idList['0'] (module) and $idList['1'] (crmID)
    $idList = vtws_getIdComponents($recordID); // does the same as above
    //    $idList = vtws_getIdComponents($element['id']);
    //    $webserviceObject = VtigerWebserviceObject::fromId($adb, $idList[0]);
    $webserviceObject = VtigerWebserviceObject::fromId($adb, $idList[0]);
    $handlerPath = $webserviceObject->getHandlerPath();
    $handlerClass = $webserviceObject->getHandlerClass();

    require_once $handlerPath;

    $handler = new $handlerClass($webserviceObject, $user, $adb, $log);
    $meta = $handler->getMeta();
    $entityName = $meta->getObjectEntityName($idList[0]);
    $product = CRMEntity::getInstance('Products');
    $product->retrieve_entity_info($idList[1], 'Products');

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    foreach ($fileData as $fieldname => $imageDetail) {
        $_FILES = array();
        //$imagename = $imageDetail['filename'];
        //$image = $imageDetail['filedata'];
        $filepath = $root_directory . 'storage/' . $imageDetail['filename'];
        file_put_contents($filepath, base64_decode($imageDetail['filedata']));

        //s$imagename = basename($image);
        $_FILES[$fieldname] = array(
            'name' => $imageDetail['filename'],
            'type' => finfo_file($finfo, $filepath),
            'tmp_name' => $filepath,
            'error' => 0,
            'size' => filesize($filepath),
        );
        $product->insertIntoAttachment($crmid, 'Products', true);
		
		// Somehow we need to catch the result here to return it.
		$result = "something or other";
        }

	// here on down is guesswork
	
	// I think here down is error checking but not sure why it is after the event ?
	// Should the insert come after this ?
	
    $types = vtws_listtypes(null, $user);
    if (!in_array($entityName, $types['types'])) {
        throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation is denied");
    }

    if ($entityName !== $webserviceObject->getEntityName()) {
        throw new WebServiceException(WebServiceErrorCode::$INVALIDID, "Id specified is incorrect");
    }

    if (!$meta->hasPermission(EntityMeta::$UPDATE, $element['id'])) {
        throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to read given object is denied");
    }

    if (!$meta->exists($idList[1])) {
        throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, "Record you are trying to access is not found");
    }

    if ($meta->hasWriteAccess() !== true) {
        throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to write is denied");
    }

    $referenceFields = $meta->getReferenceFieldDetails();
    foreach ($referenceFields as $fieldName => $details) {
        if (isset($element[$fieldName]) && strlen($element[$fieldName]) > 0) {
            $ids = vtws_getIdComponents($element[$fieldName]);
            $elemTypeId = $ids[0];
            $elemId = $ids[1];
            $referenceObject = VtigerWebserviceObject::fromId($adb, $elemTypeId);
            if (!in_array($referenceObject->getEntityName(), $details)) {
                throw new WebServiceException(WebServiceErrorCode::$REFERENCEINVALID, "Invalid reference specified for $fieldName");
            }
            if ($referenceObject->getEntityName() == 'Users') {
                if (!$meta->hasAssignPrivilege($element[$fieldName])) {
                    throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Cannot assign record to the given user");
                }
            }
            if (!in_array($referenceObject->getEntityName(), $types['types']) && $referenceObject->getEntityName() != 'Users') {
                throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to access reference type is denied " . $referenceObject->getEntityName());
            }
        } else if (isset($element[$fieldName]) and $element[$fieldName] !== NULL) {
            unset($element[$fieldName]);
        }
    }

    $meta->hasMandatoryFields($element);

    $ownerFields = $meta->getOwnerFields();
    if (is_array($ownerFields) && sizeof($ownerFields) > 0) {
        foreach ($ownerFields as $ownerField) {
            if (isset($element[$ownerField]) && $element[$ownerField] !== null && !$meta->hasAssignPrivilege($element[$ownerField])) {
                throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Cannot assign record to the given user");
            }
        }
    }

    VTWS_PreserveGlobal::flush();
    if (!empty($_FILES)) {
        foreach ($_FILES as $field => $file) {
            unlink($file['tmp_name']);
        }
    }

    // return a result ?
    return $entity;
}

?>
