<?php
/************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of CobroPago vtiger CRM Extension.
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
		$adb->query('delete from vtiger_cobropagoconfig');
		$adb->pquery('insert into vtiger_cobropagoconfig values(?,?,?,?)', array(0, 0, 0, isset($_POST['block_paid']) ? 'on' : '0'));
	}
	$results = $adb->query('select * from vtiger_cobropagoconfig');
	$ts_bpaid = $adb->query_result($results, 0, 'block_paid');
	$smarty->assign('ts_bpaid', ($ts_bpaid ? 'checked' : ''));
	$smarty->display('SetMenu.tpl');
	$smarty->display('modules/CobroPago/CyPEdit.tpl');
}
?>