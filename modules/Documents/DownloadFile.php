<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';

global $adb, $fileId, $current_user;

$fileid = vtlib_purify($_REQUEST['fileid']);
$folderid = vtlib_purify($_REQUEST['folderid']);

$returnmodule='Documents';
$noteQuery = $adb->pquery('select crmid from vtiger_seattachmentsrel where attachmentsid = ?', array($fileid));
$noteid = $adb->query_result($noteQuery, 0, 'crmid');
$result = $adb->pquery('SELECT * FROM vtiger_notes WHERE notesid = ? and folderid= ?', array($noteid, $folderid));
if ($result && $adb->num_rows($result) == 1) {
	$fileType = @$adb->query_result($result, 0, 'filetype');
	$name = @$adb->query_result($result, 0, 'filename');
	$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
	$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid = ?', array($fileid));
	$filepath = $adb->query_result($pathQuery, 0, 'path');

	$saved_filename = $fileid.'_'.$name;
	if (!$filepath.$saved_filename) {
		$saved_filename = $fileid.'_'.$name;
	}
	$filesize = filesize($filepath.$saved_filename);
	if (!fopen($filepath.$saved_filename, 'r')) {
		echo 'unable to open file';
		$log->debug('Unable to open file');
	} else {
		$fileContent = fread(fopen($filepath.$saved_filename, 'r'), $filesize);
	}
	if ($fileContent != '') {
		$log->debug('About to update download count');
		$rs = $adb->pquery('select filedownloadcount from vtiger_notes where notesid= ?', array($fileid));
		$download_count = $adb->query_result($rs, 0, 'filedownloadcount') + 1;
		$res=$adb->pquery('update vtiger_notes set filedownloadcount= ? where notesid= ?', array($download_count, $fileid));
	}

	header("Content-type: $fileType");
	header("Content-length: $filesize");
	header('Cache-Control: private');
	header("Content-Disposition: attachment; filename=\"$name\"");
	header('Content-Description: PHP Generated Data');
	echo $fileContent;
} else {
	echo 'Record does not exist.';
}
?>