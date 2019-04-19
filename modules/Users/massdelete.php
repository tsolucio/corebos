<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/CommonUtils.php';
$idlist = vtlib_purify($_REQUEST['idlist']);
$viewid = urlencode(vtlib_purify($_REQUEST['viewname']));
$returnmodule = vtlib_purify($_REQUEST['return_module']);
$return_action = isset($_REQUEST['return_action']) ? urlencode(vtlib_purify($_REQUEST['return_action'])) : '';
$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
$rstart='';
$url = getBasic_Advance_SearchURL();

//split the string and store in an array
$storearray = getSelectedRecords($_REQUEST, $returnmodule, $idlist, $excludedRecords);
$storearray = array_filter($storearray);
$ids_list = array();
$errormsg = '';
foreach ($storearray as $id) {
	if (isPermitted($returnmodule, 'Delete', $id) == 'yes') {
		$focus = CRMEntity::getInstance($returnmodule);
		DeleteEntity($returnmodule, $returnmodule, $focus, $id, '');
	} else {
		$ids_list[] = $id;
	}
}
if (count($ids_list) > 0) {
	$ret = getEntityName($returnmodule, $ids_list);
	if (count($ret) > 0) {
		$errormsg = urlencode(implode(',', $ret));
	}
}

if (isset($_REQUEST['start']) && ($_REQUEST['start']!='')) {
	$rstart = "&start=".urlencode(vtlib_purify($_REQUEST['start']));
}
$returnmodule = urlencode($returnmodule);
if ($returnmodule == 'Emails') {
	if (isset($_REQUEST['folderid']) && $_REQUEST['folderid'] != '') {
		$folderid = urlencode(vtlib_purify($_REQUEST['folderid']));
	} else {
		$folderid = 1;
	}
	header("Location: index.php?module=$returnmodule&action=".$returnmodule."Ajax&folderid=".$folderid."&ajax=delete".$rstart."&file=ListView&errormsg=".$errormsg.$url);
} elseif ($return_action == 'ActivityAjax') {
	$req = new Vtiger_Request();
	$req->set('return_view', $_REQUEST['view']);
	$req->set('return_hour', $_REQUEST['hour']);
	$req->set('return_day', $_REQUEST['day']);
	$req->set('return_month', $_REQUEST['month']);
	$req->set('return_year', $_REQUEST['year']);
	$req->set('return_type', $_REQUEST['type']);
	$req->set('return_subtab', $_REQUEST['subtab']);
	$req->set('return_onlyforuser', $_REQUEST['onlyforuser']);
	$urlpart = $req->getReturnURL();
	header('Location: index.php?module='.$returnmodule.'&action='.$return_action.$rstart.$urlpart.'&viewOption='.urlencode(vtlib_purify($_REQUEST['viewOption'])).$url);
} else {
	if (!isset($_REQUEST['__NoReload'])) {
		header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&ajax=delete".$rstart."&file=ListView&errormsg=".$errormsg.$url);
	}
}
?>