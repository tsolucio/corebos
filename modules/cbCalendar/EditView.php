<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$record = isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : null;
$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();
if ($record and cbCalendar::getCalendarActivityType($record)=='Emails') {
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
	$_REQUEST['return_action'] = 'DetailView';
	$_REQUEST['return_module'] = 'cbCalendar';
	$smarty->display('Buttons_List.tpl');
	echo '<script type="text/javascript" src="modules/Emails/Emails.js"></script><br>';
	$currentModule = 'Emails';
	$emailStrings = return_module_language($current_language, $currentModule);
	$mod_strings = array_merge($mod_strings,$emailStrings);
	include 'modules/Emails/EditView.php';
} else {

require_once 'modules/Vtiger/EditView.php';

if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$activitytype = cbCalendar::getCalendarActivityType($record);
	$_REQUEST['activity_mode'] = $activitytype;
	list($focus->column_fields['date_start'],$focus->column_fields['time_start']) = explode(' ', $focus->column_fields['dtstart'].' ');
	list($focus->column_fields['due_date'],$focus->column_fields['time_end']) = explode(' ', $focus->column_fields['dtend'].' ');
	$focus->column_fields['parent_id'] = $_REQUEST['parent_id'] = $focus->column_fields['rel_id'];
	$act_data = getBlocks('Events','edit_view','edit',$focus->column_fields);
	foreach($act_data as $header=>$blockitem) {
		foreach($blockitem as $row=>$data) {
			foreach($data as $key=>$maindata) {
				if (count($maindata)==0) continue;
				$fldlabel[$maindata[2][0]] = isset($maindata[1][0]) ? $maindata[1][0] : '';
				$fldlabel_sel[$maindata[2][0]] = isset($maindata[1][1]) ? $maindata[1][1] : '';
				$fldlabel_combo[$maindata[2][0]] = isset($maindata[1][2]) ? $maindata[1][2] : '';
				$value[$maindata[2][0]] = isset($maindata[3][0]) ? $maindata[3][0] : '';
				$secondvalue[$maindata[2][0]] = isset($maindata[3][1]) ? $maindata[3][1] : '';
				$thirdvalue[$maindata[2][0]] = isset($maindata[3][2]) ? $maindata[3][2] : '';
			}
		}
	}
	$smarty->assign("secondvalue",$secondvalue);
	$smarty->assign("thirdvalue",$thirdvalue);
	$smarty->assign("fldlabel_combo",$fldlabel_combo);
	$smarty->assign("fldlabel_sel",$fldlabel_sel);
	$sql = 'select vtiger_users.*,vtiger_invitees.* from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
	$result = $adb->pquery($sql, array($focus->id));
	$num_rows=$adb->num_rows($result);
	$invited_users=Array();
	for($i=0;$i<$num_rows;$i++) {
		$userid=$adb->query_result($result,$i,'inviteeid');
		$username = getFullNameFromQResult($result, $i, 'Users');
		$invited_users[$userid]=$username;
	}
	$smarty->assign('INVITEDUSERS',$invited_users);
	$userDetails = getOtherUserName($current_user->id);
	$smarty->assign('USERSLIST',$userDetails);

	$query = 'SELECT vtiger_recurringevents.*, vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.due_date, vtiger_activity.time_end
		FROM vtiger_recurringevents
		INNER JOIN vtiger_activity ON vtiger_activity.activityid = vtiger_recurringevents.activityid
		WHERE vtiger_recurringevents.activityid = ?';
	$res = $adb->pquery($query, array($focus->id));
	$rows = $adb->num_rows($res);
	if ($rows > 0) {
		$recurringObject = RecurringType::fromDBRequest($adb->query_result_rowdata($res, 0));

		$value['recurringcheck'] = 'Yes';
		$value['repeat_frequency'] = $recurringObject->getRecurringFrequency();
		$value['eventrecurringtype'] = $recurringObject->getRecurringType();
		$recurringInfo = $recurringObject->getUserRecurringInfo();

		if($recurringObject->getRecurringType() == 'Weekly') {
			$noOfDays = count($recurringInfo['dayofweek_to_repeat']);
			for ($i = 0; $i < $noOfDays; ++$i) {
				$value['week'.$recurringInfo['dayofweek_to_repeat'][$i]] = 'checked';
			}

		} elseif ($recurringObject->getRecurringType() == 'Monthly') {
			$value['repeatMonth'] = $recurringInfo['repeatmonth_type'];
			if ($recurringInfo['repeatmonth_type'] == 'date') {
				$value['repeatMonth_date'] = $recurringInfo['repeatmonth_date'];
			} else {
				$value['repeatMonth_daytype'] = $recurringInfo['repeatmonth_daytype'];
				$value['repeatMonth_day'] = $recurringInfo['dayofweek_to_repeat'][0];
			}
		}
	} else {
		$value['recurringcheck'] = 'No';
		$value['repeatMonth'] = $value['repeatMonth_daytype'] = $value['repeatMonth_day'] = $value['repeat_frequency'] = $value['eventrecurringtype'] = $value['repeatMonth_date'] = '';
		for ($i = 0; $i < 7; ++$i) {
			$value['week'.$i] = '';
		}
	}
	$smarty->assign("ACTIVITYDATA",$value);
	$smarty->assign("LABEL",$fldlabel);
}

$smarty->assign("REPEAT_LIMIT_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$smarty->display('salesEditView.tpl');
}
?>