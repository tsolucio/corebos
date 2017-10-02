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
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
require_once('Smarty_setup.php');
require_once('include/ListView/ListView.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/DatabaseUtil.php');

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");

$category = getParentTab();
$url_string = '';

if(isset($tool_buttons)==false) {
	$tool_buttons = Button_Check($currentModule);
}

$focus = new $currentModule();
$focus->initSortbyField($currentModule);
$list_buttons=$focus->getListButtons($app_strings,$mod_strings);

if(ListViewSession::hasViewChanged($currentModule)) {
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
$statusdetails = $customView->isPermittedChangeStatus($viewinfo['status'],$viewid);

// To check if a user is able to edit/delete a CustomView
$edit_permit = $customView->isPermittedCustomView($viewid,'EditView',$currentModule);
$delete_permit = $customView->isPermittedCustomView($viewid,'Delete',$currentModule);
$smarty->assign('CUSTOMVIEW_PERMISSION',$statusdetails);
$smarty->assign('CV_EDIT_PERMIT',$edit_permit);
$smarty->assign('CV_DELETE_PERMIT',$delete_permit);
$smarty->assign('VIEWID', $viewid);

if($viewinfo['viewname'] == 'All') $smarty->assign('ALL', 'All');

if ($viewid == 0) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	exit;
}

global $current_user;
$sql_error = false;
$queryGenerator = new QueryGenerator($currentModule, $current_user);
try {
	if ($viewid != "0") {
		$queryGenerator->initForCustomViewById($viewid);
	} else {
		$queryGenerator->initForDefaultCustomView();
	}
} catch (Exception $e) {
	$sql_error = true;
}
$smarty->assign('SQLERROR',$sql_error);
if ($sql_error) {
	$smarty->assign('ERROR', getTranslatedString('ERROR_GETTING_FILTER'));
	$smarty->assign("CUSTOMVIEW_OPTION",$customview_html);
} else {
// Enabling Module Search
$url_string = '';
if(isset($_REQUEST['query']) and $_REQUEST['query'] == 'true') {
	$queryGenerator->addUserSearchConditions($_REQUEST);
	$ustring = getSearchURL($_REQUEST);
	$url_string .= "&query=true$ustring";
}
$smarty->assign('SEARCH_URL', $url_string);

$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.before', $queryGenerator);
$list_query = $queryGenerator->getQuery();
$queryGenerator = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.after', $queryGenerator);
$list_query = cbEventHandler::do_filter('corebos.filter.listview.querygenerator.query', $list_query);
$where = $queryGenerator->getConditionalWhere();
if(isset($where) && $where != '') {
	coreBOS_Session::set('export_where', $where);
} else {
	coreBOS_Session::delete('export_where');
}
$smarty->assign('export_where',to_html($where));

// Sorting
if(!empty($order_by)) {
	if($order_by == 'smownerid') {
		$list_query .= ' ORDER BY vtiger_users.user_name '.$sorder;
	} else {
		$tablename = getTableNameForField($currentModule, $order_by);
		$tablename = ($tablename != '')? ($tablename . '.') : '';
		$list_query .= ' ORDER BY ' . $tablename . $order_by . ' ' . $sorder;
	}
}
if (GlobalVariable::getVariable('Debug_ListView_Query', '0')=='1') {
	echo '<br>'.$list_query.'<br>';
}
try {
	$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
	$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, $queryMode);
	$limit_start_rec = ($start-1) * $list_max_entries_per_page;
	$list_query = 'SELECT SQL_CALC_FOUND_ROWS'.substr($list_query, 6);
	$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());
	$count_result = $adb->query('SELECT FOUND_ROWS();');
	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
		$noofrows = $adb->query_result($count_result,0,0);
	}else {
		$noofrows = null;
	}
	$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);
} catch (Exception $e) {
	$sql_error = true;
}
$smarty->assign('SQLERROR',$sql_error);
if ($sql_error) {
	$smarty->assign('ERROR', getTranslatedString('ERROR_GETTING_FILTER'));
	$smarty->assign("CUSTOMVIEW_OPTION",$customview_html);
} else {

$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec,$noofrows);
$smarty->assign('recordListRange',$recordListRangeMsg);

$smarty->assign("CUSTOMVIEW_OPTION",$customview_html);

// Navigation
$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $currentModule, 'index', $viewid);
$smarty->assign("NAVIGATION", $navigationOutput);

$controller = new ListViewController($adb, $current_user, $queryGenerator);

if(!isset($skipAction)){
	$skipAction = false;
}
$smarty->assign('Document_Folder_View',0);
if ($currentModule == 'Documents') {
	include 'modules/Documents/ListViewCalculations.php';
}

$listview_header = $controller->getListViewHeader($focus,$currentModule,$url_string,$sorder,$order_by,$skipAction);
$listview_entries = $controller->getListViewEntries($focus,$currentModule,$list_result,$navigation_array,$skipAction);

$listview_header_search = $controller->getBasicSearchFieldInfoList();

$smarty->assign('LISTHEADER', $listview_header);
$smarty->assign('LISTENTITY', $listview_entries);
$smarty->assign('SEARCHLISTHEADER',$listview_header_search);

// Module Search
$alphabetical = AlphabeticalSearch($currentModule,'index',$focus->def_basicsearch_col,'true','basic','','','','',$viewid);
$fieldnames = $controller->getAdvancedSearchOptionString();
$criteria = getcriteria_options();
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("FIELDNAMES", $fieldnames);
$smarty->assign("CRITERIA", $criteria);

$smarty->assign("AVALABLE_FIELDS", getMergeFields($currentModule,"available_fields"));
$smarty->assign("FIELDS_TO_MERGE", getMergeFields($currentModule,"fileds_to_merge"));

//Added to select Multiple records in multiple pages
$smarty->assign('SELECTEDIDS', isset($_REQUEST['selobjs']) ? vtlib_purify($_REQUEST['selobjs']) : '');
$smarty->assign('ALLSELECTEDIDS', isset($_REQUEST['selobjs']) ? vtlib_purify($_REQUEST['allselobjs']) : '');
$smarty->assign('CURRENT_PAGE_BOXES', implode(array_keys($listview_entries),';'));
ListViewSession::setSessionQuery($currentModule,$list_query,$viewid);

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']), 'CATEGORY'=> $category);
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));
// END

if(isPermitted($currentModule, "Merge") == 'yes' && file_exists("modules/$currentModule/Merge.php")) {
	$wordTemplates = array();
	$wordTemplateResult = fetchWordTemplateList($currentModule);
	$tempCount = $adb->num_rows($wordTemplateResult);
	$tempVal = $adb->fetch_array($wordTemplateResult);
	for ($templateCount = 0; $templateCount < $tempCount; $templateCount++) {
		$wordTemplates[$tempVal["templateid"]] = $tempVal["filename"];
		$tempVal = $adb->fetch_array($wordTemplateResult);
	}
	$smarty->assign('WORDTEMPLATES', $wordTemplates);
}
}} // try query
$smarty->assign('IS_ADMIN', is_admin($current_user));

// Search Panel Status
$DEFAULT_SEARCH_PANEL_STATUS = GlobalVariable::getVariable('Application_Search_Panel_Open',1);
$smarty->assign('DEFAULT_SEARCH_PANEL_STATUS',($DEFAULT_SEARCH_PANEL_STATUS ? 'display: block' : 'display: none'));

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("ListViewEntries.tpl");
else
	if (isset($custom_list_template) && $custom_list_template != '') {
		$smarty->display($custom_list_template);
	} else {
		$smarty->display('ListView.tpl');		
	}

?>