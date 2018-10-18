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
require_once 'modules/Reports/ReportUtils.php';
require_once 'modules/Reports/CustomReportUtils.php';

global $adb, $log,$current_user;
$reportid = vtlib_purify($_REQUEST["record"]);

//<<<<<<<selectcolumn>>>>>>>>>
$selectedcolumnstring = (isset($_REQUEST['selectedColumnsString']) ? $_REQUEST['selectedColumnsString'] : '');
//<<<<<<<selectcolumn>>>>>>>>>

//<<<<<<<reportsortcol>>>>>>>>>
$sort_by1 = isset($_REQUEST['Group1']) ? decode_html(vtlib_purify($_REQUEST['Group1'])) : '';
$sort_order1 = isset($_REQUEST['Sort1']) ? vtlib_purify($_REQUEST['Sort1']) : '';
$sort_by2 = isset($_REQUEST['Group2']) ? decode_html(vtlib_purify($_REQUEST['Group2'])) : '';
$sort_order2 = isset($_REQUEST['Sort2']) ? vtlib_purify($_REQUEST['Sort2']) : '';
$sort_by3 = isset($_REQUEST['Group3']) ? decode_html(vtlib_purify($_REQUEST['Group3'])) : '';
$sort_order3 = isset($_REQUEST['Sort3']) ? vtlib_purify($_REQUEST['Sort3']) : '';

//<<<<<<<reportgrouptime>>>>>>>
$groupTime1 = isset($_REQUEST['groupbytime1']) ? vtlib_purify($_REQUEST['groupbytime1']) : '';
$groupTime2 = isset($_REQUEST['groupbytime2']) ? vtlib_purify($_REQUEST['groupbytime2']) : '';
$groupTime3 = isset($_REQUEST['groupbytime3']) ? vtlib_purify($_REQUEST['groupbytime3']) : '';
//<<<<<<<reportgrouptime>>>>>>>

//<<<<<<<reportsortcol>>>>>>>>>
$selectedcolumns = explode(";", $selectedcolumnstring);
if (!in_array($sort_by1, $selectedcolumns)) {
	$selectedcolumns[] = $sort_by1;
}
if (!in_array($sort_by2, $selectedcolumns)) {
	$selectedcolumns[] = $sort_by2;
}
if (!in_array($sort_by3, $selectedcolumns)) {
	$selectedcolumns[] = $sort_by3;
}
//<<<<<<<reportmodules>>>>>>>>>
$pmodule = (isset($_REQUEST['primarymodule']) ? vtlib_purify($_REQUEST['primarymodule']) : '');
$smodule = (isset($_REQUEST['secondarymodule']) ? vtlib_purify($_REQUEST['secondarymodule']) : '');
//<<<<<<<reportmodules>>>>>>>>>

//<<<<<<<report>>>>>>>>>
$reportname = (isset($_REQUEST['reportName']) ? vtlib_purify($_REQUEST['reportName']) : '');
$reportdescription = (isset($_REQUEST['reportDesc']) ? vtlib_purify($_REQUEST['reportDesc']) : '');
$cbreporttype = (!empty($_REQUEST['cbreporttype']) ? vtlib_purify($_REQUEST['cbreporttype']) : '');
$reporttype = (isset($_REQUEST['reportType']) ? vtlib_purify($_REQUEST['reportType']) : 'tabular');
$folderid = (!empty($_REQUEST['folder']) ? vtlib_purify($_REQUEST['folder']) : !empty($_REQUEST['reportfolder']) ? vtlib_purify($_REQUEST['reportfolder']) : 1);
//<<<<<<<report>>>>>>>>>

//<<<<<<<standarfilters>>>>>>>>>
$stdDateFilterField = (isset($_REQUEST['stdDateFilterField']) ? vtlib_purify($_REQUEST['stdDateFilterField']) : '');
$stdDateFilter = (isset($_REQUEST['stdDateFilter']) ? vtlib_purify($_REQUEST['stdDateFilter']) : '');
$startdate = (isset($_REQUEST['startdate']) ? vtlib_purify($_REQUEST['startdate']) : '');
$enddate = (isset($_REQUEST['enddate']) ? vtlib_purify($_REQUEST['enddate']) : '');
$dbCurrentDateTime = new DateTimeField(date('Y-m-d H:i:s'));
if (!empty($startdate)) {
	$startDateTime = new DateTimeField($startdate.' '. $dbCurrentDateTime->getDisplayTime());
	$startdate = $startDateTime->getDBInsertDateValue();
}
if (!empty($enddate)) {
	$endDateTime = new DateTimeField($enddate.' '. $dbCurrentDateTime->getDisplayTime());
	$enddate = $endDateTime->getDBInsertDateValue();
}
//<<<<<<<standardfilters>>>>>>>>>

//<<<<<<<shared entities>>>>>>>>>
$sharetype = (isset($_REQUEST['stdtypeFilter']) ? vtlib_purify($_REQUEST['stdtypeFilter']) : '');
$shared_entities = (isset($_REQUEST['selectedColumnsStr']) ? vtlib_purify($_REQUEST['selectedColumnsStr']) : '');
//<<<<<<<shared entities>>>>>>>>>

//<<<<<<<columnstototal>>>>>>>>>>
$columnstototal = array();
$allKeys = array_keys($_REQUEST);
for ($i=0; $i<count($allKeys); $i++) {
	$string = substr($allKeys[$i], 0, 3);
	if ($string == 'cb:') {
		$columnstototal[] = $allKeys[$i];
	}
}
//<<<<<<<columnstototal>>>>>>>>>

//<<<<<<<advancedfilter>>>>>>>>
$advft_criteria = !empty($_REQUEST['advft_criteria']) ? $_REQUEST['advft_criteria'] : '[]';
$advft_criteria = json_decode($advft_criteria, true);

$advft_criteria_groups = !empty($_REQUEST['advft_criteria_groups']) ? $_REQUEST['advft_criteria_groups'] : '[]';
$advft_criteria_groups = json_decode($advft_criteria_groups, true);
//<<<<<<<advancedfilter>>>>>>>>

//<<<<<<<scheduled report>>>>>>>>
$isReportScheduled  = isset($_REQUEST['isReportScheduled']) ? vtlib_purify($_REQUEST['isReportScheduled']) : '';
$selectedRecipients = isset($_REQUEST['selectedRecipientsString']) ? vtlib_purify($_REQUEST['selectedRecipientsString']) : '';
$scheduledFormat    = isset($_REQUEST['scheduledReportFormat']) ? vtlib_purify($_REQUEST['scheduledReportFormat']) : '';
$scheduledInterval  = isset($_REQUEST['scheduledIntervalString']) ? vtlib_purify($_REQUEST['scheduledIntervalString']) : '';
//<<<<<<<scheduled report>>>>>>>>

$newreportname=vtlib_purify($_REQUEST['newreportname']);
if ($reportid == '' || ($reportid!='' && isset($_REQUEST['saveashidden']) && $_REQUEST['saveashidden'] == 'saveas' && $newreportname!='')) {
	if ($reportid!='' && isset($_REQUEST['saveashidden']) && $_REQUEST['saveashidden'] == 'saveas' && $newreportname!='') {
		$reportdetails=$adb->pquery(
			'select *
				from vtiger_report as report
				join vtiger_reportmodules as repmodules on report.reportid=repmodules.reportmodulesid
				join vtiger_selectcolumn as sc on sc.queryid=report.reportid
				where reportid=?',
			array($reportid)
		);
		$folderid=$adb->query_result($reportdetails, 0, 'folderid');
		$reporttype=$adb->query_result($reportdetails, 0, 'reporttype');
		$cbreporttype=$adb->query_result($reportdetails, 0, 'cbreporttype');
		$_REQUEST['cbreporttype'] = $cbreporttype;
		$sharetype=$adb->query_result($reportdetails, 0, 'sharingtype');
		$reportdescription=$adb->query_result($reportdetails, 0, 'description');
		$pmodule = $adb->query_result($reportdetails, 0, 'primarymodule');
		$smodule = $adb->query_result($reportdetails, 0, 'secondarymodules');
		for ($in=0; $in < $adb->num_rows($reportdetails); $in++) {
			$selectedcolumns[] = $adb->query_result($reportdetails, $in, 'columnname');
		}
	}
	$pivotcolumns = '';
	$genQueryId = $adb->getUniqueID("vtiger_selectquery");
	if ($genQueryId != "") {
		$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
		$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
		$log->info("Reports :: Save->Successfully saved vtiger_selectquery");
		if ($iquerysqlresult!=false) {
			//<<<<step2 vtiger_selectcolumn>>>>>>>>
			if (!empty($selectedcolumns)) {
				$pcols = array();
				for ($i=0; $i<count($selectedcolumns); $i++) {
					if (!empty($selectedcolumns[$i]) && $selectedcolumns[$i]!='none') {
						$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
						$icolumnsqlresult = $adb->pquery($icolumnsql, array($genQueryId,$i,(decode_html($selectedcolumns[$i]))));
						$colinfo = explode(':', $selectedcolumns[$i]);
						$pcols[] = $colinfo[0].'.'.$colinfo[1].' as '.$colinfo[2];
					}
				}
				$pivotcolumns = implode(',', $pcols);
			}
			if ($shared_entities != "") {
				if ($sharetype == "Shared") {
					$selectedcolumn = explode(";", $shared_entities);
					for ($i=0; $i< count($selectedcolumn) -1; $i++) {
						$temp = explode("::", $selectedcolumn[$i]);
						$icolumnsql = "insert into vtiger_reportsharing (reportid,shareid,setype) values (?,?,?)";
						$icolumnsqlresult = $adb->pquery($icolumnsql, array($genQueryId,$temp[1],$temp[0]));
					}
				}
			}
			$log->info("Reports :: Save->Successfully saved vtiger_selectcolumn");
			//<<<<step2 vtiger_selectcolumn>>>>>>>>

			if ($genQueryId != "") {
				if ($reportid!='') {
					$reportname=$newreportname;
				}
				list($cbreporttype,$minfo) = report_getMoreInfoFromRequest($cbreporttype, $pmodule, $smodule, $pivotcolumns);
				if (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype'] != 'corebos') {
					$reporttype='';
				}
				$ireportsql = 'insert into vtiger_report (REPORTID,FOLDERID,REPORTNAME,DESCRIPTION,REPORTTYPE,QUERYID,STATE,OWNER,SHARINGTYPE,moreinfo,cbreporttype) values (?,?,?,?,?,?,?,?,?,?,?)';
				$ireportparams = array($genQueryId, $folderid, $reportname, $reportdescription, $reporttype, $genQueryId, 'CUSTOM', $current_user->id, $sharetype, $minfo, $cbreporttype);
				$ireportresult = $adb->pquery($ireportsql, $ireportparams);
				$log->info('Reports :: Save->Successfully saved vtiger_report');
				if ($ireportresult!=false) {
					//<<<<reportmodules>>>>>>>
					$ireportmodulesql = 'insert into vtiger_reportmodules (REPORTMODULESID,PRIMARYMODULE,SECONDARYMODULES) values (?,?,?)';
					$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($genQueryId, $pmodule, $smodule));
					$log->info('Reports :: Save->Successfully saved vtiger_reportmodules');
					//<<<<reportmodules>>>>>>>

					//<<<<step3 vtiger_reportsortcol>>>>>>>
					if ($sort_by1 != '') {
						$sort_by1sql = 'insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)';
						$sort_by1result = $adb->pquery($sort_by1sql, array(1, $genQueryId, $sort_by1, $sort_order1));
						if (CustomReportUtils::isDateField($sort_by1)) {
							$groupByTime1Sql = 'INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)';
							$groupByTime1Res = $adb->pquery($groupByTime1Sql, array($genQueryId,1,$sort_by1,$groupTime1));
						}
					}
					if ($sort_by2 != '') {
						$sort_by2sql = 'insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)';
						$sort_by2result = $adb->pquery($sort_by2sql, array(2,$genQueryId,$sort_by2,$sort_order2));
						if (CustomReportUtils::isDateField($sort_by2)) {
							$groupByTime2Sql = 'INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)';
							$groupByTime2Res = $adb->pquery($groupByTime2Sql, array($genQueryId,2,$sort_by2,$groupTime2));
						}
					}
					if ($sort_by3 != '') {
						$sort_by3sql = 'insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)';
						$sort_by3result = $adb->pquery($sort_by3sql, array(3,$genQueryId,$sort_by3,$sort_order3));
						if (CustomReportUtils::isDateField($sort_by3)) {
							$groupByTime3Sql = 'INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)';
							$groupByTime3Res = $adb->pquery($groupByTime3Sql, array($genQueryId,3,$sort_by3,$groupTime3));
						}
					}
					$log->info('Reports :: Save->Successfully saved vtiger_reportsortcol');
					//<<<<step3 vtiger_reportsortcol>>>>>>>

					//<<<<step5 standarfilder>>>>>>>
					$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
					$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($genQueryId, $stdDateFilterField, $stdDateFilter, $startdate, $enddate));
					$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
					//<<<<step5 standarfilder>>>>>>>

					//<<<<step4 columnstototal>>>>>>>
					for ($i=0; $i<count($columnstototal); $i++) {
						$ireportsummarysql = "insert into vtiger_reportsummary (REPORTSUMMARYID,SUMMARYTYPE,COLUMNNAME) values (?,?,?)";
						$ireportsummaryresult = $adb->pquery($ireportsummarysql, array($genQueryId, $i, $columnstototal[$i]));
					}
					$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
					//<<<<step4 columnstototal>>>>>>>

					//<<<<step5 advancedfilter>>>>>>>
					updateAdvancedCriteria($genQueryId, $advft_criteria, $advft_criteria_groups);
					//<<<<step7 scheduledReport>>>>>>>
					if ($isReportScheduled == 'on' || $isReportScheduled == '1') {
						$scheduleReportSql = 'INSERT INTO vtiger_scheduled_reports (reportid,recipients,schedule,format,next_trigger_time) VALUES (?,?,?,?,?)';
						$adb->pquery($scheduleReportSql, array($genQueryId,$selectedRecipients,$scheduledInterval,$scheduledFormat,date("Y-m-d H:i:s")));
					}
					//<<<<step7 scheduledReport>>>>>>>
				} else {
					$errormessage = "<font color='red'><B>Error Message<ul>
						<li><font color='red'>Error while inserting the record</font>
						</ul></B></font> <br>" ;
					echo $errormessage;
					die;
				}
			}
		} else {
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			die;
		}

		echo '<script>
				window.opener.location.href = "'. $site_URL.'/index.php?module=Reports&action=SaveAndRun&record='.$genQueryId.'&folderid='.$folderid.'";
				window.open("index.php?module=Reports&action=ListView", "_blank");
				self.close();
			</script>';
	}
} else {
	if ($reportid != "") {
		$pivotcolumns = '';
		if (!empty($selectedcolumns)) {
			$idelcolumnsql = "delete from vtiger_selectcolumn where queryid=?";
			$idelcolumnsqlresult = $adb->pquery($idelcolumnsql, array($reportid));
			if ($idelcolumnsqlresult != false) {
				$pcols = array();
				for ($i=0; $i<count($selectedcolumns); $i++) {
					if (!empty($selectedcolumns[$i]) && strpos($selectedcolumns[$i], ':')>0) {
						$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
						$icolumnsqlresult = $adb->pquery($icolumnsql, array($reportid,$i,(decode_html($selectedcolumns[$i]))));
						$colinfo = explode(':', $selectedcolumns[$i]);
						$pcols[] = $colinfo[0].'.'.$colinfo[1].' as '.$colinfo[2];
					}
				}
				$pivotcolumns = implode(',', $pcols);
			}
		}
		$delsharesqlresult = $adb->pquery("DELETE FROM vtiger_reportsharing WHERE reportid=?", array($reportid));
		if ($delsharesqlresult != false && $sharetype=="Shared" && $shared_entities!='') {
			$selectedcolumn = explode(";", $shared_entities);
			for ($i=0; $i< count($selectedcolumn) -1; $i++) {
				$temp = explode("::", $selectedcolumn[$i]);
				$icolumnsql = "INSERT INTO vtiger_reportsharing (reportid,shareid,setype) VALUES (?,?,?)";
				$icolumnsqlresult = $adb->pquery($icolumnsql, array($reportid,$temp[1],$temp[0]));
			}
		}

		//<<<<reportmodules>>>>>>>
		$ireportmodulesql = "UPDATE vtiger_reportmodules SET primarymodule=?,secondarymodules=? WHERE reportmodulesid=?";
		$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($pmodule, $smodule,$reportid));
		$log->info("Reports :: Save->Successfully saved vtiger_reportmodules");
		//<<<<reportmodules>>>>>>>
		$select = 'SELECT cbreporttype FROM vtiger_report WHERE reportid=?';
		$res = $adb->pquery($select, array($reportid));
		$cbreporttype = $adb->query_result($res, 0, 0);
		if ($cbreporttype != 'corebos') {
			$reporttype='';
		}
		list($cbreporttype,$minfo) = report_getMoreInfoFromRequest($cbreporttype, $pmodule, $smodule, $pivotcolumns);
		$ireportsql = "update vtiger_report set REPORTNAME=?, DESCRIPTION=?, REPORTTYPE=?, SHARINGTYPE=?, folderid=?, moreinfo=? where REPORTID=?";
		$ireportparams = array($reportname, $reportdescription, $reporttype, $sharetype, $folderid, $minfo, $reportid);
		$ireportresult = $adb->pquery($ireportsql, $ireportparams);
		$log->info("Reports :: Save->Successfully saved vtiger_report");

		$idelreportsortcolsql = "delete from vtiger_reportsortcol where reportid=?";
		$idelreportsortcolsqlresult = $adb->pquery($idelreportsortcolsql, array($reportid));
		$delReportGroupTimeSQL = "DELETE FROM vtiger_reportgroupbycolumn WHERE reportid=?";
		$delReportGroupTimeRES = $adb->pquery($delReportGroupTimeSQL, array($reportid));

		$log->info("Reports :: Save->Successfully deleted vtiger_reportsortcol");

		if ($idelreportsortcolsqlresult!=false) {
			//<<<<step3 vtiger_reportsortcol>>>>>>>
			if ($sort_by1 != '') {
				$sort_by1sql = 'insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)';
				$sort_by1result = $adb->pquery($sort_by1sql, array(1, $reportid, $sort_by1, $sort_order1));
				if (CustomReportUtils::isDateField($sort_by1)) {
					$groupByTime1Sql = 'INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)';
					$groupByTime1Res = $adb->pquery($groupByTime1Sql, array($reportid,1,$sort_by1,$groupTime1));
				}
			}
			if ($sort_by2 != '') {
				$sort_by2sql = 'insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)';
				$sort_by2result = $adb->pquery($sort_by2sql, array(2, $reportid, $sort_by2, $sort_order2));
				if (CustomReportUtils::isDateField($sort_by2)) {
					$groupByTime2Sql = 'INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)';
					$groupByTime2Res = $adb->pquery($groupByTime2Sql, array($reportid,2,$sort_by2,$groupTime2));
				}
			}
			if ($sort_by3 != '') {
				$sort_by3sql = 'insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)';
				$sort_by3result = $adb->pquery($sort_by3sql, array(3, $reportid, $sort_by3, $sort_order3));
				if (CustomReportUtils::isDateField($sort_by3)) {
					$groupByTime3Sql = 'INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)';
					$groupByTime3Res = $adb->pquery($groupByTime3Sql, array($reportid,3,$sort_by3,$groupTime3));
				}
			}
			$log->info("Reports :: Save->Successfully saved vtiger_reportsortcol");
			//<<<<step3 vtiger_reportsortcol>>>>>>>

			$idelreportdatefiltersql = "delete from vtiger_reportdatefilter where datefilterid=?";
			$idelreportdatefiltersqlresult = $adb->pquery($idelreportdatefiltersql, array($reportid));

			//<<<<step5 standarfilder>>>>>>>
			$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
			$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($reportid, $stdDateFilterField, $stdDateFilter, $startdate, $enddate));
			$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
			//<<<<step5 standarfilder>>>>>>>

			//<<<<step4 columnstototal>>>>>>>
			$idelreportsummarysql = "delete from vtiger_reportsummary where reportsummaryid=?";
			$idelreportsummarysqlresult = $adb->pquery($idelreportsummarysql, array($reportid));

			for ($i=0; $i<count($columnstototal); $i++) {
				$ireportsummarysql = "insert into vtiger_reportsummary (REPORTSUMMARYID,SUMMARYTYPE,COLUMNNAME) values (?,?,?)";
				$ireportsummaryresult = $adb->pquery($ireportsummarysql, array($reportid, $i, $columnstototal[$i]));
			}
			$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
			//<<<<step4 columnstototal>>>>>>>

			//<<<<step5 advancedfilter>>>>>>>
			updateAdvancedCriteria($reportid, $advft_criteria, $advft_criteria_groups);
			//<<<<step5 advancedfilter>>>>>>>

			//<<<<step7 scheduledReport>>>>>>>
			if ($isReportScheduled == 'off' || $isReportScheduled == '0' || $isReportScheduled == '') {
				$deleteScheduledReportSql = "DELETE FROM vtiger_scheduled_reports WHERE reportid=?";
				$adb->pquery($deleteScheduledReportSql, array($reportid));
			} else {
				$checkScheduledResult = $adb->pquery('SELECT 1 FROM vtiger_scheduled_reports WHERE reportid=?', array($reportid));
				if ($adb->num_rows($checkScheduledResult) > 0) {
					$scheduledReportSql = 'UPDATE vtiger_scheduled_reports SET recipients=?,schedule=?,format=? WHERE reportid=?';
					$adb->pquery($scheduledReportSql, array($selectedRecipients,$scheduledInterval,$scheduledFormat,$reportid));
				} else {
					$scheduleReportSql = 'INSERT INTO vtiger_scheduled_reports (reportid,recipients,schedule,format,next_trigger_time) VALUES (?,?,?,?,?)';
					$adb->pquery($scheduleReportSql, array($reportid,$selectedRecipients,$scheduledInterval,$scheduledFormat,date("Y-m-d H:i:s")));
				}
			}
			//<<<<step7 scheduledReport>>>>>>>
		} else {
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			die;
		}
	} else {
		$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while inserting the record</font>
			</ul></B></font> <br>" ;
		echo $errormessage;
		die;
	}
	echo '<script>window.opener.location.href = window.opener.location.href;self.close();</script>';
}
?>
