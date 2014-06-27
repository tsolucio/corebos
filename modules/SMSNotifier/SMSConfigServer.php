<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('Smarty_setup.php');

include_once dirname(__FILE__) . '/SMSNotifier.php';

global $theme, $currentModule, $mod_strings, $app_strings, $current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty();
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MODULE_LBL",$currentModule);
// Operation to be restricted for non-admin users.
if(!is_admin($current_user)) {	
	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));	
} else {

	$mode = $_REQUEST['mode'];
	if(empty($mode)) {
		
		$smarty->assign('SMSSERVERS', SMSNotifierManager::listConfiguredServers());
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSConfigServerList.tpl'));
		
	} else if($mode == 'Edit') {
		
		$record = vtlib_purify($_REQUEST['record']);
		if(empty($record)) {
			$smarty->assign('SMSSERVERINFO', array());
			$smarty->assign('SMSSERVERPARAMS', $smsserverparams);
		} else {
			$smsserverinfo = SMSNotifierManager::listConfiguredServer($record);
			$smsserverparams = array();
			if(!empty($smsserverinfo['parameters'])) {
				$smsserverparams = Zend_Json::decode($smsserverinfo['parameters']);
			}
			$smarty->assign('SMSSERVERINFO', $smsserverinfo);			
			$smarty->assign('SMSSERVERPARAMS', $smsserverparams);
				
		}
		$smsproviders = SMSNotifierManager::listAvailableProviders();
		
		// Collect required parameters to be made available in the EditForm
		$smsproviderparams = array();
		if(!empty($smsproviders)) {
			foreach($smsproviders as $smsprovidername) {
				$smsprovider = SMSProvider::getInstance($smsprovidername);
				$requiredparameters = $smsprovider->getRequiredParams();
				if(!empty($requiredparameters)) {
					$smsproviderparams[$smsprovidername] = $requiredparameters;
				}
			}
		}
		$smarty->assign('SMSPROVIDERS', $smsproviders);
		$smarty->assign('SMSPROVIDERSPARAMS', $smsproviderparams);
		
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSConfigServerEdit.tpl'));
		
	} else if($mode == 'Save') {

		SMSNotifierManager::updateConfiguredServer($_REQUEST['smsserver_id'], $_REQUEST);
		
		$smarty->assign('SMSSERVERS', SMSNotifierManager::listConfiguredServers());
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSConfigServerListContents.tpl'));
		
	} else if($mode == 'Delete') {
		
		SMSNotifierManager::deleteConfiguredServer(vtlib_purify($_REQUEST['record']));
		
		$smarty->assign('SMSSERVERS', SMSNotifierManager::listConfiguredServers());
		$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSConfigServerListContents.tpl'));
		
	}
}

?>