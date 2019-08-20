<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
@include 'modules/Vtiger/default_module_view.php';

global $currentModule;
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
$errinfo='';
if ($mode == 'delete') {
	// Split the string of ids
	$ids = explode(';', trim($idlist, ';'));
	if (!empty($ids)) {
		$focus->delete_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
	}
} else {
	coreBOS_Settings::delSetting('RLERRORMESSAGE');
	coreBOS_Settings::delSetting('RLERRORMESSAGECLASS');
	if (!empty($idlist)) {
		$ids = explode(';', trim($idlist, ';'));
	} elseif (!empty($_REQUEST['entityid'])) {
		$ids = $_REQUEST['entityid'];
	}
	if (!empty($ids)) {
		relateEntities($focus, $currentModule, $forCRMRecord, $destinationModule, $ids);
	}
	$emsg = coreBOS_Settings::getSetting('RLERRORMESSAGE', '');
	if ($emsg!='') {
		$emsgclass = coreBOS_Settings::getSetting('RLERRORMESSAGECLASS', '');
		$errinfo = '&error_msg='.urlencode($emsg).'&error_msgclass='.urlencode($emsgclass);
	}
	coreBOS_Settings::delSetting('RLERRORMESSAGE');
	coreBOS_Settings::delSetting('RLERRORMESSAGECLASS');
}
header('Location: index.php?module='.urlencode($currentModule).'&record='.urlencode($forCRMRecord)."&action=$action".$errinfo);
?>
