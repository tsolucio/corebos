<?php
/*+***********************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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
 *************************************************************************************/
require_once 'Smarty_setup.php';

global $theme, $currentModule, $mod_strings, $app_strings, $current_user, $current_language;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty();
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('MODULE_LBL', $currentModule);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
// Operation to be restricted for non-admin users.
if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	$smarty->assign('THEME', $theme);
	$smarty->assign('coreBOSOnDemandActive', $coreBOSOnDemandActive);
	$smarty->assign('LICENSEFILE', "modules/Documents/language/$current_language.showLicense.html");
	$sistoragesize = coreBOS_Settings::getSetting('cbod_storagesize', 0);
	$sistoragesizelimit = coreBOS_Settings::getSetting('cbod_storagesizelimit', $cbodStorageSizeLimit);
	$newsize = isset($_REQUEST['storagenewsize']) ? vtlib_purify($_REQUEST['storagenewsize']) : $sistoragesizelimit;
	if (empty($newsize)) {
		$newsize = $sistoragesizelimit;
	}
	$mode = isset($_REQUEST['mode']) ? trim(vtlib_purify($_REQUEST['mode'])) : '';
	if (!empty($mode) && $mode == trim($app_strings['LBL_SAVE_BUTTON_LABEL'])) {
		if ($newsize >= $sistoragesizelimit) {
			coreBOS_Settings::setSetting('cbod_storagesizelimit', $newsize);
			$sistoragesizelimit = $newsize;
		} else {
			echo '<div id="errorcontainer" style="padding:20px">
				<div id="errormsg" style="color: #f85454; font-weight: bold; padding: 10px; border: 1px solid #FF0000; background: #FFFFFF; -moz-border-radius: 5px; margin-bottom: 10px;">'.
				getTranslatedString('StorageMustIncrement', $currentModule).'</div></div>';
		}
	}
	$smarty->assign('SISTORAGESIZE', $sistoragesize);
	$smarty->assign('SISTORAGESIZELIMIT', $sistoragesizelimit);
	$smarty->display('modules/Documents/StorageConfig.tpl');
}
?>