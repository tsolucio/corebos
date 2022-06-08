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

function onoffTask($adb, $request) {
	if ($request['isactive'] == 1) {
		$status = 0;
	} else {
		$status = 1;
	}
	$vtw = new VTWorkflowApplication('edittask');
	$tm = new VTTaskManager($adb);
	$task = $tm->retrieveTask($request['task_id']);
	$task->active = $status;
	$tm->saveTask($task);
	$vtw->setReturnUrl('');
	$returnUrl = $vtw->editWorkflowUrl($task->workflowId);
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo $returnUrl?>";
	</script>
	<?php
}
Vtiger_Request::validateRequest();
onoffTask($adb, $_REQUEST);
?>