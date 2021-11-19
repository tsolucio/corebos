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
	global $mod_strings, $app_strings, $theme, $current_user, $current_language;
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign('LANGUAGE', $current_language);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

	$mode = !empty($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';
	$smarty->assign('MODE', $mode);

	//ICONS
	$iconCategories = array('action', 'custom', 'doctype', 'standard', 'utility');
	$_ICONS = array();
	foreach ($iconCategories as $cat) {
		$icons = glob('include/LD/assets/icons/'.$cat.'/*.svg');
		foreach ($icons as $icon) {
			$_ICONS[] = $cat.'-'.basename($icon, '.svg');
		}
	}
	//get parent Tabs
	$_MENU = getMenuArray(0);
	$arrmenu = array();
	foreach ($_MENU as $key => $value) {
		if ($value['mparent'] == 0) {
			array_push($arrmenu, $value['mlabel']);
		}
	}

	$smarty->assign('ICONS', $_ICONS);
	$smarty->assign('MENU', $arrmenu);
	$smarty->display('Settings/ModuleBuilder/ModuleBuilder.tpl');
}
?>