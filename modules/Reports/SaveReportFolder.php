<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Reports/Reports.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

global $adb, $default_charset;
Vtiger_Request::validateRequest();
$local_log = LoggerManager::getLogger('index');
$focus = new Reports();

$rfid = isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : 0;
$mode = vtlib_purify($_REQUEST['savemode']);
$foldername = vtlib_purify($_REQUEST['foldername']);
$foldername = function_exists('iconv') ? @iconv('UTF-8', $default_charset, $foldername) : $foldername;
$folderdesc = vtlib_purify($_REQUEST['folderdesc']);
$foldername = str_replace('*amp*', '&', $foldername);
$folderdesc = str_replace('*amp*', '&', $folderdesc);

if ($mode=='Save') {
	if (empty($rfid)) {
		$sql = 'INSERT INTO vtiger_reportfolder (foldername,description,state) VALUES (?,?,?)';
		$sql_params = array(trim($foldername), $folderdesc, 'CUSTOMIZED');
		$result = $adb->pquery($sql, $sql_params);
		if (!$result) {
			$_REQUEST['del_denied'] = getTranslatedString('LBL_ERROR_WHILE_INSERTING_RECORD', 'Reports');
		}
		$_REQUEST['file'] = 'ListView';
		include 'modules/Reports/ListView.php';
	}
} elseif ($mode=='Edit') {
	if (!empty($rfid)) {
		$sql = 'update vtiger_reportfolder set foldername=?, description=? where folderid=?';
		$params = array(trim($foldername), $folderdesc, $rfid);
		$result = $adb->pquery($sql, $params);
		if (!$result) {
			$_REQUEST['del_denied'] = getTranslatedString('LBL_ERROR_WHILE_UPDATING_RECORD', 'Reports');
		}
		$_REQUEST['file'] = 'ListView';
		include 'modules/Reports/ListView.php';
	}
} elseif ($mode=='Layout') {
	coreBOS_Settings::setSetting('ReportGridLayout'.$current_user->id, $_REQUEST['layout']);
}
?>