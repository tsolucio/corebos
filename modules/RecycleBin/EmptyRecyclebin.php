<?php
/*********************************************************************************
 *** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 ** ("License"); You may not use this file except in compliance with the License
 ** The Original Code is:  vtiger CRM Open Source
 ** The Initial Developer of the Original Code is vtiger.
 ** Portions created by vtiger are Copyright (C) vtiger.
 ** All Rights Reserved.
 *********************************************************************************/

require_once 'RecycleBinUtils.php';
global $adb,$log,$default_charset;
$allrec=vtlib_purify($_REQUEST['allrec']);
$idlist=vtlib_purify($_REQUEST['idlist']);
$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
$selected_module = vtlib_purify($_REQUEST['selectmodule']);
$idlists = getSelectedRecordIds($_REQUEST, $selected_module, $idlist, $excludedRecords);
$idlists = array_filter($idlists); // this is to eliminate the empty value we always get from selection
//Delete documents from storage
if (empty($selected_module) || $selected_module == 'Documents') {
	$docstodel = array();
	if (count($idlists)==0) {
		$delcrm=$adb->pquery('SELECT crmid FROM vtiger_crmentity WHERE deleted = 1 and setype=?', array('Documents'));
		if ($delcrm) {
			while ($row = $adb->fetch_array($delcrm)) {
				$docstodel[] = $row;
			}
		}
	} elseif ($selected_module == 'Documents') {
		$docstodel = $idlists;
	}

	foreach ($docstodel as $key => $id) {
		if (!empty($id)) {
			$result = $adb->pquery('SELECT * FROM vtiger_notes WHERE notesid = ?', array($id));
			if ($adb->num_rows($result) == 1) {
				$fileType = @$adb->query_result($result, 0, 'filetype');
				$name = @$adb->query_result($result, 0, 'filename');
				$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
				$seQuery = $adb->pquery("select attachmentsid from vtiger_seattachmentsrel where crmid = ?", array($id));
				$fileid = $adb->query_result($seQuery, 0, 'attachmentsid');
				$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid = ?', array($fileid));
				$filepath = $adb->query_result($pathQuery, 0, 'path');
				$saved_filename = $filepath.$fileid."_".$name;
				if (file_exists($saved_filename)) {
					@unlink($saved_filename);
				}
			}
		}
	}
}
if ($allrec==1 && !empty($selected_module)) {
	$delcrm=$adb->pquery('DELETE FROM vtiger_crmentity WHERE deleted = 1 and setype=?', array($selected_module));
	$delrel = $adb->pquery('DELETE FROM vtiger_relatedlists_rb WHERE entityid in ('.generateQuestionMarks($idlists).')', array($idlists));
} elseif ($allrec==1 && empty($selected_module)) {  // empty all modules
	$adb->query('DELETE FROM vtiger_crmentity WHERE deleted = 1');
	// TODO Related records for the module records deleted from vtiger_crmentity have to be deleted
	// It needs lookup in the related tables and needs to be removed if doesn't have a reference record in vtiger_crmentity
	$adb->query('DELETE FROM vtiger_relatedlists_rb');
} else {
	if (count($idlists)>0) {
		$delselcrm=$adb->pquery('DELETE FROM vtiger_crmentity WHERE deleted = 1 and crmid in ('.generateQuestionMarks($idlists).')', array($idlists));
		$delselrel = $adb->pquery('DELETE FROM vtiger_relatedlists_rb WHERE entityid in ('.generateQuestionMarks($idlists).')', array($idlists));
	}
}
header('Location: index.php?module=RecycleBin&action=RecycleBinAjax&file=index&mode=ajax&selected_module='.urlencode($selected_module));
?>
