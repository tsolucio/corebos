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

global $fileId, $default_charset;

$templateid = vtlib_purify($_REQUEST['record']);

$result = $adb->pquery('SELECT filename,filetype, data FROM vtiger_wordtemplates WHERE templateid=?', array($templateid));
if ($result && $adb->num_rows($result) == 1) {
	$fileType = $adb->query_result($result, 0, 'filetype');
	$name = $adb->query_result($result, 0, 'filename');
	//echo 'filetype is ' .$fileType;
	$fileContent = $adb->query_result($result, 0, 'data');
	$size = $adb->query_result($result, 0, 'filesize');
	$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
	header("Content-type: $fileType");
	//header("Content-length: $size");
	header('Cache-Control: private');
	header("Content-Disposition: attachment; filename=$name");
	header('Content-Description: PHP Generated Data');
	echo base64_decode($fileContent);
} else {
	echo 'Record does not exist.';
}
?>