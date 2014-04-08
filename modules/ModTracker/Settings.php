<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
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

	// Few more configuration
	$menu_array['CONFIGURATION']['location'] = 'index.php?module=ModTracker&action=BasicSettings&parenttab=Settings&formodule=ModTracker';
	$menu_array['CONFIGURATION']['image_src']= 'themes/images/audit.gif';
	$menu_array['CONFIGURATION']['desc'] = getTranslatedString('LBL_CONFIGURATION_DESCRIPTION', $module);
	$menu_array['CONFIGURATION']['label']= getTranslatedString('LBL_CONFIGURATION', $module);


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
