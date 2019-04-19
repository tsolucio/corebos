<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;
$focus = CRMEntity::getInstance($currentModule);

$record = vtlib_purify($_REQUEST['record']);
$module = urlencode(vtlib_purify($_REQUEST['module']));
$return_module = vtlib_purify($_REQUEST['return_module']);
$return_action = urlencode(vtlib_purify($_REQUEST['return_action']));
$return_id = isset($_REQUEST['return_id']) ? vtlib_purify($_REQUEST['return_id']) : '';
$url = getBasic_Advance_SearchURL();
if (!empty($_REQUEST['start']) && !empty($_REQUEST['return_viewname'])) {
	$start = vtlib_purify($_REQUEST['start']);
	$relationId = vtlib_purify($_REQUEST['return_viewname']);
	coreBOS_Session::set('rlvs^'.$return_module.'^'.$relationId.'^start', $start);
}
if (isset($_REQUEST['activity_mode'])) {
	$url .= '&activity_mode='.urlencode(vtlib_purify($_REQUEST['activity_mode']));
}
list($delerror,$errormessage) = DeleteEntity($currentModule, $return_module, $focus, $record, $return_id);
if ($delerror) {
	header("Location: index.php?module=$module&action=DetailView&record=" . urlencode($record) . '&error_msg=' . urlencode($errormessage) . $url);
} else {
	header('Location: index.php?module=' . urlencode($return_module) . "&action=$return_action&record=" . urlencode($return_id) . "&relmodule=$module" . $url);
}
?>