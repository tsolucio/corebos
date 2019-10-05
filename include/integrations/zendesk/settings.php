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
 *  Module    : Zendesk Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/zendesk/Zendesk.php';

$smarty = new vtigerCRM_Smarty();
$zd = new corebos_zendesk();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['zendesk_active'])) {
	$isActive = ((empty($_REQUEST['zendesk_active']) || $_REQUEST['zendesk_active']!='on') ? '0' : '1');
	$API_URL = (empty($_REQUEST['API_URL']) ? '' : vtlib_purify($_REQUEST['API_URL']));
	$accessCode = (empty($_REQUEST['accessCode']) ? '' : vtlib_purify($_REQUEST['accessCode']));
	$username = (empty($_REQUEST['username']) ? '' : vtlib_purify($_REQUEST['username']));
	$zd->saveSettings($isActive, $API_URL, $accessCode, $username);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Zendesk Activation', $currentModule));
$zdsettings = $zd->getSettings();
$smarty->assign('isActive', $zd->isActive());
$smarty->assign('API_URL', $zdsettings['API_URL']);
$smarty->assign('accessCode', $zdsettings['accessCode']);
$smarty->assign('username', $zdsettings['username']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/zendesk.tpl');
?>
