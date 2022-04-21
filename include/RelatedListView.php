<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/utils.php';
require_once 'include/ListView/ListViewSession.php';
require_once 'include/ListView/RelatedListViewSession.php';
require_once 'include/DatabaseUtil.php';

if (!function_exists('GetRelatedList')) {
	function GetRelatedList($module, $relatedmodule, $focus, $query, $button, $returnset, $id = '', $edit_val = '', $del_val = '', $skipActions = false) {
		return GetRelatedListBase($module, $relatedmodule, $focus, $query, $button, $returnset, $id, $edit_val, $del_val, $skipActions);
	}
}

/** Function to get related list entries in detailed array format
  * @param string module name
  * @param string related module
  * @param object related module object to get the list information
  * @param string SQL query to execute to get the rows and values
  * @param string HTML of the buttons to show on related list
  * @param string returnset
  * @param string id
  * @param string edit value (not used)
  * @param string delete value (not used)
  * @param boolean skip actions column or not
  * @return array related entires
  */
function GetRelatedListBase($module, $relatedmodule, $focus, $query, $button, $returnset, $id = '', $edit_val = '', $del_val = '', $skipActions = false) {
	$log = LoggerManager::getLogger('GetRelatedList');
	$log->debug('> GetRelatedList '.$module.','.$relatedmodule.','.get_class($focus).','.$query.','.$button.','.$returnset.','.$edit_val.','.$del_val);
	global $GetRelatedList_ReturnOnlyQuery;
	if (isset($GetRelatedList_ReturnOnlyQuery) && $GetRelatedList_ReturnOnlyQuery) {
		$order_by = $focus->getOrderBy();
		$sorder = $focus->getSortOrder();
		if (!empty($order_by)) {
			$tabname = getTableNameForField($relatedmodule, $order_by);
			if ($tabname !== '' && $tabname != null) {
				$query .= ' ORDER BY '.$tabname.'.'.$order_by.' '.$sorder;
			} else {
				$query .= ' ORDER BY '.$order_by.' '.$sorder;
			}
		}
		return array('query'=>$query);
	}
	require_once 'Smarty_setup.php';
	require_once 'data/Tracker.php';
	require_once 'include/database/PearDatabase.php';

	global $adb, $app_strings, $current_language;

	return_module_language($current_language, $module);

	global $currentModule, $theme, $theme_path, $theme_path, $mod_strings;
	$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
	$smarty = new vtigerCRM_Smarty;
	if (!isset($where)) {
		$where = '';
	}

	$button = '<table cellspacing=0 cellpadding=2><tr><td>'.$button.'</td></tr></table>';

	// Added to have Purchase Order as form Title
	$theme_path='themes/'.$theme.'/';
	$image_path=$theme_path.'images/';
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', $image_path);
	$smarty->assign('MODULE', $relatedmodule);

	$focus->initSortByField($relatedmodule);
	// Append security parameter
	if ($relatedmodule != 'Users') {
		global $current_user;
		$secQuery = getNonAdminAccessControlQuery($relatedmodule, $current_user);
		if (strlen($secQuery) > 1) {
			$query = appendFromClauseToQuery($query, $secQuery);
		}
	}
	if ($relatedmodule == 'Leads') {
		$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
		$query .= " AND vtiger_leaddetails.converted = $val_conv";
	}

	if (isset($where) && $where != '') {
		$query .= ' and '.$where;
	}

	if (empty($_SESSION['rlvs'][$module][$relatedmodule])) {
		$modObj = new ListViewSession();
		$modObj->sortby = $focus->getOrderBy();
		$modObj->sorder = $focus->getSortOrder();
		coreBOS_Session::set('rlvs^'.$module.'^'.$relatedmodule, get_object_vars($modObj));
	}

	if (!empty($_REQUEST['order_by'])) {
		if (method_exists($focus, 'getSortOrder')) {
			$sorder = $focus->getSortOrder();
		}
		if (method_exists($focus, 'getOrderBy')) {
			$order_by = $focus->getOrderBy();
		}

		if (isset($order_by) && $order_by != '') {
			coreBOS_Session::set('rlvs^'.$module.'^'.$relatedmodule.'^sorder', $sorder);
			coreBOS_Session::set('rlvs^'.$module.'^'.$relatedmodule.'^sortby', $order_by);
		}
	} elseif ($_SESSION['rlvs'][$module][$relatedmodule]) {
		$sorder = $_SESSION['rlvs'][$module][$relatedmodule]['sorder'];
		$order_by = $_SESSION['rlvs'][$module][$relatedmodule]['sortby'];
	} else {
		$order_by = $focus->getOrderBy();
		$sorder = $focus->getSortOrder();
	}

	// AssignedTo ordering issue in Related Lists
	$query_order_by = $order_by;
	if ($order_by == 'smownerid') {
		$query_order_by = "case when (vtiger_users.user_name not like '') then vtiger_users.ename else vtiger_groups.groupname end ";
	} elseif ($order_by != 'crmid' && !empty($order_by)) {
		$tabname = getTableNameForField($relatedmodule, $order_by);
		if ($tabname !== '' && $tabname != null) {
			$query_order_by = $tabname.'.'.$query_order_by;
		}
	}
	if (!empty($query_order_by)) {
		$query .= ' ORDER BY '.$query_order_by.' '.$sorder;
	}

	$mod_listquery = strtolower($relatedmodule).'_listquery';
	coreBOS_Session::set($mod_listquery, $query);

	$url_qry ='&order_by='.$order_by.'&sorder='.$sorder;
	$computeCount = isset($_REQUEST['withCount']) ? $_REQUEST['withCount'] : '';
	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0, $module) || (boolean)$computeCount) {
		list($specialPermissionWithDuplicateRows,$cached) = VTCacheUtils::lookupCachedInformation('SpecialPermissionWithDuplicateRows');
		if (false && $specialPermissionWithDuplicateRows) {
			// FIXME FIXME FIXME FIXME
			// the FALSE above MUST be eliminated, we need to execute mkCountWithFullQuery for modified queries
			// the problem is that related list queries are hardcoded and can (mostly do) repeat columns which is not supported as a
			// subquery which is what mkCountWithFullQuery does
			// This works on ListView because we use query generator that eliminates those repeated columns
			// It is currently incorrect and will produce wrong count on related lists when special permissions are active
			// FIXME FIXME FIXME FIXME
			$count_result = $adb->query(mkCountWithFullQuery($query));
		} else {
			$count_result = $adb->query(mkCountQuery($query));
		}
		$noofrows = $adb->query_result($count_result, 0, 'count');
	} else {
		$noofrows = null;
	}

	//Setting Listview session object while sorting/pagination
	if (isset($_REQUEST['relmodule']) && $_REQUEST['relmodule']!='' && $_REQUEST['relmodule'] == $relatedmodule) {
		$relmodule = vtlib_purify($_REQUEST['relmodule']);
		if ($_SESSION['rlvs'][$module][$relmodule]) {
			setSessionVar($_SESSION['rlvs'][$module][$relmodule], $noofrows, $list_max_entries_per_page, $module, $relmodule);
		}
	}
	global $relationId;
	$start = RelatedListViewSession::getRequestCurrentPage($relationId, $query);
	$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);

	$limit_start_rec = ($start-1) * $list_max_entries_per_page;

	if (GlobalVariable::getVariable('Debug_RelatedList_Query', '0') == '1') {
		echo '<br>'."$query LIMIT $limit_start_rec, $list_max_entries_per_page".'<br>';
	}
	$list_result = $adb->pquery($query." LIMIT $limit_start_rec, $list_max_entries_per_page", array());

	// Save the related list in the session so when we click on a register from this list we can navigate with the arrows and move only in this related list
	$relcv = new CustomView();
	$relviewId = $relcv->getViewId($relatedmodule);
	ListViewSession::setSessionQuery($relatedmodule, $query, $relviewId);
	coreBOS_Session::set('lvs^'.$relatedmodule.'^'.$relviewId.'^start', $start);

	$id = vtlib_purify($_REQUEST['record']);
	$listview_header = getListViewHeader($focus, $relatedmodule, '', $sorder, $order_by, $id, '', $module, $skipActions);//"Accounts");
	if ($noofrows > 15) {
		$smarty->assign('SCROLLSTART', '<div style="overflow:auto;height:315px;width:100%;">');
		$smarty->assign('SCROLLSTOP', '</div>');
	}
	$smarty->assign('LISTHEADER', $listview_header);

	if ($relatedmodule == 'SalesOrder') {
		$listview_entries = getListViewEntries($focus, $relatedmodule, $list_result, $navigation_array, 'relatedlist', $returnset, 'SalesOrderEditView', 'DeleteSalesOrder', '', '', '', '', $skipActions);
	} else {
		$listview_entries = getListViewEntries($focus, $relatedmodule, $list_result, $navigation_array, 'relatedlist', $returnset, $edit_val, $del_val, '', '', '', '', $skipActions);
	}

	$navigationOutput = array();
	$navigationOutput[] = getRecordRangeMessage($list_result, $limit_start_rec, $noofrows);
	if (empty($id) && !empty($_REQUEST['record'])) {
		$id = vtlib_purify($_REQUEST['record']);
	}
	if (($module == 'Products' && $relatedmodule == 'Products') && strpos($returnset, 'is_parent') !== false) {
		$navigationOutput[] = getRelatedTableHeaderNavigation($navigation_array, $url_qry, $module, 'Parent Product', $id);
	} else {
		$navigationOutput[] = getRelatedTableHeaderNavigation($navigation_array, $url_qry, $module, $relatedmodule, $id);
	}
	$related_entries = array('header'=>$listview_header,'entries'=>$listview_entries,'navigation'=>$navigationOutput);

	$log->debug('< GetRelatedList');
	return $related_entries;
}

/** Function to get related list entries in detailed array format
  * @param string parent module name
  * @param string query
  * @param string id
  * @return array data
  */
function getHistory($parentmodule, $query, $id) {
	global $log, $adb, $app_strings, $current_user;
	$log->debug('> getHistory '.$parentmodule.','.$query.','.$id);

	//Appending the security parameter
	$userprivs = $current_user->getPrivileges();
	$tab_id = getTabid('cbCalendar');
	if (!$userprivs->hasGlobalReadPermission() && !$userprivs->hasModuleReadSharing($tab_id)) {
		$sec_parameter=getListViewSecurityParameter('cbCalendar');
		$query .= ' '.$sec_parameter;
	}
	$query.= ' ORDER BY vtiger_activity.date_start DESC,vtiger_activity.time_start DESC';
	$result=$adb->query($query);
	$noofrows = $adb->num_rows($result);

	//Form the header columns
	$header = array();
	$header[] = $app_strings['LBL_TYPE'];
	$header[] = $app_strings['LBL_SUBJECT'];
	$header[] = $app_strings['LBL_RELATED_TO'];
	$header[] = $app_strings['LBL_START_DATE'].' & '.$app_strings['LBL_TIME'];
	$header[] = $app_strings['LBL_END_DATE'].' & '.$app_strings['LBL_TIME'];
	$header[] = $app_strings['LBL_STATUS'];
	$header[] = $app_strings['LBL_ASSIGNED_TO'];
	$entries_list = array();
	if ($noofrows > 0) {
		$i = 1;
		while ($row = $adb->fetch_array($result)) {
			$entries = array();
			if ($row['activitytype'] == 'Task') {
				$activitymode = 'Task';
				$status = $row['status'];
				$status = $app_strings[$status];
			} else {
				$activitymode = 'Events';
				$status = $row['eventstatus'];
				$status = $app_strings[$status];
			}

			$typeofactivity = $row['activitytype'];
			$typeofactivity = getTranslatedString($typeofactivity, 'cbCalendar');
			$entries[] = $typeofactivity;

			$entries[] = '<a href="index.php?module=cbCalendar&action=DetailView&return_module='.$parentmodule.'&return_action=DetailView&record='.$row['activityid']
				.'&activity_mode='.$activitymode.'&return_id='.vtlib_purify($_REQUEST['record']).'">'.$row['subject'].'</a></td>';

			$entries[] = getRelatedTo('cbCalendar', $result, $i-1);

			$date = new DateTimeField($row['date_start'].' '.$row['time_start']);
			$entries[] = $date->getDisplayDateTimeValue();
			$date = new DateTimeField($row['due_date'].' '.$row['time_end']);
			$entries[] = $date->getDisplayDate();

			$entries[] = $status;

			if ($row['user_name'] == null && $row['groupname'] != null) {
				$entries[] = $row['groupname'];
			} else {
				$entries[] = $row['user_name'];
			}

			$i++;
			$entries_list[] = $entries;
		}
	}
	$log->debug('< getHistory');
	return array('header' => $header, 'entries' => $entries_list);
}

function CheckFieldPermission($fieldname, $module) {
	global $current_user;
	if ($fieldname == '' || $module == '') {
		return 'false';
	}
	if (getFieldVisibilityPermission($module, $current_user->id, $fieldname) == '0') {
		return 'true';
	}
	return 'false';
}

function CheckColumnPermission($tablename, $columnname, $module) {
	global $adb;
	static $cache = array();
	$cachekey = $module . ':' . $tablename . ':' . $columnname;
	if (!array_key_exists($cachekey, $cache)) {
		$res = $adb->pquery('select fieldname from vtiger_field where tablename=? and columnname=? and vtiger_field.presence in (0,2)', array($tablename, $columnname));
		$fieldname = $adb->query_result($res, 0, 'fieldname');
		$cache[$cachekey] = CheckFieldPermission($fieldname, $module);
	}
	return $cache[$cachekey];
}
?>
