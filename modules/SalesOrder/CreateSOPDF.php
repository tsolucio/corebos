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

$currentModule = vtlib_purify($_REQUEST['module']);
$controller = new Vtiger_SalesOrderPDFController($currentModule);
$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$salesorder_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
$moduleName = str_replace(' ', '', getTranslatedString('SINGLE_SalesOrder', $currentModule));
if (isset($purpose) && $purpose == 'webservice') {
	$PDFBuffer = $controller->Output('', 'S'); // S means send the pdf output in buffer instead of file
} else {
	$controller->Output($moduleName.'_'.$salesorder_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
	exit();
}
?>
