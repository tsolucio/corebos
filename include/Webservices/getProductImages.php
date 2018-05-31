<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
* Allows a webservice client to retrieve the information of the image attachments associated to a product
* Which can then be used with the build/HelperScripts/getImageData.php script to obtain the image
* params:
*   id: webservice product id
* returns json string:
*   results: number of images available
*   images: array
*     name: image name
*     path: image path in application
*     fullpath: image absolute path
*     type: image mime type
*     id: image id
 *************************************************************************************************/

require_once 'include/Webservices/getRecordImages.php';

function cbws_getproductimageinfo($id, $user) {
	global $log, $adb;
	$log->debug("Entering function cbws_getproductimageinfo($id)");

	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($id);
	if ($entityName!='Products') {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Entity ID must be a product');
	}
	$log->debug('Leaving function cbws_getproductimageinfo');
	return cbws_getrecordimageinfo($id, $user);
}
?>