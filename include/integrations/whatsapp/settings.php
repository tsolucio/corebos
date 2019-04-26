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
 *  Module    : Whatsapp Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/whatsapp/whatsapp.php';

$smarty = new vtigerCRM_Smarty();
$wa = new corebos_whatsapp();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['sid'])) {
	$isActive = ((empty($_REQUEST['whatsapp_active']) || $_REQUEST['whatsapp_active']!='on') ? '0' : '1');
	$sid = (empty($_REQUEST['sid']) ? '' : vtlib_purify($_REQUEST['sid']));
	$token = (empty($_REQUEST['token']) ? '' : vtlib_purify($_REQUEST['token']));
	$senderphone = (empty($_REQUEST['senderphone']) ? '' : vtlib_purify($_REQUEST['senderphone']));
	$wa->saveSettings($isActive, $sid, $token, $senderphone);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Whatsapp Activation', $currentModule));
$wasettings = $wa->getSettings();
$smarty->assign('isActive', $wa->isActive());
$smarty->assign('sid', $wasettings['sid']);
$smarty->assign('token', $wasettings['token']);
$smarty->assign('senderphone', $wasettings['senderphone']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/whatsapp.tpl');
?>
