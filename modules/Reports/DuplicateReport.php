<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Reports/Reports.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

global $adb,$mod_strings,$app_strings;

$reportid = vtlib_purify($_REQUEST['record']);
$newreportname = vtlib_purify($_REQUEST['newreportname']);
$newreportdescription = vtlib_purify($_REQUEST['newreportdescription']);
$newreportfolder = vtlib_purify($_REQUEST['newreportfolder']);

$sql = 'select * from vtiger_report where reportid=?';
$res = $adb->pquery($sql, array($reportid));
$Report_ID = $adb->query_result($res, 0, 'reportid');
$numOfRows = $adb->num_rows($res);

$response_array = array();

if ($numOfRows > 0) {
	global $current_user;

	$ogReport = new Reports($reportid);
	$primarymodule = $ogReport->primodule;
	$restrictedmodules = array();
	if ($ogReport->secmodule!='') {
		$rep_modules = explode(":", $ogReport->secmodule);
	} else {
		$rep_modules = array();
	}

	$rep_modules[] = $primarymodule;
	$modules_permitted = true;
	foreach ($rep_modules as $mod) {
		if (isPermitted($mod, 'index')!= "yes" || vtlib_isModuleActive($mod)==false) {
			$modules_permitted = false;
			$restrictedmodules[] = $mod;
		}
	}

	if (isPermitted($primarymodule, 'index') == "yes" && $modules_permitted == true) {
		$genQueryId = $adb->getUniqueID("vtiger_selectquery");
		if ($genQueryId != "") {
			$response_array['reportid'] = $genQueryId;
			$response_array['folderid'] = $newreportfolder;
			$response_array['errormessage'] = '';

			$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
			$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
			$log->debug("Reports :: Save->Successfully saved vtiger_selectquery");

			if ($iquerysqlresult != false) {
				$adb->pquery("INSERT INTO vtiger_selectcolumn (queryid,columnindex,columnname)
						SELECT $genQueryId, columnindex, columnname FROM vtiger_selectcolumn WHERE queryid = ?", array($reportid));

				$adb->pquery("INSERT INTO vtiger_reportsharing (reportid,shareid,setype)
						SELECT $genQueryId,shareid,setype FROM vtiger_reportsharing WHERE reportid=?", array($reportid));

				$owner = $current_user->id;
				$secsql = $adb->convert2Sql('?,?,?', array($newreportfolder, $newreportname, $newreportdescription));
				$ireportresult = $adb->pquery(
					"INSERT INTO vtiger_report (reportid,folderid,reportname,description,reporttype,queryid,state,owner,sharingtype,moreinfo,cbreporttype)
						SELECT $genQueryId,$secsql,reporttype,$genQueryId,state,$owner,sharingtype,moreinfo,cbreporttype
						FROM vtiger_report WHERE reportid=?",
					array($reportid)
				);
				if ($ireportresult != false) {
					$log->debug('Reports :: Save->Successfully saved report');
					$adb->pquery("INSERT INTO vtiger_reportmodules (reportmodulesid,primarymodule,secondarymodules)
							SELECT $genQueryId,primarymodule,secondarymodules FROM vtiger_reportmodules WHERE reportmodulesid=?", array($reportid));
					$log->debug("Reports :: Save->Successfully saved vtiger_reportmodules");

					$adb->pquery("INSERT INTO vtiger_reportsortcol (sortcolid,reportid,columnname,sortorder)
							SELECT sortcolid,$genQueryId,columnname,sortorder FROM vtiger_reportsortcol WHERE reportid=?", array($reportid));
					$log->debug("Reports :: Save->Successfully saved vtiger_reportsortcol");

					$adb->pquery("INSERT INTO vtiger_reportdatefilter (datefilterid,datecolumnname,datefilter,startdate,enddate)
							SELECT $genQueryId,datecolumnname,datefilter,startdate,enddate FROM vtiger_reportdatefilter WHERE datefilterid=?", array($reportid));
					$log->debug("Reports :: Save->Successfully saved vtiger_reportdatefilter");

					$adb->pquery("INSERT INTO vtiger_reportsummary (reportsummaryid,summarytype,columnname)
							SELECT $genQueryId,summarytype,columnname FROM vtiger_reportsummary WHERE reportsummaryid=?", array($reportid));
					$log->debug("Reports :: Save->Successfully saved vtiger_reportsummary");

					$adb->pquery(
						"INSERT INTO vtiger_relcriteria (queryid,columnindex,columnname,comparator,value,groupid,column_condition)
							SELECT $genQueryId,columnindex,columnname,comparator,value,groupid,column_condition FROM vtiger_relcriteria WHERE queryid=?",
						array($reportid)
					);
					$log->debug('Reports :: Save->Successfully saved vtiger_relcriteria');

					$adb->pquery("INSERT INTO vtiger_relcriteria_grouping (groupid,queryid,group_condition,condition_expression)
							SELECT groupid,$genQueryId,group_condition,condition_expression FROM vtiger_relcriteria_grouping WHERE queryid=?", array($reportid));
					$log->debug('Reports :: Save->Successfully saved vtiger_relcriteria_grouping');

					$advft_criteria = $_REQUEST['advft_criteria'];
					$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
					if (!empty($advft_criteria) && !empty($advft_criteria_groups)) {
						$advft_criteria = json_decode($advft_criteria, true);
						$advft_criteria_groups = json_decode($advft_criteria_groups, true);
						updateAdvancedCriteria($genQueryId, $advft_criteria, $advft_criteria_groups);
					}
				} else {
					$log->debug('Reports :: Save->ERROR saving report info');
					$response_array['errormessage'] = $mod_strings['ERR_CREATE_REPORT'];
				}
			} else {
				$log->debug('Reports :: Save->ERROR saving report query');
				$response_array['errormessage'] = $mod_strings['ERR_CREATE_REPORT'];
			}
		}
	} else {
		$errormessage = "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		$errormessage .= "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_ACCESS']." : ".
		implode(",", $restrictedmodules)." </span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
		$response_array['errormessage'] = $errormessage;
	}
} else {
		$errormessage = "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
		$errormessage .= "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		$errormessage .= "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204,204,204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_REPORT_DELETED']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
		$response_array['errormessage'] = $errormessage;
}

echo json_encode($response_array);
?>