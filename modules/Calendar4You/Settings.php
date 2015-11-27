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

global $mod_strings, $app_strings, $theme, $adb, $current_user;
$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD',$mod_strings);
$smarty->assign('APP',$app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

// Operation to be restricted for non-admin users.
if(!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));
} else {
	$module = vtlib_purify($_REQUEST['formodule']);

	$menu_array = Array();

	$menu_array['CustomFields']['location'] = 'index.php?module=Settings&action=CustomFieldList&parenttab=Settings&formodule='.$module;
	$menu_array['CustomFields']['image_src'] = vtiger_imageurl('orgshar.gif', $theme);
	$menu_array['CustomFields']['desc'] = getTranslatedString('LBL_CALENDER_CUSTOMFIELDS_DESCRIPTION');
	$menu_array['CustomFields']['label'] = getTranslatedString('LBL_CALENDER_CUSTOMFIELDS');
	$menu_array["Privilegies"]["location"] = "index.php?module=".$module."&action=ProfilesPrivilegies&parenttab=Settings";
	$menu_array["Privilegies"]["image_src"] = "themes/images/ico-profile.gif";
	$menu_array["Privilegies"]["desc"] = getTranslatedString("LBL_PROFILES_DESC",$module);
	$menu_array["Privilegies"]["label"] = getTranslatedString("LBL_PROFILES",$module);
	if(VTWorkflowUtils::checkModuleWorkflow($module)){
		$sql_result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND active=0",array('LBL_WORKFLOW_LIST'));
		if($adb->num_rows($sql_result) > 0) {
			$menu_array['Workflow']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&list_module='.$module;
			$menu_array['Workflow']['image_src'] = vtiger_imageurl($adb->query_result($sql_result, 0, 'iconpath'), $theme);
			$menu_array['Workflow']['desc'] = getTranslatedString($adb->query_result($sql_result, 0, 'description'),'com_vtiger_workflow');
			$menu_array['Workflow']['label'] = getTranslatedString($adb->query_result($sql_result, 0, 'name'),'com_vtiger_workflow');
		}
	}
	//add blanks for 3-column layout
	$count = count($menu_array)%3;
	if($count>0) {
		for($i=0;$i<3-$count;$i++) {
			$menu_array[] = array();
		}
	}

	$smarty->assign('MODULE',$module);
	$smarty->assign('MODULE_LBL',getTranslatedString($module,$module));
	$smarty->assign('MENU_ARRAY', $menu_array);

	$smarty->display(vtlib_getModuleTemplate('Vtiger','Settings.tpl'));
}
?>