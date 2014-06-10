<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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
*************************************************************************************************/
/*
* David VALMINOS 12/11/2009
* Allows a webservice client to retrieve PDF data from a coreBOS module (Invoice, Quotes, ...)
********************************************************/

function cbws_getpdfdata($id, $user){
	global $log,$adb;
	$log->debug("Entering function vtws_getpdfdata");

	$webserviceObject = VtigerWebserviceObject::fromId($adb,$id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	
	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($id);
	$types = vtws_listtypes(null, $user);
	if(!in_array($entityName,$types['types'])){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
	}
	if($meta->hasReadAccess()!==true){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to write is denied");
	}

	if($entityName !== $webserviceObject->getEntityName()){
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Id specified is incorrect");
	}
	
	if(!$meta->hasPermission(EntityMeta::$RETRIEVE,$id)){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to read given object is denied");
	}
	
	$idComponents = vtws_getIdComponents($id);
	if(!$meta->exists($idComponents[1])){
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND,"Record you are trying to access is not found");
	}

	$objectName = $webserviceObject->getEntityName();
	if(!in_array($objectName, array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Only Inventory modules support PDF Output.");
	}
	$ids = vtws_getIdComponents($id);

	$document_id = $ids[1];

	$entity = get_module_pdf($objectName, $document_id);

	VTWS_PreserveGlobal::flush();

	$log->debug("Leaving function vtws_getpdfdata");
	return $entity;
}

function get_module_pdf($modulename, $recordid) {
	global $adb, $log;
	$log->debug("Entering function get_module_pdf($recordid)");

	$_pdf_data = GetRawPDFData($modulename, $recordid);

    $recordpdf[0]["recordid"] = $recordid;
    $recordpdf[0]["modulename"] = $modulename;
    $recordpdf[0]["pdf_data"] = base64_encode($_pdf_data) ;

	$log->debug("Leaving function get_module_pdf($recordid)");
    return $recordpdf;
}

function GetRawPDFData($modulename, $recordid) {
	global $log, $currentModule;

	$log->debug("Entering function GetRawPDFData. Module = $modulename record = $recordid");

	$_REQUEST['record'] = $recordid;
	$_REQUEST['module'] = $modulename;
	$currentModule = $modulename;
	$PDFBuffer = "";
	$purpose = "webservice";

	if ($modulename=='SalesOrder') {
		require("modules/".$modulename."/CreateSOPDF.php");
	} else {
		require("modules/".$modulename."/CreatePDF.php");
	}

	$log->debug("Leaving function GetRawPDFData. Module = $modulename record = $recordid");
	return $PDFBuffer;
}
?>