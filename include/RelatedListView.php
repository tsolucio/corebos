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
  * @param $module -- modulename:: Type string
  * @param $relatedmodule -- relatedmodule:: Type string
  * @param $focus -- focus:: Type object
  * @param $query -- query:: Type string
  * @param $button -- buttons:: Type string
  * @param $returnset -- returnset:: Type string
  * @param $id -- id:: Type string
  * @param $edit_val -- edit value:: Type string
  * @param $del_val -- delete value:: Type string
  * @returns $related_entries -- related entires:: Type string array
  */
function GetRelatedListBase($module, $relatedmodule, $focus, $query, $button, $returnset, $id = '', $edit_val = '', $del_val = '', $skipActions = false) {
	$log = LoggerManager::getLogger('account_list');
	$log->debug('Entering GetRelatedList('.$module.','.$relatedmodule.','.get_class($focus).','.$query.','.$button.','.$returnset.','.$edit_val.','.$del_val.') method');
	global $GetRelatedList_ReturnOnlyQuery;
	if (isset($GetRelatedList_ReturnOnlyQuery) && $GetRelatedList_ReturnOnlyQuery) {
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

	// We do not have RelatedListView in Detail View mode of Calendar module. So need to skip it.
	if ($module!= 'Calendar') {
		$focus->initSortByField($relatedmodule);
	}
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
		$modObj->sortby = $focus->default_order_by;
		$modObj->sorder = $focus->default_sort_order;
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
		$order_by = $focus->default_order_by;
		$sorder = $focus->default_sort_order;
	}

	// AssignedTo ordering issue in Related Lists
	$query_order_by = $order_by;
	if ($order_by == 'smownerid') {
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name',
														'last_name' => 'vtiger_users.last_name'), 'Users');
		$query_order_by = "case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end ";
	} elseif ($order_by != 'crmid' && !empty($order_by)) {
		$tabname = getTableNameForField($relatedmodule, $order_by);
		if ($tabname !== '' && $tabname != null) {
			$query_order_by = $tabname.'.'.$query_order_by;
		}
	}
	if (!empty($query_order_by)) {
		$query .= ' ORDER BY '.$query_order_by.' '.$sorder;
	}

	if ($relatedmodule == 'Calendar') {
		$mod_listquery = 'activity_listquery';
	} else {
		$mod_listquery = strtolower($relatedmodule).'_listquery';
	}
	coreBOS_Session::set($mod_listquery, $query);

	$url_qry ='&order_by='.$order_by.'&sorder='.$sorder;
	$computeCount = isset($_REQUEST['withCount']) ? $_REQUEST['withCount'] : '';
	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0, $module) || (boolean) $computeCount == true) {
		// Retreiving the no of rows
		list($specialPermissionWithDuplicateRows,$cached) = VTCacheUtils::lookupCachedInformation('SpecialPermissionWithDuplicateRows');
		if (false && ($specialPermissionWithDuplicateRows || $relatedmodule == 'Calendar')) {
			// FIXME FIXME FIXME FIXME
			// the FALSE above MUST be eliminated, we need to execute mkCountWithFullQuery for modified queries
			// the problem is that related list queries are hardcoded and can (mostly do) repeat columns which is not supported as a
			// subquery which is what mkCountWithFullQuery does
			// This works on ListView because we use query generator that eliminates those repeated columns
			// It is currently incorrect and will produce wrong count on related lists when special permissions are active
			// FIXME FIXME FIXME FIXME
			// for calendar (with multiple contacts for single activity) and special permissions, count will change
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

	$list_result = $adb->pquery($query." LIMIT $limit_start_rec, $list_max_entries_per_page", array());

	/* Save the related list in session for when we click in a register
	 * from this list we will can navigate with the arrows left and right, to move only in this related list
	 */
	$relcv = new CustomView();
	$relviewId = $relcv->getViewId($relatedmodule);
	ListViewSession::setSessionQuery($relatedmodule, $query, $relviewId);
	coreBOS_Session::set('lvs^'.$relatedmodule.'^'.$relviewId.'^start', $start);

	//Retreive the List View Table Header
	$id = vtlib_purify($_REQUEST['record']);
	$listview_header = getListViewHeader($focus, $relatedmodule, '', $sorder, $order_by, $id, '', $module, $skipActions);//"Accounts");
	if ($noofrows > 15) {
		$smarty->assign('SCROLLSTART', '<div style="overflow:auto;height:315px;width:100%;">');
		$smarty->assign('SCROLLSTOP', '</div>');
	}
	$smarty->assign('LISTHEADER', $listview_header);

	if ($module == 'PriceBook' && $relatedmodule == 'Products') {
		$listview_entries = getListViewEntries($focus, $relatedmodule, $list_result, $navigation_array, 'relatedlist', $returnset, $edit_val, $del_val, '', '', '', '', $skipActions);
	}
	if ($module == 'Products' && $relatedmodule == 'PriceBooks') {
		$listview_entries = getListViewEntries($focus, $relatedmodule, $list_result, $navigation_array, 'relatedlist', $returnset, 'EditListPrice', 'DeletePriceBookProductRel', '', '', '', '', $skipActions);
	} elseif ($relatedmodule == 'SalesOrder') {
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

	$log->debug('Exiting GetRelatedList method ...');
	return $related_entries;
}

/** Function to get related list entries in detailed array format
  * @param $parentmodule -- parentmodulename:: Type string
  * @param $query -- query:: Type string
  * @param $id -- id:: Type string
  * @returns $return_data -- return data:: Type string array
  */
function getHistory($parentmodule, $query, $id) {
	global $log, $adb, $app_strings, $current_user;
	$log->debug('Entering getHistory('.$parentmodule.','.$query.','.$id.') method ...');

	//Appending the security parameter
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	$tab_id = getTabid('Calendar');
	if ($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3) {
		$sec_parameter=getListViewSecurityParameter('Calendar');
		$query .= ' '.$sec_parameter;
	}
	$query.= ' ORDER BY vtiger_activity.date_start DESC,vtiger_activity.time_start DESC';
	$result=$adb->query($query);
	$noofrows = $adb->num_rows($result);

	if ($noofrows == 0) {
		//There is no entries for history
	} else {
		//Form the header columns
		$header[] = $app_strings['LBL_TYPE'];
		$header[] = $app_strings['LBL_SUBJECT'];
		$header[] = $app_strings['LBL_RELATED_TO'];
		$header[] = $app_strings['LBL_START_DATE'].' & '.$app_strings['LBL_TIME'];
		$header[] = $app_strings['LBL_END_DATE'].' & '.$app_strings['LBL_TIME'];
		//$header[] = $app_strings['LBL_DESCRIPTION'];
		$header[] = $app_strings['LBL_STATUS'];
		$header[] = $app_strings['LBL_ASSIGNED_TO'];

		$i = 1;
		while ($row = $adb->fetch_array($result)) {
			$entries = array();
			if ($row['activitytype'] == 'Task') {
				$activitymode = 'Task';
				$icon = 'Tasks.gif';
				$status = $row['status'];
				$status = $app_strings[$status];
			} else {
				$activitymode = 'Events';
				$icon = 'Activities.gif';
				$status = $row['eventstatus'];
				$status = $app_strings[$status];
			}

			$typeofactivity = $row['activitytype'];
			$typeofactivity = getTranslatedString($typeofactivity, 'Calendar');
			$entries[] = $typeofactivity;

			$activity = '<a href="index.php?module=cbCalendar&action=DetailView&return_module='.$parentmodule.'&return_action=DetailView&record='.$row['activityid'] .'&activity_mode='.$activitymode.'&return_id='.vtlib_purify($_REQUEST['record']).'&parenttab='.vtlib_purify($_REQUEST['parenttab']).'">'.$row['subject'].'</a></td>';
			$entries[] = $activity;

			$parentname = getRelatedTo('Calendar', $result, $i-1);
			$entries[] = $parentname;

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

		$return_data = array('header'=>$header,'entries'=>$entries_list);
		$log->debug('Exiting getHistory method ...');
		return $return_data;
	}
}

/**	Function to display the Products which are related to the PriceBook
 *	@param string $query - query to get the list of products which are related to the current PriceBook
 *	@param object $focus - PriceBook object which contains all the information of the current PriceBook
 *	@param string $returnset - return_module, return_action and return_id which are sequenced with & to pass to the URL which is optional
 *	@return array $return_data which will be formed like
 *		array('header'=>$header,'entries'=>$entries_list)
 *		where as $header contains all the header columns and $entries_list will contain all the Product entries
 */
function getPriceBookRelatedProducts($query, $focus, $returnset = '') {
	global $log, $adb, $app_strings, $mod_strings, $current_language,$current_user, $theme;
	$log->debug('Entering getPriceBookRelatedProducts('.$query.','.get_class($focus).','.$returnset.') method ...');

	return_module_language($current_language, 'PriceBook');
	$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, 'PriceBook');
	$pricebook_id = vtlib_purify($_REQUEST['record']);

	$computeCount = (isset($_REQUEST['withCount']) ? $_REQUEST['withCount'] : false);
	if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0, 'PriceBooks') || ((boolean) $computeCount) == true) {
		$rs = $adb->query(mkCountQuery($query));
		$noofrows = $adb->query_result($rs, 0, 'count');
	} else {
		$noofrows = null;
	}

	$module = 'PriceBooks';
	$relatedmodule = 'Products';
	if (empty($_SESSION['rlvs'][$module][$relatedmodule])) {
		$modObj = new ListViewSession();
		$modObj->sortby = $focus->default_order_by;
		$modObj->sorder = $focus->default_sort_order;
		coreBOS_Session::set('rlvs^'.$module.'^'.$relatedmodule, get_object_vars($modObj));
	}

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

	$list_result = $adb->pquery($query." LIMIT $limit_start_rec, $list_max_entries_per_page", array());

	$header=array();
	$header[]=$mod_strings['LBL_LIST_PRODUCT_NAME'];
	if (getFieldVisibilityPermission('Products', $current_user->id, 'productcode') == '0') {
		$header[]=$mod_strings['LBL_PRODUCT_CODE'];
	}
	if (getFieldVisibilityPermission('Products', $current_user->id, 'unit_price') == '0') {
		$header[]=$mod_strings['LBL_PRODUCT_UNIT_PRICE'];
	}
	$header[]=$mod_strings['LBL_PB_LIST_PRICE'];
	if (isPermitted('PriceBooks', 'EditView', '') == 'yes' || isPermitted('PriceBooks', 'Delete', '') == 'yes') {
		$header[]=$mod_strings['LBL_ACTION'];
	}

	$currency_id = $focus->column_fields['currency_id'];
	$numRows = $adb->num_rows($list_result);
	for ($i=0; $i<$numRows; $i++) {
		$entity_id = $adb->query_result($list_result, $i, 'crmid');
		$unit_price = $adb->query_result($list_result, $i, 'unit_price');
		if ($currency_id != null) {
			$prod_prices = getPricesForProducts($currency_id, array($entity_id));
			$unit_price = $prod_prices[$entity_id];
		}
		$listprice = $adb->query_result($list_result, $i, 'listprice');

		$entries = array();
		$entries[] = textlength_check($adb->query_result($list_result, $i, 'productname'));
		if (getFieldVisibilityPermission('Products', $current_user->id, 'productcode') == '0') {
			$entries[] = $adb->query_result($list_result, $i, 'productcode');
		}
		if (getFieldVisibilityPermission('Products', $current_user->id, 'unit_price') == '0') {
			$entries[] = CurrencyField::convertToUserFormat($unit_price, null, true);
		}

		$entries[] = CurrencyField::convertToUserFormat($listprice, null, true);
		$action = '';
		if (isPermitted('PriceBooks', 'EditView', '') == 'yes' && isPermitted('Products', 'EditView', $entity_id) == 'yes') {
			$action .= '<img style="cursor:pointer;" src="'. vtiger_imageurl('editfield.gif', $theme)
				.'" border="0" onClick="fnvshobj(this,\'editlistprice\'),editProductListPrice(\''.$entity_id.'\',\''.$pricebook_id.'\',\''.$listprice.'\')" alt="'
				.$app_strings['LBL_EDIT_BUTTON'].'" title="'.$app_strings['LBL_EDIT_BUTTON'].'"/>';
		} else {
			$action .= '<img src="'. vtiger_imageurl('blank.gif', $theme).'" border="0" />';
		}
		if (isPermitted('PriceBooks', 'Delete', '') == 'yes' && isPermitted('Products', 'Delete', $entity_id) == 'yes') {
			if ($action != '') {
				$action .= '&nbsp;|&nbsp;';
			}
			$action .= '<img src="'. vtiger_imageurl('delete.gif', $theme).'" onclick="if(confirm(\''.$app_strings['ARE_YOU_SURE'].'\')) deletePriceBookProductRel('
				.$entity_id.','.$pricebook_id.');" alt="'.$app_strings['LBL_DELETE'].'" title="'.$app_strings['LBL_DELETE'].'" style="cursor:pointer;" border="0">';
		}
		if ($action != '') {
			$entries[] = $action;
		}
		$entries_list[] = $entries;
	}
	$navigationOutput[] = getRecordRangeMessage($list_result, $limit_start_rec, $noofrows);
	$navigationOutput[] = getRelatedTableHeaderNavigation(
		$navigation_array,
		'',
		$module,
		$relatedmodule,
		$focus->id
	);
	$return_data = array('header'=>$header,'entries'=>$entries_list,'navigation'=>$navigationOutput);

	$log->debug('Exiting getPriceBookRelatedProducts method ...');
	return $return_data;
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