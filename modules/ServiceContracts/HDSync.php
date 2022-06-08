<?php
/************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.  --  This file is a part of CobroPago vtiger CRM Extension.
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

global $theme, $currentModule, $mod_strings, $app_strings, $current_user, $adb;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty();
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('CMOD', $mod_strings);
$smarty->assign('MODULE_LBL', $currentModule);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
// Operation to be restricted for non-admin users.
if (!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'OperationNotPermitted.tpl'));
} else {
	$mode = isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : '';
	if (!empty($mode) && $mode == 'Save') {
		$ev = new VTEventsManager($adb);
		if (isset($_POST['synchd'])) {
			$ev->registerHandler('vtiger.entity.beforesave', 'modules/ServiceContracts/ServiceContractsHandler.php', 'ServiceContractsHandler');
			$ev->registerHandler('vtiger.entity.aftersave', 'modules/ServiceContracts/ServiceContractsHandler.php', 'ServiceContractsHandler');
		} else {
			$ev->unregisterHandler('ServiceContractsHandler');
		}
	}
	$result = $adb->pquery(
		'SELECT is_active FROM vtiger_eventhandlers WHERE event_name=? AND handler_path=? AND handler_class=? limit 1',
		array('vtiger.entity.aftersave', 'modules/ServiceContracts/ServiceContractsHandler.php', 'ServiceContractsHandler')
	);
	$smarty->assign('hdsyncactive', ($adb->num_rows($result)===0 ? '' : 'checked'));
	$smarty->display('modules/ServiceContracts/SyncwithHelpDesk.tpl');
}
?>