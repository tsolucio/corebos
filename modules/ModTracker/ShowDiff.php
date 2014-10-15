<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
global $list_max_entries_per_page;

require_once('Smarty_setup.php');

include_once dirname(__FILE__) . '/core/ModTracker_Basic.php';

$smarty = new vtigerCRM_Smarty();

// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', true);

$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', $currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);

$reqid = vtlib_purify($_REQUEST['id']);
$atpoint = vtlib_purify($_REQUEST['atpoint']);

// Calculate the paging before hand
$prevAtPoint = ($atpoint + 1);
$nextAtPoint = ($atpoint - 1);

$trackrecord = false;
if($_REQUEST['mode'] == 'history') {
	// Retrieve the track record at required point
	$trackrecord = ModTracker_Basic::getByCRMId($reqid, $atpoint);
	// If there is no more older records, show the last record itself
	if($trackrecord === false && $atpoint > 0) {
		$atpoint = $atpoint - 1;
		$prevAtPoint = $atpoint; // Singal no more previous
		$trackrecord = ModTracker_Basic::getByCRMId($reqid, $atpoint);
	}
} else {
	$trackrecord = ModTracker_Basic::getById($reqid);
}

if($trackrecord === false || !$trackrecord->exists()) {
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'ShowDiffNotExist.tpl'));
} else {
	if ($trackrecord && $trackrecord->isViewPermitted()) {
		$smarty->assign('TRACKRECORD', $trackrecord);

		$smarty->assign("ATPOINT", $atpoint);
		$smarty->assign("ATPOINT_PREV", $prevAtPoint);
		$smarty->assign("ATPOINT_NEXT", $nextAtPoint);

		$smarty->display(vtlib_getModuleTemplate($currentModule, 'ShowDiff.tpl'));

	} else{
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'ShowDiffDenied.tpl'));
	}
}
?>