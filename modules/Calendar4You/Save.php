<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
require_once 'modules/cbCalendar/CalendarCommon.php';
require_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar4You/CalendarUtils.php';

global $adb, $theme, $current_user;
$local_log = LoggerManager::getLogger('index');

$Calendar4You = new Calendar4You();

$Calendar4You->GetDefPermission($current_user);

if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
	$edit_permissions = $Calendar4You->CheckPermissions('EDIT', $_REQUEST['record']);
} else {
	$edit_permissions = $Calendar4You->CheckPermissions('CREATE', $_REQUEST['record']);
}

if (!$edit_permissions) {
	NOPermissionDiv();
}

$tab_type = 'cbCalendar';
$focus = CRMEntity::getInstance($tab_type);
$search=vtlib_purify($_REQUEST['search_url']);

$focus->column_fields['activitytype'] = 'Task';
$mode = vtlib_purify($_REQUEST['mode']);
$record=vtlib_purify($_REQUEST['record']);
if ($mode) {
	$focus->mode = $mode;
}
if ($record) {
	$focus->id  = $record;
}

$timeFields = array('time_start', 'time_end');
$tabId = getTabid($tab_type);
foreach ($focus->column_fields as $fieldname => $val) {
	$fieldInfo = getFieldRelatedInfo($tabId, $fieldname);
	$uitype = $fieldInfo['uitype'];
	$typeofdata = $fieldInfo['typeofdata'];
	if (isset($_REQUEST[$fieldname])) {
		if (is_array($_REQUEST[$fieldname])) {
			$value = $_REQUEST[$fieldname];
		} else {
			$value = trim($_REQUEST[$fieldname]);
		}

		if ((($typeofdata == 'T~M') || ($typeofdata == 'T~O')) && ($uitype == 2 || $uitype == 70 )) {
			if (!in_array($fieldname, $timeFields)) {
				$date = DateTimeField::convertToDBTimeZone($value);
				$value = $date->format('H:i');
			}
			$focus->column_fields[$fieldname] = $value;
		} else {
			$focus->column_fields[$fieldname] = $value;
		}
		if (($fieldname == 'notime') && ($focus->column_fields[$fieldname])) {
			$focus->column_fields['time_start'] = '';
			$focus->column_fields['duration_hours'] = '';
			$focus->column_fields['duration_minutes'] = '';
		}
		if (($fieldname == 'recurringtype') && ! isset($_REQUEST['recurringcheck'])) {
			$focus->column_fields['recurringtype'] = '--None--';
		}
	}
}
if (isset($_REQUEST['visibility']) && $_REQUEST['visibility']!= '') {
		$focus->column_fields['visibility'] = $_REQUEST['visibility'];
} else {
	$focus->column_fields['visibility'] = 'Private';
}

if ($_REQUEST['assigntype'] == 'U') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif ($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

$dateField = 'date_start';
$fieldname = 'time_start';
$date = new DateTimeField($_REQUEST[$dateField]. ' ' . $_REQUEST[$fieldname]);
$focus->column_fields[$dateField] = $date->getDBInsertDateValue();
$focus->column_fields[$fieldname] = $date->getDBInsertTimeValue();
if (empty($_REQUEST['time_end'])) {
	$_REQUEST['time_end'] = date('H:i', strtotime('+10 minutes', strtotime($focus->column_fields['date_start'].' '.$_REQUEST['time_start'])));
}
$dateField = 'due_date';
$fieldname = 'time_end';
$date = new DateTimeField($_REQUEST[$dateField]. ' ' . $_REQUEST[$fieldname]);
$focus->column_fields[$dateField] = $date->getDBInsertDateValue();
$focus->column_fields[$fieldname] = $date->getDBInsertTimeValue();

$focus->save($tab_type);
/* For Followup START */
if (isset($_REQUEST['followup']) && $_REQUEST['followup']=='on' && isset($_REQUEST['followup_time_start']) &&  $_REQUEST['followup_time_start']!='') {
	$heldevent_id = $focus->id;
	$focus->column_fields['subject'] = '['.getTranslatedString('LBL_FOLLOWUP', 'cbCalendar').'] '.$focus->column_fields['subject'];
	$startDate = new DateTimeField($_REQUEST['followup_date'].' '.$_REQUEST['followup_time_start']);
	$endDate = new DateTimeField($_REQUEST['followup_due_date'].' '.$_REQUEST['followup_time_end']);
	$focus->column_fields['date_start'] = $startDate->getDBInsertDateValue();
	$focus->column_fields['due_date'] = $endDate->getDBInsertDateValue();
	$focus->column_fields['time_start'] = $startDate->getDBInsertTimeValue();
	$focus->column_fields['time_end'] = $endDate->getDBInsertTimeValue();
	$focus->column_fields['eventstatus'] = 'Planned';
	$focus->column_fields['activitytype'] = $_REQUEST['follow_activitytype'];
	$focus->mode = 'create';
	$focus->save($tab_type);
}
/* For Followup END */
$return_id = $focus->id;

if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '') {
	$return_module = vtlib_purify($_REQUEST['return_module']);
} else {
	$return_module = 'Calendar4You';
}
if (isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != '') {
	$return_action = vtlib_purify($_REQUEST['return_action']);
} else {
	$return_action = 'EventDetailView';
}
if (isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != '') {
	$returnid = vtlib_purify($_REQUEST['return_id']);
}

function getRequestData($return_id) {
	global $adb;
	$cont_qry = 'select contactid from vtiger_cntactivityrel where activityid=?';
	$cont_res = $adb->pquery($cont_qry, array($return_id));
	$noofrows = $adb->num_rows($cont_res);
	$cont_id = array();
	if ($noofrows > 0) {
		for ($i=0; $i<$noofrows; $i++) {
			$cont_id[] = $adb->query_result($cont_res, $i, 'contactid');
		}
	}
	$cont_name = '';
	foreach ($cont_id as $id) {
		if ($id != '') {
			$displayValueArray = getEntityName('Contacts', $id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $field_value) {
					$contact_name = $field_value;
				}
			}
			$cont_name .= $contact_name .', ';
		}
	}
	$cont_name  = trim($cont_name, ', ');
	$mail_data = array();
	$mail_data['user_id'] = $_REQUEST['assigned_user_id'];
	$mail_data['subject'] = $_REQUEST['subject'];
	$mail_data['status'] = $_REQUEST['eventstatus'];
	$mail_data['activity_mode'] = $_REQUEST['activity_mode'];
	$mail_data['taskpriority'] = $_REQUEST['taskpriority'];
	$mail_data['relatedto'] = $_REQUEST['parent_name'];
	$mail_data['contact_name'] = $cont_name;
	$mail_data['description'] = $_REQUEST['description'];
	$mail_data['assign_type'] = $_REQUEST['assigntype'];
	$mail_data['group_name'] = getGroupName($_REQUEST['assigned_group_id']);
	$mail_data['mode'] = $_REQUEST['mode'];
	$value = getaddEventPopupTime($_REQUEST['time_start'], $_REQUEST['time_end'], '24');
	$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
	if ($_REQUEST['activity_mode']!='Task') {
		$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
	}
	$startDate = new DateTimeField($_REQUEST['date_start'].' '.$start_hour);
	$endDate = new DateTimeField($_REQUEST['due_date'].' '.$end_hour);
	$mail_data['st_date_time'] = $startDate->getDBInsertDateTimeValue();
	$mail_data['end_date_time'] = $endDate->getDBInsertDateTimeValue();
	$mail_data['location']=vtlib_purify($_REQUEST['location']);
	return $mail_data;
}

function getFieldRelatedInfo($tabId, $fieldName) {
	$fieldInfo = VTCacheUtils::lookupFieldInfo($tabId, $fieldName);
	if ($fieldInfo === false) {
		getColumnFields(getTabModuleName($tabId));
		$fieldInfo = VTCacheUtils::lookupFieldInfo($tabId, $fieldName);
	}
	return $fieldInfo;
}

if (isset($_REQUEST['contactidlist']) && $_REQUEST['contactidlist'] != '') {
	//split the string and store in an array
	$storearray = explode(';', $_REQUEST['contactidlist']);
	$adb->pquery('delete from vtiger_cntactivityrel where activityid=?', array($record));
	$record = $focus->id;
	foreach ($storearray as $id) {
		if ($id != '') {
			$adb->pquery('insert into vtiger_cntactivityrel values (?,?)', array($id, $record));
			if (!empty($heldevent_id)) {
				$adb->pquery('insert into vtiger_cntactivityrel values (?,?)', array($id, $heldevent_id));
			}
		}
	}
}

//code added to send mail to the vtiger_invitees
if (isset($_REQUEST['inviteesid']) && $_REQUEST['inviteesid']!='') {
	$mail_contents = getRequestData($return_id);
	$focus->sendInvitation($_REQUEST['inviteesid'], $_REQUEST['subject'], $mail_contents);
}

//to delete contact account relation while editing event
if (isset($_REQUEST['deletecntlist']) && $_REQUEST['deletecntlist'] != '' && $_REQUEST['mode'] == 'edit') {
	//split the string and store it in an array
	$storearray = explode(';', $_REQUEST['deletecntlist']);
	$sql = 'delete from vtiger_cntactivityrel where contactid=? and activityid=?';
	$record = $focus->id;
	foreach ($storearray as $id) {
		if ($id != '') {
			$adb->pquery($sql, array($id, $record));
		}
	}
}

//to delete activity and its parent table relation
if (isset($_REQUEST['del_actparent_rel']) && $_REQUEST['del_actparent_rel'] != '' && $_REQUEST['mode'] == 'edit') {
	$parnt_id = $_REQUEST['del_actparent_rel'];
	$adb->pquery('delete from vtiger_seactivityrel where crmid=? and activityid=?', array($parnt_id, $record));
}

if (isset($_REQUEST['view']) && $_REQUEST['view']!='') {
	$view=vtlib_purify($_REQUEST['view']);
}
if (isset($_REQUEST['hour']) && $_REQUEST['hour']!='') {
	$hour=vtlib_purify($_REQUEST['hour']);
}
if (isset($_REQUEST['day']) && $_REQUEST['day']!='') {
	$day=vtlib_purify($_REQUEST['day']);
}
if (isset($_REQUEST['month']) && $_REQUEST['month']!='') {
	$month=vtlib_purify($_REQUEST['month']);
}
if (isset($_REQUEST['year']) && $_REQUEST['year']!='') {
	$year=vtlib_purify($_REQUEST['year']);
}
if (isset($_REQUEST['viewOption']) && $_REQUEST['viewOption']!='') {
	$viewOption=vtlib_purify($_REQUEST['viewOption']);
}
if (isset($_REQUEST['subtab']) && $_REQUEST['subtab']!='') {
	$subtab=vtlib_purify($_REQUEST['subtab']);
}

if ($_REQUEST['recurringcheck']) {
	include_once 'modules/cbCalendar/RepeatEvents.php';
	Calendar_RepeatEvents::repeatFromRequest($focus);
}

//code added for returning back to the current view after edit from list view
if ($_REQUEST['return_viewname'] == '') {
	$return_viewname='0';
}
if ($_REQUEST['return_viewname'] != '') {
	$return_viewname=vtlib_purify($_REQUEST['return_viewname']);
}

if (!empty($_REQUEST['start'])) {
	$page='&start='.vtlib_purify($_REQUEST['start']);
}
if (!empty($_REQUEST['pagenumber'])) {
	$page = '&start='.vtlib_purify($_REQUEST['pagenumber']);
}
$url = 'Location: index.php?action='.$return_action.'&module='.$return_module.'&view='.$view.'&hour='.$hour.'&day='.$day.'&month='.$month.'&year='.$year;
if (!empty($return_id) && empty($returnid)) {
	header('&record='.$return_id.'&viewOption='.$viewOption.'&subtab='.$subtab);
} else {
	header('&record='.$returnid.'&viewname='.$return_viewname . $page . $search);
}
?>
