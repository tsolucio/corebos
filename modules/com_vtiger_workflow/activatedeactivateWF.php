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

function activatedeactivateWorkflow($request) {
	$status = $request['active'];
	if (($status=='true' || $status=='false') && !empty($request['workflow_id']) && is_numeric($request['workflow_id'])) {
		$wf = new Workflow();
		$wf->id = $request['workflow_id'];
		$wf->setActiveStateTo($status);
	}
	if (empty($request['wfajax'])) {
		?>
		<script type="text/javascript" charset="utf-8">
			window.location='index.php?module=com_vtiger_workflow&action=workflowlist';
		</script>
		<?php
	}
}
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST[$GLOBALS['csrf']['input-name']] = empty($_REQUEST[$GLOBALS['csrf']['input-name']]) ? '' : $_REQUEST[$GLOBALS['csrf']['input-name']];
Vtiger_Request::validateRequest();
$_SERVER['REQUEST_METHOD'] = 'GET';
activatedeactivateWorkflow($_REQUEST);
?>