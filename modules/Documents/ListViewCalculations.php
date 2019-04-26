<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$result = $adb->pquery('select * from vtiger_attachmentsfolder', array());
$foldercount = $adb->num_rows($result);
$folders = array();
$emptyfolders = array();
if ($foldercount > 0) {
	for ($i=0; $i<$foldercount; $i++) {
		$query = '';
		$displayFolder='';
		$query = substr($list_query, 0, stripos($list_query, ' WHERE '));
		$folder_id = $adb->query_result($result, $i, 'folderid');
		$query .= " where vtiger_crmentity.deleted=0 and vtiger_notes.folderid = $folder_id";
		//Retreiving the no of rows
		$count_result = $adb->query(mkCountQuery($query));
		$num_records = $adb->query_result($count_result, 0, 'count');
		if ($num_records > 0) {
			$displayFolder=true;
		}
		$folder_details=array();
		$folderid = $adb->query_result($result, $i, 'folderid');
		$folder_details['folderid']=$folderid;
		$folder_details['foldername']=$adb->query_result($result, $i, 'foldername');
		$foldername = $folder_details['foldername'];
		$folder_details['description']=$adb->query_result($result, $i, 'description');
		if ($displayFolder == true) {
			$folders[$foldername] = $folder_details;
		} else {
			$emptyfolders[$foldername] = $folder_details;
		}
		if ($folderid == 1) {
			$default_folder_details = $folder_details;
		}
	}
	if (count($folders) == 0) {
		$folders[$default_folder_details['foldername']] = $default_folder_details;
	}
}
$smarty->assign('NO_OF_FOLDERS', $foldercount);
$smarty->assign('FOLDERS', $folders);
$smarty->assign('EMPTY_FOLDERS', $emptyfolders);
$smarty->assign('ALL_FOLDERS', array_merge($folders, $emptyfolders));
?>