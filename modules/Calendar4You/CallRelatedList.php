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
require 'user_privileges/default_module_view.php';
require_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar4You/CalendarUtils.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme;
$action = vtlib_purify($_REQUEST['action']);
$record = vtlib_purify($_REQUEST['record']);

if ($singlepane_view == 'true' && $action == 'CallRelatedList') {
	echo "<script>document.location='index.php?action=DetailView&module=cbCalendar&record=".urlencode($record)."';</script>";
	die();
} else {
	$c_mod_strings = return_specified_module_language($current_language, "Calendar");

	$focus = CRMEntity::getInstance('cbCalendar');
	if (isset($_REQUEST['record']) && $_REQUEST['record']!='') {
		$focus->retrieve_entity_info($record, 'cbCalendar');
		$focus->id = $record;
		$focus->name=$focus->column_fields['subject'];
	}

	$smarty = new vtigerCRM_Smarty;

	$activity_mode = vtlib_purify($_REQUEST['activity_mode']);

	if ($activity_mode =='' || strlen($activity_mode) < 1) {
		$activity_mode = getEventActivityMode($record);
	}

	if ($activity_mode == 'Task') {
		$tab_type = 'Calendar';
		$rel_tab_type = 'Calendar4You';
		$smarty->assign('SINGLE_MOD', $c_mod_strings['LBL_TODO']);
	} elseif ($activity_mode == 'Events') {
		$rel_tab_type = $tab_type = 'Events';
		$smarty->assign('SINGLE_MOD', $c_mod_strings['LBL_EVENT']);
	}

	$tab_id=getTabid($rel_tab_type);

	if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
		$focus->id = "";
	}
	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') {
		$smarty->assign('OP_MODE', vtlib_purify($_REQUEST['mode']));
	}
	if (empty($_SESSION['rlvs'][$module])) {
		coreBOS_Session::delete('rlvs');
	}

	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('CATEGORY', getParentTab());
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('THEME', $theme);
	$smarty->assign('ID', $focus->id);
	$smarty->assign('MODE', 'RelatedList');
	$smarty->assign('TABTYPE', $tab_type);
	$check_button = Button_Check($module);

	$smarty->assign("CHECK", $check_button);
	if (isset($focus->name)) {
		$smarty->assign('NAME', $focus->name);
	}
	$smarty->assign('UPDATEINFO', updateInfo($focus->id));
	$smarty->assign('TODO_PERMISSION', CheckFieldPermission('parent_id', 'Calendar'));
	$smarty->assign('EVENT_PERMISSION', CheckFieldPermission('parent_id', 'Events'));

	// Module Sequence Numbering
	$mod_seq_field = getModuleSequenceField($currentModule);
	if ($mod_seq_field != null) {
		$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
	} else {
		$mod_seq_id = $focus->id;
	}
	$smarty->assign('MOD_SEQ_ID', $mod_seq_id);

	$related_array=getRelatedLists($rel_tab_type, $focus);
	$smarty->assign('RELATEDLISTS', $related_array);
	require_once 'include/ListView/RelatedListViewSession.php';
	if (!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		$relationId = vtlib_purify($_REQUEST['relation_id']);
		RelatedListViewSession::addRelatedModuleToSession($relationId, vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign('SELECTEDHEADERS', $open_related_modules);

	$smarty->display('modules/Calendar4You/RelatedLists.tpl');
}
?>
