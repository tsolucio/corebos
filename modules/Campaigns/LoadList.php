<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ('License'); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): mmbrich
 ********************************************************************************/
require_once 'modules/CustomView/CustomView.php';

global $currentModule, $current_user;
$queryGenerator = new QueryGenerator(vtlib_purify($_REQUEST['list_type']), $current_user);
if ($_REQUEST['cvid'] != '0') {
	$queryGenerator->initForCustomViewById(vtlib_purify($_REQUEST['cvid']));
} else {
	$queryGenerator->initForDefaultCustomView();
}

$rs = $adb->query($queryGenerator->getQuery());
$acl = true;
if ($_REQUEST['list_type'] == 'Leads') {
	$reltable = 'vtiger_campaignleadrel';
	$relid = 'leadid';
} elseif ($_REQUEST['list_type'] == 'Contacts') {
	$reltable = 'vtiger_campaigncontrel';
	$relid = 'contactid';
} elseif ($_REQUEST['list_type'] == 'Accounts') {
	$reltable = 'vtiger_campaignaccountrel';
	$relid = 'accountid';
} else {
	$acl = false;
}
$focus = CRMEntity::getInstance($currentModule);
if ($acl) {
	while ($row=$adb->fetch_array($rs)) {
		relateEntities($focus, $currentModule, vtlib_purify($_REQUEST['return_id']), vtlib_purify($_REQUEST['list_type']), $row[$relid]);
	}
} else {
	if ($_REQUEST['list_type'] == 'Potentials') {
		while ($row=$adb->fetch_array($rs)) {
			$reltors = $adb->pquery(
				'select related_to,setype from vtiger_potential left join vtiger_crmentity on crmid=related_to where potentialid=?',
				[$row['potentialid']]
			);
			if ($reltors && $adb->num_rows($reltors)>0) {
				relateEntities($focus, $currentModule, vtlib_purify($_REQUEST['return_id']), $reltors->fields['setype'], $reltors->fields['related_to']);
			}
		}
	}
}
header('Location: index.php?module=Campaigns&action=CampaignsAjax&file=CallRelatedList&ajax=true&record='.vtlib_purify($_REQUEST['return_id']));
?>
