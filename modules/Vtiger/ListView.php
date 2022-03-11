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

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once "modules/$currentModule/$currentModule.php";

$url_string = '';
$massedit1x1 = isset($_REQUEST['massedit1x1']) ? vtlib_purify($_REQUEST['massedit1x1']) : 0;
if ($massedit1x1=='c') { // mass edit 1x1 cancelled
	coreBOS_Session::delete('ME1x1Info');
}

if (!isset($tool_buttons)) {
	$tool_buttons = Button_Check($currentModule);
}

$focus = new $currentModule();
$focus->initSortbyField($currentModule);
$list_buttons=$focus->getListButtons($app_strings, $mod_strings);

if (ListViewSession::hasViewChanged($currentModule)) {
	coreBOS_Session::set($currentModule.'_Order_By', '');
}
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

coreBOS_Session::set($currentModule.'_Order_By', $order_by);
coreBOS_Session::set($currentModule.'_Sort_Order', $sorder);

$smarty = new vtigerCRM_Smarty();

// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);

$smarty->assign('MAX_RECORDS', $list_max_entries_per_page);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('BUTTONS', $list_buttons);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

if (empty($ERROR_MESSAGE) && !empty($_REQUEST['error_msg'])) {
	if (isset($_REQUEST['error_msgclass'])) {
		$ERROR_MESSAGE_CLASS = vtlib_purify($_REQUEST['error_msgclass']);
	}
	$ERROR_MESSAGE = vtlib_purify($_REQUEST['error_msg']);
}
if (!empty($ERROR_MESSAGE)) {
	$smarty->assign('ERROR_MESSAGE_CLASS', isset($ERROR_MESSAGE_CLASS) ? $ERROR_MESSAGE_CLASS : 'cb-alert-info');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString($ERROR_MESSAGE, $currentModule));
}
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
$sql_error = false;
$queryGenerator = new QueryGenerator($currentModule, $current_user);
try {
	if ($viewid != '0') {
		$queryGenerator->initForCustomViewById($viewid);
	} else {
		$queryGenerator->initForDefaultCustomView();
	}
} catch (Exception $e) {
	$sql_error = true;
}
$smarty->assign('SQLERROR', $sql_error);
if ($sql_error) {
	$smarty->assign('ERROR', getTranslatedString('ERROR_GETTING_FILTER'));
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-error');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERROR_GETTING_FILTER', $currentModule));
	$smarty->assign('CUSTOMVIEW_OPTION', $customview_html);
	$smarty->assign('SEARCHLISTHEADER', array());
	$alphabetical = AlphabeticalSearch($currentModule, 'index', $focus->def_basicsearch_col, 'true', 'basic', '', '', '', '', $viewid);
	$smarty->assign('ALPHABETICAL', $alphabetical);
	$smarty->assign('FIELDNAMES', array());
	$smarty->assign('CRITERIA', array());
	$smarty->assign('SEARCH_URL', '');
	$smarty->assign('export_where', '');
	$smarty->assign('SELECTEDIDS', '');
	$smarty->assign('ALLSELECTEDIDS', '');
	$smarty->assign('CURRENT_PAGE_BOXES', '');
	$smarty->assign('NAVIGATION', '');
	$smarty->assign('recordListRange', '');
	$smarty->assign('CUSTOM_LINKS', '');
	$smarty->assign('LISTHEADER', '');
	$smarty->assign('LISTENTITY', array());
} else {
// Enabling Module Search
	$url_string = '';
	if (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') {
		$queryGenerator->addUserSearchConditions($_REQUEST);
		$ustring = getSearchURL($_REQUEST);
		$url_string .= "&query=true$ustring";
	}
	$smarty->assign('SEARCH_URL', $url_string);
	if (!empty($order_by)) {
		$queryGenerator->addWhereField($order_by);
	}
	$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.before', $queryGenerator);
	$list_query = $queryGenerator->getQuery();
	$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.after', $queryGenerator);
	$list_query = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.query', $list_query);
	$where = $queryGenerator->getConditionalWhere();
	if (isset($where) && $where != '') {
		coreBOS_Session::set('export_where', $where);
	} else {
		coreBOS_Session::delete('export_where');
	}
	$smarty->assign('export_where', to_html($where));

	// Sorting
	if (!empty($order_by)) {
		$list_query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.$sorder;
	}
	if (GlobalVariable::getVariable('Debug_ListView_Query', '0')=='1') {
		echo '<br>'.$list_query.'<br>';
	}
	try {
		$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
		$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, $queryMode);
		$limit_start_rec = ($start-1) * $list_max_entries_per_page;
		$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());
		if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
			$count_result = $adb->query(mkCountQuery($list_query));
			$noofrows = $adb->query_result($count_result, 0, 0);
		} else {
			$noofrows = null;
		}
		$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);
	} catch (Exception $e) {
		$sql_error = true;
	}
	$smarty->assign('SQLERROR', $sql_error);
	if ($sql_error) {
		$smarty->assign('ERROR', getTranslatedString('ERROR_GETTING_FILTER'));
		$smarty->assign('CUSTOMVIEW_OPTION', $customview_html);
		$smarty->assign('SEARCHLISTHEADER', array());
		$smarty->assign('FIELDNAMES', array());
		$smarty->assign('CRITERIA', array());
		$smarty->assign('SEARCH_URL', '');
		$smarty->assign('export_where', '');
		$smarty->assign('SELECTEDIDS', '');
		$smarty->assign('ALLSELECTEDIDS', '');
		$smarty->assign('CURRENT_PAGE_BOXES', '');
		$smarty->assign('NAVIGATION', '');
		$smarty->assign('recordListRange', '');
		$smarty->assign('CUSTOM_LINKS', '');
		$smarty->assign('LISTHEADER', '');
		$smarty->assign('LISTENTITY', array());
	} else {
		$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec, $noofrows);
		$smarty->assign('recordListRange', $recordListRangeMsg);

		$smarty->assign('CUSTOMVIEW_OPTION', $customview_html);

		// Navigation
		$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $currentModule, 'index', $viewid);
		$smarty->assign('NAVIGATION', $navigationOutput);

		$controller = new ListViewController($adb, $current_user, $queryGenerator);

		if (!isset($skipAction)) {
			$skipAction = false;
		}
		$smarty->assign('Document_Folder_View', 0);
		if ($currentModule == 'Documents') {
			include 'modules/Documents/ListViewCalculations.php';
		}

		$listview_header = $controller->getListViewHeader($focus, $currentModule, $url_string, $sorder, $order_by, $skipAction);
		$listview_entries = $controller->getListViewEntries($focus, $currentModule, $list_result, $navigation_array, $skipAction);

		$listview_header_search = $controller->getBasicSearchFieldInfoList();

		$smarty->assign('LISTHEADER', $listview_header);
		$smarty->assign('LISTENTITY', $listview_entries);
		$smarty->assign('SEARCHLISTHEADER', $listview_header_search);

	// Module Search
		$alphabetical = AlphabeticalSearch($currentModule, 'index', $focus->def_basicsearch_col, 'true', 'basic', '', '', '', '', $viewid);
		$fieldnames = $controller->getAdvancedSearchOptionString();
		$fieldnames_array = $controller->getAdvancedSearchOptionArray();
		$smarty->assign('ALPHABETICAL', $alphabetical);
		$smarty->assign('FIELDNAMES', $fieldnames);
		$smarty->assign('FIELDNAMES_ARRAY', $fieldnames_array);

		$smarty->assign('AVALABLE_FIELDS', getMergeFields($currentModule, 'available_fields'));
		$smarty->assign('FIELDS_TO_MERGE', getMergeFields($currentModule, 'fields_to_merge'));

	//Added to select Multiple records in multiple pages
		$smarty->assign('SELECTEDIDS', isset($_REQUEST['selobjs']) ? vtlib_purify($_REQUEST['selobjs']) : '');
		$smarty->assign('ALLSELECTEDIDS', isset($_REQUEST['selobjs']) ? vtlib_purify($_REQUEST['allselobjs']) : '');
		$smarty->assign('CURRENT_PAGE_BOXES', implode(';', array_keys($listview_entries)));
		ListViewSession::setSessionQuery($currentModule, $list_query, $viewid);

	// Gather the custom link information to display
		include_once 'vtlib/Vtiger/Link.php';
		$customlink_params = array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']));
		$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));
	}
} // try query
$smarty->assign('IS_ADMIN', is_admin($current_user));
if (isset($listview_header_search) && is_array($listview_header_search)) {
	require_once 'include/utils/ListViewUtils.php';
	$tks_list = getListColumnSearch($listview_header_search, $currentModule);
	$smarty->assign('TKS_LIST_SEARCH', $tks_list);
	$smarty->assign('LVCSearchActive', GlobalVariable::getVariable('Application_ListView_SearchColumns', 0));
	$smarty->assign('LVCSearchAcTrigger', GlobalVariable::getVariable('Application_ListView_SearchColumns_AC_Trigger', 3));
}
// Search Panel Status
$DEFAULT_SEARCH_PANEL_STATUS = GlobalVariable::getVariable('Application_ListView_SearchPanel_Open', 1);
$smarty->assign('DEFAULT_SEARCH_PANEL_STATUS', ($DEFAULT_SEARCH_PANEL_STATUS ? 'display: block' : 'display: none'));
$smarty->assign('EDIT_FILTER_ALL', GlobalVariable::getVariable('Application_Filter_All_Edit', 1));
$smarty->assign('moduleView', GlobalVariable::getVariable('Application_ListView_Layout', 'table'));
$smarty->assign('Apache_Tika_URL', GlobalVariable::getVariable('Apache_Tika_URL', ''));

if (!empty($custom_list_include) && file_exists($custom_list_include)) {
	include $custom_list_include;
}
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '') {
	$smarty->display('ListViewEntries.tpl');
} elseif (isset($custom_list_template) && $custom_list_template != '') {
	$smarty->display($custom_list_template);
} else {
	$smarty->display('ListView.tpl');
}
?>