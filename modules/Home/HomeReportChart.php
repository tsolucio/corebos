<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $adb, $current_user;

require 'user_privileges/user_privileges_'.$current_user->id.'.php';
require_once 'include/utils/GetUserGroups.php';
$params = array();
$userGroups = new GetUserGroups();
$userGroups->getAllUserGroups($current_user->id);
$user_groups = $userGroups->user_groups;
$user_group_query = '';
if (!empty($user_groups) && $is_admin==false) {
	$user_group_query = " (shareid IN (".generateQuestionMarks($user_groups).") AND setype='groups') OR";
	$params[] = $user_groups;
}
$summaryReportQuery = "SELECT * FROM vtiger_report
 INNER JOIN vtiger_reportsortcol ON vtiger_report.reportid = vtiger_reportsortcol.reportid
 WHERE vtiger_reportsortcol.columnname!='none'";
$non_admin_query = " vtiger_report.reportid IN (SELECT reportid from vtiger_reportsharing WHERE $user_group_query (shareid=? AND setype='users'))";
if (!is_admin($current_user)) {
	$summaryReportQuery .= " and ( (".$non_admin_query.") or vtiger_report.sharingtype='Public' or vtiger_report.owner = ? or";
	$summaryReportQuery .= " vtiger_report.owner in (select vtiger_user2role.userid from vtiger_user2role
		inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
		where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%'))";
	$params[] = $current_user->id;
	$params[] = $current_user->id;
}

$reportRes = $adb->pquery($summaryReportQuery, $params);

$selectElement = '<select name=selreportchart id=selreportchart_id class="detailedViewTextBox" onfocus="this.className=\'detailedViewTextBoxOn\'"';
$selectElement.= ' onblur="this.className=\'detailedViewTextBox\'" style="width:60%">';
$num_rows = $adb->num_rows($reportRes);
for ($i = 0; $i < $num_rows; $i++) {
	$reportId = $adb->query_result($reportRes, $i, 'reportid');
	$reportName = $adb->query_result($reportRes, $i, 'reportname');
	$selectElement .= '<option value="' . $reportId . '">' . getTranslatedString($reportName, 'Reports') . '</option>';
}
$selectElement.='</select>';
echo $selectElement;
?>
