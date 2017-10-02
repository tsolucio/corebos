<?php
 /*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the GNU General Public License (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://www.gnu.org/licenses/>
 *************************************************************************************************
 *  Module       : Reports
 *  Author       : Opencubed
 *************************************************************************************************/
global $php_max_execution_time;
set_time_limit($php_max_execution_time);

require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

global $tmp_dir, $root_directory;
$fname = tempnam($root_directory.$tmp_dir, "merge2.csv");

# Write out the data
$reportid = vtlib_purify($_REQUEST["record"]);
$oReportRun = new ReportRun($reportid);
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
$oReportRun->writeReportToCSVFile($fname, $filterlist);

if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
{
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
header("Content-Type: application/csv;charset=utf-8");
header("Content-Length: ".@filesize($fname));
header('Content-disposition: attachment; filename="Reports.csv"');
$fh=fopen($fname, "rb");
fpassthru($fh);
exit();
?>