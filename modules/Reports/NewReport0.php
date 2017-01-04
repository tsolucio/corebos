<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');

global $app_strings, $app_list_strings, $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');
$log = LoggerManager::getLogger('report_list');
global $currentModule, $image_path, $theme;
$recordid = vtlib_purify($_REQUEST['record']);
$report = new Reports();

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$list_report_form = new vtigerCRM_Smarty;
// Pass on the authenticated user language
global $current_language;
$list_report_form->assign('LANGUAGE', $current_language);
$list_report_form->assign("MOD", $mod_strings);
$list_report_form->assign("APP", $app_strings);
$repObj = new Reports ();
$folderid = 0;
if($recordid!=''){
	$oRep = new Reports($recordid);
	$sec_module = array();
	if($oRep->secmodule!=''){
		$sec_mod = explode(":",$oRep->secmodule);
		$rel_modules = getReportRelatedModules($oRep->primodule,$oRep);
		if(!empty($sec_mod)){
			foreach($sec_mod as $module){
				if(!in_array($module,$rel_modules))
					$restricted_modules[] = $module;
				else
					$sec_module[$module] = 1;
			}
		}
	}
	if(vtlib_isModuleActive($oRep->primodule)==false){
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_ACCESS']." : ".$oRep->primodule." </span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.close();'>".$app_strings['LBL_CLOSE']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
		die();
	}
	$list_report_form->assign("RELATEDMODULES",getReportRelatedModules($oRep->primodule,$oRep));
	$list_report_form->assign("RECORDID",$recordid);
	$list_report_form->assign("REPORTNAME",$oRep->reportname);
	$list_report_form->assign("REPORTDESC",$oRep->reportdescription);
	$list_report_form->assign("REP_MODULE",$oRep->primodule);
	$folderid = $oRep->folderid;
	if(!isset($_REQUEST['secondarymodule'])){
		$list_report_form->assign("SEC_MODULE",$sec_module);
	}
	if(!empty($restricted_modules)){
		$restrictedmod = implode(",",$restricted_modules);
	} else {
		$restrictedmod = '';
	}
	$list_report_form->assign("RESTRICTEDMODULES",$restrictedmod);
	$list_report_form->assign("BACK",'true');

}
if(!empty($_REQUEST['reportmodule'])) {
	if(vtlib_isModuleActive($_REQUEST['reportmodule'])==false || isPermitted($_REQUEST['reportmodule'],'index')!= "yes"){
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_ACCESS']." : ".getTranslatedString($_REQUEST['reportmodule'],$_REQUEST['reportmodule'])." </span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.close();'>".$app_strings['LBL_CLOSE']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
		die();
	}
	$list_report_form->assign("RELATEDMODULES",getReportRelatedModules($_REQUEST['reportmodule'],$repObj));
	$list_report_form->assign("REP_MODULE",vtlib_purify($_REQUEST['reportmodule']));
}
if(!empty($_REQUEST['reportName'])) {
	$list_report_form->assign("RELATEDMODULES",getReportRelatedModules($_REQUEST['primarymodule'],$repObj));
	$list_report_form->assign("REPORTNAME",vtlib_purify($_REQUEST['reportName']));
	$list_report_form->assign("REPORTDESC",vtlib_purify($_REQUEST['reportDesc']));
	$list_report_form->assign("REP_MODULE",vtlib_purify($_REQUEST['primarymodule']));
	$sec_mod = explode(":",vtlib_purify($_REQUEST['secondarymodule']));
	$sec_module = array();
	foreach($sec_mod as $module){
		$sec_module[$module] = 1;
	}
	$list_report_form->assign("SEC_MODULE",$sec_module);
	$list_report_form->assign("BACK_WALK",'true');
}

// Schedule Report
require_once 'modules/Reports/ScheduledReports.php';
$availableUsersHTML = VTScheduledReport::getAvailableUsersHTML();
$availableGroupsHTML = VTScheduledReport::getAvailableGroupsHTML();
$availableRolesHTML = VTScheduledReport::getAvailableRolesHTML();
$availableRolesAndSubHTML = VTScheduledReport::getAvailableRolesAndSubordinatesHTML();

getBrowserVariables($list_report_form);

$list_report_form->assign("AVAILABLE_USERS", $availableUsersHTML);
$list_report_form->assign("AVAILABLE_GROUPS", $availableGroupsHTML);
$list_report_form->assign("AVAILABLE_ROLES", $availableRolesHTML);
$list_report_form->assign("AVAILABLE_ROLESANDSUB", $availableRolesAndSubHTML);

$reportid = vtlib_purify($_REQUEST["record"]);
$scheduledReport = new VTScheduledReport($adb, $current_user, $reportid);
$scheduledReport->getReportScheduleInfo();

$list_report_form->assign('IS_SCHEDULED', $scheduledReport->isScheduled);
$list_report_form->assign('REPORT_FORMAT', $scheduledReport->scheduledFormat);

$selectedRecipientsHTML = $scheduledReport->getSelectedRecipientsHTML();
$list_report_form->assign("SELECTED_RECIPIENTS", $selectedRecipientsHTML);

$list_report_form->assign("schtypeid",$scheduledReport->scheduledInterval['scheduletype']);
$list_report_form->assign("schtime",$scheduledReport->scheduledInterval['time']);
$list_report_form->assign("schday",$scheduledReport->scheduledInterval['date']);
$list_report_form->assign("schweek",$scheduledReport->scheduledInterval['day']);
$list_report_form->assign("schmonth",$scheduledReport->scheduledInterval['month']);

$list_report_form->assign('FOLDERID',isset($_REQUEST['folder'])?vtlib_purify($_REQUEST['folder']):$folderid);
$list_report_form->assign("REP_FOLDERS",$repObj->sgetRptFldr());
$list_report_form->assign("IMAGE_PATH", $image_path);
$list_report_form->assign("THEME_PATH", $theme_path);
$list_report_form->assign("ERROR_MSG", $mod_strings['LBL_NO_PERMISSION']);

$BLOCKJS = $repObj->getCriteriaJS();
$list_report_form->assign("BLOCKJS_STD",$BLOCKJS);
$list_report_form->assign("DATEFORMAT",$current_user->date_format);
$list_report_form->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$list_report_form->assign('MODULE','Reports');

$list_report_form->display("ReportsStep0.tpl");
?>