<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
global $app_strings, $currentModule, $image_path, $theme, $adb, $current_user;

require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
require_once 'modules/cbCalendar/cbCalendar.php';
require_once 'modules/cbCalendar/CalendarCommon.php';

$cur_time = time();
coreBOS_Session::set('last_reminder_check_time', $cur_time);
coreBOS_Session::set('next_reminder_interval', 60);
$returnResponse = array();
$clicked = isset($_REQUEST['clicked']) ? $_REQUEST['clicked'] : 'false';
if ($clicked=='false' && isset($_SESSION['next_reminder_time']) && $_SESSION['next_reminder_time'] == 'None') {
	$returnResponse['next_reminder_interval'] = "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>None</script>";
	echo json_encode($returnResponse);
	die();
} elseif ($clicked=='false' && isset($_SESSION['next_reminder_time']) && (($_SESSION['next_reminder_time'] - $_SESSION['next_reminder_interval']) > $cur_time)) {
	$returnResponse['next_reminder_interval'] = "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>".
		($_SESSION['next_reminder_interval'] * 1000).'</script>';
	echo json_encode($returnResponse);
	return;
}
$log = LoggerManager::getLogger('Activity_Reminder');
if (isPermitted('cbCalendar', 'index') == 'yes') {
	$active = $adb->pquery('select reminder_interval from vtiger_users where id=?', array($current_user->id));
	$interval = $adb->query_result($active, 0, 'reminder_interval');
	if ($interval == 'None') {
		coreBOS_Session::set('next_reminder_time', 'None');
	}
	if ($interval!='None') {
		$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 10, 'cbCalendar');
		$Calendar_PopupReminder_DaysPast = GlobalVariable::getVariable('Calendar_PopupReminder_DaysPast', 7, 'cbCalendar');
		$intervalInMinutes = ConvertToMinutes($interval);
		// check for reminders every minute
		$time = time();
		coreBOS_Session::set('next_reminder_time', $time + ($intervalInMinutes * 60));
		$date = date('Y-m-d', strtotime("+$intervalInMinutes minutes", $time));
		$date_inpast = date('Y-m-d', strtotime('-'.$Calendar_PopupReminder_DaysPast.' day', $time));
		$time = date('H:i', strtotime("+$intervalInMinutes minutes", $time));
		$callback_query = cbCalendar::getActionsQuery($current_user, $date, $date_inpast, $time, $list_max_entries_per_page);
		$result = $adb->query($callback_query);

		$cbrows = $adb->num_rows($result);
		$notreaded = 0;
		$activities_reminder = array();
		if ($cbrows > 0) {
			for ($index = 0; $index < $cbrows; ++$index) {
				$activity = array();
				$reminderid = $adb->query_result($result, $index, 'reminderid');
				$cbrecord = $adb->query_result($result, $index, 'recordid');
				$cbmodule = $adb->query_result($result, $index, 'semodule');
				$cbreaded = $adb->query_result($result, $index, 'readed');
				if ($cbreaded == '0') {
					$notreaded++;
				}

				if ($cbmodule == 'cbCalendar') {
					$focus = CRMEntity::getInstance($cbmodule);
					$focus->retrieve_entity_info($cbrecord, $cbmodule);
					$cbsubject = $focus->column_fields['subject'];
					$cbactivitytype = getTranslatedString($focus->column_fields['activitytype'], 'cbCalendar');
					$cbdate = $focus->column_fields['date_start'];
					$cbtime = $focus->column_fields['time_start'];
					$cbstatus = getTranslatedString($focus->column_fields['eventstatus'], 'cbCalendar');
					switch ($focus->column_fields['activitytype']) {
						case 'Call':
							$activity['activityimage'] = array('action', 'call');
							break;
						case 'Task':
							$activity['activityimage'] = array('utility', 'event');
							break;
						case 'Meeting':
							$activity['activityimage'] = array('utility', 'people');
							break;
						default:
							$activity['activityimage'] = array('utility', 'date_time');
							break;
					}
				} else {
					// For non-calendar records.
					if (empty($cbrecord)) {
						$moreinfo = $adb->query_result($result, $index, 'moreinfo');
						$moreinfo = json_decode(html_entity_decode($moreinfo, ENT_QUOTES), true);
						$cbsubject = $moreinfo['subject'];
						$cbactivitytype = $moreinfo['subtitle'];
						$activity['activityimage'] = $moreinfo['icon'];
					} else {
						$cbsubject = array_values(getEntityName($cbmodule, $cbrecord));
						$cbsubject = $cbsubject[0];
						$cbactivitytype = getTranslatedString($cbmodule, $cbmodule);
						$mod = CRMEntity::getInstance($cbmodule);
						$activity['activityimage'] = [$mod->moduleIcon['library'], $mod->moduleIcon['icon']];
					}
					$cbdate = $adb->query_result($result, $index, 'date_start');
					$cbtime = $adb->query_result($result, $index, 'time_start');
					$cbaction = $adb->query_result($result, $index, 'moreaction');
					if (!empty($cbaction)) {
						$cbaction = json_decode(html_entity_decode($cbaction, ENT_QUOTES), true);
						$activity['cbactionlabel'] = $cbaction['label'];
						$activity['cbactionlink'] = $cbaction['link'];
					}
					$cbstatus = '';
				}
				if ($cbtime != '') {
					$date = new DateTimeField($cbdate.' '.$cbtime);
					$cbtime = $date->getDisplayTime();
					$cbdate = $date->getDisplayDate();
					if (empty($current_user->hour_format)) {
						$format = '24';
					} else {
						$format = $current_user->hour_format;
					}
					$cbtimeArr = getaddEventPopupTime($cbtime, '', $format);
					$cbtime = $cbtimeArr['starthour'].':'.$cbtimeArr['startmin'].''.$cbtimeArr['startfmt'];
				}

				// Appending recordid we can get unique callback dom id for that record.
				$popupid = "ActivityReminder_$cbrecord";
				$cbcolor = '';
				if ($cbdate <= date('Y-m-d') && !($cbdate == date('Y-m-d') && $cbtime > date('H:i'))) {
					$cbcolor= '#FF1515';
				}
				$activity['popupid'] = $popupid;
				$activity['cbreminderid'] = $reminderid;
				$activity['cbdate'] = $cbdate;
				$activity['cbtime'] = $cbtime;
				$activity['cbsubject'] = $cbsubject;
				$activity['cbmodule'] = $cbmodule;
				$activity['cbrecord'] = $cbrecord;
				$activity['cbstatus'] = $cbstatus;
				$activity['cbcolor'] = $cbcolor;
				$activity['activitytype'] = $cbactivitytype;
				$activity['cbreaded'] = $cbreaded;

				$adb->pquery('UPDATE vtiger_activity_reminder_popup set status=1 where reminderid=?', array($reminderid));

				$activities_reminder[] = $activity;
			}
			$returnResponse['template'] = printToDoList($activities_reminder);
		} else {
			$smarty = new vtigerCRM_Smarty;
			$smarty->assign('NOTASKSInfo', getTranslatedString('TASKS_FINISHED', 'Calendar4You'));
			$smarty->assign('NOTASKSSize', 'small');
			$returnResponse['noTasks'] = $smarty->fetch('Components/NoTasks.tpl');
		}
		$returnResponse['not_readed'] = $notreaded;
		$returnResponse['next_reminder_interval'] = "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>".
			($_SESSION['next_reminder_interval'] * 1000).'</script>';
	} else {
		if ($clicked=='true') {
			$smarty = new vtigerCRM_Smarty;
			$smarty->assign('NOTASKSInfo', getTranslatedString('TASKS_FINISHED', 'Calendar4You'));
			$smarty->assign('NOTASKSSize', 'small');
			$returnResponse['noTasks'] = $smarty->fetch('Components/NoTasks.tpl');
		}
		coreBOS_Session::set('next_reminder_time', 'None');
		$returnResponse['next_reminder_interval'] = "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>None</script>";
	}
	echo json_encode($returnResponse);
}

function printToDoList($activities_reminder) {
	return cbCalendar::printToDoListCards($activities_reminder);
}
?>
