<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $adb, $singlepane_view;

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");

$search = vtlib_purify($_REQUEST['search_url']);

$focus = new $currentModule();
setObjectValuesFromRequest($focus);

$mode = vtlib_purify($_REQUEST['mode']);
$record=vtlib_purify($_REQUEST['record']);
if($mode) $focus->mode = $mode;
if($record)$focus->id  = $record;
//Added to update the ticket history
//Before save we have to construct the update log. 
if($mode == 'edit')
{
	$usr_qry = $adb->pquery('select * from vtiger_crmentity where crmid=?', array($focus->id));
	$old_user_id = $adb->query_result($usr_qry,0,"smownerid");
}
$grp_name = getGroupName($_REQUEST['assigned_group_id']);

if($_REQUEST['assigntype'] == 'U') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}
list($saveerror,$errormessage,$error_action,$returnvalues) = $focus->preSaveCheck($_REQUEST);
if ($saveerror) { // there is an error so we go back to EditView.
	$return_module=$return_id=$return_action='';
	if (!empty($_REQUEST['return_action'])) {
		$return_action = '&return_action='.vtlib_purify($_REQUEST['return_action']);
	}
	if (!empty($_REQUEST['return_module'])) {
		$return_action .= '&return_module='.vtlib_purify($_REQUEST['return_module']);
	}
	if (isset($_REQUEST['return_id']) and $_REQUEST['return_id'] != '') {
		$return_action = '&return_id='.vtlib_purify($_REQUEST['return_id']);
	}
	if (!empty($_REQUEST['activity_mode'])) {
		$return_action .= '&activity_mode='.vtlib_purify($_request['activity_mode']);
	}
	if (empty($_REQUEST['return_viewname'])) {
		$return_viewname = '0';
	} else {
		$return_viewname = vtlib_purify($_REQUEST['return_viewname']);
	}
	$field_values_passed.="";
	foreach($focus->column_fields as $fieldname => $val) {
		if(isset($_REQUEST[$fieldname])) {
			$field_values_passed.="&";
			if($fieldname == 'assigned_user_id') { // assigned_user_id already set correctly above
				$value = vtlib_purify($focus->column_fields['assigned_user_id']);
			} else {
				$value = vtlib_purify($_REQUEST[$fieldname]);
			}
			if (is_array($value)) $value = implode(' |##| ',$value); // for multipicklists
			$field_values_passed.=$fieldname."=".urlencode($value);
		}
	}
	$encode_field_values=base64_encode($field_values_passed);
	$error_module = $currentModule;
	$error_action = (empty($error_action) ? 'EditView' : $error_action);
	$errormessage = urlencode($errormessage);
	header("location: index.php?action=$error_action&module=$error_module&record=$record&return_viewname=$return_viewname".$search.$return_action.$returnvalues."&error_msg=$errormessage&save_error=true&encode_val=$encode_field_values");
	die();
}

$fldvalue = $focus->constructUpdateLog($focus, $mode, $grp_name, $_REQUEST['assigntype']);
$fldvalue = from_html($fldvalue,($mode == 'edit')?true:false);

$focus->save($currentModule);

//After save the record, we should update the log
$adb->pquery("update vtiger_troubletickets set update_log=? where ticketid=?", array($fldvalue,$focus->id));

$return_id = $focus->id;

$parenttab = getParentTab();
if(!empty($_REQUEST['return_module'])) {
	$return_module = vtlib_purify($_REQUEST['return_module']);
} else {
	$return_module = $currentModule;
}
if(!empty($_REQUEST['return_action'])) {
	$return_action = vtlib_purify($_REQUEST['return_action']);
} else {
	$return_action = 'DetailView';
}
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != '') {
	$return_id = vtlib_purify($_REQUEST['return_id']);
}
//code added for returning back to the current view after edit from list view
if(empty($_REQUEST['return_viewname']) or $singlepane_view == 'true') {
	$return_viewname='0';
} else {
	$return_viewname=vtlib_purify($_REQUEST['return_viewname']);
}
if(isset($_REQUEST['activity_mode'])) {
	$return_action .= '&activity_mode='.vtlib_purify($_REQUEST['activity_mode']);
}
if($_REQUEST['return_module'] == 'Products' & $_REQUEST['product_id'] != '' &&  $focus->id != '')
	$return_id = vtlib_purify($_REQUEST['product_id']);

header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&parenttab=$parenttab&viewname=$return_viewname&start=".vtlib_purify($_REQUEST['pagenumber']).$search);
?>