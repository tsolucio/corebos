<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
global $app_strings, $currentModule,$image_path,$theme,$adb, $current_user;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once("data/Tracker.php");
require_once('modules/Vtiger/layout_utils.php');
require_once('include/utils/utils.php');

$log = LoggerManager::getLogger('Activity_Reminder');

$cbaction = vtlib_purify($_REQUEST['cbaction']);
$cbmodule = vtlib_purify($_REQUEST['cbmodule']);
$cbrecord = vtlib_purify($_REQUEST['cbrecord']);

if($cbaction == 'POSTPONE') {
	if(isset($cbmodule) && isset($cbrecord)) {
		$reminderid = $_REQUEST['cbreminderid'];
		if(!empty($reminderid) ) {
			unset($_SESSION['next_reminder_time']);
			$reminder_query = "UPDATE vtiger_activity_reminder_popup set status = 0 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
			$adb->pquery($reminder_query, array($reminderid, $cbmodule, $cbrecord));
			echo ":#:SUCCESS";
		} else {
			echo ":#:FAILURE";
		}
	}
}
?>
