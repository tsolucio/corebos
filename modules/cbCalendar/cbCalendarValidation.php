<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : cbCalendar Validation
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
global $log,$currentModule,$adb;
include_once 'include/validation/load_validations.php';
include_once 'modules/cbMap/processmap/Validations.php';

$screen_values = json_decode($_REQUEST['structure'], true);
$editingDTEnd = !empty($screen_values['dtend']);
if ((empty($screen_values['dtstart']) || empty($screen_values['dtend'])) && !empty($screen_values['record'])) {
	$rs = $adb->pquery('select dtstart,dtend from vtiger_activity where activityid=?', array($screen_values['record']));
	$dbdatestart = $adb->query_result($rs, 0, 'dtstart');
	$dbdateend = $adb->query_result($rs, 0, 'dtend');
	if (empty($screen_values['dtstart'])) {
		$screen_values['dtstart'] = $adb->query_result($rs, 0, 'dtstart');
	} else {
		$screen_values['dtstart'] = DateTimeField::formatDatebaseTimeString($screen_values['dtstart'], $screen_values['timefmt_dtstart']);
		$dt = new DateTimeField($screen_values['dtstart']);
		$screen_values['dtstart'] = $dt->getDBInsertDateTimeValue();
	}
	if (empty($screen_values['dtend'])) {
		$screen_values['dtend'] = $adb->query_result($rs, 0, 'dtend');
	} else {
		$screen_values['dtend'] = DateTimeField::formatDatebaseTimeString($screen_values['dtend'], $screen_values['timefmt_dtend']);
		$dt = new DateTimeField($screen_values['dtend']);
		$screen_values['dtend'] = $dt->getDBInsertDateTimeValue();
	}
} else {
	if (!empty($screen_values['dtstart'])) {
		$screen_values['dtstart'] = DateTimeField::formatDatebaseTimeString($screen_values['dtstart'], $screen_values['timefmt_dtstart']);
		$dt = new DateTimeField($screen_values['dtstart']);
		$screen_values['dtstart'] = $dt->getDBInsertDateTimeValue();
	}
	if (!empty($screen_values['dtend'])) {
		$screen_values['dtend'] = DateTimeField::formatDatebaseTimeString($screen_values['dtend'], $screen_values['timefmt_dtend']);
		$dt = new DateTimeField($screen_values['dtend']);
		$screen_values['dtend'] = $dt->getDBInsertDateTimeValue();
	}
}

if (empty($screen_values['action']) && !empty($screen_values['record']) && !$editingDTEnd) { // DetailView Edit
	list($screen_values['date_start'],$screen_values['time_start']) = explode(' ', $screen_values['dtstart']);
	list($screen_values['due_date'],$screen_values['time_end']) = explode(' ', $screen_values['dtend']);
	$pushenddate = GlobalVariable::getVariable('Calendar_Push_End_On_Start_Change', 'No', 'cbCalendar');
	switch ($pushenddate) {
		case 'Distance':
			$dist = strtotime($dbdateend)-strtotime($dbdatestart);
			$newend = strtotime($screen_values['dtstart'])+$dist;
			$screen_values['dtend'] = date('Y-m-d H:i:s', $newend);
			list($screen_values['due_date'],$screen_values['time_end']) = explode(' ', $screen_values['dtend']);
			$adb->pquery(
				'update vtiger_activity set dtend=?,due_date=?,time_end=? where activityid=?',
				array($screen_values['dtend'],$screen_values['due_date'],$screen_values['time_end'],$screen_values['record'])
			);
			break;
		case 'Set':
			if ($screen_values['dtend'] < $screen_values['dtstart']) {
				$dist = GlobalVariable::getVariable('Calendar_call_default_duration', 5, 'Calendar4You');
				$newend = strtotime($screen_values['dtstart'])+($dist*60);
				$screen_values['dtend'] = date('Y-m-d H:i:s', $newend);
				list($screen_values['due_date'],$screen_values['time_end']) = explode(' ', $screen_values['dtend']);
				$adb->pquery(
					'update vtiger_activity set dtend=?,due_date=?,time_end=? where activityid=?',
					array($screen_values['dtend'],$screen_values['due_date'],$screen_values['time_end'],$screen_values['record'])
				);
			}
			break;
		default:
			break;
	}
}

if (isset($screen_values['action']) && $screen_values['action'] == 'MassEditSave') {
	echo '%%%OK%%%';
} else {
	if (isset($screen_values['followupcreate']) && $screen_values['followupcreate'] == '1' && !empty($screen_values['record']) && empty($screen_values['followupdt'])) {
		$rs = $adb->pquery('select followupdt from vtiger_activity where activityid=?', array($screen_values['record']));
		$screen_values['followupdt'] = $adb->query_result($rs, 0, 'followupdt');
	}
	$v = new cbValidator($screen_values);
	$v->rule('required', 'dtstart');
	$v->rule('required', 'dtend');
	$v->rule('dateAfter', 'dtend', $screen_values['dtstart'])->label(getTranslatedString('Due Date', 'cbCalendar'));
	// Planned must have start date in future
	if (isset($screen_values['eventstatus']) && $screen_values['eventstatus'] == 'Planned') {
		$v->rule('dateAfter', 'dtstart', date('Y-m-d H:i:s', strtotime('now')-600))->label(getTranslatedString('DATE_SHOULDNOT_PAST', 'cbCalendar'));
	}
	if (isset($screen_values['recurringcheck']) && $screen_values['recurringcheck'] == '1'
		&& $screen_values['recurringtype'] == 'Monthly' && $screen_values['repeatMonth'] == 'date') {
			$v->rule('required', 'repeatMonth_date')->label(getTranslatedString('day of the month', 'cbCalendar'));
			$v->rule('between', 'repeatMonth_date', array(0,31));
	}
	if (isset($screen_values['followupcreate']) && $screen_values['followupcreate'] == '1') {
		$v->rule('required', 'followupdt')->label(getTranslatedString('Fecha Seguimiento', 'cbCalendar'));
		$v->rule('dateAfter', 'followupdt', $screen_values['dtend']);
	}
	if ($v->validate()) {
		echo '%%%OK%%%';
	} else {
		// Errors
		echo Validations::formatValidationErrors($v->errors(), 'cbCalendar');
	}
}
