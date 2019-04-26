<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'modules/Documents/Documents.php';

global $adb, $current_user;

if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'updateDldCnt') {
	$res=$adb->pquery('update vtiger_notes set filedownloadcount=filedownloadcount+1 where notesid=?', array($_REQUEST['file_id']));
}

if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'checkFileIntegrityDetailView') {
	$result = $adb->pquery('SELECT * FROM vtiger_notes where notesid=?', array($_REQUEST['noteid']));
	$fileidResult = $adb->pquery('select attachmentsid from vtiger_seattachmentsrel where crmid=?', array($_REQUEST['noteid']));
	//$activeToinactive_count = 0;

	$file_status = $adb->query_result($result, 0, 'filestatus');
	$download_type = $adb->query_result($result, 0, 'filelocationtype');
	$notesid = $adb->query_result($result, 0, 'notesid');
	$fileid = $adb->query_result($fileidResult, 0, 'attachmentsid');
	$folderid = $adb->query_result($result, 0, 'folderid');
	$name = $adb->query_result($result, 0, 'filename');
	$filepath = '';
	if ($download_type == 'I') {
		$saved_filename = $fileid.'_'.$name;
		$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid = ?', array($fileid));
		$filepath = $adb->query_result($pathQuery, 0, 'path');
	} else {
		echo 'file_not_available';
		die();
	}

	if (!fopen($filepath.$saved_filename, 'r')) {
		if ($file_status == 1) {
			$result1 = $adb->pquery('update vtiger_notes set filestatus = 0 where notesid= ?', array($notesid));
			echo 'lost_integrity';
		} else {
			echo 'file_not_available';
		}
	} else {
		echo 'file_available';
	}
}

if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'massDldCnt') {
	$all_files = vtlib_purify($_REQUEST['file_id']);
	$zipfilename = 'cache/Documents'.$current_user->id.'.zip';
	if (file_exists($zipfilename)) {
		@unlink($zipfilename);
	}
	$zip = new Vtiger_Zip($zipfilename);
	$dec_files =json_decode($all_files, true);
	foreach ($dec_files as $folder_id => $files_id) {
		if ($files_id) {
			$folderQuery = $adb->pquery('SELECT foldername FROM vtiger_attachmentsfolder WHERE folderid = ?', array($folder_id));
			$folderName = $adb->query_result($folderQuery, 0, 'foldername');
			$files = explode(';', $files_id);
			foreach ($files as $file) {
				if ($file) {
					$dbQuery = 'SELECT *
						FROM vtiger_attachments
						JOIN vtiger_seattachmentsrel ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
						WHERE crmid = ?';
					$result = $adb->pquery($dbQuery, array($file));
					if ($adb->num_rows($result) == 1) {
						$pname = @$adb->query_result($result, 0, 'attachmentsid');
						$name = @$adb->query_result($result, 0, 'name');
						$filepath = @$adb->query_result($result, 0, 'path');
						$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
						$saved_filename = $pname."_".$name;
						$zip->addFile($filepath.$saved_filename, $folderName.'/'.$saved_filename);
					}
				}
			}
		}
	}
	$zip->save();
	$zip->forceDownload($zipfilename);
}
?>