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
if ($isadmin && $_REQUEST['_op'] =='setconfigdenormalization' && $_REQUEST['denormalize_isactive']=='on') {
	$isActive = ((empty($_REQUEST['denormalize_isactive']) || $_REQUEST['denormalize_isactive']!='on') ? 0 : 1);
	$selectedModuleList = (empty($_REQUEST['denor_mods']) ? array() : $_REQUEST['denor_mods']);
	if (count($selectedModuleList) > 0) {
		$denormalize_res = $cbosDenormalize->dernormalizeModules($selectedModuleList);
	}
	$cbosDenormalize->saveSettings(
		$isActive,
		json_encode($selectedModuleList)
	);
}
$allmodules = $cbosDenormalize->denormGetAllModules();
$smarty->assign('TITLE_MESSAGE', getTranslatedString('Denormalization Activation', $currentModule));
$dmsettings = $cbosDenormalize->getSettings();
$denormodulelist = empty($dmsettings['denormodule_list']) ? array():json_decode($dmsettings['denormodule_list']);
$smarty->assign('isActive', $cbosDenormalize->isActive());
$smarty->assign('modulelist', $allmodules);
$smarty->assign('denormodulelist', $denormodulelist);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/denormalizemodule.tpl');