<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (isset($_REQUEST['ajax']) && !empty($_REQUEST['emailfilter']) && isset($_REQUEST['folderid'])) {
	// Custom View
	global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
	$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
	require_once 'Smarty_setup.php';
	require_once 'include/ListView/ListView.php';
	require_once 'modules/CustomView/CustomView.php';
	require_once 'include/DatabaseUtil.php';
	$currentModule = 'Emails';
	checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
	require_once "modules/$currentModule/$currentModule.php";
	$focus = new $currentModule();
	$focus->initSortbyField($currentModule);
	$list_buttons=$focus->getListButtons($app_strings, $mod_strings);
	$tool_buttons = Button_Check($currentModule);
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('CUSTOMVIEW_OPTION', '');
	$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);
	$smarty->assign('MAX_RECORDS', $list_max_entries_per_page);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
	$smarty->assign('CATEGORY', '');
	$smarty->assign('BUTTONS', $list_buttons);
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('SQLERROR', 0);
	$customView = new CustomView($currentModule);
	$viewid = $customView->getViewId($currentModule);
	$smarty->assign('CUSTOMVIEW_PERMISSION', array('Status' => CV_STATUS_DEFAULT, 'ChangedStatus' => '', 'Label' => ''));
	$smarty->assign('CV_EDIT_PERMIT', 'no');
	$smarty->assign('CV_DELETE_PERMIT', 'no');
	$smarty->assign('VIEWID', $viewid);
	$folderid = vtlib_purify($_REQUEST['folderid']);
	$prevfolderid = coreBOS_Session::get('lvs^Emails^emailfolder', $folderid);
	coreBOS_Session::set('lvs^Emails^emailfolder', $folderid);
	if ($prevfolderid!=$folderid) {
		coreBOS_Session::set('lvs^Emails^'.$viewid.'^start', 1);
	}
	$queryGenerator = new QueryGenerator($currentModule, $current_user);
	if ($viewid != '0') {
		$queryGenerator->initForCustomViewById($viewid);
	} else {
		$queryGenerator->initForDefaultCustomView();
	}
	$smarty->assign('SEARCH_URL', '');
	$sorder = $focus->getSortOrder();
	$order_by = $focus->getOrderBy();
	if (!empty($order_by)) {
		$queryGenerator->addWhereField($order_by);
	}
	$list_query = $queryGenerator->getQuery();
	$where = $queryGenerator->getConditionalWhere();
	$smarty->assign('export_where', to_html($where));
	$emailwhere = $queryGenerator->getWhereClause();
	$addseactrel = false;
	$addsemanrel = false;
	switch ($folderid) {
		case '2':
			$emailwhere .= " AND vtiger_seactivityrel.crmid in (select contactid from vtiger_contactdetails) AND vtiger_emaildetails.email_flag !='WEBMAIL'";
			$addseactrel = true;
			break;
		case '3':
			$emailwhere .= ' AND vtiger_seactivityrel.crmid in (select accountid from vtiger_account)';
			$addseactrel = true;
			break;
		case '4':
			$emailwhere .= ' AND vtiger_seactivityrel.crmid in (select leadid from vtiger_leaddetails)';
			$addseactrel = true;
			break;
		case '5':
			$emailwhere .= ' AND vtiger_salesmanactivityrel.smid in (select id from vtiger_users)';
			$addsemanrel = true;
			break;
		case '6':
			$emailwhere .= " AND vtiger_emaildetails.email_flag ='WEBMAIL'";
			break;
	}
	if ($addseactrel || $addsemanrel) {
		$list_query = 'SELECT '.$queryGenerator->getSelectClauseColumnSQL().' '.$queryGenerator->getFromClause();
		if ($addseactrel) {
			$list_query.= 'INNER JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid=vtiger_activity.activityid';
		} else {
			$list_query.= 'INNER JOIN vtiger_salesmanactivityrel ON vtiger_salesmanactivityrel.activityid=vtiger_activity.activityid';
		}
		$list_query.= $emailwhere;
	}
	// Sorting
	if (!empty($order_by)) {
		$list_query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($order_by).' '.$sorder;
	}
	if (GlobalVariable::getVariable('Debug_ListView_Query', '0')=='1') {
		echo '<br>'.$list_query.'<br>';
	}
	$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, false);
	$limit_start_rec = ($start-1) * $list_max_entries_per_page;
	$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());
	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
		$count_result = $adb->query(mkCountQuery($list_query));
		$noofrows = $adb->query_result($count_result, 0, 0);
	} else {
		$noofrows = null;
	}
	$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);
	$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec, $noofrows);
	$smarty->assign('recordListRange', $recordListRangeMsg);
	// Navigation
	$_REQUEST['cbcustompopupinfo'] = 'emailfilter;folderid';
	$_REQUEST['emailfilter']=1;
	$_REQUEST['folderid']=$folderid;
	$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, '', 'MailManager', 'index', $viewid);
	$smarty->assign('NAVIGATION', $navigationOutput);

	$controller = new ListViewController($adb, $current_user, $queryGenerator);
	$listview_header = $controller->getListViewHeader($focus, $currentModule, '', $sorder, $order_by, false);
	$listview_entries = $controller->getListViewEntries($focus, $currentModule, $list_result, $navigation_array, false);

	$smarty->assign('LISTHEADER', $listview_header);
	$smarty->assign('LISTENTITY', $listview_entries);

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
	$smarty->assign('CUSTOM_LINKS', '');
	$smarty->assign('IS_ADMIN', is_admin($current_user));
	$smarty->display('modules/Emails/ListViewEntries.tpl');
	$currentModule = 'MailManager';
} else {
	include __DIR__ . '/index.php';
}
?>