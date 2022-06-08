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
 *  Module    : ClickHouse Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/clickhouse/clickhouse.php';
include_once 'include/database/ClickHouseDatabase.php';

$smarty = new vtigerCRM_Smarty();
$mu = new corebos_clickhouse();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['clickhouse_active']) && isset($_REQUEST['btnchsave'])) {
	$isActive = ((empty($_REQUEST['clickhouse_active']) || $_REQUEST['clickhouse_active']!='on') ? '0' : '1');
	$clickhouse_database = (empty($_REQUEST['clickhouse_database']) ? '' : vtlib_purify($_REQUEST['clickhouse_database']));
	$clickhouse_username = (empty($_REQUEST['clickhouse_username']) ? '' : vtlib_purify($_REQUEST['clickhouse_username']));
	$clickhouse_password = (empty($_REQUEST['clickhouse_password']) ? '' : vtlib_purify($_REQUEST['clickhouse_password']));
	$clickhouse_host = (empty($_REQUEST['clickhouse_host']) ? '' : vtlib_purify($_REQUEST['clickhouse_host']));
	$clickhouse_port = (empty($_REQUEST['clickhouse_port']) ? '' : vtlib_purify($_REQUEST['clickhouse_port']));
	$mu->saveSettings($isActive, $clickhouse_host, $clickhouse_port, $clickhouse_database, $clickhouse_username, $clickhouse_password);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('ClickHouse Activation', $currentModule));
$musettings = $mu->getSettings();
$smarty->assign('isActive', $mu->isActive());
$smarty->assign('clickhouse_host', $musettings['clickhouse_host']);
$smarty->assign('clickhouse_port', $musettings['clickhouse_port']);
$smarty->assign('clickhouse_database', $musettings['clickhouse_database']);
$smarty->assign('clickhouse_username', $musettings['clickhouse_username']);
$smarty->assign('clickhouse_password', $musettings['clickhouse_password']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
if (!isset($_REQUEST['btnchquery']) || empty($_REQUEST['chquery']) || !$mu->isActive()) {
	$smarty->assign('CHQUERYRDO', []);
} else {
	$rdo = [];
	try {
		$r = $cdb->query($_REQUEST['chquery']);
		$limit = 0;
		while ($rw = $cdb->fetch_array($r)) {
			$rdo[] = json_encode($rw);
			if ($limit++ > 100) {
				break;
			}
		}
	} catch (\Throwable $th) {
		$rdo[] = '<span style="color:red;">'.$cdb->getErrorMsg().'</span>';
	}
	$smarty->assign('CHQUERY', $_REQUEST['chquery']);
	$smarty->assign('CHQUERYRDO', $rdo);
}
$smarty->display('modules/Utilities/clickhouse.tpl');
?>
