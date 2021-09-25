<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme;

$focus = CRMEntity::getInstance($currentModule);

if (!isset($tool_buttons)) {
	$tool_buttons = Button_Check($currentModule);
}
$smarty = new vtigerCRM_Smarty();

$record = vtlib_purify($_REQUEST['record']);
$tabid = getTabid($currentModule);

if ($record != '') {
	$focus->id = $record;
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->name=$focus->column_fields['notes_title'];
}
$errormessageclass = isset($_REQUEST['error_msgclass']) ? vtlib_purify($_REQUEST['error_msgclass']) : '';
$errormessage = isset($_REQUEST['error_msg']) ? vtlib_purify($_REQUEST['error_msg']) : '';
$smarty->assign('ERROR_MESSAGE_CLASS', $errormessageclass);
$smarty->assign('ERROR_MESSAGE', $errormessage);
$focus->preViewCheck($_REQUEST, $smarty);

// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);

//needed when creating a new note with default values passed in
if (isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) {
	$focus->contact_name = vtlib_purify($_REQUEST['contact_name']);
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) {
	$focus->contact_id = vtlib_purify($_REQUEST['contact_id']);
}
if (isset($_REQUEST['opportunity_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = vtlib_purify($_REQUEST['opportunity_name']);
}
if (isset($_REQUEST['opportunity_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = vtlib_purify($_REQUEST['opportunity_id']);
}
if (isset($_REQUEST['account_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = vtlib_purify($_REQUEST['account_name']);
}
if (isset($_REQUEST['account_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = vtlib_purify($_REQUEST['account_id']);
}
$filename=$focus->column_fields['filename'];
$folderid = $focus->column_fields['folderid'];
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
$smarty->assign('FILEID', $fileid);
$smarty->assign('FILE_STATUS', $filestatus);
$smarty->assign('DLD_TYPE', $filelocationtype);
$smarty->assign('NOTESID', $focus->id);
$smarty->assign('FOLDERID', $folderid);
$smarty->assign('DLD_PATH', $filepath);
$smarty->assign('FILENAME', $filename);
$allblocks = getBlocks($currentModule, 'detail_view', '', $focus->column_fields);
$custom_blocks = getCustomBlocks($currentModule, 'detail_view');
$smarty->assign('BLOCKS', $allblocks);
$smarty->assign('CUSTOMBLOCKS', $custom_blocks);
$smarty->assign('FIELDS', $focus->column_fields);
$flag = 0;
foreach ($allblocks as $blocks) {
	foreach ($blocks as $block_entries) {
		if (!empty($block_entries[getTranslatedString('File Name', $currentModule)]['value'])) {
			$flag = 1;
		}
	}
}
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
}
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$smarty->assign('ID', $focus->id);
$smarty->assign('RECORDID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('USE_ASTERISK', get_use_asterisk($current_user->id));

$recordName = array_values(getEntityName($currentModule, $focus->id));
$recordName = isset($recordName[0]) ? $recordName[0] : '';
$smarty->assign('NAME', $recordName);
$smarty->assign('UPDATEINFO', updateInfo($focus->id));

// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($mod_seq_field != null) {
	$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
} else {
	$mod_seq_id = $focus->id;
}
$smarty->assign('MOD_SEQ_ID', $mod_seq_id);

$validationArray = split_validationdataArray(getDBValidationData($focus->tab_name, $tabid));
$smarty->assign('VALIDATION_DATA_FIELDNAME', $validationArray['fieldname']);
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', $validationArray['datatype']);
$smarty->assign('VALIDATION_DATA_FIELDLABEL', $validationArray['fieldlabel']);

$smarty->assign('EDIT_PERMISSION', isPermitted($currentModule, 'EditView', $record));
$smarty->assign('CHECK', $tool_buttons);

if (GlobalVariable::getVariable('Application_DetailView_Record_Navigation', 1) && isset($_SESSION[$currentModule.'_listquery'])) {
	$recordNavigationInfo = ListViewSession::getListViewNavigation($focus->id);
	VT_detailViewNavigation($smarty, $recordNavigationInfo, $focus->id);
} else {
	$smarty->assign('privrecord', '');
	$smarty->assign('nextrecord', '');
}

$smarty->assign('IS_REL_LIST', isPresentRelatedLists($currentModule));
$singlepane_view = 'true';
if ($singlepane_view == 'true') {
	$related_array = getRelatedLists($currentModule, $focus);
	$smarty->assign('RELATEDLISTS', $related_array);

	require_once 'include/ListView/RelatedListViewSession.php';
	if (!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		RelatedListViewSession::addRelatedModuleToSession(vtlib_purify($_REQUEST['relation_id']), vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign('SELECTEDHEADERS', $open_related_modules);
}
$smarty->assign('SinglePane_View', $singlepane_view);

if (isPermitted($currentModule, 'CreateView', $record) == 'yes') {
	$smarty->assign('CREATE_PERMISSION', 'permitted');
} else {
	$smarty->assign('CREATE_PERMISSION', '');
}
if (isPermitted($currentModule, 'Delete', $record) == 'yes') {
	$smarty->assign('DELETE', 'permitted');
} else {
	$smarty->assign('DELETE', '');
}
$smarty->assign('BLOCKINITIALSTATUS', $_SESSION['BLOCKINITIALSTATUS']);
// Gather the custom link information to display
include_once 'vtlib/Vtiger/Link.php';
$customlink_params = array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$smarty->assign(
	'CUSTOM_LINKS',
	Vtiger_Link::getAllByType($tabid, array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWWIDGET','DETAILVIEWBUTTON','DETAILVIEWBUTTONMENU'), $customlink_params, null, $focus->id)
);

// Hide Action Panel
$DEFAULT_ACTION_PANEL_STATUS = GlobalVariable::getVariable('Application_DetailView_ActionPanel_Open', 1);
$smarty->assign('DEFAULT_ACTION_PANEL_STATUS', ($DEFAULT_ACTION_PANEL_STATUS ? '' : 'display:none'));
$smarty->assign('Module_Popup_Edit', isset($_REQUEST['Module_Popup_Edit']) ? vtlib_purify($_REQUEST['Module_Popup_Edit']) : 0);

// Record Change Notification
$focus->markAsViewed($current_user->id);
$bmapname = $currentModule.'_FieldDependency';
$cbMapFDEP = array();
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$cbMapFDEP = $cbMap->FieldDependency();
	$cbMapFDEP = $cbMapFDEP['fields'];
}
$smarty->assign('FIELD_DEPENDENCY_DATASOURCE', json_encode($cbMapFDEP));

$smarty->assign('DETAILVIEW_AJAX_EDIT', GlobalVariable::getVariable('Application_DetailView_Inline_Edit', 1));

$smarty->display('DetailView.tpl');
?>
