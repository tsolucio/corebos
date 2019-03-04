<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

$record = vtlib_purify($_REQUEST['record']);
$activitytype = cbCalendar::getCalendarActivityType($record);
if ($activitytype == 'Emails') {
	$tool_buttons = Button_Check($currentModule);
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
	$smarty->assign('CATEGORY', '');
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('THEME', $theme);
	$smarty->assign('ID', $record);
	$smarty->assign('RECORDID', $record);
	$smarty->assign('MODE', '');
	$smarty->display('Buttons_List.tpl');
	echo '<script type="text/javascript" src="modules/Emails/Emails.js"></script><br>';
	$currentModule = 'Emails';
	$emailStrings = return_module_language($current_language, $currentModule);
	$mod_strings = array_merge($mod_strings, $emailStrings);
	include 'modules/Emails/DetailView.php';
} else {
	require_once 'modules/Vtiger/DetailView.php';

	list($focus->column_fields['date_start'],$focus->column_fields['time_start']) = explode(' ', $focus->column_fields['dtstart'].' ');
	list($focus->column_fields['due_date'],$focus->column_fields['time_end']) = explode(' ', $focus->column_fields['dtend'].' ');
	$focus->column_fields['parent_id'] = $focus->column_fields['rel_id'];
	$evfocus = CRMEntity::getInstance('Events');
	$evfocus->retrieve_entity_info($record, 'Events');
	$colfields = array_merge($evfocus->column_fields, $focus->column_fields);
	$act_data = getBlocks('Events', 'detail_view', '', $colfields);
	$finaldata = $fldlabel = array();
	foreach ($act_data as $block => $entry) {
		foreach ($entry as $key => $value) {
			foreach ($value as $label => $field) {
				$fldlabel[$field['fldname']] = $label;
				if ($field['ui'] == 15 || $field['ui'] == 16) {
					foreach ($field['options'] as $index => $arr_val) {
						if ($arr_val[2] == "selected") {
							$finaldata[$field['fldname']] = $arr_val[0];
						}
					}
				} else {
					$fldvalue = $field['value'];
					if ($field['fldname'] == 'description') {
						$fldvalue = nl2br($fldvalue);
					}
					$finaldata[$field['fldname']] = $fldvalue;
				}
				$finaldata[$field['fldname'].'link'] = $field['link'];
				if (isset($field['secid'])) {
					$finaldata[$field['fldname'].'secid'] = $field['secid'];
				}
			}
		}
	}
	if (empty($current_user->hour_format)) {
		$format = '24';
	} else {
		$format = $current_user->hour_format;
	}
	list($stdate,$sttime) = explode(' ', $finaldata['date_start'].' ');
	list($enddate,$endtime) = explode(' ', $finaldata['due_date'].' ');
	$time_arr = getaddEventPopupTime($sttime, $endtime, $format);
	$data = array();
	$data['starthr'] = $time_arr['starthour'];
	$data['startmin'] = $time_arr['startmin'];
	$data['startfmt'] = $time_arr['startfmt'];
	$data['endhr'] = $time_arr['endhour'];
	$data['endmin'] = $time_arr['endmin'];
	$data['endfmt'] = $time_arr['endfmt'];
	$data['record'] = $focus->id;
	if (isset($finaldata['sendnotification']) && $finaldata['sendnotification'] == strtolower($app_strings['LBL_YES'])) {
		$data['sendnotification'] = $app_strings['LBL_YES'];
	} else {
		$data['sendnotification'] = $app_strings['LBL_NO'];
	}
	$data['subject'] = $finaldata['subject'];
	$data['date_start'] = $stdate;
	$data['due_date'] = $enddate;
	$data['assigned_user_id'] = $finaldata['assigned_user_id'];
	$data['visibility'] = (isset($finaldata['visibility']) ? $finaldata['visibility'] : '');
	$data['activitytype'] = (isset($finaldata['activitytype']) ? $finaldata['activitytype'] : $activitytype);
	$data['location'] = $finaldata['location'];
//Calculating reminder time
	$rem_days = 0;
	$rem_hrs = 0;
	$rem_min = 0;
	if (!empty($focus->column_fields['reminder_time'])) {
		$data['set_reminder'] = $app_strings['LBL_YES'];
		$data['reminder_str'] = $finaldata['reminder_time'];
	} else {
		$data['set_reminder'] = $app_strings['LBL_NO'];
	}
//To set recurring details
	$query = 'SELECT vtiger_recurringevents.*, vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.due_date, vtiger_activity.time_end
		FROM vtiger_recurringevents
		INNER JOIN vtiger_activity ON vtiger_activity.activityid = vtiger_recurringevents.activityid
		WHERE vtiger_recurringevents.activityid = ?';
	$res = $adb->pquery($query, array($focus->id));
	$rows = $adb->num_rows($res);
	if ($rows > 0) {
		$recurringObject = RecurringType::fromDBRequest($adb->query_result_rowdata($res, 0));
		$recurringInfoDisplayData = $recurringObject->getDisplayRecurringInfo();
		$data = array_merge($data, $recurringInfoDisplayData);
	} else {
		$data['recurringcheck'] = getTranslatedString('LBL_NO', $currentModule);
		$data['repeat_str'] = $data['repeat_frequency'] = '';
	}
	$sql = 'select vtiger_users.*,vtiger_invitees.* from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
	$result = $adb->pquery($sql, array($focus->id));
	$num_rows=$adb->num_rows($result);
	$invited_users=array();
	for ($i=0; $i<$num_rows; $i++) {
		$userid=$adb->query_result($result, $i, 'inviteeid');
		$username = getFullNameFromQResult($result, $i, 'Users');
		$invited_users[$userid]=$username;
	}
	$smarty->assign("INVITEDUSERS", $invited_users);

	$smarty->assign("LABEL", $fldlabel);
	$smarty->assign("ACTIVITYDATA", $data);

	$smarty->display('DetailView.tpl');
}
?>
