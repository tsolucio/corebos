<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb;
require_once 'Smarty_setup.php';
require_once 'VTWorkflowApplication.inc';
require_once 'VTWorkflowTemplateManager.inc';
require_once 'VTWorkflowUtils.php';

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once "modules/$currentModule/$currentModule.php";
$smarty = new vtigerCRM_Smarty();
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$util = new VTWorkflowUtils();
$edit_return_url = 'index.php?module=com_vtiger_workflow&action=workflowlist';
$module = new VTWorkflowApplication('saveworkflow', $edit_return_url);
$mod = return_module_language($current_language, $module->name);
if (!$util->checkAdminAccess()) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	exit;
}
$smarty->assign('MODULE_NAME', $module->label);
$smarty->assign('module', $module);
$smarty->assign('MODULE', $module->name);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
if (isset($_FILES) && isset($_FILES['wfimportfile']) && is_uploaded_file($_FILES['wfimportfile']['tmp_name'])) {
	$files = $_FILES['wfimportfile'];
	if ($files['name'] != '' && $files['size'] > 0) {
		$upload_status = @move_uploaded_file($files['tmp_name'], 'cache/import/wfimport.csv');
		if ($upload_status) {
			$row = 0;
			$imports = array();
			if (($handle = fopen('cache/import/wfimport.csv', 'r')) !== false) {
				$wfm= new VTworkflowManager($adb);
				$url= 'index.php?module=com_vtiger_workflow&action=editworkflow&return_url=index.php%3Fmodule%3Dcom_vtiger_workflow%26action%3Dworkflowlist&workflow_id=';
				while (($data = fgetcsv($handle, 1000, ',')) !== false) {
					if ($row==0 && $data[0] != 'workflow_id') {
						break;
					}
					if ($row==0) {
						$row++;
						continue;
					}
					$error = false;
					if (!empty($data[0])) {
						$wfs = base64_decode($data[0]);
						if ($wfs) {
							$workflow = $wfm->deserializeWorkflow($wfs);
							$imports[] = array(
								'row' => $row,
								'url' => $url.$workflow->id,
								'summary' => $data[2],
								'result' => true,
							);
						} else {
							$error = true;
						}
					} else {
						$error = true;
					}
					if ($error) {
						$imports[] = array(
							'row' => $row,
							'url' => '',
							'summary' => getTranslatedString('ERR_CannotProcess', 'com_vtiger_workflow'),
							'result' => false,
						);
					}
					$row++;
				}
				fclose($handle);
				if ($row>0) {
					$smarty->assign('IMPRDO', $imports);
					$smarty->display('com_vtiger_workflow/ImportResult.tpl');
					exit;
				}
			}
		}
	}
}
$module = new VTWorkflowApplication('workflowlist');
$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERR_IncorrectFile', 'com_vtiger_workflow'));
$smarty->assign('MODULE_NAME', $module->label);
$smarty->assign('module', $module);
$smarty->assign('MODULE', $module->name);
$smarty->display('com_vtiger_workflow/Import.tpl');
?>