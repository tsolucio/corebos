<?PHP
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('Smarty_setup.php');
require_once("include/utils/utils.php");
require_once("modules/com_vtiger_workflow/VTWorkflowUtils.php");

global $mod_strings, $app_strings, $theme, $adb;
$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", "$theme");
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");

// Operation to be restricted for non-admin users.
global $current_user;
if(!is_admin($current_user)) {	
	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));	
} else {
	$module = vtlib_purify($_REQUEST['formodule']);

	$menu_array = Array();
	
	if(VTWorkflowUtils::checkModuleWorkflow($module)){
		$sql_result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND active=0",array('LBL_WORKFLOW_LIST'));
			if($adb->num_rows($sql_result) > 0) {
				$menu_array['Workflow']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&list_module='.$module;
				$menu_array['Workflow']['image_src'] = vtiger_imageurl($adb->query_result($sql_result, 0, 'iconpath'), $theme);
				$menu_array['Workflow']['desc'] = getTranslatedString($adb->query_result($sql_result, 0, 'description'),'com_vtiger_workflow');
				$menu_array['Workflow']['label'] = getTranslatedString($adb->query_result($sql_result, 0, 'name'),'com_vtiger_workflow');
			}
	}
	
	// Few more configuration
	$menu_array['SMS_SERVER_CONFIGURATION']['location'] = 'index.php?module=SMSNotifier&action=SMSConfigServer&parenttab=Settings&formodule=SMSNotifier';
	$menu_array['SMS_SERVER_CONFIGURATION']['image_src']= 'themes/images/proxy.gif';
	$menu_array['SMS_SERVER_CONFIGURATION']['desc']     = getTranslatedString('SERVER_CONFIGURATION_DESCRIPTION', $module);
	$menu_array['SMS_SERVER_CONFIGURATION']['label']     = getTranslatedString('SERVER_CONFIGURATION', $module); 
	
	
	//add blanks for 3-column layout
	$count = count($menu_array)%3;
	if($count>0) {
		for($i=0;$i<3-$count;$i++) {
			$menu_array[] = array();
		}
	}
	
	$smarty->assign('MODULE',$module);
	$smarty->assign('MODULE_LBL',getTranslatedString($module));
	$smarty->assign('MENU_ARRAY', $menu_array);

	$smarty->display(vtlib_getModuleTemplate('Vtiger','Settings.tpl'));
}

?>
