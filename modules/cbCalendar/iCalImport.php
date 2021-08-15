<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
define('_BENNU_VERSION', '0.1');
require_once 'Smarty_setup.php';
include 'modules/cbCalendar/iCal/iCalendar_rfc2445.php';
include 'modules/cbCalendar/iCal/iCalendar_components.php';
include 'modules/cbCalendar/iCal/iCalendar_properties.php';
include 'modules/cbCalendar/iCal/iCalendar_parameters.php';
include 'modules/cbCalendar/iCal/ical-parser-class.php';
require_once 'modules/cbCalendar/iCalLastImport.php';

require_once 'include/utils/utils.php';
require_once 'data/CRMEntity.php';

global $import_dir,$current_user,$mod_strings,$app_strings,$currentModule;
$theme_path = 'themes/'.$theme.'/';
$image_path = $theme_path.'images/';

if (empty($_REQUEST['step']) || $_REQUEST['step']!='undo') {
	$last_import = new iCalLastImport();
	$last_import->clearRecords($current_user->id);
	$file_details = $_FILES['ics_file'];
	$binFile = 'vtiger_import'.date('YmdHis');
	$file = $import_dir.''.$binFile;
	$filetmp_name = $file_details['tmp_name'];
	$upload_status = move_uploaded_file($filetmp_name, $file);

	$module = 'cbCalendar';
	$calendar = CRMEntity::getInstance($module);
	$calendar->initRequiredFields($module);
	$required_fields = array_keys($calendar->required_fields);
	$ical = new iCal();
	$ical_activities = $ical->iCalReader($binFile);

	$cnt = $skip_count = 0;
	for ($i=0; $i<count($ical_activities); $i++) {
		$activity = new iCalendar_event;
		$cnt++;
		$calendar->column_fields = $activity->generateArray($ical_activities[$i]);
		$calendar->column_fields['assigned_user_id'] = $current_user->id;
		$calendar->column_fields['followupdt']='';
		$calendar->column_fields['rel_id']='';
		$calendar->column_fields['cto_id']='';
		$skip_record = false;
		foreach ($required_fields as $key) {
			if (empty($calendar->column_fields[$key])) {
				$skip_count++;
				$skip_record = true;
				break;
			}
		}
		if ($skip_record === true) {
			continue;
		}
		$calendar->save($module);
		$last_import = new iCalLastImport();
		$last_import->setFields(array('userid' => $current_user->id, 'entitytype' => $module, 'crmid' => $calendar->id));
		$last_import->save();
		if (!empty($ical_activities[$i]['VALARM']) && !empty($calendar->column_fields['reminder_time'])) {
			$calendar->activity_reminder($calendar->id, $calendar->column_fields['reminder_time']);
		}
	}
	unlink($file);
	$smarty = new vtigerCRM_Smarty;

	if (!isset($tool_buttons)) {
		$tool_buttons = Button_Check('cbCalendar');
	}
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->assign('UNDO', '');
	$smarty->assign("MOD", $mod_strings);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH", $image_path);
	$smarty->assign('MODULE', 'cbCalendar');
	$smarty->assign('SINGLE_MOD', 'SINGLE_cbCalendar');
	$smarty->display("Buttons_List.tpl");
	$imported_tasks = $cnt - $skip_count;
	$message= "<b>".$mod_strings['LBL_SUCCESS']."</b>"
		."<br><br>" .$mod_strings['LBL_SUCCESS_CALENDAR_1']."  $imported_tasks"
		."<br><br>" .$mod_strings['LBL_SKIPPED_CALENDAR_1'].$skip_count
		."<br><br>";

	$smarty->assign("MESSAGE", $message);
	$smarty->assign("RETURN_MODULE", $currentModule);
	$smarty->assign("RETURN_ACTION", 'ListView');
	$smarty->assign("MODULE", $currentModule);
	$smarty->assign("MODULENAME", $currentModule);
	$smarty->display("iCalImport.tpl");
} else {
	$smarty = new vtigerCRM_Smarty;

	if (!isset($tool_buttons)) {
		$tool_buttons = Button_Check('cbCalendar');
	}
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->assign("MOD", $mod_strings);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH", $image_path);
	$smarty->assign('MODULE', 'cbCalendar');
	$smarty->assign('SINGLE_MOD', 'SINGLE_cbCalendar');
	$smarty->display("Buttons_List.tpl");

	$last_import = new iCalLastImport();
	$ret_value = $last_import->undo('cbCalendar', $current_user->id);

	if (!empty($ret_value)) {
		$message= "<b>".$mod_strings['LBL_SUCCESS'].'</b><br><br>' .$mod_strings['LBL_LAST_IMPORT_UNDONE']." ";
	} else {
		$message= "<b>".$mod_strings['LBL_FAILURE'].'</b><br><br>' .$mod_strings['LBL_NO_IMPORT_TO_UNDO']." ";
	}

	$smarty->assign("MESSAGE", $message);
	$smarty->assign("UNDO", 'yes');
	$smarty->assign("RETURN_MODULE", $currentModule);
	$smarty->assign("RETURN_ACTION", 'ListView');
	$smarty->assign("MODULE", $currentModule);
	$smarty->assign("MODULENAME", $currentModule);
	$smarty->display("iCalImport.tpl");
}
?>