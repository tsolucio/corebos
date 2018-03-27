<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
global $current_user, $adb;
if ($current_user->is_admin != 'on') {
	echo 'NOT_PERMITTED';
	die;
} else {
	$new_folderid = $_REQUEST['folderid'];
	$idlist = vtlib_purify($_REQUEST['idlist']);
	$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);

	if (isset($_REQUEST['idlist']) && $_REQUEST['idlist']!= '') {
		$id_array = getSelectedRecords($_REQUEST, 'Documents', $idlist, $excludedRecords);//explode(';',$_REQUEST['idlist']);
		$id_array = array_filter($id_array);
		$sql = 'update vtiger_notes set folderid=? where notesid=?';
		for ($i = 0; $i < count($id_array); $i++) {
			$adb->pquery($sql, array($new_folderid, $id_array[$i]));
		}
		if (!isset($_REQUEST['__NoReload'])) {
			header('Location: index.php?action=DocumentsAjax&file=ListView&mode=ajax&ajax=true&module=Documents');
		}
	}
}
?>
