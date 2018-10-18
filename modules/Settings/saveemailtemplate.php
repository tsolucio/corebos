<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';

global $log;
$db = PearDatabase::getInstance();
$folderName = vtlib_purify($_REQUEST['foldername']);
$templateName = vtlib_purify($_REQUEST['templatename']);
$templateid = vtlib_purify($_REQUEST['templateid']);
$description = vtlib_purify($_REQUEST['description']);
$subject = vtlib_purify($_REQUEST['subject']);
$body = vtlib_purify($_REQUEST['body']);
$emailfrom = vtlib_purify($_REQUEST['emailfrom']);

if (!empty($templateid)) {
	$log->info('the templateid is set');
	$adb->pquery(
		'update vtiger_emailtemplates set foldername =?, templatename =?, subject =?, description =?, body =?, sendemailfrom=? where templateid =?',
		array($folderName, $templateName, $subject, $description, $body, $emailfrom, $templateid)
	);
	$log->info('about to invoke the detailviewemailtemplate file');
} else {
	$templateid = $db->getUniqueID('vtiger_emailtemplates');
	$adb->pquery(
		'insert into vtiger_emailtemplates (foldername, templatename, subject, description, body, deleted, templateid, sendemailfrom) values (?,?,?,?,?,?,?,?)',
		array($folderName, $templateName, $subject, $description, $body, 0, $templateid, $emailfrom)
	);
	$log->info('added to the db the emailtemplate');
}
header('Location:index.php?module=Settings&action=detailviewemailtemplate&parenttab=Settings&templateid='.urlencode($templateid));
?>