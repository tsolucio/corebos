<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');
require_once('modules/Calendar/calendarLayout.php');
//include_once 'modules/Calendar/header.php';
require_once 'modules/CustomView/CustomView.php';
require_once("modules/Calendar4You/Calendar4You.php");
require_once("modules/Calendar4You/CalendarUtils.php");

global $mod_strings,$app_strings,$theme, $currentModule,$adb, $current_user, $singlepane_view, $current_language;
$_REQUEST = vtlib_purify($_REQUEST);  // clean up ALL values
$record = vtlib_purify($_REQUEST['record']);

$Calendar4You = new Calendar4You();

$Calendar4You->GetDefPermission($current_user->id);

$detail_permissions = $Calendar4You->CheckPermissions("DETAIL",$record);

if(!$detail_permissions) {
  	NOPermissionDiv();
}

if( $_SESSION['mail_send_error']!="") {
	echo '<b><font color=red>'. $mod_strings{"LBL_NOTIFICATION_ERROR"}.'</font></b><br>';
}
session_unregister('mail_send_error');

$c_mod_strings = return_specified_module_language($current_language, "Calendar");

$focus = CRMEntity::getInstance("Calendar");
$smarty =  new vtigerCRM_Smarty();
$activity_mode = vtlib_purify($_REQUEST['activity_mode']);
//If activity_mode == null
if(empty($activity_mode)) {
    $activity_mode = getEventActivityMode($record);
}

if($activity_mode == 'Task') {
    $tab_type = 'Calendar';
    $rel_tab_type = 'Calendar4You';
	$smarty->assign("SINGLE_MOD",$c_mod_strings['LBL_TODO']);
} elseif($activity_mode == 'Events') {
    $rel_tab_type = $tab_type = 'Events';
	$smarty->assign("SINGLE_MOD",$c_mod_strings['LBL_EVENT']);
}

if(isset($record) && $record!="") {
    $focus->retrieve_entity_info($record,$tab_type);
    $focus->id = $record;
    $focus->name=$focus->column_fields['subject'];
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
} 

//needed when creating a new task with default values passed in
if (isset($_REQUEST['contactname']) && is_null($focus->contactname)) {
	$focus->contactname = $_REQUEST['contactname'];
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) {
	$focus->contact_id = $_REQUEST['contact_id'];
}
if (isset($_REQUEST['opportunity_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['opportunity_name'];
}
if (isset($_REQUEST['opportunity_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['opportunity_id'];
}
if (isset($_REQUEST['accountname']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['accountname'];
}
if (isset($_REQUEST['accountid']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['accountid'];
}

$act_data = getBlocks($tab_type,"detail_view",'',$focus->column_fields);

foreach($act_data as $block=>$entry) {
	foreach($entry as $key=>$value) {
		foreach($value as $label=>$field) {
			$fldlabel[$field['fldname']] = $label;
			if($field['ui'] == 15 || $field['ui'] == 16) {
				foreach($field['options'] as $index=>$arr_val)
				{
					if($arr_val[2] == "selected")
					$finaldata[$field['fldname']] = $arr_val[0];
				}
			} else {
				$fldvalue = $field['value'];
				if($field['fldname'] == 'description') { $fldvalue = nl2br($fldvalue); }
				$finaldata[$field['fldname']] = $fldvalue;
			}	
			
			$finaldata[$field['fldname'].'link'] = $field['link'];
		}
	}
}

//Start
//To set user selected hour format
if($current_user->hour_format == '')
	$format = 'am/pm';
else
	$format = $current_user->hour_format;
list($stdate,$sttime) = split(' ',$finaldata['date_start']);
list($enddate,$endtime) = split(' ',$finaldata['due_date']);
$time_arr = getaddEventPopupTime($sttime,$endtime,$format);
$data['starthr'] = $time_arr['starthour'];
$data['startmin'] = $time_arr['startmin'];
$data['startfmt'] = $time_arr['startfmt'];
$data['endhr'] = $time_arr['endhour'];
$data['endmin'] = $time_arr['endmin'];
$data['endfmt'] = $time_arr['endfmt'];
$data['record'] = $focus->id;
if(isset($finaldata['sendnotification']) && $finaldata['sendnotification'] == strtolower($c_mod_strings['LBL_YES'])) 
	$data['sendnotification'] = $c_mod_strings['LBL_YES'];
else
	$data['sendnotification'] = $c_mod_strings['LBL_NO'];
$data['subject'] = $finaldata['subject'];
$data['date_start'] = $stdate;
$data['due_date'] = $enddate;
$data['assigned_user_id'] = $finaldata['assigned_user_id'];
if($c_mod_strings[$finaldata['taskpriority']] != '')
	$data['taskpriority'] = $c_mod_strings[$finaldata['taskpriority']];
else
	$data['taskpriority'] = $finaldata['taskpriority'];
$data['modifiedtime'] = $finaldata['modifiedtime'];
$data['createdtime'] = $finaldata['createdtime'];
$data['modifiedby'] = $finaldata['modifiedby'];
$data['parent_name'] = $finaldata['parent_id'];
$data['description'] = $finaldata['description'];
if($activity_mode == 'Task') {
	if($c_mod_strings[$finaldata['taskstatus']] != '')
		$data['taskstatus'] = $c_mod_strings[$finaldata['taskstatus']];
	else
		$data['taskstatus'] = $finaldata['taskstatus'];
	$data['activitytype'] = $activity_mode;
	$data['contact_id'] = $finaldata['contact_id'];
	$data['contact_idlink'] = $finaldata['contact_idlink'];
} elseif($activity_mode == 'Events') {
	$data['visibility'] = $finaldata['visibility'];
	if($c_mod_strings[$finaldata['eventstatus']] != '')
		$data['eventstatus'] = $c_mod_strings[$finaldata['eventstatus']];
	else
		$data['eventstatus'] = $finaldata['eventstatus'];
	$data['activitytype'] = $finaldata['activitytype'];
	$data['location'] = $finaldata['location'];
	//Calculating reminder time
	$rem_days = 0;
	$rem_hrs = 0;
	$rem_min = 0;
	if(!empty($focus->column_fields['reminder_time'])) {
		$data['set_reminder'] = $c_mod_strings['LBL_YES'];
		$data['reminder_str'] = $finaldata['reminder_time'];
	} else
		$data['set_reminder'] = $c_mod_strings['LBL_NO'];
	//To set recurring details
	$query = 'SELECT vtiger_recurringevents.*, vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.due_date, vtiger_activity.time_end
				FROM vtiger_recurringevents
					INNER JOIN vtiger_activity ON vtiger_activity.activityid = vtiger_recurringevents.activityid
					WHERE vtiger_recurringevents.activityid = ?';
	$res = $adb->pquery($query, array($focus->id));
	$rows = $adb->num_rows($res);
	if($rows > 0) {
		$recurringObject = RecurringType::fromDBRequest($adb->query_result_rowdata($res, 0));
		$recurringInfoDisplayData = $recurringObject->getDisplayRecurringInfo();
		$data = array_merge($data, $recurringInfoDisplayData);
	} else  {
		$data['recurringcheck'] = getTranslatedString('LBL_NO', $currentModule);
		$data['repeat_str'] = '';
	}
	$sql = 'select vtiger_users.*,vtiger_invitees.* from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
	$result = $adb->pquery($sql, array($focus->id));
	$num_rows=$adb->num_rows($result);
	$invited_users=Array();
	for($i=0;$i<$num_rows;$i++) {
		$userid=$adb->query_result($result,$i,'inviteeid');
		$username = getFullNameFromQResult($result, $i, 'Users');
		$invited_users[$userid]=$username;
	}
	$smarty->assign("INVITEDUSERS",$invited_users);
	$related_array = getRelatedListsInformation("Calendar", $focus);
	$fieldsname = $related_array['Contacts']['header'];
	$contact_info = $related_array['Contacts']['entries'];

	$entityIds = array_keys($contact_info);
	$displayValueArray = getEntityName('Contacts', $entityIds);
	if (!empty($displayValueArray)) {
		foreach ($displayValueArray as $key => $field_value) {
			$entityname[] = '<a href="index.php?module=Contacts&action=DetailView&record=' . $key . '">' . $field_value . '</a>';
		}
	}
	$smarty->assign("CONTACTS",$entityname);
	
	$is_fname_permitted = getFieldVisibilityPermission("Contacts", $current_user->id, 'firstname');
	$smarty->assign("IS_PERMITTED_CNT_FNAME",$is_fname_permitted);
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Calendar-Activities detail view");
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("CMOD", $c_mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("ACTIVITY_MODE", $activity_mode);

if (isset($focus->name)) 
$smarty->assign("NAME", $focus->name);
else 
$smarty->assign("NAME", "");
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
if (isset($_REQUEST['return_module'])) 
$smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if (isset($_REQUEST['return_action'])) 
$smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if (isset($_REQUEST['return_id'])) 
$smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string'].'&activity_mode='.$activity_mode);
$smarty->assign("ID", $focus->id);
$smarty->assign("NAME", $focus->name);
$smarty->assign("BLOCKS", $act_data);
$smarty->assign("LABEL", $fldlabel);
$smarty->assign("VIEWTYPE", vtlib_purify($_REQUEST['viewtype']));
$smarty->assign("CUSTOMFIELD", $cust_fld);
$smarty->assign("ACTIVITYDATA", $data);
$smarty->assign("ID", $record);

if($Calendar4You->CheckPermissions("EDIT")) {
	$smarty->assign("EDIT","permitted");
    $smarty->assign("EDIT_DUPLICATE","permitted");
}

if($Calendar4You->CheckPermissions("DELETE",$record))
	$smarty->assign("DELETE","permitted");

if($Calendar4You->CheckPermissions("EDIT",$record))
	$smarty->assign("EDIT_PERMISSION","permitted");

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$tabid = getTabid($tab_type);
$validationData = getDBValidationData($focus->tab_name,$tabid);
$data2 = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$data2['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data2['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$data2['fieldlabel']);

$smarty->assign("MODULE",$currentModule);

if(PerformancePrefs::getBoolean('DETAILVIEW_RECORD_NAVIGATION', true) && isset($_SESSION[$currentModule.'_listquery'])){
	$recordNavigationInfo = ListViewSession::getListViewNavigation($focus->id);
	VT_detailViewNavigation($smarty,$recordNavigationInfo,$focus->id);
}

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>'Calendar', 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid("Calendar"), Array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWWIDGET'), $customlink_params));
// END

$custom_fields_data = getCalendarCustomFields($tabid,'detail_view',$focus->column_fields);
$smarty->assign("CUSTOM_FIELDS_DATA", $custom_fields_data);

$smarty->assign("IS_REL_LIST",isPresentRelatedLists($rel_tab_type));

if($singlepane_view == 'true') {
	$related_array = getRelatedLists($tab_type,$focus);
	$smarty->assign("RELATEDLISTS", $related_array);
	require_once('include/ListView/RelatedListViewSession.php');
	if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		RelatedListViewSession::addRelatedModuleToSession(vtlib_purify($_REQUEST['relation_id']),
				vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign("SELECTEDHEADERS", $open_related_modules);
}
$smarty->assign("SinglePane_View", $singlepane_view);

$smarty->assign("MODE", "DetailView");

$smarty->display("modules/Calendar4You/EventDetailView.tpl");

?>