<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');
require('user_privileges/default_module_view.php');

global $mod_strings, $app_strings, $currentModule, $current_user, $theme;

$category = getParentTab();
$action = vtlib_purify($_REQUEST['action']);
$record = vtlib_purify($_REQUEST['record']);
$isduplicate = vtlib_purify($_REQUEST['isDuplicate']);

	$tool_buttons = Button_Check($currentModule);

	$focus = CRMEntity::getInstance($currentModule);
	if($record != '') {
		$focus->retrieve_entity_info($record, $currentModule);
		$focus->id = $record;
	}

	$smarty = new vtigerCRM_Smarty;

	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') $smarty->assign("OP_MODE",vtlib_purify($_REQUEST['mode']));

	// Identify this module as custom module.
	$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);

	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule, $currentModule));
	$smarty->assign('CATEGORY', $category);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('THEME', $theme);
	$smarty->assign('ID', $focus->id);
	$smarty->assign('MODE', $focus->mode);
	$smarty->assign('CHECK', $tool_buttons);

	$smarty->assign('NAME', $focus->column_fields[$focus->def_detailview_recname]);
	$smarty->assign('UPDATEINFO',updateInfo($focus->id));

	$related_array = getRelatedLists($currentModule, $focus);
	$smarty->assign('RELATEDLISTS', $related_array);

	require_once('include/ListView/RelatedListViewSession.php');
	if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		$relationId = vtlib_purify($_REQUEST['relation_id']);
		RelatedListViewSession::addRelatedModuleToSession($relationId,vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign("SELECTEDHEADERS", $open_related_modules);

	$smarty->display('RelatedLists.tpl');
?>