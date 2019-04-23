<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';

global $app_strings,$mod_strings,$current_user,$theme,$adb;
$image_path = 'themes/'.$theme.'/images/';
$idlist = vtlib_purify($_REQUEST['idlist']);
$pmodule=vtlib_purify($_REQUEST['return_module']);
$excludedRecords = isset($_REQUEST['excludedRecords']) ? vtlib_purify($_REQUEST['excludedRecords']) : '';

$single_record = false;
if (!strpos($idlist, ':')) {
	$single_record = true;
}
$smarty = new vtigerCRM_Smarty;

$userid =  $current_user->id;

$querystr = 'select fieldid, fieldname, fieldlabel, columnname from vtiger_field where tabid=? and uitype=13 and vtiger_field.presence in (0,2)';
$res=$adb->pquery($querystr, array(getTabid($pmodule)));
$numrows = $adb->num_rows($res);
$returnvalue = array();
for ($i = 0; $i < $numrows; $i++) {
	$value = array();
	$fieldname = $adb->query_result($res, $i, 'fieldname');
	$permit = getFieldVisibilityPermission($pmodule, $userid, $fieldname);
	if ($permit == '0') {
		$temp = $adb->query_result($res, $i, 'columnname');
		$columnlists[] = $temp;
		$fieldid = $adb->query_result($res, $i, 'fieldid');
		$fieldlabel = $adb->query_result($res, $i, 'fieldlabel');
		$value[] = getTranslatedString($fieldlabel, $pmodule);
		$returnvalue[$fieldid] = $value;
	}
}
$entity_name = '';
$field_value = array();
if ($single_record && count($columnlists) > 0) {
	$count = 0;
	$val_cnt = 0;
	switch ($pmodule) {
		case 'Accounts':
			$query = 'select accountname,'.implode(',', $columnlists)
				.' from vtiger_account
				left join vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid
				where vtiger_account.accountid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$acc_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $acc_eval;
				if ($acc_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'accountname');
			break;
		case 'Leads':
			$query = 'select concat(firstname," ",lastname) as leadname,'.implode(',', $columnlists)
				.' from vtiger_leaddetails
				left join vtiger_leadscf on vtiger_leadscf.leadid = vtiger_leaddetails.leadid
				where vtiger_leaddetails.leadid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$lead_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $lead_eval;
				if ($lead_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'leadname');
			break;
		case 'Contacts':
			$query = 'select concat(firstname," ",lastname) as contactname,'.implode(',', $columnlists)
				.' from vtiger_contactdetails
				left join vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid
				where vtiger_contactdetails.contactid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$con_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $con_eval;
				if ($con_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'contactname');
			break;
		case 'Project':
			$query = 'select projectname,'.implode(',', $columnlists)
				.' from vtiger_project
				left join vtiger_projectcf on vtiger_projectcf.projectid = vtiger_project.projectid
				where vtiger_project.projectid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$con_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $con_eval;
				if ($con_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'projectname');
			break;
		case 'ProjectTask':
			$query = 'select projecttaskname,'.implode(',', $columnlists)
				.' from vtiger_projecttask
				left join vtiger_projecttaskcf on vtiger_projecttaskcf.projecttaskid = vtiger_projecttask.projecttaskid
				where vtiger_projecttask.projecttaskid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$con_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $con_eval;
				if ($con_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'projecttaskname');
			break;
		case 'Potentials':
			$query = 'select potentialname,'.implode(',', $columnlists)
				.' from vtiger_potential
				left join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid
				where vtiger_potential.potentialid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$con_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $con_eval;
				if ($con_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'potentialname');
			break;
		case 'HelpDesk':
			$query = 'select title,'.implode(',', $columnlists)
				.' from vtiger_troubletickets
				left join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
				where vtiger_troubletickets.ticketid = ?';
			$result=$adb->pquery($query, array($idlist));
			foreach ($columnlists as $columnname) {
				$con_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $con_eval;
				if ($con_eval != '') {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, 'title');
			break;
		default:
			$minfo = getEntityField($pmodule);
			$qg = new QueryGenerator($pmodule, $current_user);
			$fields = $columnlists;
			$fields[] = $minfo['fieldname'];
			$qg->setFields($fields);
			$qg->addCondition('id', $idlist, 'e');
			$query = $qg->getQuery();
			$result = $adb->query($query);
			foreach ($columnlists as $columnname) {
				$con_eval = $adb->query_result($result, 0, $columnname);
				$field_value[$count++] = $con_eval;
				if ($con_eval != "") {
					$val_cnt++;
				}
			}
			$entity_name = $adb->query_result($result, 0, $minfo['fieldname']);
			break;
	}
}
$smarty->assign('PERMIT', $permit);
$smarty->assign('ENTITY_NAME', $entity_name);
$smarty->assign('ONE_RECORD', $single_record);
$smarty->assign('MAILDATA', $field_value);
$smarty->assign('MAILINFO', $returnvalue);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('IDLIST', $idlist);
$smarty->assign('APP', $app_strings);
$smarty->assign('FROM_MODULE', $pmodule);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('EXE_REC', $excludedRecords);
$smarty->assign('SEARCH_URL', isset($_REQUEST['searchurl']) ? vtlib_purify($_REQUEST['searchurl']) : '');
$smarty->assign('VIEWID', isset($_REQUEST['viewname']) ? vtlib_purify($_REQUEST['viewname']) : '');
$smarty->assign('RECORDID', isset($_REQUEST['recordid']) ? vtlib_purify($_REQUEST['recordid']) : '');

if (($single_record && count($columnlists) > 0)) {
	$smarty->display('SelectEmail.tpl');
} elseif (!$single_record && count($columnlists) > 0) {
	$smarty->display('SelectEmail.tpl');
} elseif ($val_cnt < 0) {
	echo 'Mail Ids not permitted';
} else {
	echo 'No Mail Ids';
}
?>