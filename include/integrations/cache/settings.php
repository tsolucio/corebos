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
 * ************************************************************************************************
 *  Module    : PSR-16 cache integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/integrations/cache/cache.php';

$smarty = new vtigerCRM_Smarty();
$cache = new corebos_cache();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['adapter_type'])) {
	$isActive = ((empty($_REQUEST['cache_active']) || $_REQUEST['cache_active']!='on') ? '0' : '1');
	$adapterType = (empty($_REQUEST['adapter_type']) ? '' : vtlib_purify($_REQUEST['adapter_type']));
	$ip = (empty($_REQUEST['ip']) ? '' : vtlib_purify($_REQUEST['ip']));
	$port = (empty($_REQUEST['port']) ? '' : vtlib_purify($_REQUEST['port']));
	if ($adapterType == corebos_cache::ADAPTER_MEMORY) {
		$ip = "";
		$port = "";
	} elseif (empty($ip) || empty($port)) {
		$adapterType = corebos_cache::ADAPTER_MEMORY;
		$ip = "";
		$port = "";
	}
	$cache->saveSettings($isActive, $adapterType, $ip, $port);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Cache Activation', $currentModule));
$cacheSettings = $cache->getSettings();
$smarty->assign('isActive', $cache->isActive());
$smarty->assign('adapter', $cacheSettings['adapter']);
$smarty->assign('ip', $cacheSettings['ip']);
$smarty->assign('port', $cacheSettings['port']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
require 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/cache.tpl');
?>
