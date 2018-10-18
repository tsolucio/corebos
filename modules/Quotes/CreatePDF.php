<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'modules/Quotes/QuotePDFController.php';

global $root_directory;

$controller = new Vtiger_QuotePDFController($currentModule);
$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$quote_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
$moduleName = str_replace(' ', '', getTranslatedString('SINGLE_Quotes', $currentModule));
if (isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
	$quote_id = vtlib_purify($_REQUEST['record']);
	$filepath=$root_directory.'cache/'.$quote_id.'_'.$moduleName.'_'.$quote_no.'.pdf';
	$controller->Output($filepath, 'F');
} elseif (isset($purpose) && $purpose == 'webservice') {
	$log->debug('Switched to buffer. Purpose = '. $purpose);
	$PDFBuffer = $controller->Output('', 'S'); // S means send the pdf output in buffer instead of file
} else {
	$controller->Output($moduleName.'_'.$quote_no.'.pdf', 'D');
	exit();
}
?>
