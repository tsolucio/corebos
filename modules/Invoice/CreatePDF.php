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

$controller = new Vtiger_InvoicePDFController($currentModule);
$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$invoice_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
$moduleName = str_replace(' ', '', getTranslatedString('SINGLE_Invoice', $currentModule));
if (isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
	$id = vtlib_purify($_REQUEST['record']);
	$filepath=$root_directory.'cache/'.$id.'_'.$moduleName.'_'.$invoice_no.'.pdf';
	$controller->Output($filepath, 'F'); //added file name to make it work in IE, also forces the download giving the user the option to save
} elseif (isset($purpose) && $purpose == 'webservice') {
	$log->debug('Switched to buffer. Purpose = '. $purpose);
	$PDFBuffer = $controller->Output('', 'S'); // S means send the pdf output in buffer instead of file
} else {
	$controller->Output($moduleName.'_'.$invoice_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
	exit();
}
?>
