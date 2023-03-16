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
require_once 'VTWorkflowManager.inc';
require_once 'VTWorkflowUtils.php';

function vtDeleteWorkflow($adb, $request) {
	$util = new VTWorkflowUtils();
	$module = new VTWorkflowApplication('deleteworkflow');

	if (!$util->checkAdminAccess()) {
		$errorUrl = $module->errorPageUrl(getTranslatedString('LBL_ERROR_NOT_ADMIN', $module->name));
		$util->redirectTo($errorUrl, getTranslatedString('LBL_ERROR_NOT_ADMIN', $module->name));
		return;
	}

	$wm = new VTWorkflowManager($adb);
	$affected = $wm->delete($request['workflow_id']);
	if ($affected==0) {
		if (isset($request['mode']) && $request['mode'] == 'ajax') {
			echo json_encode(false);
			return;
		}
		$errorUrl = $module->errorPageUrl(getTranslatedString('LBL_PERMISSION', $module->name));
		$util->redirectTo($errorUrl, getTranslatedString('LBL_PERMISSION', $module->name));
		return;
	}
	if (isset($request['mode']) && $request['mode'] == 'ajax') {
		echo json_encode(true);
		return;
	}
	if (isset($request['return_url'])) {
		$returnUrl=$request['return_url'];
	} else {
		$returnUrl=$module->listViewUrl();
	}
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo $returnUrl?>";
	</script>
	<?php
}
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST[$GLOBALS['csrf']['input-name']] = empty($_REQUEST[$GLOBALS['csrf']['input-name']]) ? '' : $_REQUEST[$GLOBALS['csrf']['input-name']];
Vtiger_Request::validateRequest();
$_SERVER['REQUEST_METHOD'] = 'GET';
vtDeleteWorkflow($adb, $_REQUEST);
?>