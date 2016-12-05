<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/database/PearDatabase.php');
@include('user_privileges/default_module_view.php');

global $adb, $currentModule;
$idlist            = vtlib_purify($_REQUEST['idlist']);
$destinationModule = vtlib_purify($_REQUEST['destination_module']);
$parenttab         = getParentTab();

$forCRMRecord = vtlib_purify($_REQUEST['parentid']);
$mode = $_REQUEST['mode'];

if($singlepane_view == 'true')
	$action = "DetailView";
else
	$action = "CallRelatedList";

if($mode == 'delete') {
	if ($idlist=='All') {
		switch($destinationModule) {
			case 'Accounts' : $rel_table = 'vtiger_campaignaccountrel'; $rel_field = 'accountid';
			break;
			case 'Contacts' : $rel_table = 'vtiger_campaigncontrel'; $rel_field = 'contactid';
			break;
			case 'Leads' : $rel_table = 'vtiger_campaignleadrel'; $rel_field = 'leadid';
			break;
		}
		$adb->pquery("delete from $rel_table where campaignid=?", array($forCRMRecord));
		$action = 'CampaignsAjax&file=CallRelatedList&ajax=true';
	} else {
		// Split the string of ids
		$ids = explode (";",$idlist);
		if(!empty($ids)) {
			$focus = CRMEntity::getInstance($currentModule);
			$focus->delete_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
		}
	}
} else {
	$storearray = array();
	if(!empty($_REQUEST['idlist'])) {
		// Split the string of ids
		$storearray = explode (";",trim($idlist,";"));
	} else if(!empty($_REQUEST['entityid'])){
		$storearray = array($_REQUEST['entityid']);
	}
	$focus = CRMEntity::getInstance($currentModule);
	if(!empty($storearray)) {
		relateEntities($focus, $currentModule, $forCRMRecord, $destinationModule, $storearray);
	}
}
header("Location: index.php?action=$action&module=$currentModule&record=".$forCRMRecord."&parenttab=".$parenttab);
?>
