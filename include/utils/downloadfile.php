<?php
/********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $adb, $fileId, $default_charset, $app_strings;

function downloadErrorFile($error) {
	$msg = getTranslatedString($error, 'Documents');
	header('Content-type: text/plain');
	header('Pragma: public');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private');
	header('Content-Description: File Transfer');
	header('Content-length: '.strlen($msg));
	header('Content-Disposition: attachment; filename="error.txt"');
	echo $msg;
}

$attachmentsid = vtlib_purify($_REQUEST['fileid']);
$entityid = vtlib_purify($_REQUEST['entityid']);
$deletecheck = false;
if (!empty($entityid)) {
	$deletecheck = $adb->pquery('SELECT deleted FROM vtiger_crmobject WHERE crmid=?', array($entityid));
}
if (!empty($deletecheck) && $adb->query_result($deletecheck, 0, 'deleted') == 1) {
	downloadErrorFile('LBL_RECORD_DELETE');
} else {
	$result = $adb->pquery('SELECT * FROM vtiger_attachments WHERE attachmentsid=?', array($attachmentsid));
	if ($result && $adb->num_rows($result) == 1) {
		$fileType = @$adb->query_result($result, 0, 'type');
		$name = @$adb->query_result($result, 0, 'name');
		$filepath = @$adb->query_result($result, 0, 'path');
		$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
		$saved_filename = $attachmentsid.'_'.$name;
		if (file_exists($filepath.$saved_filename)) {
			$disk_file_size = filesize($filepath.$saved_filename);
			$filesize = $disk_file_size + ($disk_file_size % 1024);
			$fileContent = fread(fopen($filepath.$saved_filename, 'r'), $filesize);
			header('Content-type: '.$fileType);
			header('Pragma: public');
			header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private');
			header('Content-Description: File Transfer');
			header('Content-length: '.$disk_file_size);
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename="'.$name.'"');
			echo $fileContent;
		} else {
			downloadErrorFile('FILE_HAS_NO_DATA');
		}
	} else {
		downloadErrorFile('LBL_RECORD_NOT_FOUND');
	}
}
die();
?>
