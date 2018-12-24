<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'modules/Calendar4You/CalendarUtils.php';
include_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar/Calendar.php';

global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $current_user, $default_charset;
require_once 'Smarty_setup.php';
$tasklabel = getAllModulesWithDateFields();

$category = getParentTab($currentModule);

$smarty = new vtigerCRM_Smarty();

$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);

$Calendar4You = new Calendar4You();

$Calendar4You->GetDefPermission($current_user->id);
$Calendar4You->setgoogleaccessparams($current_user->id);
$Ch_Views = $Calendar4You->GetView();

if (count($Ch_Views) > 0) {
	$load_ch = true;
} else {
	$load_ch = false;
}

$Calendar_Settings = $Calendar4You->getSettings();
$smarty->assign('CALENDAR_SETTINGS', $Calendar_Settings);

$c_mod_strings = return_specified_module_language($current_language, 'Calendar');
$smarty->assign('CMOD', $c_mod_strings);

$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ID', '');
$smarty->assign('MODE', '');

// Left Panel Order
$Calendar_Panel_Order = array(
	'ActivityType',
	'ModulePanel',
	'AssignedUser',
	'ActivityStatus',
	'ActivityPriority',
);
$smarty->assign('Calendar_Panel_Order', $Calendar_Panel_Order);

$viewBox = 'hourview';
$smarty->assign('CREATE_PERMISSION', ($Calendar4You->CheckPermissions('CREATE') ? 'permitted' : ''));

//if($Calendar4You->CheckPermissions('EDIT')) {
	$smarty->assign('EDIT', ($Calendar4You->CheckPermissions('EDIT') ? 'permitted' : ''));
	$hour_startat = timeString(array('hour' => date('H:i', (time() + (5 * 60))), 'minute' => 0), '24');
	$hour_endat = timeString(array('hour'=>date('H:i', (time() + (60 * 60))),'minute'=>0), '24');
	$time_arr = getaddITSEventPopupTime($hour_startat, $hour_endat, $Calendar_Settings['hour_format']);

	$date = new DateTimeField(null);

	//To get date in user selected format
	$temp_date = $date->getDisplayDate();

if ($current_user->column_fields['is_admin']=='on') {
	$Res = $adb->pquery('select activitytype from vtiger_activitytype where activitytype!=?', array('Emails'));
} else {
	$roleid=$current_user->roleid;
	$subrole = getRoleSubordinates($roleid);
	if (count($subrole)> 0) {
		$roleids = $subrole;
		$roleids[] = $roleid;
	} else {
		$roleids = array($roleid);
	}
	$Res=$adb->pquery(
		'select distinct activitytype
			from vtiger_activitytype
			inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid
			where activitytype!=? and roleid in ('.generateQuestionMarks($roleids).') and picklistid in (select picklistid from vtiger_picklist)
			order by sortid asc',
		array('Emails',$roleids)
	);
}

	$eventlist='';
	$eventlists_array='';
	$abelist = $btnelist = '';
for ($i=0; $i<$adb->num_rows($Res); $i++) {
	$actname = $adb->query_result($Res, $i, 'activitytype');
	$actname = html_entity_decode($actname, ENT_QUOTES, $default_charset);
	$eventlist .= $actname.';';
	$eventlists_array .= '"'.html_entity_decode($actname, ENT_QUOTES, $default_charset).'",';
	$i18actname = getTranslatedString($actname, 'Calendar');
	$abelist.='<tr><td><a id="add'.strtolower($actname)
		.'" href="index.php?module=cbCalendar&action=EditView&return_module=Calendar4You&return_action=index&activity_mode=Events&activitytype='.$actname
		.'" class="drop_down">'.$i18actname.'</a></td></tr>';
	$btnelist .= '<tr><td><a id="btnadd'.strtolower($actname)
		.'" href="index.php?module=cbCalendar&action=EditView&return_module=Calendar4You&return_action=index&activity_mode=Events&activitytype='.$actname
		.'" class="drop_down">'.$i18actname.'</a></td></tr>';
}
	$smarty->assign('EVENTLIST', trim($eventlists_array, ','));
	$timeModluleDetails = array();
	$timeModules_array = '';
	$timeModules = getAllModulesWithDateTimeFields();
foreach ($timeModules as $tmid => $tmmod) {
	$tmline = getTranslatedString($tmmod, $tmmod);
	$tmlineid = str_replace(' ', '', $tmmod);
	$abelist .= '<tr><td><a href="" id="addmod' . strtolower($tmlineid) . '" class="drop_down">' . $tmline . '</a></td></tr>';
	$timeModluleDetails[$tmmod] = getModuleCalendarFields($tmmod);
	$timeModules_array.= '"'.html_entity_decode($tmmod, ENT_QUOTES, $default_charset).'",';
}
	$smarty->assign('ADD_BUTTONEVENTLIST', $abelist);
	$smarty->assign('ADD_ADDEVENTLIST', $btnelist);
	$add_javascript = "onMouseOver='fnAddITSEvent(this,\"addButtonDropDown\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\""
		.$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\""
		.$viewBox."\",\"".(isset($subtab) ? $subtab : '')."\",\"".$eventlist."\");'";
	$smarty->assign('ADD_ONMOUSEOVER', $add_javascript);
	$smarty->assign('TIMEMODULEARRAY', trim($timeModules_array, ','));
	$smarty->assign('TIMEMODULEDETAILS', json_encode($timeModluleDetails));
//}

//Sunday=0, Monday=1, Tuesday=2, etc.
$smarty->assign('FISRTDAY', $Calendar_Settings['number_dayoftheweek']);

if ($Calendar_Settings['hour_format'] == '24') {
	$is_24 = true;
} else {
	$is_24 = false;
}

$smarty->assign('IS_24', $is_24);

include_once 'modules/Calendar4You/class/color_converter.class.php';
include_once 'modules/Calendar4You/class/color_harmony.class.php';

$Event_Colors = $Calendar4You->getEventColors();
$colorHarmony = new colorHarmony();

$Task_Colors = getEColors('type', 'task');

$Task_Colors_Palete = $colorHarmony->Monochromatic($Task_Colors['bg']);

if (!$load_ch || !empty($Ch_Views['1']['task'])) {
	$task_checked = true;
} else {
	$task_checked = false;
}

$Activity_Types = $Module_Types = array();

$ActTypes = getActTypesForCalendar();
if (!$load_ch || !empty($Ch_Views['1']['invite'])) {
	$invite_checked = true;
} else {
	$invite_checked = false;
}

foreach ($ActTypes as $act_id => $act_name) {
	if (!$load_ch || !empty($Ch_Views['1'][$act_id])) {
		$event_checked = true;
	} else {
		$event_checked = false;
	}

	$Colors = getEColors('type', $act_id);
	$Colors_Palete = $colorHarmony->Monochromatic($Colors['bg']);

	$Activity_Types[$act_id] = array(
		'typename'=>html_entity_decode($act_name, ENT_QUOTES, $default_charset),
		'label'=>getTranslatedString(html_entity_decode($act_name, ENT_QUOTES, $default_charset), 'Calendar'),
		'act_type'=>'event',
		'title_color'=>$Colors_Palete[0],
		'color'=>$Colors_Palete[1],
		'textColor'=>$Colors['text'],
		'checked'=>$event_checked
	);
	//  add modules
	foreach ($tasklabel as $tbid => $mname) {
		$Modules_Colors = getEColors('type', $mname);
		$Module_Types[$mname] = array(
			'typename'=>$mname,
			'act_type'=>'task',
			'label'=>getTranslatedString($mname, $mname),
			'title_color'=>$Modules_Colors['text'],
			'color'=>$Modules_Colors['bg'],
			'textColor'=>$Modules_Colors['text'],
			'checked'=>(empty($Ch_Views['4'][$mname]) ? 'F' : 'T'),
		);
	}
	unset($Colors, $Colors_Palete);
}

$Invite_Colors = getEColors('type', 'invite');
$Invite_Colors_Palette = $colorHarmony->Monochromatic($Invite_Colors['bg']);

$Activity_Types['invite'] = array(
	'typename'=>'Invite',
	'act_type'=>'event',
	'label'=>$mod_strings['LBL_INVITE'],
	'title_color'=>$Invite_Colors_Palette[0],
	'color'=>$Invite_Colors_Palette[1],
	'textColor'=>$Invite_Colors['text'],
	'checked'=>$invite_checked
);

if (isset($_REQUEST['viewOption']) && $_REQUEST['viewOption']!= '') {
	$default_view = $_REQUEST['viewOption'];
} else {
	if ($current_user->activity_view == 'This Year') {
		$default_view = 'month';
	} elseif ($current_user->activity_view == 'This Month') {
		$default_view = 'month';
	} elseif ($current_user->activity_view == 'This Week') {
		$default_view = 'agendaWeek';
	} else {
		$default_view = 'agendaDay';
	}
}

$mysel = convertFullCalendarView($default_view);
$smarty->assign('DEFAULTVIEW', $default_view);
$smarty->assign('ACTIVITYTYPES', $Activity_Types);
$smarty->assign('MODULETYPES', $Module_Types);
$smarty->assign('upEVENTBLOCK_DISPLAY', isset($Ch_Views['6']) ? $Ch_Views['6'] : 'block');
$smarty->assign('upMODULEBLOCK_DISPLAY', isset($Ch_Views['7']) ? $Ch_Views['7'] : 'block');
$smarty->assign('upESTATUSBLOCK_DISPLAY', isset($Ch_Views['8']) ? $Ch_Views['8'] : 'block');
$smarty->assign('upTPRIORITYBLOCK_DISPLAY', isset($Ch_Views['9']) ? $Ch_Views['9'] : 'block');
if (isset($_REQUEST['user_view_type']) && $_REQUEST['user_view_type'] != '') {
	$user_view_type = $_REQUEST['user_view_type'];
} else {
	if ($Calendar_Settings['user_view']== 'all') {
		$user_view_type = 'all';
	} else {
		$user_view_type = $current_user->id;
	}
}

if (strtolower(trim($user_view_type)) == 'me') {
	$user_view_type = $current_user->id;
}

if ($user_view_type == $current_user->id) {
	$smarty->assign('SHOW_ONLY_ME', 'true');
}

$smarty->assign('USER_VIEW_TYPE', $user_view_type);

$Users = $Calendar4You->GetCalendarUsersData();

$smarty->assign('CALENDAR_USERS', $Users);
$smarty->assign('CURRENT_USER_ID', $current_user->id);

if (isset($tool_buttons)==false) {
	$tool_buttons = Button_Check('Calendar');
}

$smarty->assign('CHECK', $tool_buttons);

$calendar_arr = array();
$calendar_arr['IMAGE_PATH'] = $image_path;
$views = array('day','week','month','year');
if (empty($mysel) || !in_array($mysel, $views)) {
	if ($current_user->activity_view == 'This Year') {
		$mysel = 'year';
	} elseif ($current_user->activity_view == 'This Month') {
		$mysel = 'month';
	} elseif ($current_user->activity_view == 'This Week') {
		$mysel = 'week';
	} else {
		$mysel = 'day';
	}
}
$date_data = array();
if (isset($_REQUEST['day']) && is_numeric(vtlib_purify($_REQUEST['day']))) {
	$date_data['day'] = vtlib_purify($_REQUEST['day']);
}
if (isset($_REQUEST['month']) && is_numeric(vtlib_purify($_REQUEST['month']))) {
	$date_data['month'] = vtlib_purify($_REQUEST['month']);
	$date_data['fc_month'] = vtlib_purify($_REQUEST['month']) - 1;
}
if (isset($_REQUEST['week']) && is_numeric(vtlib_purify($_REQUEST['week']))) {
	$date_data['week'] = vtlib_purify($_REQUEST['week']);
}
if (isset($_REQUEST['year']) && is_numeric(vtlib_purify($_REQUEST['year']))) {
	if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970) {
		print("<font color='red'>".$app_strings['LBL_CAL_LIMIT_MSG'].'</font>');
		exit;
	}
	$date_data['year'] = vtlib_purify($_REQUEST['year']);
}

if (empty($date_data)) {
	$dateTimeField = new DateTimeField('');
	$dateValue = $dateTimeField->getDisplayDate();
	$timeValue = $dateTimeField->getDisplayTime();
	$dbDateValue = DateTimeField::convertToDBFormat($dateValue);
	$dateValueArray = explode('-', $dbDateValue);
	$timeValueArray = explode(':', $timeValue);
	$date_data = array(
		'day'=>$dateValueArray[2],
		'month'=>$dateValueArray[1],
		'fc_month'=>$dateValueArray[1] - 1,
		'year'=>$dateValueArray[0],
		'hour'=>$timeValueArray[0],
		'min'=>$timeValueArray[1],
	);
}
$smarty->assign('DATE_DATA', $date_data);

$calendar_arr['calendar'] = new Calendar($mysel, $date_data);

$add_to_url = 'view='.$calendar_arr['calendar']->view.$calendar_arr['calendar']->date_time->get_date_str();

$smarty->assign('CALENDAR_TO_URL', $add_to_url);

if (getFieldVisibilityPermission('Events', $current_user->id, 'eventstatus', 'readwrite') == '0') {
	$Events_Status = $Calendar4You->getActStatusFieldValues('eventstatus', 'vtiger_eventstatus');
}
$smarty->assign('EVENT_STATUS', $Events_Status);

if (getFieldVisibilityPermission('Calendar', $current_user->id, 'taskpriority', 'readwrite') == '0') {
	$Task_Priority = $Calendar4You->getActStatusFieldValues('taskpriority', 'vtiger_taskpriority');
} else {
	$Task_Priority = array();
}
$smarty->assign('TASK_PRIORITY', $Task_Priority);

$dat_fmt = $current_user->date_format;
if ($dat_fmt == '') {
	$dat_fmt = 'dd-mm-yyyy';
}
$smarty->assign('USER_LANGUAGE', substr($current_language, 0, 2));
$dat_fmt = str_replace('mm', 'MM', $dat_fmt);
$smarty->assign('USER_DATE_FORMAT', $dat_fmt);
$smarty->assign('Calendar_Slot_Minutes', '00:'.GlobalVariable::getVariable('Calendar_Slot_Minutes', 15).':00');
$smarty->assign('Calendar_Slot_Event_Overlap', (GlobalVariable::getVariable('Calendar_Slot_Event_Overlap', 1) ? 'true' : 'false'));
$smarty->assign('Calendar_Modules_Panel_Visible', GlobalVariable::getVariable('Calendar_Modules_Panel_Visible', 1));
$smarty->assign('Calendar_Priority_Panel_Visible', GlobalVariable::getVariable('Calendar_Priority_Panel_Visible', 1));
$smarty->assign('Calendar_Status_Panel_Visible', GlobalVariable::getVariable('Calendar_Status_Panel_Visible', 1));
$smarty->assign('Calendar_Other_Default_Duration', GlobalVariable::getVariable('calendar_other_default_duration', 1));
$smarty->assign('Calendar_Show_WeekNumber', GlobalVariable::getVariable('Calendar_Show_WeekNumber', 0) ? 'true' : 'false');

$smarty->display('modules/Calendar4You/CalendarView.tpl');
