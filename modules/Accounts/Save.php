<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $adb;

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");

$search = vtlib_purify($_REQUEST['search_url']);
if (isset($_REQUEST['dup_check']) && $_REQUEST['dup_check'] != '') {
	$value = vtlib_purify($_REQUEST['accountname']);
	$query = 'SELECT accountname FROM vtiger_account,vtiger_crmentity WHERE accountname =? and vtiger_account.accountid = vtiger_crmentity.crmid and vtiger_crmentity.deleted != 1';
	$params = array($value);
	$id = vtlib_purify($_REQUEST['record']);
	if(isset($id) && $id !='') {
		$query .= ' and vtiger_account.accountid != ?';
		array_push($params, $id);
	}
	$result = $adb->pquery($query, $params);
	if($adb->num_rows($result) > 0) {
		echo $mod_strings['LBL_ACCOUNT_EXIST'];
	} else {
		echo 'SUCCESS';
	}
	die;
}

$focus = new $currentModule();
setObjectValuesFromRequest($focus);

$mode = vtlib_purify($_REQUEST['mode']);
$record=vtlib_purify($_REQUEST['record']);
if($mode) $focus->mode = $mode;
if($record)$focus->id  = $record;
if($_REQUEST['assigntype'] == 'U') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

//When changing the Account Address Information it should also change the related contact address - dina
if($focus->mode == 'edit' && $_REQUEST['address_change'] == 'yes')
{
	$query = 'update vtiger_contactaddress set mailingcity=?,mailingstreet=?,mailingcountry=?,mailingzip=?,mailingpobox=?,mailingstate=?,othercountry=?,othercity=?,otherstate=?,otherzip=?,otherstreet=?,otherpobox=? where contactaddressid in (select contactid from vtiger_contactdetails where accountid=?)';
	$params = array($focus->column_fields['bill_city'], $focus->column_fields['bill_street'], $focus->column_fields['bill_country'],
		$focus->column_fields['bill_code'], $focus->column_fields['bill_pobox'], $focus->column_fields['bill_state'], $focus->column_fields['ship_country'],
		$focus->column_fields['ship_city'], $focus->column_fields['ship_state'], $focus->column_fields['ship_code'], $focus->column_fields['ship_street'],
		$focus->column_fields['ship_pobox'], $focus->id);
	$adb->pquery($query, $params);
}
//Changing account address - Ends
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

$focus->save($currentModule);
$return_id = $focus->id;
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Campaigns')
{
	if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != '')
	{
		$campAccStatusResult = $adb->pquery("select campaignrelstatusid from vtiger_campaignaccountrel where campaignid=? AND accountid=?",array($_REQUEST['return_id'], $focus->id));
		$accountStatus = $adb->query_result($campAccStatusResult,0,'campaignrelstatusid');
		$sql = "delete from vtiger_campaignaccountrel where accountid = ?";
		$adb->pquery($sql, array($focus->id));
		if(isset($accountStatus) && $accountStatus!=''){
			$sql = "insert into vtiger_campaignaccountrel values (?,?,?)";
			$adb->pquery($sql, array($_REQUEST['return_id'], $focus->id,$accountStatus));
		} else{
			$sql = "insert into vtiger_campaignaccountrel values (?,?,1)";
			$adb->pquery($sql, array($_REQUEST['return_id'], $focus->id));
		}
	}
}

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
if(empty($_REQUEST['return_viewname'])) {
	$return_viewname='0';
} else {
	$return_viewname=vtlib_purify($_REQUEST['return_viewname']);
}
if(isset($_REQUEST['activity_mode'])) {
	$return_action .= '&activity_mode='.vtlib_purify($_REQUEST['activity_mode']);
}

header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&parenttab=$parenttab&viewname=$return_viewname&start=".vtlib_purify($_REQUEST['pagenumber']).$search);
?>