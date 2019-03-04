<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Documents/Documents.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

global $adb;

$local_log = LoggerManager::getLogger('index');
$folderid = isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : '';
$foldername = utf8RawUrlDecode($_REQUEST["foldername"]);
$folderdesc = utf8RawUrlDecode($_REQUEST["folderdesc"]);

if (isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'Save') {
	if ($folderid == '') {
		$params = array();
		$sqlfid = 'select max(folderid) from vtiger_attachmentsfolder';
		$rs = $adb->pquery($sqlfid, $params);
		$fid = $adb->query_result($rs, 0, 0) + 1;
		$params = array();
		$sqlseq = 'select max(sequence) from vtiger_attachmentsfolder';
		$rs = $adb->pquery($sqlseq, $params);
		$sequence = $adb->query_result($rs, 0, 0) + 1;
		$dbQuery = 'select foldername from vtiger_attachmentsfolder where foldername = ?';
		$result1 = $adb->pquery($dbQuery, array($foldername));
		if ($result1 && $adb->num_rows($result1)>0) {
			echo 'DUPLICATE_FOLDERNAME';
		} else {
			$sql = 'insert into vtiger_attachmentsfolder (folderid,foldername,description,createdby,sequence) values (?,?,?,?,?)';
			$params = array($fid, $foldername, $folderdesc, $current_user->id, $sequence);
			$result = $adb->pquery($sql, $params);
			if (!$result) {
				echo 'Failure';
			} else {
				header('Location: index.php?action=DocumentsAjax&file=ListView&mode=ajax&ajax=true&module=Documents');
			}
		}
	} elseif ($folderid != '') {
		$dbQuery = 'select count(*) from vtiger_attachmentsfolder where foldername=? and folderid!=?';
		$result1 = $adb->pquery($dbQuery, array($foldername, $folderid));
		if ($result1 && $adb->query_result($result1, 0, 0)==0) {
			if (empty($folderdesc)) {
				$sql = 'update vtiger_attachmentsfolder set foldername=? where folderid= ?';
				$result = $adb->pquery($sql, array($foldername,$folderid));
			} else {
				$sql = 'update vtiger_attachmentsfolder set foldername=?, description=? where folderid= ?';
				$result = $adb->pquery($sql, array($foldername, $folderdesc, $folderid));
			}
			if (!$result) {
				echo 'Failure';
			} else {
				echo 'Success';
			}
		} else {
			echo 'DUPLICATE_FOLDERNAME';
		}
	}
}
?>
