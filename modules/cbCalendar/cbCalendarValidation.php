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

$screen_values = json_decode($_REQUEST['structure'],true);
if ((empty($screen_values['dtstart']) or empty($screen_values['dtend'])) and !empty($screen_values['record'])) {
	$rs = $adb->pquery('select dtstart,dtend from vtiger_activity where activityid=?',array($screen_values['record']));
	if (empty($screen_values['dtstart'])) {
		$screen_values['dtstart'] = $adb->query_result($rs, 0, 'dtstart');
	}
	if (empty($screen_values['dtend'])) {
		$screen_values['dtend'] = $adb->query_result($rs, 0, 'dtend');
	}
}

$v = new cbValidator($screen_values);
$v->rule('required', 'dtstart');
$v->rule('required', 'dtend');
$v->rule('dateAfter', 'dtend', $screen_values['dtstart'])->label(getTranslatedString('Due Date','cbCalendar'));
if ($v->validate()) {
	echo '%%%OK%%%';
} else {
	// Errors
	echo Validations::formatValidationErrors($v->errors(),'cbCalendar');
}
