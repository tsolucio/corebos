<?php
header('Content-type: text/json');
chdir (dirname(__FILE__) . '/../../');
include_once dirname(__FILE__) . '/api/wsapi.php';

$sessionid = HTTP_Session::detectId();
session_id($sessionid);
session_start();
$current_user_ID = $_SESSION ['_authenticated_user_id'];

$query  = "SELECT case when (vtiger_users.user_name not like '') then CONCAT(vtiger_users.last_name,' ',vtiger_users.first_name) else vtiger_groups.groupname end as user_name, vtiger_activity.activityid , vtiger_activity.subject, vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_activity.due_date, vtiger_activity.time_start,vtiger_activity.time_end,  vtiger_crmentity.crmid, vtiger_crmentity.description, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_recurringevents.recurringtype, case when (vtiger_activity.activitytype = 'Task') then vtiger_activity.status else vtiger_activity.eventstatus end as status,  vtiger_seactivityrel.crmid as parent_id  from vtiger_activity 
inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activity.activityid  
left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_activity.activityid  
left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid 
left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid 
left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid 
where vtiger_crmentity.deleted = 0 and ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred')) or (vtiger_activity.activitytype Not in ('Emails','Task') and  vtiger_activity.eventstatus not in ('','Held')))  AND vtiger_users.id =? ORDER BY vtiger_activity.due_date ASC";
$params = array($current_user_ID);
$result = $adb->pquery($query, $params);
$numofrows = $adb->num_rows($result);

// echo '[';
$separator = "";
$tmp_arr = array();
$tmp_str = "[";
for($k=0;$k < $adb->num_rows($result);$k++) {
	$descr  = '';
	$startdate  = $adb->query_result($result,$k,"date_start");
	//only European time format is currently supported
    $newStartDate = gmdate("d-m-Y h:m:s", strtotime($startdate));
	$starttime  =$adb->query_result($result,$k,"time_start");
	$startDateTime = $startdate. ' '.$starttime;
 	//requires timestamp in miliseconds = unix time stamp * 1000
	$startDateTime=strtotime($startDateTime)*1000; 
	$starttmp  =$adb->query_result($result,$k,"time_start");
	$endtime  =$adb->query_result($result,$k,"time_end");
	$subject  =$adb->query_result($result,$k,"subject");
	$activitytype  = $adb->query_result($result,$k,"activitytype");
	$activitytypetrans  = getTranslatedString($adb->query_result($result,$k,"activitytype"),'Calendar');
	$descr  = ($adb->query_result($result,$k,"description"));
	$cal_id  =$adb->query_result($result,$k,"crmid");
	$moduleWSID = Mobile_WS_Utils::getEntityModuleWSId('Calendar');
	$eventsWSID = Mobile_WS_Utils::getEntityModuleWSId('Events');

	if ($activitytype !='Task') {
		$calid = "{$eventsWSID}x".$cal_id;
	}
	else {
		$calid = "{$moduleWSID}x".$cal_id;
	}
	$tmp_str .= $separator;
	$tmp_str .= '	{ "date": "'; $tmp_str .= $startDateTime; $tmp_str .= '", "type": "meeting", "title":  "'.$subject.' '; $tmp_str .= '-'; $tmp_str .= $activitytype.'", "description": "'.$descr.'", "url": "?_operation=fetchRecordWithGrouping&amp;record='.$calid.'" }';
	$separator = ",";
	$tmp_arr[] = array('date' => "$startDateTime", 'type' => 'meeting', 'title' => $subject.' - '.$activitytypetrans, 'description' => $descr, 'url' => "?_operation=fetchRecordWithGrouping&amp;record=$calid");
}
echo json_encode($tmp_arr);
?>