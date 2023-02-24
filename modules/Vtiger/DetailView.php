<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$focus = CRMEntity::getInstance($currentModule);

if (!isset($tool_buttons)) {
	$tool_buttons = Button_Check($currentModule);
}

$record = vtlib_purify($_REQUEST['record']);
$tabid = getTabid($currentModule);

if ($record != '') {
	$focus->id = $record;
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->name=$focus->column_fields[$focus->list_link_field];
}
$errormessageclass = isset($_REQUEST['error_msgclass']) ? vtlib_purify($_REQUEST['error_msgclass']) : '';
$errormessage = isset($_REQUEST['error_msg']) ? vtlib_purify($_REQUEST['error_msg']) : '';
$smarty->assign('ERROR_MESSAGE_CLASS', $errormessageclass);
$smarty->assign('ERROR_MESSAGE', $errormessage);
$focus->preViewCheck($_REQUEST, $smarty);

// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);

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
$IS_REL_LIST = isPresentRelatedLists($currentModule, true);
$RLdata = array();
if (!empty($IS_REL_LIST)) {
	foreach ($IS_REL_LIST as $id => $label) {
		$name = getTabModuleName($id);
		if (empty($name)) {
			continue;
		}
		$modInstance = CRMEntity::getInstance($name);
		$icon = $modInstance->moduleIcon;
		$RLdata[$id] = $icon;
	}
}
$smarty->assign('IS_REL_LIST', $IS_REL_LIST);
$smarty->assign('REL_MOD_ICONS', $RLdata);
$smarty->assign('currentModuleIcon', $focus->moduleIcon);
$isPresentRelatedListBlock = isPresentRelatedListBlock($currentModule);
$smarty->assign('IS_RELBLOCK_LIST', $isPresentRelatedListBlock);
$singlepane_view = GlobalVariable::getVariable('Application_Single_Pane_View', 0, $currentModule);
$singlepane_view = empty($singlepane_view) ? 'false' : 'true';
$smarty->assign('SinglePane_View', $singlepane_view);
$bmapname = $currentModule.'RelatedPanes';
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$rltabs = $cbMap->RelatedPanes($focus->id);
	$smarty->assign('RLTabs', $rltabs['panes']);
	$smarty->assign('HASRELATEDPANES', 'true');
} else {
	$smarty->assign('HASRELATEDPANES', 'false');
}
if ($singlepane_view == 'true' || $isPresentRelatedListBlock) {
	$related_array = getRelatedLists($currentModule, $focus);
	$smarty->assign('RELATEDLISTS', $related_array);

	require_once 'include/ListView/RelatedListViewSession.php';
	if (!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		RelatedListViewSession::addRelatedModuleToSession(vtlib_purify($_REQUEST['relation_id']), vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign('SELECTEDHEADERS', $open_related_modules);
} else {
	$smarty->assign('RELATEDLISTS', array());
}

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

$custom_blocks = getCustomBlocks($currentModule, 'detail_view');
$smarty->assign('CUSTOMBLOCKS', $custom_blocks);
$smarty->assign('FIELDS', $focus->column_fields);
if (is_admin($current_user)) {
	$smarty->assign('hdtxt_IsAdmin', 1);
} else {
	$smarty->assign('hdtxt_IsAdmin', 0);
}

// Gather the custom link information to display
include_once 'vtlib/Vtiger/Link.php';
$customlink_params = array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$dvwidget = Vtiger_Link::getAllByType(
	$tabid,
	array('DETAILVIEWWIDGET'),
	$customlink_params,
	null,
	$focus->id
);
$blocks = getBlocks($currentModule, 'detail_view', '', $focus->column_fields);
$smarty->assign('BLOCKINITIALSTATUS', $_SESSION['BLOCKINITIALSTATUS']);
$blocks = array_merge($blocks, $dvwidget['DETAILVIEWWIDGET']);
$mergedBlocks = array();
$headers = array();
foreach ($blocks as $block) {
	if (is_object($block)) {
		$mergedBlocks[] = array(
			'__type' => 'widget',
			'__sequence' => (int)$block->sequence,
			'__fields' => $block
		);
	} else {
		if (isset($block['__header'])) {
			if (in_array($block['__header'], $headers)) {
				continue;
			}
			$headers[] = $block['__header'];
			$mergedBlocks[] = $block;
		} else {
			//suport for related lists
			if (!isset($block['sequence'])) {
				continue;
			}
			$header = array_keys($block);
			$mergedBlocks[] = array(
				'__sequence' => (int)$block['sequence'],
				'__type' => 'block',
				'__header' => $header[0],
				'__fields' => $block,
			);
		}
	}
}
sort_array_data($mergedBlocks, '__sequence');
$smarty->assign('BLOCKS', $mergedBlocks);
$smarty->assign(
	'CUSTOM_LINKS',
	Vtiger_Link::getAllByType(
		$tabid,
		array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWWIDGET','DETAILVIEWBUTTON','DETAILVIEWBUTTONMENU','DETAILVIEWHTML'),
		$customlink_params,
		null,
		$focus->id
	)
);
if ($isPresentRelatedListBlock) {
	$related_list_block = array();
	foreach ($blocks as $blabel => $binfo) {
		if (is_object($binfo)) {
			continue;
		}
		if (!empty($binfo['relatedlist'])) {
			foreach ($related_array as $rlabel => $rinfo) {
				if ($rinfo['relationId']==$binfo['relatedlist']) {
					$related_list_block[$binfo['relatedlist']] = array($rlabel=>$rinfo);
					break;
				}
			}
		}
	}
	foreach ($related_list_block as $rlid => $rl) {
		$keys = array_keys($rl);
		if (array_key_exists($keys[0], $_SESSION['BLOCKINITIALSTATUS']) && $_SESSION['BLOCKINITIALSTATUS'][$keys[0]] == 1) {
			$open_related_modules[] = $keys[0];
			$smarty->assign('SELECTEDHEADERS', $open_related_modules);
		}
	}
	$smarty->assign('RELATEDLISTBLOCK', $related_list_block);
}

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
	$cbMapFDEP = $cbMap->FieldDependency('detail');
	$cbMapFDEP = $cbMapFDEP['fields'];
}
$smarty->assign('FIELD_DEPENDENCY_DATASOURCE', json_encode($cbMapFDEP));
$Application_DetailView_Inline_Edit = GlobalVariable::getVariable('Application_DetailView_Inline_Edit', 1, $currentModule, $current_user->id);
$Application_Inline_Edit = GlobalVariable::getVariable('Application_Inline_Edit', 1, $currentModule, $current_user->id, $_REQUEST['action']);
$Application_Inline_Edit_Boolean = !(!$Application_DetailView_Inline_Edit || !$Application_Inline_Edit);
$smarty->assign('DETAILVIEW_AJAX_EDIT', $Application_Inline_Edit_Boolean);
$smarty->assign('Application_Toolbar_Show', GlobalVariable::getVariable('Application_Toolbar_Show', 1));
$smarty->assign('Application_Textarea_Style', GlobalVariable::getVariable('Application_Textarea_Style', 'height:140px;', $currentModule, $current_user->id));
$smarty->assign('App_Header_Buttons_Position', GlobalVariable::getVariable('Application_Header_Buttons_Position', ''));

// sending PopupFilter map results to the frontEnd
$bmapname = $currentModule.'_PopupFilter';
$Mapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
if ($Mapid) {
	$MapObject = new cbMap();
	$MapObject->id = $Mapid;
	$MapObject->mode = '';
	$MapObject->retrieve_entity_info($Mapid, 'cbMap');
	$MapResult = $MapObject->PopupFilter($record, $currentModule);
	$smarty->assign('PopupFilterMapResults', $MapResult);
}
?>
