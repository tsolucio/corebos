<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
@include 'user_privileges/default_module_view.php';

global $adb, $currentModule;
$idlist            = isset($_REQUEST['idlist']) ? vtlib_purify($_REQUEST['idlist']) : '';
$destinationModule = vtlib_purify($_REQUEST['destination_module']);

$forCRMRecord = vtlib_purify($_REQUEST['parentid']);
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
if (isset($override_action)) {
	$action = $override_action;
} elseif ($singlepane_view == 'true' || isPresentRelatedListBlockWithModule($currentModule, $destinationModule)) {
	$action = 'DetailView';
} else {
	$action = 'CallRelatedList';
}
$focus = CRMEntity::getInstance($currentModule);

if ($mode == 'delete') {
	if ($idlist=='All') {
		switch ($destinationModule) {
			case 'Accounts':
				$rel_table = 'vtiger_campaignaccountrel';
				$rel_field = 'accountid';
				break;
			case 'Contacts':
				$rel_table = 'vtiger_campaigncontrel';
				$rel_field = 'contactid';
				break;
			case 'Leads':
				$rel_table = 'vtiger_campaignleadrel';
				$rel_field = 'leadid';
				break;
		}
		$adb->pquery("delete from $rel_table where campaignid=?", array($forCRMRecord));
		$action = 'CampaignsAjax&file=CallRelatedList&ajax=true';
	} else {
		// Split the string of ids
		$ids = explode(';', trim($idlist, ';'));
		if (!empty($ids)) {
			$focus->delete_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
		}
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'CampaignsAjax' && isset($_REQUEST['ajax'])) {
			$action = 'CampaignsAjax&file=CallRelatedList&ajax=true';
		}
	}
} else {
	if (!empty($idlist)) {
		$ids = explode(';', trim($idlist, ';'));
	} elseif (!empty($_REQUEST['entityid'])) {
		$ids = $_REQUEST['entityid'];
	}
	if (!empty($ids)) {
		relateEntities($focus, $currentModule, $forCRMRecord, $destinationModule, $ids);
	}
}
header('Location: index.php?module='.urlencode($currentModule).'&record='.urlencode($forCRMRecord)."&action=$action");
?>
