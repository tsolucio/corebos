<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : GMP Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/gmp/gmp.php';

$smarty = new vtigerCRM_Smarty();
$gmp = new corebos_gmp();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['gid'])) {
	$isActive = ((empty($_REQUEST['gmp_active']) || $_REQUEST['gmp_active']!='on') ? '0' : '1');
	$gid = (empty($_REQUEST['gid']) ? '' : vtlib_purify($_REQUEST['gid']));
	$gversion = (empty($_REQUEST['gversion']) ? '' : vtlib_purify($_REQUEST['gversion']));
	$result = $gmp->saveSettings($isActive, $gid, $gversion);
}
if (!$result) {
	$smarty->assign('ERROR_MESSAGE_CLASS', 'danger');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('gmp_deacterror', $currentModule));
}
$smarty->assign('TITLE_MESSAGE', getTranslatedString('GMP Activation', $currentModule));
$gmpsettings = $gmp->getSettings();
$smarty->assign('isActive', $gmp->isActive());
$smarty->assign('gid', $gmpsettings['gid']);
$smarty->assign('gversion', $gmpsettings['gversion']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/gmp.tpl');
?>
