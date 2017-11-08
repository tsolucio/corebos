<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
$check=$_REQUEST['check'];
global $default_charset;
$id='';
if ($_REQUEST['check']== 'reportCheck') {
	$reportName = vtlib_purify($_REQUEST['reportName']);
	$reportID = (isset($_REQUEST['reportid']) ? vtlib_purify($_REQUEST['reportid']) : 0);
	$sSQL = 'select 1 from vtiger_report where reportname=?';
	$params = array(trim($reportName));
	if (!empty($reportID)) {
		$sSQL .= ' and reportid != ?';
		$params[] = $reportID;
	}
	$sqlresult = $adb->pquery($sSQL,$params);
	echo $adb->num_rows($sqlresult);
} else if ($_REQUEST['check']== 'folderCheck') {
	$folderName = function_exists('iconv') ? @iconv('UTF-8',$default_charset, $_REQUEST['folderName']) : $_REQUEST['folderName'];
	$folderName = str_replace(array("'",'"'),'',$folderName);
	if ($folderName == "" || !$folderName) {
		echo "999";
	} else {
		$SQL = 'select * from vtiger_reportfolder where foldername=?';
		$sqlresult = $adb->pquery($SQL, array(trim($folderName)));
		$id = $adb->query_result($sqlresult,0,"folderid");
		echo trim($adb->num_rows($sqlresult)."::".$id);
	}
}

?>
