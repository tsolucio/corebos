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

$currentModule = vtlib_purify($_REQUEST['module']);
$controller = new Vtiger_QuotePDFController($currentModule);
$controller->loadRecord(vtlib_purify($_REQUEST['record']));

$filenameid = vtlib_purify($_REQUEST['record']);
$quote_no = getModuleSequenceNumber($currentModule, vtlib_purify($_REQUEST['record']));
if (empty($filenameid)) {
	$filenameid = time();
}
$filepath=$root_directory.'storage/Quote_'.$quote_no.'.pdf';
$controller->Output($filepath, 'F');

echo '<script>window.history.back();</script>';
exit();
?>
