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
		$status = ' vtiger_activity_reminder_popup.status<>2 ';
		$callback_query = cbCalendar::getActionsQuery($current_user, $date, $date_inpast, $time, $list_max_entries_per_page, $status);
		$result = $adb->query($callback_query);

		$cbrows = $adb->num_rows($result);
		$notreaded = 0;
		$activities_reminder = array();
		if ($cbrows > 0) {
			for ($index = 0; $index < $cbrows; ++$index) {
				$reminderid = $adb->query_result($result, $index, 'reminderid');
				$cbrecord = $adb->query_result($result, $index, 'recordid');
				$cbmodule = $adb->query_result($result, $index, 'semodule');
				$cbreaded = $adb->query_result($result, $index, 'readed');
				if ($cbreaded == '0') {
					$notreaded++;
				}
				$moreinfo = $adb->query_result($result, $index, 'moreinfo');
				$cbdate = $adb->query_result($result, $index, 'date_start');
				$cbtime = $adb->query_result($result, $index, 'time_start');
				$cbaction = $adb->query_result($result, $index, 'moreaction');
				$adb->pquery('UPDATE vtiger_activity_reminder_popup set status=1 where reminderid=?', array($reminderid));
				$activities_reminder[] = cbCalendar::getActionElement($reminderid, $cbmodule, $cbrecord, $moreinfo, $cbdate, $cbtime, $cbaction, $cbreaded);
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
