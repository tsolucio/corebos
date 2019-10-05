<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $adb,$current_user,$default_timezone;

if ($_REQUEST['mode'] == 'event_drop' || $_REQUEST['mode'] == 'event_resize') {
	list($void,$processed) = cbEventHandler::do_filter('corebos.filter.CalendarModule.save', array($_REQUEST, false));
	if ($processed) {
		exit;
	}

	$record = (isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : 0);
	if (!empty($record) && isPermitted('cbCalendar', 'EditView', $record) == 'yes') {
		if ($current_user->date_format == 'dd-mm-yyyy') {
			$dt_fmt = 'd-m-Y';
		} elseif ($current_user->date_format == 'mm-dd-yyyy') {
			$dt_fmt = 'm-d-Y';
		} elseif ($current_user->date_format == 'yyyy-mm-dd') {
			$dt_fmt = 'Y-m-d';
		}
		$hr_fmt = ($current_user->hour_format=='24' ? '24' : '12');

		$focus = CRMEntity::getInstance('cbCalendar');
		$entityModuleHandler = vtws_getModuleHandlerFromName('cbCalendar', $current_user);
		$meta = $entityModuleHandler->getMeta();
		$focus->id = $record;
		$focus->retrieve_entity_info($focus->id, 'cbCalendar');
		$focus->mode = 'edit';

		$ActStart = $focus->column_fields['dtstart'];
		$ActEnd = $focus->column_fields['dtend'];
		$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);

		$day_drop = vtlib_purify($_REQUEST['day']);
		if ($day_drop > 0) {
			$d = '+';
		} else {
			$d = '-';
		}
		if (empty($_REQUEST['allday'])) {
			$allday = $focus->column_fields['notime'];
		} else {
			$allday = ($_REQUEST['allday']=='true' ? '1' : '0');
		}
		if ($allday != $focus->column_fields['notime']) {
			$minute_drop = 0;
		} else {
			$minute_drop = vtlib_purify($_REQUEST['minute']);
		}
		$focus->column_fields['notime'] = $allday;
		if ($minute_drop > 0) {
			$m = '+';
		} else {
			$m = '-';
		}
		$date = new DateTime($ActStart);
		if ($_REQUEST['mode'] == 'event_drop') {
			if ($day_drop != 0) {
				$date->modify($d.abs($day_drop).' day');
			}
			if ($minute_drop != 0) {
				$date->modify($m.abs($minute_drop).' minutes');
			}
			$new_date_start = $date->format('Y-m-d');
			$new_time_start = $date->format('H:i:s');
			if ($new_date_start.$new_time_start>date('Y-m-dH:i:s')) {
				// event in the future so we make sure the reminder is set to send
				$adb->pquery('update vtiger_activity_reminder set reminder_sent=0 where activity_id=?', array($focus->id));
				$adb->pquery(
					'update vtiger_activity_reminder_popup set date_start=?,time_start=?,status=0 where recordid=?',
					array($new_date_start,$new_time_start,$focus->id)
				);
			}
		}
		$new_time_start_time = $date->format('U');
		$tzstartdatetime = DateTimeField::convertTimeZone($date->format('H:i:s'), $default_timezone, $current_user->time_zone);
		$tzstarttime = $tzstartdatetime->format('H:i:s');
		$newdtstart = $date->format($dt_fmt) . ' ' . DateTimeField::formatUserTimeString($tzstarttime, $hr_fmt);

		$date = new DateTime($ActEnd);
		if ($day_drop != 0) {
			$date->modify($d.abs($day_drop).' day');
		}
		if ($minute_drop != 0) {
			$date->modify($m.abs($minute_drop).' minutes');
		}
		$new_due_date = $date->format('Y-m-d');
		$new_time_end = $date->format('H:i:s');
		$new_time_end_time = $date->format('U');
		$tzenddatetime = DateTimeField::convertTimeZone($date->format('H:i:s'), $default_timezone, $current_user->time_zone);
		$tzendtime = $tzenddatetime->format('H:i:s');
		$focus->column_fields['dtend'] = $date->format($dt_fmt) . ' ' . DateTimeField::formatUserTimeString($tzendtime, $hr_fmt);

		if ($_REQUEST['mode'] == 'event_resize') {
			$focus->column_fields['time_end'] = $new_time_end;
		} else {
			$focus->column_fields['dtstart'] = $newdtstart;
		}
		$focus->save('cbCalendar');
	}
}
exit;
?>
