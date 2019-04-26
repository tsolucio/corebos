<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailScannerInfo.php';
require_once 'Smarty_setup.php';

$scannername = vtlib_purify($_REQUEST['scannername']);
$scannerinfo = new Vtiger_MailScannerInfo($scannername);

$folderinfo = array();
foreach ($_REQUEST as $key => $value) {
	$matches = array();
	if (preg_match("/folder_([0-9]+)/", vtlib_purify($key), $matches)) {
		$folderinfo[vtlib_purify($value)] = array('folderid'=>$matches[1], 'enabled'=>1);
	}
}
$scannerinfo->enableFoldersForScan($folderinfo);

include 'modules/Settings/MailScanner/MailScannerInfo.php';
?>
