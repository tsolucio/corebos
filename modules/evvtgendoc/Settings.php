<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/utils/utils.php';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

global $mod_strings, $app_strings, $theme, $adb, $current_user;
$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

// Operation to be restricted for non-admin users.
if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'gendoc_server') {
		$active = 0;
		if (array_key_exists('active', $_REQUEST) && $_REQUEST['active'] == 'on') {
			$active = 1;
		}
		coreBOS_Settings::setSetting('cbgendoc_active', $active);
		coreBOS_Settings::setSetting('cbgendoc_server', $_REQUEST['server']);
		coreBOS_Settings::setSetting('cbgendoc_user', $_REQUEST['user']);
		coreBOS_Settings::setSetting('cbgendoc_accesskey', $_REQUEST['key']);
	}
	if (isset($_REQUEST['mode'])) {
		if (isset($_REQUEST['pdflinkactive']) && $_REQUEST['pdflinkactive'] == 'on') {
			coreBOS_Settings::setSetting('cbgendoc_showpdflinks', 1);
		} else {
			coreBOS_Settings::delSetting('cbgendoc_showpdflinks');
		}
	}

	$module = empty($_REQUEST['formodule']) ? 'evvtgendoc' : vtlib_purify($_REQUEST['formodule']);

	$menu_array = array();

	$menu_array['CONFIGURATION']['location'] = 'index.php?module=evvtgendoc&action=BasicSettings&formodule=evvtgendoc';
	$menu_array['CONFIGURATION']['image_src']= 'modules/evvtgendoc/images/oomerge.jpg';
	$menu_array['CONFIGURATION']['desc'] = getTranslatedString('LBL_CONFIGURATION_DESCRIPTION', 'evvtgendoc');
	$menu_array['CONFIGURATION']['label']= getTranslatedString('LBL_evvtgendoc_SETTINGS', 'evvtgendoc');

	$menu_array['SERVERCONFIGURATION']['location'] = 'index.php?module=evvtgendoc&action=ServerSettings&formodule=evvtgendoc';
	$menu_array['SERVERCONFIGURATION']['image_src']= 'modules/evvtgendoc/images/gendoc_server.png';
	$menu_array['SERVERCONFIGURATION']['desc'] = getTranslatedString('LBL_SERVERCONFIGURATION_DESCRIPTION', 'evvtgendoc');
	$menu_array['SERVERCONFIGURATION']['label']= getTranslatedString('LBL_evvtgendoc_SERVERSETTINGS', 'evvtgendoc');

	//add blanks for 3-column layout
	$count = count($menu_array)%3;
	if ($count>0) {
		for ($i=0; $i<3-$count; $i++) {
			$menu_array[] = array();
		}
	}

	$smarty->assign('MODULE', $module);
	$smarty->assign('MODULE_LBL', getTranslatedString($module, $module));
	$smarty->assign('MENU_ARRAY', $menu_array);

	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'Settings.tpl'));
}
?>
