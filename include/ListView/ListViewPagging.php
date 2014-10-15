<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
global $list_max_entries_per_page;

require_once('Smarty_setup.php');
require_once('include/ListView/ListView.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/DatabaseUtil.php');

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");
if(!is_string($_SESSION[$currentModule.'_listquery']) || !empty($_REQUEST['globalSearch'])){
	// Custom View
	$customView = new CustomView($currentModule);
	$viewid = $customView->getViewId($currentModule);
	$customview_html = $customView->getCustomViewCombo($viewid);
	$viewinfo = $customView->getCustomViewByCvid($viewid);

	if($viewid != "0"&& $viewid != 0){
		$listquery = getListQuery($currentModule);
		$list_query= $customView->getModifiedCvListQuery($viewid, $listquery, $currentModule);
	}else{
		$list_query = getListQuery($currentModule);
	}
    // Enabling Module Search
	$url_string = '';
	if($_REQUEST['query'] == 'true') {
        if(!empty($_REQUEST['tagSearchText'])){
            $searchValue = vtlib_purify($_REQUEST['globalSearchText']);
			$where = '(' . getTagWhere($searchValue, $current_user->id) . ')';
        } else if(!empty($_REQUEST['globalSearch'])){
			$searchValue = vtlib_purify($_REQUEST['globalSearchText']);
			$where = '(' . getUnifiedWhere($list_query,$currentModule,$searchValue) . ')';
			$url_string .= '&query=true&globalSearch=true&globalSearchText='.$searchValue;
		}else{
			list($where, $ustring) = explode('#@@#', getWhereCondition($currentModule));
			$url_string .= "&query=true$ustring";
		}
	}
	//print_r($where);
	if($where != '') {
		$list_query = "$list_query AND $where";
		$_SESSION['export_where'] = $where;
	}else{
		unset($_SESSION['export_where']);
	}
	// Sorting
	if($order_by) {
		if($order_by == 'smownerid'){
			if( $adb->dbType == "pgsql"){
				$list_query .= ' GROUP BY user_name';
			}
			$list_query .= ' ORDER BY user_name '.$sorder;
		}else {
			$tablename = getTableNameForField($currentModule, $order_by);
			$tablename = ($tablename != '')? ($tablename . '.') : '';
			if( $adb->dbType == "pgsql"){
				$list_query .= ' GROUP BY '. $tablename . $order_by;
			}
			$list_query .= ' ORDER BY ' . $tablename . $order_by . ' ' . $sorder;
		}
	}
}else{
	//TODO: remove after calendar module listview cleanup.
	//its failing for calendar module.
	$dummyQuery = getListQuery($currentModule);
	$list_query = $_SESSION[$currentModule.'_listquery'];
}

$count_result = $adb->query(mkCountQuery($list_query));
$noofrows = $adb->query_result($count_result,0,"count");

$pageNumber = ceil($noofrows/$list_max_entries_per_page);
if($pageNumber == 0){
	$pageNumber = 1;
}
echo $app_strings['LBL_LIST_OF'].' '.$pageNumber;
?>