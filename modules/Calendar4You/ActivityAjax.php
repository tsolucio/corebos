<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $theme,$mod_strings,$current_language,$adb,$currentModule,$current_user,$app_strings;
$theme_path = 'themes/'.$theme.'/';
$image_path = $theme_path.'images/';

require_once 'modules/cbCalendar/calendarLayout.php';
require_once 'modules/Calendar4You/CalendarUtils.php';
require_once 'include/logging.php';
require_once 'include/utils/utils.php';
require_once 'modules/cbCalendar/CalendarCommon.php';
require_once 'modules/cbCalendar/Calendar.php';
$cal_log = LoggerManager::getLogger('calendar');
$cal_log->debug('> Calendar4YouAjax');

$mysel = isset($_REQUEST['view']) ? vtlib_purify($_REQUEST['view']) : 'day';
$calendar_arr = array();
$calendar_arr['IMAGE_PATH'] = $image_path;
$date_data = array();

if (isset($_REQUEST['day'])) {
	$date_data['day'] = vtlib_purify($_REQUEST['day']);
}
if (isset($_REQUEST['month'])) {
	$date_data['month'] = vtlib_purify($_REQUEST['month']);
}
if (isset($_REQUEST['week'])) {
	$date_data['week'] = vtlib_purify($_REQUEST['week']);
}
if (isset($_REQUEST['year'])) {
	if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970) {
		print("<font color='red'>".$app_strings['LBL_CAL_LIMIT_MSG'].'</font>');
		exit;
	}
	$date_data['year'] = vtlib_purify($_REQUEST['year']);
}

if ((isset($_REQUEST['type']) && $_REQUEST['type'] !='') || (isset($_REQUEST['n_type']) && $_REQUEST['n_type'] !='')) {
	$type = (isset($_REQUEST['type']) ? vtlib_purify($_REQUEST['type']) : '');
	$n_type = (isset($_REQUEST['n_type']) ? vtlib_purify($_REQUEST['n_type']) : '');

	if ($type == 'minical') {
		$temp_module = $currentModule;
		$mod_strings = return_module_language($current_language, 'cbCalendar');
		$currentModule = 'Calendar';
		$calendar_arr['IMAGE_PATH'] = $image_path;
		$calendar_arr['calendar'] = new Calendar('month', $date_data);
		$calendar_arr['view'] = 'month';
		$calendar_arr['size'] = 'small';
		if ($current_user->hour_format != '') {
			$calendar_arr['calendar']->hour_format=$current_user->hour_format;
		}
		get_its_mini_calendar($calendar_arr);
		$mod_strings = return_module_language($current_language, $temp_module);
		$currentModule = vtlib_purify($_REQUEST['module']);
	} elseif ($type == 'settings') {
		require_once 'modules/Calendar4You/calendar_share.php';
	} elseif ($type == 'event_settings') {
		require_once 'modules/Calendar4You/event_settings.php';
	}
} else {
	require_once 'include/Ajax/CommonAjax.php';
}
?>
