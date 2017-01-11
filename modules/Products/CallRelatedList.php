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

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$category = getParentTab();
$action = vtlib_purify($_REQUEST['action']);
$record = vtlib_purify($_REQUEST['record']);
$isduplicate = isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : false;

if($singlepane_view == 'true' && $action == 'CallRelatedList') {
	echo "<script>document.location='index.php?action=DetailView&module=$currentModule&record=$record&parenttab=$category';</script>";
	die();
} else {

	$tool_buttons = Button_Check($currentModule);

	$focus = CRMEntity::getInstance($currentModule);
	if($record != '') {
		$focus->retrieve_entity_info($record, $currentModule);
		$focus->id = $record;
		$product_base_currency = getProductBaseCurrency($focus->id,$currentModule);
	} else {
		$product_base_currency = fetchCurrency($current_user->id);
	}

	$smarty = new vtigerCRM_Smarty;

	if($isduplicate == 'true') $focus->id = '';
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') $smarty->assign("OP_MODE",vtlib_purify($_REQUEST['mode']));
	if(empty($_SESSION['rlvs'][$currentModule])) coreBOS_Session::delete('rlvs');

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
	$smarty->assign('TODO_PERMISSION',CheckFieldPermission('parent_id','Calendar'));
	$smarty->assign('EVENT_PERMISSION',CheckFieldPermission('parent_id','Events'));
	$smarty->assign('CURRENCY_ID',$product_base_currency);
	$is_member = $focus->ismember_check();
	$smarty->assign('IS_MEMBER',$is_member);

	// Module Sequence Numbering
	$mod_seq_field = getModuleSequenceField($currentModule);
	if ($mod_seq_field != null) {
		$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
	} else {
		$mod_seq_id = $focus->id;
	}
	$smarty->assign('MOD_SEQ_ID', $mod_seq_id);
	$bmapname = $currentModule.'RelatedPanes';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		if (empty($_REQUEST['RelatedPane'])) {
			$_RelatedPane=vtlib_purify($_SESSION['RelatedPane']);
		} else {
			$_RelatedPane=vtlib_purify($_REQUEST['RelatedPane']);
			coreBOS_Session::set('RelatedPane',$_RelatedPane);
		}
		$smarty->assign("RETURN_RELATEDPANE", $_RelatedPane);
		$cbMap = cbMap::getMapByID($cbMapid);
		$rltabs = $cbMap->RelatedPanes($focus->id);
		$smarty->assign('RLTabs', $rltabs['panes']);
		$restrictedRelations = (isset($rltabs['panes'][$_RelatedPane]['restrictedRelations']) ? $rltabs['panes'][$_RelatedPane]['restrictedRelations'] : null);
		$related_array = array();
		$rel_array = getRelatedLists($currentModule, $focus, $restrictedRelations);
		foreach ($rltabs['panes'][$_RelatedPane]['blocks'] as $blk) {
			if ($blk['type']=='RelatedList') {
				$related_array[$blk['loadfrom']] = empty($rel_array[$blk['loadfrom']]) ? $rel_array[$blk['label']] : $rel_array[$blk['loadfrom']];
			} else {
				if (!empty($blk['loadphp'])) {
					try {
						include $blk['loadphp'];
					} catch (Exception $e) {
						$log->fatal('Related Pane LoadPHP error ('.$blk['loadphp'].'): '.$e->getMessage());
					}
				}
				$related_array[$blk['sequence']] = $blk;
			}
		}
		$smarty->assign('HASRELATEDPANES', 'true');
		if (file_exists("modules/$currentModule/RelatedPaneActions.php")) {
			include "modules/$currentModule/RelatedPaneActions.php";
			$smarty->assign('HASRELATEDPANESACTIONS', 'true');
		} else {
			$smarty->assign('HASRELATEDPANESACTIONS', 'false');
		}
	} else {
		$smarty->assign('HASRELATEDPANES', 'false');
		$restrictedRelations = null;
		$related_array = getRelatedLists($currentModule, $focus, $restrictedRelations);
	}
	$smarty->assign('RELATEDLISTS', $related_array);

	require_once('include/ListView/RelatedListViewSession.php');
	if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		$relationId = vtlib_purify($_REQUEST['relation_id']);
		RelatedListViewSession::addRelatedModuleToSession($relationId,vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign("SELECTEDHEADERS", $open_related_modules);

	$smarty->display('RelatedLists.tpl');
}
?>
