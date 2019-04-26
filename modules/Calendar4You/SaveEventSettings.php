<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
global $adb,$current_user;

$user_view_type = $_REQUEST['user_view_type'];

$mode = $_REQUEST['mode'];
$entity = $_REQUEST['id'];

$adb->pquery('delete from its4you_calendar4you_colors where userid=? and mode=? and entity=?', array($current_user->id,$mode,$entity));

$Save_Colors = array('bg'=>'event_color_bg','text'=>'event_color_text');

$sql = 'insert into its4you_calendar4you_colors (userid, mode, type, entity, color) values (?,?,?,?,?)';
foreach ($Save_Colors as $type => $color) {
	$color_val = vtlib_purify($_REQUEST[$color]);
	$adb->pquery($sql, array($current_user->id, $mode, $type, $entity, $color_val));
}

$Save_Data = array();
$save_fields = $_REQUEST['save_fields'];

if ($save_fields == '1') {
	$event = $entity;
	$userid = $current_user->id;

	$adb->pquery('DELETE FROM its4you_calendar4you_event_fields WHERE userid =? AND event = ?', array($userid,$event));

	$Views = array('day','week','month');

	foreach ($Views as $view) {
		$Save_Data[] = array($userid,$event,'1',$view,$_REQUEST[$view.'_showed_field']);

		$Selected_Fields = explode(';', $_REQUEST[$view.'_selected_fields']);

		if (count($Selected_Fields) > 0) {
			foreach ($Selected_Fields as $fieldname) {
				//userid, event, type, view, fieldname
				if (trim($fieldname) != '') {
					$Save_Data[] = array($userid,$event,'2',$view,$fieldname);
				}
			}
		}
		unset($Selected_Fields);
	}
	$sql_i = 'INSERT INTO its4you_calendar4you_event_fields (userid, event, type, view, fieldname) VALUES (?,?,?,?,?)';
	foreach ($Save_Data as $Data) {
		$adb->pquery($sql_i, $Data);
	}
}

$savegooglesync = vtlib_purify($_REQUEST['savegooglesync']);

if ($savegooglesync == '1') {
	$adb->pquery('DELETE FROM its4you_googlesync4you_dis WHERE userid = ? AND event = ?', array($current_user->id,$entity));

	$sql2 = 'INSERT INTO its4you_googlesync4you_dis (userid, event, type) VALUES (?,?,?)';
	$export_to_calendar = vtlib_purify($_REQUEST['export_to_calendar']);
	$import_from_calendar = vtlib_purify($_REQUEST['import_from_calendar']);

	if ($export_to_calendar != '1') {
		$adb->pquery($sql2, array($current_user->id,$entity,1));
	}

	if ($import_from_calendar != '1') {
		$adb->pquery($sql2, array($current_user->id,$entity,2));
	}

	$adb->pquery('DELETE FROM its4you_googlesync4you_calendar WHERE userid = ? AND event = ?', array($current_user->id,$entity));

	$to_calendar = vtlib_purify($_REQUEST['selected_calendar']);

	if ($to_calendar != '') {
		$adb->pquery('INSERT INTO its4you_googlesync4you_calendar (userid, event, calendar, type) VALUES (?,?,?,?)', array($current_user->id,$entity,$to_calendar,'1'));
	}
}
$url = 'Location: index.php?action=index&module=Calendar4You&viewOption='.vtlib_purify($_REQUEST['view']).'&hour='.vtlib_purify($_REQUEST['hour'])
	.'&day='.vtlib_purify($_REQUEST['day']).'&month='.vtlib_purify($_REQUEST['month']).'&year='.vtlib_purify($_REQUEST['year'])
	.'&user_view_type='.vtlib_purify($_REQUEST['user_view_type']);
header($url);
?>