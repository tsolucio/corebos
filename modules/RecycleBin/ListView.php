<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);

require_once 'Smarty_setup.php';
require_once 'include/ListView/ListView.php';
require_once 'include/utils/utils.php';
require_once 'modules/CustomView/CustomView.php';
require_once 'modules/RecycleBin/RecycleBinUtils.php';

global $adb, $log, $current_user;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

require 'user_privileges/user_privileges_'.$current_user->id.'.php';

$smarty = new vtigerCRM_Smarty;
// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', 'true');

// Data from the below modules will not be allowed to restore
$skip_modules = array();
$skip_tab_ids = array();

for ($i=0; $i<count($skip_modules); $i++) {
	$tab_id = getTabid($skip_modules[$i]);
	if ($tab_id != null && $tab_id != '') {
		$skip_tab_ids[] = $tab_id;
	}
}

$sql = 'SELECT tabid, name FROM vtiger_tab WHERE presence=0 AND isentitytype=1 ';
if (count($skip_tab_ids) > 0) {
	$sql .= ' AND tabid NOT IN ('. generateQuestionMarks($skip_tab_ids) .')';
}
$sql .= ' ORDER BY name';
$result =$adb->pquery($sql, $skip_tab_ids);
$noofrows = $adb->num_rows($result);

$module_name =array();
$module_data =array();

if ($noofrows > 0) {
	for ($x=0,$y=0; $x<$noofrows; $x++) {
		$tabid = $adb->query_result($result, $x, 'tabid');
		if ($is_admin || $profileGlobalPermission[2]==0 || $profileGlobalPermission[1]==0 || $profileTabsPermission[$tabid]==0) {
			$mod_name = $adb->query_result($result, $x, "name");
			$module_name[$y] = $mod_name;
			$y++;
		}
	}
}

if (isset($_REQUEST['selected_module']) && $_REQUEST['selected_module'] != '') {
	$select_module = vtlib_purify($_REQUEST['selected_module']);
	if (!in_array($select_module, $module_name)) {
		show_error_msg();
	}
} else {
	if (count($module_name) > 0) {
		$select_module = $module_name[0];
	} else {
		show_error_msg('no_permitted_modules');
	}
}

$focus = CRMEntity::getInstance($select_module);
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

if (count($module_name) > 0) {
	$cur_mod_view = new CustomView($select_module);
	$viewid = $cur_mod_view->getViewIdByName('All', $select_module);

	$queryGenerator = new QueryGenerator($select_module, $current_user);
	$queryGenerator->initForCustomViewById($viewid);
	$rbfields = $queryGenerator->getFields();
	if (!in_array('modifiedtime', $rbfields)) {
		// Recycle Bin List view always shows modifiedtime
		$rbfields[] = 'modifiedtime';
		$queryGenerator->setFields($rbfields);
	}
	if (!in_array('modifiedby', $rbfields)) {
		// Recycle Bin List view always shows modifiedby
		$rbfields[] = 'modifiedby';
		$queryGenerator->setFields($rbfields);
	}
	// Enabling Module Search
	$url_string = '';
	if (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') {
		$queryGenerator->addUserSearchConditions($_REQUEST);
		$ustring = getSearchURL($_REQUEST);
		$url_string .= "&query=true$ustring";
		$smarty->assign('SEARCH_URL', $url_string);
	}

	$list_query = $queryGenerator->getQuery();
	$list_query = preg_replace("/vtiger_crmentity.deleted\s*=\s*0/i", 'vtiger_crmentity.deleted = 1', $list_query);
	//Search criteria added to the list Query
	if (isset($where) && $where != '') {
		$list_query .= ' AND '.$where;
	}
	// Sorting
	if (!empty($order_by)) {
		$list_query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.$sorder;
	}
	$count_result = $adb->query(mkCountQuery($list_query));
	$noofrows = $adb->query_result($count_result, 0, 'count');
	$smarty->assign('NUMOFROWS', $noofrows);

	$controller = new ListViewController($adb, $current_user, $queryGenerator);
	$rb_listview_header = $controller->getListViewHeader($focus, $select_module, $url_string, $sorder, $order_by, true);
	$listview_header_search = $controller->getBasicSearchFieldInfoList();
	$smarty->assign('SEARCHLISTHEADER', $listview_header_search);

	if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
		$start = vtlib_purify($_REQUEST['start']);
	} else {
		$start = 1;
	}

	$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);

	// Setting the record count string
	$start_rec = $navigation_array['start'];
	$end_rec = $navigation_array['end_val'];

	//limiting the query
	if ($start_rec ==0) {
		$limit_start_rec = 0;
	} else {
		$limit_start_rec = $start_rec -1;
	}

	$list_result = $adb->query($list_query. " LIMIT ".$limit_start_rec.",".$list_max_entries_per_page);

	$record_string= $app_strings['LBL_SHOWING']." " .$start_rec." - ".$end_rec." " .$app_strings['LBL_LIST_OF'] ." ".$noofrows;

	$navigationOutput = getTableHeaderNavigation($navigation_array, $url_string, "Recyclebin", "index", "");

	$lvEntries = $controller->getListViewEntries($focus, $select_module, $list_result, $navigation_array, true);
}

$smarty->assign('NAVIGATION', $navigationOutput);
$smarty->assign('RECORD_COUNTS', $record_string);
$smarty->assign('MAX_RECORDS', $list_max_entries_per_page);

//to get the field name that mentions the module
$query = 'SELECT fieldname,tablename FROM vtiger_entityname WHERE modulename =?';
$queryResult = $adb->pquery($query, array($select_module));
$moduleColumnName = $adb->query_result($queryResult, 0, 'fieldname');
$moduleTableName = $adb->query_result($queryResult, 0, 'tablename');

if (strpos($moduleColumnName, ',')) {
	$field_array = explode(',', $moduleColumnName);
	$moduleColumnName = $field_array[0];
}

$query = 'SELECT fieldname FROM vtiger_field WHERE tablename=? and columnname=?';
$rs = $adb->pquery($query, array($moduleTableName,$moduleColumnName));
$moduleFieldName = $adb->query_result($rs, 0, 'fieldname');
$indexField = $moduleFieldName;

$alphabetical = AlphabeticalSearch($currentModule, 'index', $indexField, 'true', 'basic', '', '', '', '', $viewid);

$category = getParentTab();

$check_button = Button_Check($_REQUEST['module']);
$check_button['EditView'] = 'no';
$check_button['CreateView'] = 'no';
$check_button['Import'] = 'no';
$check_button['Export'] = 'no';
$check_button['Merge'] = 'no';
$check_button['DuplicatesHandling'] = 'no';
$smarty->assign('CHECK', $check_button);

$smarty->assign('ALPHABETICAL', $alphabetical);
$smarty->assign('NUMBER_MODULES', $noofrows);
uasort($module_name, function ($a, $b) {
	return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
});
$smarty->assign('MODULE_NAME', $module_name);
$smarty->assign('SELECTED_MODULE', $select_module);
$smarty->assign('MODULE_DATA', $rb_listview_header);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
// Pass on the authenticated user language
$smarty->assign('LANGUAGE', $current_language);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', return_module_language($current_language, $select_module));
$smarty->assign('lvEntries', $lvEntries);
$smarty->assign('ALLSELECTEDIDS', (isset($_REQUEST['allselobjs']) ? vtlib_purify($_REQUEST['allselobjs']) : ''));
$smarty->assign('CURRENT_PAGE_BOXES', implode(array_keys($lvEntries), ';'));

$smarty->assign('IS_ADMIN', is_admin($current_user));

if (empty($_REQUEST['mode']) || $_REQUEST['mode'] != 'ajax') {
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'RecycleBin.tpl'));
} else {
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'RecycleBinContents.tpl'));
}

function show_error_msg($error_type = 'permission_denied') {
	global $app_strings;
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
	if ($error_type == 'permission_denied') {
		//
	} elseif ($error_type == 'no_permitted_modules') {
		$smarty->assign('PUT_BACK_ACTION', false);
		$smarty->assign('OPERATION_MESSAGE', getTranslatedString('LBL_NO_PERMITTED_MODULES'));
	}
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	die();
}
?>