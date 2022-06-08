<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Mautic Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/mautic/mautic.php';

$smarty = new vtigerCRM_Smarty();
$mautic = new corebos_mautic();

$isadmin = is_admin($current_user);

if ($isadmin && !empty($_REQUEST['baseUrl']) && !empty($_REQUEST['mautic_username']) && !empty($_REQUEST['mautic_password']) && !empty($_REQUEST['mautic_webhook_secret'])) {
	$isActive = ((empty($_REQUEST['mautic_active']) || $_REQUEST['mautic_active']!='on') ? '0' : '1');
	$baseUrl = (empty($_REQUEST['baseUrl']) ? '' : vtlib_purify($_REQUEST['baseUrl']));
	$version = (empty($_REQUEST['version']) ? '' : vtlib_purify($_REQUEST['version']));
	$clientKey = (empty($_REQUEST['clientKey']) ? '' : vtlib_purify($_REQUEST['clientKey']));
	$clientSecret = (empty($_REQUEST['clientSecret']) ? '' : vtlib_purify($_REQUEST['clientSecret']));
	$callback = (empty($_REQUEST['callback']) ? '' : vtlib_purify($_REQUEST['callback']));
	$leadSync = ((empty($_REQUEST['mautic_sync_lead']) || $_REQUEST['mautic_sync_lead']!='on') ? '0' : '1');
	$companiesSync = ((empty($_REQUEST['mautic_sync_companies']) || $_REQUEST['mautic_sync_companies']!='on') ? '0' : '1');
	$username = (empty($_REQUEST['mautic_username']) ? '' : vtlib_purify($_REQUEST['mautic_username']));
	$password = (empty($_REQUEST['mautic_password']) ? '' : vtlib_purify($_REQUEST['mautic_password']));
	$webhook_secret = (empty($_REQUEST['mautic_webhook_secret']) ? '' : vtlib_purify($_REQUEST['mautic_webhook_secret']));
	$mautic->saveSettings($isActive, $baseUrl, $version, $clientKey, $clientSecret, $callback, $leadSync, $companiesSync, $username, $password, $webhook_secret);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Mautic Activation', $currentModule));
$mauticSettings = $mautic->getSettings();
$smarty->assign('isActive', $mautic->isActive());
$smarty->assign('baseUrl', $mauticSettings['baseUrl']);
$smarty->assign('version', $mauticSettings['version']);
$smarty->assign('clientKey', $mauticSettings['clientKey']);
$smarty->assign('clientSecret', $mauticSettings['clientSecret']);
$smarty->assign('callback', $mauticSettings['callback']);
$smarty->assign('isLeadSyncActive', $mauticSettings['leadSync']);
$smarty->assign('isCompaniesSyncActive', $mauticSettings['companiesSync']);
$smarty->assign('mauticUsername', $mauticSettings['userName']);
$smarty->assign('mauticPassword', $mauticSettings['password']);
$smarty->assign('mauticWebhookSecret', $mauticSettings['webhookSecret']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/mautic.tpl');
?>
