<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the 'License'); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an 'AS IS' BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : Mass Upload Image On Product
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme;

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty;

$record = vtlib_purify($_REQUEST['record']);
$isduplicate = isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : '';

if ($record != '') {
	$focus->id = $record;
	$focus->retrieve_entity_info($record, $currentModule);
}
if ($isduplicate == 'true') {
	$focus->id = '';
}

$filename=$focus->column_fields['filename'];
$allblocks = getBlocks($currentModule, 'detail_view', '', $focus->column_fields);
$filestatus = $focus->column_fields['filestatus'];
$filelocationtype = $focus->column_fields['filelocationtype'];
$fileattach = 'select attachmentsid from vtiger_seattachmentsrel where crmid = ?';
$res = $adb->pquery($fileattach, array($focus->id));
$fileid = $adb->query_result($res, 0, 'attachmentsid');

if ($filelocationtype == 'I') {
	$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid = ?', array($fileid));
	$filepath = $adb->query_result($pathQuery, 0, 'path');
} else {
	$filepath = $filename;
}

$flag = 0;
foreach ($allblocks as $blocks) {
	foreach ($blocks as $block_entries) {
		if (!empty($block_entries[getTranslatedString('File Name', $currentModule)]['value'])) {
			$flag = 1;
		}
	}
}

$smarty->assign('MOD', $mod_strings);
$smarty->assign('THEME', $theme);

$smarty->assign('FILEID', $fileid);
$smarty->assign('NOTESID', $focus->id);
$smarty->assign('DLD_PATH', $filepath);
$smarty->assign('FILENAME', $filename);
$smarty->assign('FILE_STATUS', $filestatus);
$smarty->assign('DLD_TYPE', $filelocationtype);

if ($flag == 1) {
	$smarty->assign('FILE_EXIST', 'yes');
} elseif ($flag == 0) {
	$smarty->assign('FILE_EXIST', 'no');
}

if (is_admin($current_user)) {
	$smarty->assign('CHECK_INTEGRITY_PERMISSION', 'yes');
	$smarty->assign('ADMIN', 'yes');
} else {
	$smarty->assign('CHECK_INTEGRITY_PERMISSION', 'no');
	$smarty->assign('ADMIN', 'no');
};

$smarty->display('modules/Documents/documentActions.tpl');

?>
