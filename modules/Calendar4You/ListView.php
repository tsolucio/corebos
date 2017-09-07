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
require_once("data/Tracker.php");
require_once('modules/Calendar/Activity.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('modules/Calendar/CalendarCommon.php');
require_once("modules/Calendar4You/Calendar4You.php");
require_once("modules/Calendar4You/CalendarUtils.php");

global $app_strings, $currentModule, $image_path, $theme, $adb, $current_user;
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
$log = LoggerManager::getLogger('task_list');

if (isset($_REQUEST['current_user_only'])) $current_user_only = vtlib_purify($_REQUEST['current_user_only']);

$Calendar4You = new Calendar4You();

$Calendar4You->GetDefPermission($current_user->id);

$focus = new Activity();
// Initialize sort by fields
$focus->initSortbyField('Calendar');
// END
$smarty = new vtigerCRM_Smarty;
$smarty->assign('ADD_ONMOUSEOVER', "onMouseOver=\"fnvshobj(this,'addButtonDropDown');\"");
$abelist = '';
if($current_user->column_fields['is_admin']=='on') {
	$Res = $adb->pquery('select * from vtiger_activitytype where activitytype!=?',array('Emails'));
} else {
	$role_id=$current_user->roleid;
	$subrole = getRoleSubordinates($role_id);
	if(count($subrole)> 0)
	{
		$roleids = $subrole;
		$roleids[] = $role_id;
	}
	else
	{
		$roleids = $role_id;
	}
	if (count($roleids) > 1) {
		$Res=$adb->pquery("select distinct activitytype from vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where activitytype!=? and roleid in (". generateQuestionMarks($roleids) .") and picklistid in (select picklistid from vtiger_picklist) order by sortid asc",array('Emails',$roleids));
	} else {
		$Res=$adb->pquery("select distinct activitytype from vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where activitytype!=? and roleid = ? and picklistid in (select picklistid from vtiger_picklist) order by sortid asc",array('Emails',$role_id));
	}
}
for($i=0; $i<$adb->num_rows($Res);$i++) {
	$eventlist = $adb->query_result($Res,$i,'activitytype');
	$eventlist = html_entity_decode($eventlist,ENT_QUOTES,$default_charset);
	$actname = getTranslatedString($eventlist,'Calendar');
	$abelist.='<tr><td><a href="index.php?module=Calendar4You&action=EventEditView&return_module=Calendar&return_action=index&activity_mode=Events&activitytype='.$eventlist.'" class="drop_down">'.$actname.'</a></td></tr>';
}
$abelist.='<tr><td><a href="index.php?module=Calendar4You&action=EventEditView&return_module=Calendar&return_action=index&activity_mode=Task" class="drop_down">'.$mod_strings['LBL_ADDTODO'].'</a></td></tr>';
$smarty->assign('ADD_BUTTONEVENTLIST', $abelist);
$other_text = Array();

$c_mod_strings = return_specified_module_language($current_language, "Calendar");

if(!$_SESSION['lvs'][$currentModule]) {
	coreBOS_Session::delete('lvs');
	$modObj = new ListViewSession();
	$modObj->sorder = $sorder;
	$modObj->sortby = $order_by;
	coreBOS_Session::set('lvs^'.$currentModule, get_object_vars($modObj));
}

if($_REQUEST['errormsg'] != '') {
	$errormsg = vtlib_purify($_REQUEST['errormsg']);
	$smarty->assign("ERROR",$mod_strings["SHARED_EVENT_DEL_MSG"]);
} else {
	$smarty->assign("ERROR","");
}

if(ListViewSession::hasViewChanged($currentModule,$viewid)) {
	coreBOS_Session::set('ACTIVITIES_ORDER_BY', '');
}

//<<<<<<< sort ordering >>>>>>>>>>>>>
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

coreBOS_Session::set('ACTIVITIES_ORDER_BY', $order_by);
coreBOS_Session::set('ACTIVITIES_SORT_ORDER', $sorder);
//<<<<<<< sort ordering >>>>>>>>>>>>>

//<<<<cutomview>>>>>>>
$oCustomView = new CustomView("Calendar");
$viewid = $oCustomView->getViewId("Calendar");
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);

//Added to handle approving or denying status-public by the admin in CustomView
$statusdetails = $oCustomView->isPermittedChangeStatus($viewnamedesc['status'],$viewid);
$smarty->assign("CUSTOMVIEW_PERMISSION",$statusdetails);

//To check if a user is able to edit/delete a customview
$edit_permit = $oCustomView->isPermittedCustomView($viewid,'EditView','Calendar');
$delete_permit = $oCustomView->isPermittedCustomView($viewid,'Delete','Calendar');
$smarty->assign("CV_EDIT_PERMIT",$edit_permit);
$smarty->assign("CV_DELETE_PERMIT",$delete_permit);

//<<<<<customview>>>>>
if($viewid == 0 ) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='".vtiger_imageurl('close.gif', $theme)."'></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>".$app_strings['LBL_PERMISSION']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br>
		</td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
	exit;
}

$changeOwner = getAssignedTo(16);
$userList = $changeOwner[0];
$groupList = $changeOwner[1];

$smarty->assign("CHANGE_USER",$userList);
$smarty->assign("CHANGE_GROUP",$groupList);
$smarty->assign("CHANGE_OWNER",getUserslist());
$smarty->assign("CHANGE_GROUP_OWNER",getGroupslist());
$smarty->assign('MAX_RECORDS', $list_max_entries_per_page);
$where = "";

$url_string = ''; // assigning http url string

if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') {

	list($where, $ustring) = explode("#@@#",getWhereCondition('Calendar'));
	// we have a query
	$url_string .="&query=true".$ustring;
	$log->info("Here is the where clause for the list view: $where");
	$smarty->assign("SEARCH_URL",$url_string);
}

if($viewnamedesc['viewname'] == 'All') {
	$smarty->assign("ALL", 'All');
}

if(isPermitted("Calendar","Delete",$_REQUEST['record']) == 'yes') {
	$other_text['del'] = $app_strings[LBL_MASS_DELETE];
}
if(isPermitted('Calendar','EditView','') == 'yes') {
	$other_text['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
}
$title_display = $current_module_strings['LBL_LIST_FORM_TITLE'];

//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
$sql_error = false;
if (!$Calendar4You->view_all) {
	$userid = $current_user->id;
	$invites = true;
} else {
	$userid = '';
	$invites = true;
}
try {
	$list_query = getCalendar4YouListQuery($userid, $invites);
	if($viewid != "0") {
		$list_query = $oCustomView->getModifiedCvListQuery($viewid,$list_query,"Calendar");
	}
} catch (Exception $e) {
	$sql_error = true;
}
//<<<<<<<<customview>>>>>>>>>
$smarty->assign('SQLERROR',$sql_error);
if ($sql_error) {
	$smarty->assign('ERROR', getTranslatedString('ERROR_GETTING_FILTER'));
	$smarty->assign("CUSTOMVIEW_OPTION",$customview_html);
} else {
if(isset($where) && $where != '') {
	if(isset($_REQUEST['from_homepagedb']) && $_REQUEST['from_homepagedb'] == 'true')
		$list_query .= " and ((vtiger_activity.status!='Completed' and vtiger_activity.status!='Deferred') or vtiger_activity.status is null) and ((vtiger_activity.eventstatus!='Held' and vtiger_activity.eventstatus!='Not Held') or vtiger_activity.eventstatus is null) AND ".$where;
	else
		$list_query .= " AND " .$where;
}
if (isset($_REQUEST['from_homepage'])) {
	$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s'));
	$userStartDate = $dbStartDateTime->getDisplayDate();
	$userStartDateTime = new DateTimeField($userStartDate.' 00:00:00');
	$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();

	$userEndDateTime = new DateTimeField($userStartDate.' 23:59:00');
	$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();

	if ($_REQUEST['from_homepage'] == 'upcoming_activities')
		$list_query .= " AND (vtiger_activity.status is NULL OR vtiger_activity.status not in ('Completed','Deferred')) and (vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus not in ('Held','Not Held')) AND (CAST((CONCAT(date_start,' ',time_start)) AS DATETIME) >= '$startDateTime' OR CAST((CONCAT(vtiger_recurringevents.recurringdate,' ',time_start)) AS DATETIME) >= '$startDateTime')";
	elseif ($_REQUEST['from_homepage'] == 'pending_activities')
		$list_query .= " AND (vtiger_activity.status is NULL OR vtiger_activity.status not in ('Completed','Deferred')) and (vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus not in ('Held','Not Held')) AND (CAST((CONCAT(due_date,' ',time_end)) AS DATETIME) <= '$endDateTime' OR CAST((CONCAT(vtiger_recurringevents.recurringdate,' ',time_start)) AS DATETIME) <= '$endDateTime')";
}
if(isset($order_by) && $order_by != '') {
	if($order_by == 'smownerid') {
		$list_query .= ' ORDER BY user_name '.$sorder;
	} else {
		$tablename = getTableNameForField('Calendar',$order_by);
		$tablename = (($tablename != '')?($tablename."."):'');
		if($order_by == 'lastname')
			$list_query .= ' ORDER BY vtiger_contactdetails.lastname '.$sorder;
		else
			$list_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
	}
}
if (GlobalVariable::getVariable('Debug_ListView_Query', '0')=='1') {
	echo '<br>'.$list_query.'<br>';
}
try {
if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0)) {
	$count_query = preg_replace("/[\n\r\s]+/", " ", $list_query);
	$count_query = 'SELECT 1 ' . substr($count_query, stripos($count_query, ' FROM '), strlen($count_query));
	if (stripos($count_query, 'ORDER BY') > 0)
		$count_query = substr($count_query, 0, stripos($count_query, 'ORDER BY'));
	$count_result = $adb->query("SELECT count(*) AS count FROM ($count_query) as calcount");
	$noofrows = $adb->query_result($count_result,0,'count');
}else{
	$noofrows = null;
}

$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
$start = ListViewSession::getRequestCurrentPage('Calendar', $list_query, $viewid, $queryMode);

$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);

$limit_start_rec = ($start-1) * $list_max_entries_per_page;

$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());
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

//Retreive the List View Table Header
if($viewid !='')
$url_string .="&viewname=".$viewid;

if (!empty($viewid)){
	if (!isset($oCustomView->list_fields['Close'])) $oCustomView->list_fields['Close']=array('vtiger_activity' => 'eventstatus');
	if (!isset($oCustomView->list_fields_name['Close'])) $oCustomView->list_fields_name['Close']='eventstatus';
}
$listview_header = getListViewHeader($focus,"Calendar",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("LISTHEADER", $listview_header);

$listview_header_search=getSearchListHeaderValues($focus,"Calendar",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("SEARCHLISTHEADER", $listview_header_search);

$edit_permissions = $Calendar4You->CheckPermissions("EDIT");

if(!$edit_permissions)
	$editlistview = 'EditView';
else
	$editlistview = '';

$delete_permissions = $Calendar4You->CheckPermissions("DELETE");

if(!$delete_permissions)
	$deletelistview = 'Delete';
else
	$deletelistview = '';

$listview_entries = getListViewEntries($focus,"Calendar",$list_result,$navigation_array,"","","","",$oCustomView);

$smarty->assign("LISTENTITY", $listview_entries);

} // end sqlerror

//Constructing the list view
$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);
$smarty->assign("VIEWID", $viewid);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("CMOD", $c_mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",getTranslatedString('SINGLE_'.$currentModule, $currentModule));
$smarty->assign("BUTTONS",$other_text);
$smarty->assign("NEW_EVENT",$app_strings['LNK_NEW_EVENT']);
$smarty->assign("NEW_TASK",$app_strings['LNK_NEW_TASK']);

//Added to select Multiple records in multiple pages
$smarty->assign("SELECTEDIDS", vtlib_purify($_REQUEST['selobjs']));
$smarty->assign("ALLSELECTEDIDS", vtlib_purify($_REQUEST['allselobjs']));
$smarty->assign("CURRENT_PAGE_BOXES", implode(array_keys($listview_entries),";"));

$navigationOutput = getTableHeaderSimpleNavigation($navigation_array,$url_string,"Calendar4You","ListView",$viewid);
$alphabetical = AlphabeticalSearch('Calendar4You','ListView','subject','true','basic',"","","","",$viewid);
$fieldnames = getAdvSearchfields($module);
$criteria = getcriteria_options();
$smarty->assign("CRITERIA", $criteria);
$smarty->assign("FIELDNAMES", $fieldnames);
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("NAVIGATION", $navigationOutput);
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

coreBOS_Session::set($currentModule.'_listquery', $list_query);

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']), 'CATEGORY'=> $category);
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));
} // try query
$smarty->assign('IS_ADMIN', is_admin($current_user));

// Search Panel Status
$DEFAULT_SEARCH_PANEL_STATUS = GlobalVariable::getVariable('Application_Search_Panel_Open',1);
$smarty->assign('DEFAULT_SEARCH_PANEL_STATUS',($DEFAULT_SEARCH_PANEL_STATUS ? 'display: block' : 'display: none'));

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("ListViewEntries.tpl");
else
	$smarty->display("modules/Calendar4You/EventListView.tpl");
?>
