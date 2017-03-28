<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Vtiger/EditView.php';

if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
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
	}
	$smarty->assign("ACTIVITYDATA",$value);
	$smarty->assign("LABEL",$fldlabel);
}

$smarty->assign("REPEAT_LIMIT_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$smarty->display('salesEditView.tpl');
?>