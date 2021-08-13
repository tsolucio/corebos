<?php

define('_BENNU_VERSION', '0.1');

require_once 'include/utils/utils.php';
require_once 'modules/cbCalendar/CalendarCommon.php';
include 'modules/cbCalendar/iCal/iCalendar_rfc2445.php';
include 'modules/cbCalendar/iCal/iCalendar_components.php';
include 'modules/cbCalendar/iCal/iCalendar_properties.php';
include 'modules/cbCalendar/iCal/iCalendar_parameters.php';

global $current_user,$adb,$default_timezone;
$filename = $_REQUEST['filename'];
$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCalendar');
$ical_query = 'select vtiger_activity.*,vtiger_crmentity.description,vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime,vtiger_activity_reminder.reminder_time, '
	.$current_user->id.' as assigned_user_id'
	.' from vtiger_activity'
	." inner join $crmEntityTable on vtiger_activity.activityid = vtiger_crmentity.crmid"
	.' LEFT JOIN vtiger_activity_reminder ON vtiger_activity_reminder.activity_id=vtiger_activity.activityid AND vtiger_activity_reminder.recurringid=0'
	.' where vtiger_crmentity.deleted=0 and vtiger_crmentity.smownerid='.$current_user->id." and vtiger_activity.activitytype NOT IN ('Emails')";

$calendar_results = $adb->query($ical_query);

// Send the right content type and filename
header('Content-type: text/calendar');
header('Content-Disposition: attachment; filename='.$filename.'.ics');

$todo_fields = getColumnFields('cbCalendar');
$todo = array();
foreach ($todo_fields as $key => $val) {
	if (getFieldVisibilityPermission('cbCalendar', $current_user->id, $key)==0) {
		$todo[$key] = 'yes';
	}
}
unset($todo['created_user_id'], $todo['modifiedby']);
if (isset($todo['taskpriority'])) {
	unset($todo['taskpriority']);
	$todo['priority'] = 'yes';
}

$tz = new iCalendar_timezone;
if (!empty($default_timezone)) {
	$tzid = explode('/', $default_timezone);
} else {
	$default_timezone = date_default_timezone_get();
	$tzid = explode('/', $default_timezone);
}

if (!empty($tzid[1])) {
	$tz->add_property('TZID', $tzid[1]);
} else {
	$tz->add_property('TZID', $tzid[0]);
}
$tz->add_property('TZOFFSETTO', date('O'));
if (date('I')==1) {
	$tz->add_property('DAYLIGHTC', date('I'));
} else {
	$tz->add_property('STANDARDC', date('I'));
}

$myical = new iCalendar;

$myical->add_component($tz);

while (!$calendar_results->EOF) {
	$this_event = $calendar_results->fields;
	$id = $this_event['activityid'];
	$type = $this_event['activitytype'];
	$temp = $todo;
	foreach ($temp as $key => $val) {
		$temp[$key] = $this_event[$key];
	}
	$temp['id'] = $id;
	$ev = new iCalendar_event;
	$ev->assign_values($temp);

	$al = new iCalendar_alarm;
	$al->assign_values($temp);
	$ev->add_component($al);

	$myical->add_component($ev);
	$calendar_results->MoveNext();
}
// Print the actual calendar
echo $myical->serialize();

?>