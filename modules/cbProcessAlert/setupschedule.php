<?php
/*************************************************************************************************
 * Copyright 2019 Spike Associates -- This file is a part of coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Process Flow Alert
 *  Version      : 5.4.0
 *  Author       : AT CONSULTING
 *************************************************************************************************/
$workflow = new Workflow();
array_walk(
	$focus->column_fields,
	function (&$val) {
		$val = decode_html($val);
	}
);
$row = $focus->column_fields;
$row['workflow_id'] = 0;
$row['module_name'] = 'cbProcessAlert';
$row['summary'] = '';
$row['test'] = '';
$row['execution_condition'] = '';
$row['defaultworkflow'] = false;
$workflow->setup($row);
if (empty($workflow->schtime)) {
	$smarty->assign('schdtime_12h', date('h:ia'));
} else {
	$smarty->assign('schdtime_12h', date('h:ia', strtotime(substr($workflow->schtime, 0, strrpos($workflow->schtime, ':')))));
}
if (!empty($workflow->schannualdates)) {
	$schannualdates = json_decode($workflow->schannualdates);
	$schannualdates = DateTimeField::convertToUserFormat($schannualdates[0]);
} else {
	$schannualdates = '';
}
$smarty->assign('schdate', $schannualdates);
if (empty($workflow->schdayofmonth)) {
	$smarty->assign('selected_days1_31', '');
} else {
	$smarty->assign('selected_days1_31', json_decode($workflow->schdayofmonth));
}
if (empty($workflow->schminuteinterval)) {
	$smarty->assign('selected_minute_interval', '');
} else {
	$smarty->assign('selected_minute_interval', json_decode($workflow->schminuteinterval));
}
if (empty($workflow->schdayofweek)) {
	$smarty->assign('dayOfWeek', '');
} else {
	$smarty->assign('dayOfWeek', json_decode($workflow->schdayofweek));
}
$dayrange = array();
$intervalrange=array();
for ($d=1; $d<=31; $d++) {
	$dayrange[$d] = $d;
}
for ($interval=5; $interval<=50; $interval+=5) {
	$intervalrange[$interval]=$interval;
}
$smarty->assign('days1_31', $dayrange);
$smarty->assign('interval_range', $intervalrange);
$smarty->assign('dateFormat', parse_calendardate($current_user->date_format));
$smarty->assign('workflow', $workflow);
$smarty->assign('MODULE_NAME', 'com_vtiger_workflow');
?>