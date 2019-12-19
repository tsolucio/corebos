<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$util = new VTWorkflowUtils();
$module = new VTWorkflowApplication('editworkflow');
$mod = return_module_language($current_language, $module->name);
if (!$util->checkAdminAccess()) {
	$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
	$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
	return;
}

$module->setReturnUrl('index.php%3Fmodule%3Dcom_vtiger_workflow%26action%3Dworkflowlist');
$returnUrl=$module->editWorkflowUrl(vtlib_purify($_REQUEST['record']));
?>
<script type="text/javascript" charset="utf-8">
	window.location="<?php echo urldecode($returnUrl); ?>";
</script>