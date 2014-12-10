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

require_once('modules/Calendar/Activity.php');
require_once('include/logging.php');
require_once("config.php");
require_once('include/database/PearDatabase.php');
require_once('modules/Calendar/CalendarCommon.php');
require_once 'modules/Calendar4You/CalendarUtils.php';
global $adb,$theme,$mod_strings,$current_user;
$local_log =& LoggerManager::getLogger('index');

$focus = new Activity();
$_REQUEST = vtlib_purify($_REQUEST);  // clean up ALL values
$activity_mode = vtlib_purify($_REQUEST['activity_mode']);
$record = vtlib_purify($_REQUEST['record']);
if (empty($activity_mode) and !empty($record)) {
	$activity_mode = getEventActivityMode($record);
}
$tab_type = 'Calendar';
if ($activity_mode == 'Events') {
	$tab_type = 'Events';
}
$search=vtlib_purify($_REQUEST['search_url']);

$focus->column_fields["activitytype"] = 'Task';
if(isset($record)) {
	$focus->id = $_REQUEST['record'];
	$local_log->debug("id is ".$id);
}

if ($_REQUEST['mode'] == "event_drop" || $_REQUEST['mode'] == "event_resize") {
    $activityid = $focus->id;
    
    if(isPermitted("Calendar","EditView",$activityid) == 'yes') {
        $focus->retrieve_entity_info($focus->id,$tab_type);
        $focus->mode = "edit";
        
        $day_drop = $_REQUEST['day'];
        
        $minute_drop = $_REQUEST['minute'];
        
        $Act = $focus->column_fields;
        
        foreach($focus->column_fields as $fieldname => $val) {
			$focus->column_fields[$fieldname] = decode_html($focus->column_fields[$fieldname]);
		}
        
        if ($day_drop > 0)
            $d = "+";
        else
            $d = "-";
        
        if ($minute_drop > 0)
            $m = "+";
        else
            $m = "-";
        
        $date = new DateTime($Act["date_start"]." ".$Act["time_start"]); 
        if ($_REQUEST['mode'] == "event_drop") {
            if ($day_drop != 0) $date->modify($d.abs($day_drop).' day');
            if ($minute_drop != 0) $date->modify($m.abs($minute_drop).' minutes');
            $new_date_start = $date->format('Y-m-d');
            $new_time_start = $date->format('H:i:s');
        }
        $new_time_start_time = $date->format('U');
        
        $date = new DateTime($Act["due_date"]." ".$Act["time_end"]); 
        if ($day_drop != 0) $date->modify($d.abs($day_drop).' day');
        if ($minute_drop != 0) $date->modify($m.abs($minute_drop).' minutes');
        $new_due_date = $date->format('Y-m-d');
        $new_time_end = $date->format('H:i:s');
        $new_time_end_time = $date->format('U');
    
        $focus->column_fields["due_date"] = $new_due_date;
    
        if ($_REQUEST['mode'] == "event_resize") {
            if ($Act["activitytype"] == "Task" && $Act["status"] != "" && $Act["eventstatus"] == "") {
                //$sql_u = "UPDATE vtiger_activity SET due_date = ? WHERE activityid = ?";
                //$adb->pquery($sql_u, array($new_due_date,$activityid));
            } else {
                $duration_time = $new_time_end_time - $new_time_start_time;
                
                $duration_hour = floor($duration_time / 3600);
                $duration_minutes = ($duration_time - ($duration_hour * 3600 ))  / 60;

                $focus->column_fields["time_end"] = $new_time_end;
                $focus->column_fields["duration_hours"] = $duration_hour;
                $focus->column_fields["duration_minutes"] = $duration_minutes;
            }
        } else {
            $focus->column_fields["date_start"] = $new_date_start;
            $focus->column_fields["time_start"] = $new_time_start;
            
            $focus->column_fields["time_end"] = $new_time_end;
        }
        $focus->save($tab_type);
    }
    exit;
}

if(isset($_REQUEST['mode'])) {
	$focus->mode = $_REQUEST['mode'];
}

if((isset($_REQUEST['change_status']) && $_REQUEST['change_status']) && ($_REQUEST['status']!='' || $_REQUEST['eventstatus']!='')) {
	$status ='';
	$activity_type='';
	$return_id = $focus->id;
	if(isset($_REQUEST['status'])) {
		$status = $_REQUEST['status'];
		$activity_type = "Task";
	} elseif(isset($_REQUEST['eventstatus'])) {
		$status = $_REQUEST['eventstatus'];
		$activity_type = "Events";
	}
	if(isPermitted("Calendar","EditView",$_REQUEST['record']) == 'yes')	{
		ChangeStatus($status,$return_id,$activity_type);
	} else {
		echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tbody><tr>
			<td rowspan='2' width='11%'><img src='".vtiger_imageurl('denied.gif', $theme)."' ></td>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$app_strings["LBL_PERMISSION"]."</span></td>
			</tr>
			<tr>
			<td class='small' align='right' nowrap='nowrap'>
			<a href='javascript:window.history.back();'>".$app_strings["LBL_GO_BACK"]."</a><br>								   						     </td>
			</tr>
			</tbody></table>
		</div>";
		echo "</td></tr></table>";
        die;
	}
	$invitee_qry = "select * from vtiger_invitees where activityid=?";
	$invitee_res = $adb->pquery($invitee_qry, array($return_id));
	$count = $adb->num_rows($invitee_res);
	if($count != 0)	{
		for($j = 0; $j < $count; $j++) {
			$invitees_ids[]= $adb->query_result($invitee_res,$j,"inviteeid");

		}
		$invitees_ids_string = implode(';',$invitees_ids);
		sendInvitation($invitees_ids_string,$activity_type,$mail_data['subject'],$mail_data);
	}

} else {
	$timeFields = array('time_start', 'time_end');
	$tabId = getTabid($tab_type);
	foreach($focus->column_fields as $fieldname => $val) {
		$fieldInfo = getFieldRelatedInfo($tabId, $fieldname);
		$uitype = $fieldInfo['uitype'];
		$typeofdata = $fieldInfo['typeofdata'];
		if(isset($_REQUEST[$fieldname])) {
			if(is_array($_REQUEST[$fieldname]))
				$value = $_REQUEST[$fieldname];
			else
				$value = trim($_REQUEST[$fieldname]);

			if((($typeofdata == 'T~M') || ($typeofdata == 'T~O')) && ($uitype == 2 || $uitype == 70 )) {
				if(!in_array($fieldname, $timeFields)) {
					$date = DateTimeField::convertToDBTimeZone($value);
					$value = $date->format('H:i');
				}
				$focus->column_fields[$fieldname] = $value;
			}else{
				$focus->column_fields[$fieldname] = $value;
			}
			if(($fieldname == 'notime') && ($focus->column_fields[$fieldname])) {
				$focus->column_fields['time_start'] = '';
				$focus->column_fields['duration_hours'] = '';
				$focus->column_fields['duration_minutes'] = '';
			}
			if(($fieldname == 'recurringtype') && ! isset($_REQUEST['recurringcheck']))
				$focus->column_fields['recurringtype'] = '--None--';
		}
	}
	if(isset($_REQUEST['visibility']) && $_REQUEST['visibility']!= '')
	        $focus->column_fields['visibility'] = $_REQUEST['visibility'];
	else
	        $focus->column_fields['visibility'] = 'Private';

	if($_REQUEST['assigntype'] == 'U') {
		$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
	} elseif($_REQUEST['assigntype'] == 'T') {
		$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
	}

	$dateField = 'date_start';
	$fieldname = 'time_start';
	$date = new DateTimeField($_REQUEST[$dateField]. ' ' . $_REQUEST[$fieldname]);
	$focus->column_fields[$dateField] = $date->getDBInsertDateValue();
	$focus->column_fields[$fieldname] = $date->getDBInsertTimeValue();
	if(empty($_REQUEST['time_end'])) {
		$_REQUEST['time_end'] = date('H:i', strtotime('+10 minutes',
		strtotime($focus->column_fields['date_start'].' '.$_REQUEST['time_start'])));
	}
	$dateField = 'due_date';
	$fieldname = 'time_end';
	$date = new DateTimeField($_REQUEST[$dateField]. ' ' . $_REQUEST[$fieldname]);
	$focus->column_fields[$dateField] = $date->getDBInsertDateValue();
	$focus->column_fields[$fieldname] = $date->getDBInsertTimeValue();
    
	$focus->save($tab_type);
	/* For Followup START -- by Minnie */
	if(isset($_REQUEST['followup']) && $_REQUEST['followup'] == 'on' && $activity_mode == 'Events' && isset($_REQUEST['followup_time_start']) &&  $_REQUEST['followup_time_start'] != '') {
		$heldevent_id = $focus->id;
		$focus->column_fields['subject'] = '[Followup] '.$focus->column_fields['subject'];
		$startDate = new DateTimeField($_REQUEST['followup_date'].' '.
				$_REQUEST['followup_time_start']);
		$endDate = new DateTimeField($_REQUEST['followup_due_date'].' '.
				$_REQUEST['followup_time_end']);
		$focus->column_fields['date_start'] = $startDate->getDBInsertDateValue();
		$focus->column_fields['due_date'] = $endDate->getDBInsertDateValue();
		$focus->column_fields['time_start'] = $startDate->getDBInsertTimeValue();
		$focus->column_fields['time_end'] = $endDate->getDBInsertTimeValue();
		$focus->column_fields['eventstatus'] = 'Planned';
		$focus->column_fields['activitytype'] = $_REQUEST['follow_activitytype'];
		$focus->mode = 'create';
		$focus->save($tab_type);
	}
	/* For Followup END -- by Minnie */
	$return_id = $focus->id;
}     

if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "")
	$return_module = vtlib_purify($_REQUEST['return_module']);
else
	$return_module = "Calendar";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "")
	$return_action = vtlib_purify($_REQUEST['return_action']);
else
	$return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "")
	$returnid = vtlib_purify($_REQUEST['return_id']);

$activemode = "";
if($activity_mode != '')
	$activemode = "&activity_mode=".$activity_mode;

function getRequestData($return_id) {
	global $adb;
	$cont_qry = "select * from vtiger_cntactivityrel where activityid=?";
	$cont_res = $adb->pquery($cont_qry, array($return_id));
	$noofrows = $adb->num_rows($cont_res);
	$cont_id = array();
	if($noofrows > 0) {
		for($i=0; $i<$noofrows; $i++) {
			$cont_id[] = $adb->query_result($cont_res,$i,"contactid");
		}
	}
	$cont_name = '';
	foreach($cont_id as $key=>$id) {
		if($id != '') {
			$displayValueArray = getEntityName('Contacts', $id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $field_value) {
					$contact_name = $field_value;
				}
			}
			$cont_name .= $contact_name .', ';
		}
	}
	$cont_name  = trim($cont_name,', ');
	$mail_data = Array();
	$mail_data['user_id'] = $_REQUEST['assigned_user_id'];
	$mail_data['subject'] = $_REQUEST['subject'];
	$mail_data['status'] = (($_REQUEST['activity_mode']=='Task')?($_REQUEST['taskstatus']):($_REQUEST['eventstatus']));
	$mail_data['activity_mode'] = $_REQUEST['activity_mode'];
	$mail_data['taskpriority'] = $_REQUEST['taskpriority'];
	$mail_data['relatedto'] = $_REQUEST['parent_name'];
	$mail_data['contact_name'] = $cont_name;
	$mail_data['description'] = $_REQUEST['description'];
	$mail_data['assign_type'] = $_REQUEST['assigntype'];
	$mail_data['group_name'] = getGroupName($_REQUEST['assigned_group_id']);
	$mail_data['mode'] = $_REQUEST['mode'];
	$value = getaddEventPopupTime($_REQUEST['time_start'],$_REQUEST['time_end'],'24');
	$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
	if($_REQUEST['activity_mode']!='Task')
		$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
	$startDate = new DateTimeField($_REQUEST['date_start']." ".$start_hour);
	$endDate = new DateTimeField($_REQUEST['due_date']." ".$end_hour);
	$mail_data['st_date_time'] = $startDate->getDBInsertDateTimeValue();
	$mail_data['end_date_time'] = $endDate->getDBInsertDateTimeValue();
	$mail_data['location']=vtlib_purify($_REQUEST['location']);
	return $mail_data;
}

function getFieldRelatedInfo($tabId, $fieldName){
	$fieldInfo = VTCacheUtils::lookupFieldInfo($tabId, $fieldName);
	if($fieldInfo === false) {
		getColumnFields(getTabModuleName($tabid));
		$fieldInfo = VTCacheUtils::lookupFieldInfo($tabId, $fieldName);
	}
	return $fieldInfo;
}

if(isset($_REQUEST['contactidlist']) && $_REQUEST['contactidlist'] != '') {
	//split the string and store in an array
	$storearray = explode (";",$_REQUEST['contactidlist']);
	$del_sql = "delete from vtiger_cntactivityrel where activityid=?";
	$adb->pquery($del_sql, array($record));
	$record = $focus->id;
	foreach($storearray as $id) {
		if($id != '') {
			$sql = "insert into vtiger_cntactivityrel values (?,?)";
			$adb->pquery($sql, array($id, $record));
			if(!empty($heldevent_id)) {
				$sql = "insert into vtiger_cntactivityrel values (?,?)";
				$adb->pquery($sql, array($id, $heldevent_id));
			}
		}
	}
}

//code added to send mail to the vtiger_invitees
if(isset($_REQUEST['inviteesid']) && $_REQUEST['inviteesid']!='') {
	$mail_contents = getRequestData($return_id);
	sendInvitation($_REQUEST['inviteesid'],$_REQUEST['activity_mode'],$_REQUEST['subject'],$mail_contents);
}

//to delete contact account relation while editing event
if(isset($_REQUEST['deletecntlist']) && $_REQUEST['deletecntlist'] != '' && $_REQUEST['mode'] == 'edit') {
	//split the string and store it in an array
	$storearray = explode (";",$_REQUEST['deletecntlist']);
	foreach($storearray as $id)	{
		if($id != '') {
			$record = $focus->id;
			$sql = "delete from vtiger_cntactivityrel where contactid=? and activityid=?";
			$adb->pquery($sql, array($id, $record));
		}
	}  
}

//to delete activity and its parent table relation
if(isset($_REQUEST['del_actparent_rel']) && $_REQUEST['del_actparent_rel'] != '' && $_REQUEST['mode'] == 'edit') {
	$parnt_id = $_REQUEST['del_actparent_rel'];
	$sql= 'delete from vtiger_seactivityrel where crmid=? and activityid=?';
	$adb->pquery($sql, array($parnt_id, $record));
}

if(isset($_REQUEST['geventid']) && $_REQUEST['geventid'] != '') {
    $q = "select activitytypeid from vtiger_activitytype where activitytype = ?";
    $Res = $adb->pquery($q,array($focus->column_fields["activitytype"]));
    $event = $adb->query_result($Res,0,"activitytypeid");
    
    $geventid = vtlib_purify($_REQUEST['geventid']);
    
    $p = array($focus->id, $geventid, $current_user->id, $event);
    
    $sql1 = "SELECT * FROM its4you_googlesync4you_events WHERE crmid = ? AND geventid = ? AND userid = ? AND eventtype = ?";
    $result1 = $adb->pquery($sql1, $p);
    $num_rows1 = $adb->num_rows($result1); 
    
    if ($num_rows1 == 0)
    {
        $sql2 = 'INSERT INTO its4you_googlesync4you_events (crmid,geventid,userid,eventtype) VALUES (?,?,?,?)';
        $result2 = $adb->pquery($sql2, $p);
    }
}

if(isset($_REQUEST['view']) && $_REQUEST['view']!='')
	$view=vtlib_purify($_REQUEST['view']);
if(isset($_REQUEST['hour']) && $_REQUEST['hour']!='')
	$hour=vtlib_purify($_REQUEST['hour']);
if(isset($_REQUEST['day']) && $_REQUEST['day']!='')
	$day=vtlib_purify($_REQUEST['day']);
if(isset($_REQUEST['month']) && $_REQUEST['month']!='')
	$month=vtlib_purify($_REQUEST['month']);
if(isset($_REQUEST['year']) && $_REQUEST['year']!='')
	$year=vtlib_purify($_REQUEST['year']);
if(isset($_REQUEST['viewOption']) && $_REQUEST['viewOption']!='')
	$viewOption=vtlib_purify($_REQUEST['viewOption']);
if(isset($_REQUEST['subtab']) && $_REQUEST['subtab']!='')
	$subtab=vtlib_purify($_REQUEST['subtab']);

if($_REQUEST['recurringcheck']) {
    include_once('modules/Calendar/RepeatEvents.php');
	Calendar_RepeatEvents::repeatFromRequest($focus);
}

$date = new DateTimeField($_REQUEST["date_start"]);
echo $date->getDBInsertDateValue();

echo "-".$mod_strings["LBL_SUCCESSFULY_CREATED"];

exit;
?> 