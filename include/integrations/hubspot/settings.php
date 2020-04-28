<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Hubspot Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/hubspot/HubSpot.php';

$smarty = new vtigerCRM_Smarty();
$hs = new corebos_hubspot();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['clientId'])) {
	$isActive = ((empty($_REQUEST['hubspot_active']) || $_REQUEST['hubspot_active']!='on') ? '0' : '1');
	$msSync = ((empty($_REQUEST['hubspot_mssync']) || $_REQUEST['hubspot_mssync']!='on') ? '0' : '1');
	$clientId = (empty($_REQUEST['clientId']) ? '' : vtlib_purify($_REQUEST['clientId']));
	$oauthclientId = (empty($_REQUEST['oauthclientId']) ? '' : vtlib_purify($_REQUEST['oauthclientId']));
	$clientSecret = (empty($_REQUEST['clientSecret']) ? '' : vtlib_purify($_REQUEST['clientSecret']));
	$API_URL = (empty($_REQUEST['API_URL']) ? '' : vtlib_purify($_REQUEST['API_URL']));
	$pollFrequency = (empty($_REQUEST['pollFrequency']) ? '360' : vtlib_purify($_REQUEST['pollFrequency']));
	$relateDealWith = (empty($_REQUEST['relateDealWith']) ? 'Contacts' : vtlib_purify($_REQUEST['relateDealWith']));
	$hs->saveSettings($isActive, $clientId, $oauthclientId, $clientSecret, $API_URL, $pollFrequency, $relateDealWith, $msSync);
	$hs->unregisterEvents();
	if ($isActive=='1') {
		$hs->activateFields();
		$hs->registerEvents();
	} else {
		$hs->deactivateFields();
	}
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('HubSpot Activation', $currentModule));
$hssettings = $hs->getSettings();
$smarty->assign('isActive', $hs->isActive());
$smarty->assign('mssyncActive', $hssettings['masterslaveSync']);
$smarty->assign('clientId', $hssettings['clientId']);
$smarty->assign('oauthclientId', $hssettings['oauthclientId']);
$smarty->assign('clientSecret', $hssettings['clientSecret']);
$smarty->assign('API_URL', $hssettings['API_URL']);
$smarty->assign('pollFrequency', $hssettings['pollFrequency']);
$smarty->assign('relateDealWith', $hssettings['relateDealWith']);
$smarty->assign('OAUTHURL', $hs->getIntegrationAuthorizationURL());
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/hubspot.tpl');
?>
