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
 *  Module    : Google Contacts Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/GContacts/GContacts.php';

$smarty = new vtigerCRM_Smarty();
$gc = new corebos_gcontacts();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['clientId'])) {
	$isActive = ((empty($_REQUEST['gcontacts_active']) || $_REQUEST['gcontacts_active']!='on') ? '0' : '1');
	$clientId = (empty($_REQUEST['clientId']) ? '' : vtlib_purify($_REQUEST['clientId']));
	$clientSecret = (empty($_REQUEST['clientSecret']) ? '' : vtlib_purify($_REQUEST['clientSecret']));
	$gc->saveSettings($isActive, $clientId, $clientSecret);
	$gc->unregisterEvents();
	if ($isActive=='1') {
		$gc->activateFields();
		$gc->registerEvents();
	} else {
		$gc->deactivateFields();
	}
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('GContacts Activation', $currentModule));
$gcsettings = $gc->getSettings();
$smarty->assign('isActive', $gc->isActive());
$smarty->assign('clientId', $gcsettings['clientId']);
$smarty->assign('clientSecret', $gcsettings['clientSecret']);
$smarty->assign('OAUTHURL', $gc->getIntegrationAuthorizationURL());
$smarty->assign('OAUTHURLMSG', sprintf(getTranslatedString('IntegrationAuthorizationClick', $currentModule), getTranslatedString('GOOGLE_CONTACTS', 'Contacts')));
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/gcontacts.tpl');
?>
