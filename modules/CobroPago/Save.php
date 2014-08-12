<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $mod_strings;

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");

$focus = new $currentModule();
setObjectValuesFromRequest($focus);

$mode = $_REQUEST['mode'];
$record=$_REQUEST['record'];
if($mode) $focus->mode = $mode;
if($record)$focus->id  = $record;

if($_REQUEST['assigntype'] == 'U') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}
if (empty($_REQUEST['register'])) {
	$refDateValue = new DateTimeField();  // right now
	$focus->column_fields['register'] = $refDateValue->getDisplayDate();
}

$update_after = false;
//echo '<pre>';var_dump($focus->column_fields);echo '</pre>';
if ($focus->column_fields['paid'] == "on"){
	if($focus->mode != 'edit'){
		$update_after = true;
		$update_log = $mod_strings['Payment Paid'].$current_user->user_name.$mod_strings['PaidOn'].date("l dS F Y h:i:s A").'--//--';
	}else{
		$SQL = "SELECT paid,update_log FROM vtiger_cobropago WHERE cobropagoid=?";
		$result = $adb->pquery($SQL,array($focus->id));
		$old_paid = $adb->query_result($result,0,'paid');
		if ($old_paid == "0"){
			$update_after = true;
			$update_log = $adb->query_result($result,0,'update_log');
			$update_log .= $mod_strings['Payment Paid'].$current_user->user_name.$mod_strings['PaidOn'].date("l dS F Y h:i:s A").'--//--';
		}
	}
}


$focus->save($currentModule);
$return_id = $focus->id;

if ($update_after){
		$SQL_UPD = "UPDATE vtiger_cobropago SET update_log=? WHERE cobropagoid=?";
		$adb->pquery($SQL_UPD,array($update_log,$focus->id));
}

$search = vtlib_purify($_REQUEST['search_url']);

$parenttab = getParentTab();
if($_REQUEST['return_module'] != '') {
	$return_module = vtlib_purify($_REQUEST['return_module']);
} else {
	$return_module = $currentModule;
}

if($_REQUEST['return_action'] != '') {
	$return_action = vtlib_purify($_REQUEST['return_action']);
} else {
	$return_action = "DetailView";
}

if($_REQUEST['return_id'] != '') {
	$return_id = vtlib_purify($_REQUEST['return_id']);
}

header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&parenttab=$parenttab&start=".vtlib_purify($_REQUEST['pagenumber']).$search);

?>
