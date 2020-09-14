<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : coreBOS Logging Extension
 *  Version   : 1.0
 *************************************************************************************************/
global $current_user, $adb;

$smarty = new vtigerCRM_Smarty();

if ($_REQUEST['_op']=='setconfigloginsync' && is_admin($current_user)) {
	$srvs = array_filter(array_unique($_REQUEST['server']), function ($var) {
		return !empty(trim($var));
	});
	$em = new VTEventsManager($adb);
	if (count($srvs)>0) {
		coreBOS_Settings::setSetting('cbwsLoginSyncServers', implode(',', $srvs));
		if (!empty($_REQUEST['srvsetpk']) && !empty($_REQUEST['spkey'])) {
			$serverID = preg_replace('/[^a-zA-Z0-9_]/', '', vtlib_purify($_REQUEST['srvsetpk']));
			coreBOS_Settings::setSetting('cbwsLoginSync'.$serverID, vtlib_purify($_REQUEST['spkey']));
		}
		$em->registerHandler('corebos.login', 'include/integrations/loginsync/loginsynchandler.php', 'cbwsLoginSyncHandler');
		$em->registerHandler('corebos.logout', 'include/integrations/loginsync/loginsynchandler.php', 'cbwsLoginSyncHandler');
	} else {
		$em->unregisterHandler('cbwsLoginSyncHandler');
	}
}

$syncServers = explode(',', coreBOS_Settings::getSetting('cbwsLoginSyncServers', ''));
$serverInfo = array();
foreach ($syncServers as $serverSite) {
	if (empty($serverSite)) {
		continue;
	}
	$serverID = preg_replace('/[^a-zA-Z0-9_]/', '', $serverSite);
	$serverInfo[$serverSite] = (coreBOS_Settings::getSetting('cbwsLoginSync'.$serverID, '')!='');
}
$smarty->assign('TITLE_MESSAGE', getTranslatedString('Login Sync Activation', $currentModule));
$smarty->assign('SERVERS', $serverInfo);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/loginsync.tpl');
?>
