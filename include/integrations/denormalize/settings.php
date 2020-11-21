<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module    : Denormalize Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/denormalize/denormalize.php';

$smarty = new vtigerCRM_Smarty();
$cbosDenormalize = new corebos_denormalize();
global $adb, $current_user;
$isadmin = is_admin($current_user);
if ($isadmin && $_REQUEST['_op'] =='setconfigdenormalization' && $_REQUEST['denorm_op']) {
	if ($_REQUEST['denorm_op'] == 'denorm') {
		$selectedModuleList = (empty($_REQUEST['denor_mods']) ? array() : $_REQUEST['denor_mods']);
		if (count($selectedModuleList) > 0) {
			$cbosDenormalize->dernormalizeModules($selectedModuleList);
			$updateddenorm_list = $cbosDenormalize->denormGetAllModules('denorm_mod');
			$cbosDenormalize->saveSettings(
				json_encode($updateddenorm_list)
			);
		}
	}
	if ($_REQUEST['denorm_op'] == 'undo_denorm') {
		$selectedModuleList = (empty($_REQUEST['denorm_mod']) ? array() : $_REQUEST['denorm_mod']);
		if (count($selectedModuleList) > 0) {
			$cbosDenormalize->undoDernormalizeModules($selectedModuleList);
			$updateddenorm_list = $cbosDenormalize->denormGetAllModules('denorm_mod');
			$cbosDenormalize->saveSettings(
				json_encode($updateddenorm_list)
			);
			$smarty->assign('denormop', 'undo_denorm');
		}
	}
}

$allmodules = $cbosDenormalize->denormGetAllModules();
$smarty->assign('TITLE_MESSAGE', getTranslatedString('Denormalization Activation', $currentModule));
$dmsettings = $cbosDenormalize->getSettings();
$denormodulelist = $cbosDenormalize->denormGetAllModules('undo_denorm');
$smarty->assign('modulelist', $allmodules);
$smarty->assign('totalmodulelist', count($allmodules));
$smarty->assign('denormodulelist', $denormodulelist);
$smarty->assign('totaldenormodulelist', count($denormodulelist));
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/denormalizemodule.tpl');