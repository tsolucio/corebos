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
 *************************************************************************************************/

function cbws_uploadProductImages($recordID, $fileData, $user) {
	global $log, $adb, $root_directory;

	$idList = vtws_getIdComponents($recordID);
	$webserviceObject = VtigerWebserviceObject::fromId($adb, $recordID);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($recordID);
	$types = vtws_listtypes(null, $user);
	if ($entityName != 'Products' || !in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to perform the operation is denied");
	}

	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, "Id specified is incorrect");
	}

	if (!$meta->hasPermission(EntityMeta::$UPDATE, $recordID)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to read given object is denied");
	}

	if (!$meta->exists($idList[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, "Record you are trying to access is not found");
	}

	if ($meta->hasWriteAccess() !== true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to write is denied");
	}

	$crmid = $idList[1];
	// Check for maximum number of images
	$maximages = GlobalVariable::getVariable('Product_Maximum_Number_Images', 6, 'Products');
	$rsimg = $adb->pquery('select count(*) as cnt
		from vtiger_attachments
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
		inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
		where deleted=0 and vtiger_crmentity.setype LIKE "Products Image" and vtiger_seattachmentsrel.crmid=?', array($crmid));
	$numimages = (int)$adb->query_result($rsimg, 0, 'cnt');
	if ($numimages >= $maximages) {
		return array(
			'Error' => '1',
			'ErrorStr' => 'Maximum number of images has been reached',
			'FileName' => array(),
			'ImagesAtStart' => $numimages,
			'NumberToAdd' => count($fileData),
			'ImagesAtFinish' => $numimages,
			'MaxImages' => $maximages,
		);
	}

	$product = CRMEntity::getInstance('Products');
	$product->retrieve_entity_info($idList[1], 'Products');

	$log->debug("Entering into add uploadProductImages($recordID) method.");

	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$myResult = array();
	$newnumimages = $numimages;
	foreach ($fileData as $imageDetail) {
		$_FILES = array();
		$filepath = $root_directory . 'cache/' . basename($imageDetail['name']);
		file_put_contents($filepath, base64_decode($imageDetail['content']));

		$_FILES['imagename'] = array(
			'name' => $imageDetail['name'],
			'type' => finfo_file($finfo, $filepath),
			'tmp_name' => $filepath,
			'error' => 0,
			'size' => filesize($filepath),
		);
		$product->insertIntoAttachment($crmid, 'Products', true);
		unlink($filepath);
		$myResult['FileName'][] = $imageDetail['name'];
		$newnumimages++;
		if ($newnumimages >= $maximages) {
			break;
		}
	}
	$rsimg = $adb->pquery('select count(*) as cnt
		from vtiger_attachments
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
		inner join vtiger_seattachmentsrel on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid
		where deleted=0 and vtiger_crmentity.setype LIKE "Products Image" and vtiger_seattachmentsrel.crmid=?', array($crmid));
	$finalnumimages = $adb->query_result($rsimg, 0, 'cnt');

	// Check that final total of images equals the start number plus the file count
	if ($finalnumimages == ($numimages + count($fileData))) {
		$myResult['Error'] = '0';
		$myResult['ImagesAtStart'] = $numimages;
		$myResult['NumberToAdd'] = count($fileData);
		$myResult['ImagesAtFinish'] = $newnumimages;
		$myResult['MaxImages'] = $maximages;
	} else { // If not return the images actually inserted and an error code
		$myResult['Error'] = '1';
		$myResult['ErrorStr'] = 'Maximum number of images has been reached';
		$myResult['ImagesAtStart'] = $numimages;
		$myResult['NumberToAdd'] = count($fileData);
		$myResult['ImagesAtFinish'] = $newnumimages;
		$myResult['MaxImages'] = $maximages;
	}

	return $myResult;
}

?>
