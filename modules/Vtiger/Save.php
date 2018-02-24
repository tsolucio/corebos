<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $singlepane_view;

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once "modules/$currentModule/$currentModule.php";

if (isset($_REQUEST['search_url'])) {
	$search = vtlib_purify($_REQUEST['search_url']);
	if (substr($search, 0, 1) != '&') {
		$search = '&' . $search;
	}
} else {
	$search = '';
}
$req = new Vtiger_Request();
$req->setDefault('return_module', $currentModule);
if (!empty($_REQUEST['return_module'])) {
	$req->set('return_module', $_REQUEST['return_module']);
}
$req->setDefault('return_action', 'DetailView');
if (!empty($_REQUEST['return_action'])) {
	$req->set('return_action', $_REQUEST['return_action']);
}
//code added for returning back to the current view after edit from list view
if (empty($_REQUEST['return_viewname']) || $singlepane_view == 'true') {
	$req->set('return_viewname', '0');
} else {
	$req->set('return_viewname', $_REQUEST['return_viewname']);
}
if (isset($_REQUEST['activity_mode'])) {
	$req->set('return_activity_mode', $_REQUEST['activity_mode']);
}
$req->set('return_start', (isset($_REQUEST['pagenumber']) ? $_REQUEST['pagenumber'] : ''));

$focus = new $currentModule();
setObjectValuesFromRequest($focus);

$mode = (isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '');
$record=vtlib_purify($_REQUEST['record']);
if ($mode) {
	$focus->mode = $mode;
}
if ($record) {
	$focus->id  = $record;
}
if (isset($_REQUEST['inventory_currency'])) {
	$focus->column_fields['currency_id'] = vtlib_purify($_REQUEST['inventory_currency']);
	$cur_sym_rate = getCurrencySymbolandCRate(vtlib_purify($_REQUEST['inventory_currency']));
	$focus->column_fields['conversion_rate'] = $cur_sym_rate['rate'];
}
if (empty($_REQUEST['assigned_user_id']) && empty($_REQUEST['assigned_group_id'])) {
	if ($focus->mode != 'edit') {
		$focus->column_fields['assigned_user_id'] = $current_user->id;
	} else {
		$ownerrs = $adb->pquery('select smownerid from vtiger_crmentity where crmid=?', array($focus->id));
		$focus->column_fields['assigned_user_id'] = $adb->query_result($ownerrs, 0, 0);
	}
} else {
	if ($_REQUEST['assigntype'] == 'U') {
		$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
	} elseif ($_REQUEST['assigntype'] == 'T') {
		$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
	}
}
list($saveerror,$errormessage,$error_action,$returnvalues) = $focus->preSaveCheck($_REQUEST);
if ($saveerror) { // there is an error so we go back to EditView.
	$return_module=$return_id=$return_action='';
	if (isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != '') {
		$req->set('RETURN_ID', $_REQUEST['return_id']);
	}
	$field_values_passed = '';
	foreach ($focus->column_fields as $fieldname => $val) {
		if (isset($_REQUEST[$fieldname])) {
			$field_values_passed.="&";
			if ($fieldname == 'assigned_user_id') { // assigned_user_id already set correctly above
				$value = vtlib_purify($focus->column_fields['assigned_user_id']);
			} else {
				$value = vtlib_purify($_REQUEST[$fieldname]);
			}
			if (is_array($value)) {
				$value = implode(' |##| ', $value); // for multipicklists
			}
			$field_values_passed.=$fieldname."=".urlencode($value);
		}
	}
	$encode_field_values=base64_encode($field_values_passed);
	$req->set('return_module', $currentModule);
	$error_action = (empty($error_action) ? 'EditView' : $error_action);
	$req->set('return_action', $error_action);
	$req->set('return_record', $record);
	$errormessage = urlencode($errormessage);
	header('Location: index.php?' . $req->getReturnURL() . $search . $returnvalues . "&error_msg=$errormessage&save_error=true&encode_val=$encode_field_values");
	die();
}
if (!empty($source_id)) {
	$focus->linkmodeid = $source_id;
}
if (!empty($source_module)) {
	$focus->linkmodemodule = $source_module;
}
$focus->save($currentModule);
$return_id = $focus->id;
$req->set('return_record', $return_id);
if (isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != '') {
	$req->set('return_record', $_REQUEST['return_id']);
}

if (!isset($__cbSaveSendHeader) || $__cbSaveSendHeader) {
	if (isset($_REQUEST['Module_Popup_Edit']) && $_REQUEST['Module_Popup_Edit']==1) {
		echo '<script>window.close();</script>';
	} else {
		header('Location: index.php?' . $req->getReturnURL() . $search);
	}
}
?>
