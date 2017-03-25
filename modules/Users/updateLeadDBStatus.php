<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/utils.php');

$idlist= vtlib_purify($_REQUEST['idlist']);
$leadstatusval = vtlib_purify($_REQUEST['leadval']);
$viewid = urlencode(vtlib_purify($_REQUEST['viewname']));
$return_module = urlencode(vtlib_purify($_REQUEST['return_module']));
$return_action = urlencode(vtlib_purify($_REQUEST['return_action']));
$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
global $rstart;
//Added to fix 4600
$url = getBasic_Advance_SearchURL();

if(isset($_REQUEST['start']) && $_REQUEST['start']!='') {
	$rstart = '&start=' . urlencode(vtlib_purify($_REQUEST['start']));
}

global $current_user, $adb, $log;
$storearray = getSelectedRecords($_REQUEST,$return_module,$idlist,$excludedRecords);//explode(";",trim($idlist,';'));
$ids_list = array();

$date_var = date('Y-m-d H:i:s');

if(isset($_REQUEST['owner_id']) && $_REQUEST['owner_id']!='')
{
	foreach($storearray as $id)
	{
		if(isPermitted($return_module,'EditView',$id) == 'yes')
		{
			$idval = vtlib_purify($_REQUEST['owner_id']);
			//Inserting changed owner information to salesmanactivityrel table
			if($return_module == "Calendar"){
				$del_act = "delete from vtiger_salesmanactivityrel where smid=(select smownerid from vtiger_crmentity where crmid=?) and activityid=?";
				$adb->pquery($del_act,array($id, $id));
				if($_REQUEST['owner_type'] == 'User') {
					$count_r = $adb->pquery("select * from vtiger_salesmanactivityrel where smid=? and activityid=?",array($idval, $id));
					if($adb->num_rows($count_r) == 0) {
						$insert = "insert into vtiger_salesmanactivityrel values(?,?)";
						$result = $adb->pquery($insert, array($idval, $id));
					}
				}
			}
			//Now we have to update the smownerid
			$sql = "update vtiger_crmentity set modifiedby=?, smownerid=?, modifiedtime=? where crmid=?";
			$result = $adb->pquery($sql, array($current_user->id, $idval, $adb->formatDate($date_var, true), $id));
		}
		else
		{
			$ids_list[] = $id;
		}
	}
}
elseif(isset($_REQUEST['leadval']) && $_REQUEST['leadval']!='')
{
	foreach($storearray as $id)
	{
		if(isPermitted($return_module,'EditView',$id) == 'yes')
		{
			if($id != '') {
				$sql = "update vtiger_leaddetails set leadstatus=? where leadid=?";
				$result = $adb->pquery($sql, array($leadstatusval, $id));
				$query = "update vtiger_crmentity set modifiedby=?, modifiedtime=? where crmid=?";
				$result1 = $adb->pquery($query, array($current_user->id, $adb->formatDate($date_var, true), $id));
			}
		}
		else
		{
			$ids_list[] = $id;
		}
	}
}
if(count($ids_list) > 0) {
	$ret_owner = getEntityName($return_module,$ids_list);
	$errormsg = urlencode(implode(',',$ret_owner));
} else {
	$errormsg = '';
}
if($return_action == 'ActivityAjax') {
	$req = new Vtiger_Request();
	$req->set('return_view',   $_REQUEST['view']);
	$req->set('return_day',   $_REQUEST['day']);
	$req->set('return_month',$_REQUEST['month']);
	$req->set('return_year',   $_REQUEST['year']);
	$req->set('return_type',   $_REQUEST['type']);
	$req->set('return_subtab',   $_REQUEST['subtab']);
	$viewOption = '&viewOption='.urlencode(vtlib_purify($_REQUEST['viewOption']));
	$urlpart = $req->getReturnURL().$viewOption;
	header("Location: index.php?module=$return_module&action=".$return_action.$rstart.$urlpart.$url);
} else {
	header("Location: index.php?module=$return_module&action=".$return_module."Ajax&file=ListView&ajax=changestate".$rstart."&viewname=".$viewid."&errormsg=".$errormsg.$url);
}
?>