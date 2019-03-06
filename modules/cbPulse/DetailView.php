<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

require_once 'modules/Vtiger/DetailView.php';
$res = $adb->pquery(
	"select workflowid from vtiger_cbpulse WHERE cbpulseid=?",
	array($focus->id)
);
$workflowId = (int)(vtlib_purify($res->fields['workflowid']));
$wfs = new VTWorkflowManager($adb);
if (!empty($workflowId)) {
	$workflowres = $adb->pquery(
		"select * from com_vtiger_workflows WHERE workflow_id = ?",
		array($workflowId)
	);
	$workflows = $wfs->getWorkflowsForResult($workflowres);
	$workflow = $workflows[$workflowId];
	$nxttTime = $workflow->getNextTriggerTime();
	$tflabel = getTranslatedString('nexttrigger_time', $currentModule);
	$idx = getFieldFromDetailViewBlockArray($blocks, $tflabel);
	$blocks[$idx['block_label']][$idx['field_key']][$tflabel]['value'] = $nxttTime;
	$smarty->assign('BLOCKS', $blocks);
}
$smarty->display('DetailView.tpl');
?>