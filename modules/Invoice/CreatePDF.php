<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'modules/Invoice/InvoicePDFController.php';
global $currentModule,$root_directory;
$moduleName = str_replace(' ', '', getTranslatedString('SINGLE_Invoice', $currentModule));
if (!empty($_REQUEST['idlist'])) {
	include_once 'include/utils/pdfConcat.php';
	$ids = explode(';', trim($_REQUEST['idlist'], ';'));
	$file2merge=array();
	$path = $root_directory.'cache/'.$current_user->id.'batchpdf';
	@mkdir($path);
	foreach ($ids as $id) {
		$controller = new Vtiger_InvoicePDFController($currentModule);
		$controller->loadRecord(vtlib_purify($id));
		$controller->Output($path.'/'.$moduleName.$id.'.pdf', 'F');
		$file2merge[]=$path.'/'.$moduleName.$id.'.pdf';
	}
	$pdf = new concat_pdf();
	$pdf->setFiles($file2merge);
	$pdf->concat();
	$pdf->Output($moduleName.'.pdf', 'D');
	@unlink($path.'/'.$moduleName.'.pdf');
	exit();
} else {
	$controller = new Vtiger_InvoicePDFController($currentModule);
	$controller->loadRecord(vtlib_purify($_REQUEST['record']));
	$invoice_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
	if (isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
		$id = vtlib_purify($_REQUEST['record']);
		$filepath=$root_directory.'cache/'.$id.'_'.$moduleName.'_'.$invoice_no.'.pdf';
		$controller->Output($filepath, 'F'); //added file name to make it work in IE, also forces the download giving the user the option to save
	} elseif (isset($purpose) && $purpose == 'webservice') {
		$PDFBuffer = $controller->Output('', 'S'); // S means send the pdf output in buffer instead of file
	} else {
		$controller->Output($moduleName.'_'.$invoice_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
		exit();
	}
}
?>
