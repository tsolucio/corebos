<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
ini_set('max_execution_time','1800');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");
require('include/tcpdf/tcpdf.php');
$language = $_SESSION['authenticated_user_language'].'.lang.php';
require_once("include/language/$language");
$reportid = vtlib_purify($_REQUEST["record"]);
$oReportRun = new ReportRun($reportid);

if(!empty($_REQUEST['startdate']) && !empty($_REQUEST['enddate']) && $_REQUEST['startdate'] != "0000-00-00" && $_REQUEST['enddate'] != "0000-00-00" ) {
	$filter = $_REQUEST['stdDateFilter'];
	$filtercolumn = $_REQUEST['stdDateFilterField'];
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

$pdf = $oReportRun->getReportPDF($filterlist);
$pdf->Output('Reports.pdf','D');

exit();
?>
