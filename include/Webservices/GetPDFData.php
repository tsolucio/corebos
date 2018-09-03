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

function cbws_getpdfdata($id, $user) {
	global $log,$adb;
	$log->debug('Entering function vtws_getpdfdata');

	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($id);
	$types = vtws_listtypes(null, $user);
	if (!in_array($entityName, $types['types'])) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}
	if ($meta->hasReadAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read is denied');
	}

	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}

	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	$idComponents = vtws_getIdComponents($id);
	if (!$meta->exists($idComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	$objectName = $webserviceObject->getEntityName();
	$native_pdf_modules = array('Invoice','Quotes','SalesOrder','PurchaseOrder');
	$custom_pdf_modules = explode(',', GlobalVariable::getVariable('CustomerPortal_PDF_Modules', ''));
	$pdfmodules = array_merge($native_pdf_modules, $custom_pdf_modules);
	if (!in_array($objectName, $pdfmodules)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Only Inventory & GV defined modules support PDF Output.');
	}
	$ids = vtws_getIdComponents($id);

	$document_id = $ids[1];

	$entity = get_module_pdf($objectName, $document_id, $user);

	VTWS_PreserveGlobal::flush();

	$log->debug('Leaving function vtws_getpdfdata');
	return $entity;
}

function get_module_pdf($modulename, $recordid, $user = '') {
	global $log, $current_user;
	if (empty($user)) {
		$user = $current_user;
	}
	$log->debug("Entering function get_module_pdf($recordid)");

	switch (GlobalVariable::getVariable('CustomerPortal_PDF', 'Native', $modulename, $user->id)) {
		case 'PDFMaker':
			$_pdf_data = __cbwsget_pdfmaker_pdf($recordid, $modulename, $user);
			break;
		case 'GenDoc':
			//$_pdf_data = GetGenDocPDFData($modulename, $recordid);
			break;
		case 'Native':
		default:
			$_pdf_data = GetRawPDFData($modulename, $recordid);
			break;
	}

	$recordpdf[0]['recordid'] = $recordid;
	$recordpdf[0]['modulename'] = $modulename;
	$recordpdf[0]['pdf_data'] = base64_encode($_pdf_data);

	$log->debug("Leaving function get_module_pdf($recordid)");
	return $recordpdf;
}

function GetRawPDFData($modulename, $recordid) {
	global $log, $currentModule;

	$log->debug("Entering function GetRawPDFData. Module = $modulename record = $recordid");

	$_REQUEST['record'] = $recordid;
	$_REQUEST['module'] = $modulename;
	$currentModule = $modulename;
	$PDFBuffer = '';
	$purpose = 'webservice';

	if ($modulename=='SalesOrder') {
		require 'modules/'.$modulename.'/CreateSOPDF.php';
	} else {
		require 'modules/'.$modulename.'/CreatePDF.php';
	}

	$log->debug("Leaving function GetRawPDFData. Module = $modulename record = $recordid");
	return $PDFBuffer;
}

function __cbwsget_pdfmaker_pdf($id, $block, $user = '') {
	global $adb, $current_user, $log, $default_language;
	global $currentModule, $mod_strings, $app_strings, $app_list_strings;
	$failure = "%PDF-1.1
%¥±ë

1 0 obj
  << /Type /Catalog
     /Pages 2 0 R
  >>
endobj

2 0 obj
  << /Type /Pages
     /Kids [3 0 R]
     /Count 1
     /MediaBox [0 0 300 144]
  >>
endobj

3 0 obj
  <<  /Type /Page
      /Parent 2 0 R
      /Resources
       << /Font
           << /F1
               << /Type /Font
                  /Subtype /Type1
                  /BaseFont /Times-Roman
               >>
           >>
       >>
      /Contents 4 0 R
  >>
endobj

4 0 obj
  << /Length 55 >>
stream
  BT
    /F1 18 Tf
    0 0 Td
    (ERROR GENERATING PDF) Tj
  ET
endstream
endobj

xref
0 5
0000000000 65535 f 
0000000018 00000 n 
0000000077 00000 n 
0000000178 00000 n 
0000000457 00000 n 
trailer
  <<  /Root 1 0 R
      /Size 5
  >>
startxref
565
%%EOF";
	if (!file_exists('modules/PDFMaker/checkGenerate.php')) {
		return $failure;
	}
	if (empty($user)) {
		$user = $current_user;
	}

	$log->debug('Entering webservice function get_pdfmaker_pdf');

	require_once 'config.inc.php';

	$currentModule = $block;
	$current_language = $default_language;
	$app_strings = return_application_language($current_language);
	$app_list_strings = return_app_list_strings_language($current_language);
	$mod_strings = return_module_language($current_language, $currentModule);

	$sql = 'SELECT a.templateid
		FROM vtiger_pdfmaker AS a
		INNER JOIN vtiger_pdfmaker_settings AS b USING(templateid)
		WHERE a.module=?'; // AND is_portal='1'";
	switch ($currentModule) {
		case 'Quotes':
			$templateid = GlobalVariable::getVariable('CustomerPortal_PDFTemplate_Quote', 0, 'Quotes', $user->id);
			$params = array('Quotes');
			break;
		case 'SalesOrder':
			$templateid = GlobalVariable::getVariable('CustomerPortal_PDFTemplate_SalesOrder', 0, 'SalesOrder', $user->id);
			$params = array('SalesOrder');
			break;
		case 'PurchaseOrder':
			$templateid = GlobalVariable::getVariable('CustomerPortal_PDFTemplate_PurchaseOrder', 0, 'PurchaseOrder', $user->id);
			$params = array('PurchaseOrder');
			break;
		case 'Invoice':
			$templateid = GlobalVariable::getVariable('CustomerPortal_PDFTemplate_Invoice', 0, 'Invoice', $user->id);
			$params = array('Invoice');
			break;
		default:
			$params = array($currentModule);
			break;
	}
	if ($templateid==0) {
		$result = $adb->pquery($sql, $params);
		$templateid = $adb->query_result($result, 0, 'templateid');
	}
	if ($templateid == '') {
		return $failure;
	}

	$_REQUEST['relmodule']= $block;
	$_REQUEST['record']= $id;
	$_REQUEST['commontemplateid']= $templateid;
	if (file_exists('modules/'.$block.'/language/'.$current_user->column_fields['language'].'.lang.php')) {
		$_REQUEST['language'] = $current_user->column_fields['language'];
	} else {
		$_REQUEST['language'] = 'en_us';
	}
	$xx10 = CRMEntity::getInstance($currentModule);
	$xx10->retrieve_entity_info($id, $currentModule);
	$xx10->id = $id;
	include 'modules/PDFMaker/InventoryPDF.php';
	include 'modules/PDFMaker/mpdf/mpdf.php';
	$xx12 = new PDFContent($templateid, $currentModule, $xx10, $_REQUEST['language']);
	$xx13 = $xx12->getContent();
	$xx14 = $xx12->getSettings();
	$xx15 = html_entity_decode($xx13['header'], ENT_COMPAT, 'utf-8');
	$xx16 = html_entity_decode($xx13['body'], ENT_COMPAT, 'utf-8');
	$xx17 = html_entity_decode($xx13['footer'], ENT_COMPAT, 'utf-8');
	if ($xx14['orientation'] == 'landscape') {
		$xx18 = $xx14['format'] . '-L';
	} else {
		$xx18 = $xx14['format'];
	}
	$xx19 = new mPDF('', $xx18, '', 'Arial', $xx14['margin_left'], $xx14['margin_right'], 0, 0, $xx14['margin_top'], $xx14['margin_bottom']);
	@$xx19->SetHTMLHeader($xx15);
	@$xx19->SetHTMLFooter($xx17);
	@$xx19->WriteHTML($xx16);
	$filenamewithpath = 'cache/' . $currentModule . $id . '.pdf';
	$xx19->Output($filenamewithpath);
	$filecontents = file_get_contents($filenamewithpath);
	$log->debug('Exiting webservice function get_pdfmaker_pdf');
	return $filecontents;
}
?>