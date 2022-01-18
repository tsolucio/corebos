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

function vtDeleteWorkflow($adb, $request) {
	global $current_language;
	$util = new VTWorkflowUtils();
	$module = new VTWorkflowApplication('deletetask');
	$mod = return_module_language($current_language, $module->name);
	$request = vtlib_purify($request);  // this cleans all values of the array
	if (!$util->checkAdminAccess()) {
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}
	$wm = new VTTaskManager($adb);
	$wfid = $wm->deleteTask($request['task_id']);
	// We should empty cb_messagequeue of any message related to this task but that is not supported
	if (isset($request['return_url'])) {
		$returnUrl=$request['return_url'];
	} else {
		$module->setReturnUrl('');
		$returnUrl=$module->editWorkflowUrl($wfid);
	}
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo urldecode($returnUrl)?>";
	</script>
	<?php
}
vtDeleteWorkflow($adb, $_REQUEST);
?>