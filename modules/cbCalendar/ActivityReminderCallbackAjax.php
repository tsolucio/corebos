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
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCalendar');
		$callback_query =
			'SELECT vtiger_activity_reminder_popup.*,vtiger_activity_reminder_popup.status as readed, vtiger_crmentity.*'
			.' FROM vtiger_activity_reminder_popup'
			.' inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_activity_reminder_popup.recordid '
			.' inner join vtiger_activity on vtiger_activity.activityid = vtiger_activity_reminder_popup.recordid '
			.' WHERE vtiger_crmentity.smownerid = '.$current_user->id.' and vtiger_crmentity.deleted = 0 '
			." AND (vtiger_activity.activitytype not in ('Emails') and vtiger_activity.eventstatus not in ('','Held','Completed','Deferred'))"
			." and ((DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') < '" . $date
			."' and DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') >= '" . $date_inpast . "')"
			." or ((DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') = '" . $date . "')"
			." AND (TIME_FORMAT(vtiger_activity_reminder_popup.time_start,'%H:%i') <= '" . $time . "')))"
			.' ORDER BY vtiger_activity_reminder_popup.date_start DESC limit 0, '.$list_max_entries_per_page;

		$result = $adb->query($callback_query);

		$cbrows = $adb->num_rows($result);
		$notreaded = 0;
		$activities_reminder = array();
		if ($cbrows > 0) {
			for ($index = 0; $index < $cbrows; ++$index) {
				$activity = array();
				$reminderid = $adb->query_result($result, $index, 'reminderid');
				$cbrecord = $adb->query_result($result, $index, 'recordid');
				$cbmodule = $adb->query_result($result, $index, 'setype');
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
					$cbsubject      = array_values(getEntityName($cbmodule, $cbrecord));
					$cbsubject      = $cbsubject[0];
					$cbactivitytype = getTranslatedString($cbmodule, $cbmodule);
					$cbdate         = $adb->query_result($result, $index, 'date_start');
					$cbtime         = $adb->query_result($result, $index, 'time_start');
					$cbstatus = '';
					$mod = CRMEntity::getInstance($cbmodule);
					$activity['activityimage'] = $mod->$moduleIcon['icon'];
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
	$smarty = new vtigerCRM_Smarty;
	$list = '';
	foreach ($activities_reminder as $ACTIVITY) {
		$smarty->assign('TASKItemID', $ACTIVITY['popupid']);
		$smarty->assign('TASKItemRead', $ACTIVITY['cbreaded']);
		$smarty->assign('TASKImage', $ACTIVITY['activityimage']);
		$smarty->assign('TASKType', $ACTIVITY['activitytype']);
		$smarty->assign('TASKTitle', vtlib_purify($ACTIVITY['cbsubject']));
		$smarty->assign('TASKSubtitle', vtlib_purify($ACTIVITY['activitytype'].' - '.$ACTIVITY['cbstatus']));
		$smarty->assign('TASKSubtitleColor', vtlib_purify($ACTIVITY['cbcolor']));
		$smarty->assign('TASKStatus', vtlib_purify($ACTIVITY['cbdate'].' '.$ACTIVITY['cbtime']));
		$actions = array();
		$actions[getTranslatedString('LBL_VIEW', 'Settings')] = array(
			'type' => 'link',
			'action' => 'index.php?action=DetailView&module=cbCalendar&record='.$ACTIVITY['cbrecord'],
		);
		$actions[getTranslatedString('LBL_POSTPONE', 'Calendar4You')] = array(
			'type' => 'click',
			'action' => "ActivityReminderPostponeCallback('cbCalendar', '".$ACTIVITY['cbrecord']."', '".$ACTIVITY['cbreminderid']."');ActivityReminderRemovePopupDOM('".$ACTIVITY['popupid']."');"
		);
		$actions[getTranslatedString('LBL_HIDE')] = array(
			'type' => 'click',
			'action' => "ActivityReminderCallbackReset(0, '".$ACTIVITY['popupid']."');ActivityReminderRemovePopupDOM('".$ACTIVITY['popupid']."');"
		);
		$smarty->assign('TASKActions', $actions);
		$list .= $smarty->fetch('Components/TaskItem.tpl');
	}
	return $list;
}
?>
