<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/utils/CommonUtils.php';
require_once 'include/events/SqlResultIterator.inc';
require_once 'VTWorkflowApplication.inc';
require_once 'VTWorkflow.php';
require_once 'VTWorkflowUtils.php';
require_once 'Smarty_setup.php';

Vtiger_Request::validateRequest();
$wfclass = ($_REQUEST['wfclass']);
$classpath = 'modules/com_vtiger_workflow/tasks/'.$wfclass;
if (!empty($wfclass) && (file_exists($classpath.'.php') || file_exists($classpath.'.inc'))) {
	include_once $classpath.'.php';
	include_once $classpath.'.inc';
	$wf = new $wfclass;
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('HELPHEADER', getTranslatedString($wfclass, 'com_vtiger_workflow'));
	$smarty->assign('HELPDESC', getTranslatedString('HELP_'.$wfclass, 'com_vtiger_workflow'));
	if (method_exists($wf, 'getContextVariables')) {
		$smarty->assign('HELPCTX', $wf->getContextVariables());
	} else {
		$smarty->assign('HELPCTX', []);
	}
	$smarty->display('com_vtiger_workflow/help.tpl');
}
?>