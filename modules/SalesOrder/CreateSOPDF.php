<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'modules/SalesOrder/SalesOrderPDFController.php';
global $current_user,$root_directory;

if (!empty($_REQUEST['idlist'])) {
	include_once 'include/utils/pdfConcat.php';
	$ids = explode(';', trim($_REQUEST['idlist'], ';'));
	$file2merge=array();
	$path = $root_directory.'cache/'.$current_user->id.'batchpdf';
	@mkdir($path);
	foreach ($ids as $id) {
		$controller = new Vtiger_SalesOrderPDFController($currentModule);
		$controller->loadRecord(vtlib_purify($id));
		$controller->Output($path.'/SObatch'.$id.'.pdf', 'F');
		$file2merge[]=$path.'/SObatch'.$id.'.pdf';
	}
	$pdf = new concat_pdf();
	$pdf->setFiles($file2merge);
	$pdf->concat();
	$moduleName = str_replace(' ', '', getTranslatedString('SINGLE_SalesOrder', $currentModule));
	$pdf->Output($moduleName.'sBatch.pdf', 'D');
	@unlink($path.'/'.$moduleName.'sBatch.pdf');
	exit();
} else {
	$controller = new Vtiger_SalesOrderPDFController($currentModule);
	$controller->loadRecord(vtlib_purify($_REQUEST['record']));
	$salesorder_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
	$moduleName = str_replace(' ', '', getTranslatedString('SINGLE_SalesOrder', $currentModule));
	if (isset($purpose) && $purpose == 'webservice') {
		$PDFBuffer = $controller->Output('', 'S'); // S means send the pdf output in buffer instead of file
	} else {
		$controller->Output($moduleName.'_'.$salesorder_no.'.pdf', 'D'); // file name forces the download giving the user the option to save
		exit();
	}
}
?>
