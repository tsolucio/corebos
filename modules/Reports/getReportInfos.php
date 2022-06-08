<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Reports/Reports.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

global $current_user,$adb;
$result = $adb->pquery('SELECT reportid, reportname FROM vtiger_report', array());
$num_rows = $adb->num_rows($result);
$report_name_arr = array();
for ($x=0; $x<$num_rows; $x++) {
	$reportId = $adb->query_result($result, $x, 'reportid');
	$reportName = $adb->query_result($result, $x, 'reportname');
	$reportInfo = array(
		'reptid' => $reportId,
		'reptname' => $reportName
	);
	array_push($report_name_arr, $reportInfo);
}
$response['result']  = $report_name_arr;
echo json_encode($response);
?>
