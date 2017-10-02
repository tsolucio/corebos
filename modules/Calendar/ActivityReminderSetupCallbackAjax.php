<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('include/utils/utils.php');
global $app_strings, $currentModule,$adb, $current_user;

$log = LoggerManager::getLogger('Activity_Reminder');

$cbaction = isset($_REQUEST['cbaction']) ? vtlib_purify($_REQUEST['cbaction']) : '';
$cbmodule = isset($_REQUEST['cbmodule']) ? vtlib_purify($_REQUEST['cbmodule']) : '';
$cbrecord = isset($_REQUEST['cbrecord']) ? vtlib_purify($_REQUEST['cbrecord']) : '';

if($cbaction == 'POSTPONE') {
	if(!empty($cbmodule) && !empty($cbrecord)) {
		$reminderid = isset($_REQUEST['cbreminderid']) ? vtlib_purify($_REQUEST['cbreminderid']) : '';
		if(!empty($reminderid) ) {
			coreBOS_Session::delete('next_reminder_time');
			$reminder_query = "UPDATE vtiger_activity_reminder_popup set status = 0 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
			$adb->pquery($reminder_query, array($reminderid, $cbmodule, $cbrecord));
			echo ":#:SUCCESS";
		} else {
			echo ":#:FAILURE";
		}
	}
}
?>
