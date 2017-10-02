<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
global $php_max_execution_time, $tmp_dir, $root_directory;
set_time_limit($php_max_execution_time);

require_once('modules/Reports/ReportRun.php');
require_once('modules/Reports/Reports.php');

$fname = tempnam($root_directory.$tmp_dir, 'merge2.xls');

# Write out the data
$reportid = vtlib_purify($_REQUEST['record']);
$oReportRun = new ReportRun($reportid);
if(!empty($_REQUEST['startdate']) && !empty($_REQUEST['enddate']) && $_REQUEST['startdate'] != '0000-00-00' && $_REQUEST['enddate'] != '0000-00-00') {
	$filtercolumn = $_REQUEST['stdDateFilterField'];
	$filter = $_REQUEST['stdDateFilter'];
	$date = new DateTimeField($_REQUEST['startdate']);
	$endDate = new DateTimeField($_REQUEST['enddate']);
	$startdate = $date->getDBInsertDateValue();//Convert the user date format to DB date format
	$enddate = $endDate->getDBInsertDateValue();//Convert the user date format to DB date format
	$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);
} else {
	if (empty($_REQUEST['advft_criteria'])) {
		$advft_criteria = '';
	} else {
		$advft_criteria = $_REQUEST['advft_criteria'];
		$advft_criteria = json_decode($advft_criteria,true);
	}
	if (empty($_REQUEST['advft_criteria_groups'])) {
		$advft_criteria_groups = '';
	} else {
		$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
		$advft_criteria_groups = json_decode($advft_criteria_groups,true);
	}
	$filterlist = $oReportRun->RunTimeAdvFilter($advft_criteria,$advft_criteria_groups);
}

$oReportRun->writeReportToExcelFile($fname, $filterlist);

if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
{
	header('Pragma: public');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
}
header('Content-Type: application/x-msexcel');
header('Content-Length: '.@filesize($fname));
header('Content-disposition: attachment; filename="Reports.xls"');
$fh=fopen($fname, 'rb');
fpassthru($fh);
//unlink($fname);
exit();
?>
