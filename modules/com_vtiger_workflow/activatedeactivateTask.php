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
require_once 'VTTaskManager.inc';
require_once 'VTWorkflowUtils.php';

function activatedeactivateTask($adb, $request) {
	$status = $request['active'];
	$vtw = new VTWorkflowApplication('edittask');
	$tm = new VTTaskManager($adb);
	$tasks = $tm->getTasksForWorkflow($request['workflow_id']);
	$returnUrl = '';
	foreach ($tasks as $task) {
		$t = $tm->retrieveTask($task->id);
		$t->active = $status;
		$tm->saveTask($t);
		$vtw->setReturnUrl('');
		$returnUrl = $vtw->editWorkflowUrl($task->workflowId);
	}
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo urldecode($returnUrl); ?>";
	</script>
	<?php
}
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST[$GLOBALS['csrf']['input-name']] = empty($_REQUEST[$GLOBALS['csrf']['input-name']]) ? '' : $_REQUEST[$GLOBALS['csrf']['input-name']];
Vtiger_Request::validateRequest();
$_SERVER['REQUEST_METHOD'] = 'GET';
activatedeactivateTask($adb, $_REQUEST);
?>