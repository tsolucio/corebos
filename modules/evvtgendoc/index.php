<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : GenDoc:: Advanced Open Office Merge
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/Users/Users.php';
require_once 'modules/evvtgendoc/OpenDocument.php';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
global $currentModule, $theme, $app_strings, $adb;
$modules = VTWorkflowUtils::vtGetModules($adb);
$modssorted = $modsfixed = array(
	'Accounts',
	'Contacts',
	'Vendors',
	'Invoice',
	'Quotes',
	'SalesOrder',
	'PurchaseOrder',
	'HelpDesk',
	'Project',
);
foreach ($modules as $mod) {
	if (!in_array($mod, $modsfixed)) {
		$modssorted[] = $mod;
	}
}
$clangs = array();
foreach (glob('modules/evvtgendoc/commands_*.php') as $tcode) {
	$clangs[] = substr($tcode, 28, 2);
}
$smarty = new vtigerCRM_Smarty;
$smarty->assign('COMPILELANGS', $clangs);
$smarty->assign('USERLANG', substr($current_language, 0, 2));
$smarty->assign('MODULE', $currentModule);
$smarty->assign('MODULES', $modssorted);
$smarty->assign('THEME', $theme);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$tool_buttons = array(
	'EditView' => 'no',
	'CreateView' => 'no',
	'index' => 'yes',
	'Import' => 'no',
	'Export' => 'no',
	'Merge' => 'no',
	'DuplicatesHandling' => 'no',
	'Calendar' => 'no',
	'moduleSettings' => 'no',
);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$smarty->display('modules/evvtgendoc/index.tpl');
?>