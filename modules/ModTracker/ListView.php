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
require_once 'modules/CustomView/CustomView.php';
require_once 'include/DatabaseUtil.php';

checkFileAccess("modules/$currentModule/$currentModule.php");
require_once "modules/$currentModule/$currentModule.php";

$category = getParentTab();
$url_string = '';

$tool_buttons = Button_Check($currentModule);
$list_buttons = array();

if (isPermitted($currentModule, 'Delete', '') == 'yes') {
	$list_buttons['del'] = $app_strings[LBL_MASS_DELETE];
}
if (isPermitted($currentModule, 'EditView', '') == 'yes') {
	$list_buttons['mass_edit'] = $app_strings[LBL_MASS_EDIT];
}

$focus = new $currentModule();
$focus->initSortbyField($currentModule);
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

coreBOS_Session::set($currentModule.'_Order_By', $order_by);
coreBOS_Session::set($currentModule.'_Sort_Order', $sorder);

$smarty = new vtigerCRM_Smarty();

// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', true);

$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('CATEGORY', $category);
$smarty->assign('BUTTONS', $list_buttons);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

$smarty->assign('CHANGE_OWNER', getUserslist());
$smarty->assign('CHANGE_GROUP_OWNER', getGroupslist());

// Custom View
$customView = new CustomView($currentModule);
$viewid = $customView->getViewId($currentModule);
$customview_html = $customView->getCustomViewCombo($viewid);
$viewinfo = $customView->getCustomViewByCvid($viewid);

// Approving or Denying status-public by the admin in CustomView
$statusdetails = $customView->isPermittedChangeStatus($viewinfo['status'], $viewid);

// To check if a user is able to edit/delete a CustomView
$edit_permit = $customView->isPermittedCustomView($viewid, 'EditView', $currentModule);
$delete_permit = $customView->isPermittedCustomView($viewid, 'Delete', $currentModule);
$smarty->assign('CUSTOMVIEW_PERMISSION', $statusdetails);
$smarty->assign('CV_EDIT_PERMIT', $edit_permit);
$smarty->assign('CV_DELETE_PERMIT', $delete_permit);
$smarty->assign('VIEWID', $viewid);

if ($viewinfo['viewname'] == 'All') {
	$smarty->assign('ALL', 'All');
}

if ($viewid == 0) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	exit;
}

global $current_user;
$queryGenerator = new QueryGenerator($currentModule, $current_user);
if ($viewid != "0") {
	$queryGenerator->initForCustomViewById($viewid);
} else {
	$queryGenerator->initForDefaultCustomView();
}

// Enabling Module Search
$url_string = '';
if ($_REQUEST['query'] == 'true') {
	$queryGenerator->addUserSearchConditions($_REQUEST);
	$ustring = getSearchURL($_REQUEST);
	$url_string .= "&query=true$ustring";
	$smarty->assign('SEARCH_URL', $url_string);
}

$list_query = $queryGenerator->getQuery();
$where = $queryGenerator->getConditionalWhere();
if (isset($where) && $where != '') {
	coreBOS_Session::set('export_where', $where);
} else {
	coreBOS_Session::delete('export_where');
}
$smarty->assign('export_where', to_html($where));

// Sorting
if (!empty($order_by)) {
	if ($order_by == 'smownerid') {
		$list_query .= ' ORDER BY vtiger_users.user_name '.$sorder;
	} else {
		$tablename = getTableNameForField($currentModule, $order_by);
		$tablename = ($tablename != '')? ($tablename . '.') : '';
		$list_query .= ' ORDER BY ' . $tablename . $order_by . ' ' . $sorder;
	}
}

if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0, $currentModule)) {
	$count_result = $adb->query(mkCountQuery($list_query));
	$noofrows = $adb->query_result($count_result, 0, "count");
} else {
	$noofrows = null;
}

$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, $queryMode);

$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);

$limit_start_rec = ($start-1) * $list_max_entries_per_page;

$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec, $noofrows);
$smarty->assign('recordListRange', $recordListRangeMsg);

$smarty->assign('CUSTOMVIEW_OPTION', $customview_html);

// Navigation
$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $currentModule, 'index', $viewid);
$smarty->assign('NAVIGATION', $navigationOutput);

$controller = new ListViewController($adb, $current_user, $queryGenerator);
$listview_header = $controller->getListViewHeader($focus, $currentModule, $url_string, $sorder, $order_by);
$listview_entries = $controller->getListViewEntries($focus, $currentModule, $list_result, $navigation_array);
$listview_header_search = $controller->getBasicSearchFieldInfoList();

$smarty->assign('LISTHEADER', $listview_header);
$smarty->assign('LISTENTITY', $listview_entries);
$smarty->assign('SEARCHLISTHEADER', $listview_header_search);

// Module Search
$alphabetical = AlphabeticalSearch($currentModule, 'index', $focus->def_basicsearch_col, 'true', 'basic', '', '', '', '', $viewid);
$fieldnames = $controller->getAdvancedSearchOptionString();
$smarty->assign('ALPHABETICAL', $alphabetical);
$smarty->assign('FIELDNAMES', $fieldnames);

$smarty->assign('AVALABLE_FIELDS', getMergeFields($currentModule, 'available_fields'));
$smarty->assign('FIELDS_TO_MERGE', getMergeFields($currentModule, 'fields_to_merge'));

//Added to select Multiple records in multiple pages
$smarty->assign('SELECTEDIDS', vtlib_purify($_REQUEST['selobjs']));
$smarty->assign('ALLSELECTEDIDS', vtlib_purify($_REQUEST['allselobjs']));
$smarty->assign('CURRENT_PAGE_BOXES', implode(array_keys($listview_entries), ';'));
coreBOS_Session::set($currentModule.'_listquery', $list_query);

// Gather the custom link information to display
include_once 'vtlib/Vtiger/Link.php';
$customlink_params = array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']), 'CATEGORY'=> $category);
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));

if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '') {
	$smarty->display("ListViewEntries.tpl");
} else {
	$smarty->display('ListView.tpl');
}
?>
