<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
include_once 'vtlib/Vtiger/Utils.php';

if (isset($_REQUEST['module_settings']) && $_REQUEST['module_settings'] == 'true') {
	$targetmodule = vtlib_purify($_REQUEST['formodule']);

	$targetSettingPage = "modules/$targetmodule/Settings.php";
	if (file_exists($targetSettingPage)) {
		Vtiger_Utils::checkFileAccessForInclusion($targetSettingPage);
		require_once $targetSettingPage;
	}
} else {
	require_once 'Smarty_setup.php';
	global $mod_strings,$app_strings,$theme, $current_user;
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

	$mode = !empty($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';
	$smarty->assign('MODE', $mode);

	//ICONS
	$_ICONS = array('account','action_list_component','actions_and_buttons', 'address','agent_session', 'all', 'announcement', 'answer_best', 'answer_private', 'answer_public'
	);
	//get parent Tabs
	$_MENU = getMenuArray(0);
	$arrmenu = array();
	foreach ($_MENU as $key => $value) {
		if ($value['mparent'] == 0) {
			array_push($arrmenu, $value['mlabel']);
		}
	}
	//get all modules
	$modules = $adb->pquery('SELECT vtiger_modulebuilder.modulebuilder_name as modulebuilder_name, vtiger_modulebuilder_name.date as date, vtiger_modulebuilder_name.completed as completed FROM vtiger_modulebuilder_name JOIN vtiger_modulebuilder ON vtiger_modulebuilder_name.modulebuilderid=vtiger_modulebuilder.modulebuilderid WHERE userid=?', array(
		$current_user->id
	));
	$moduleLists = array();
	for ($i=0; $i < $adb->num_rows($modules); $i++) {
		$_MODULES = array();
		$modulebuilder_name = $adb->query_result($modules, $i, 'modulebuilder_name');
		$date = $adb->query_result($modules, $i, 'date');
		$completed = $adb->query_result($modules, $i, 'completed');
		$_MODULES['modulebuilder_name'] = $modulebuilder_name;
		$_MODULES['date'] = $date;
		if ($completed == 'Completed') {
			$_MODULES['completed'] = $mod_strings['LBL_MB_COMPLETED'];
		} else {
			$_MODULES['completed'] = $completed;
		}
		array_push($moduleLists, $_MODULES);
	}

	$smarty->assign('MODULELISTS', $moduleLists);
	$smarty->assign('ICONS', $_ICONS);
	$smarty->assign('MENU', $arrmenu);
	$smarty->display('Settings/ModuleBuilder/ModuleBuilder.tpl');
}
?>