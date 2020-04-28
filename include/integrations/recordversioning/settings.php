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
 *  Module    : Record Versioning Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
global $current_user, $adb;
require_once 'include/integrations/recordversioning/RecordVersionUtils.php';

$smarty = new vtigerCRM_Smarty();

$tabid = isset($_REQUEST['tabid']) ? vtlib_purify($_REQUEST['tabid']) : 0;
$moduleid = getTabModuleName($tabid);
$rvutil = new RecordVersionUtils($moduleid);

if (!empty($moduleid) && $_REQUEST['_op']=='setconfigrecordversioning') {
	$isFormActive = ((empty($_REQUEST['onoroff']) || $_REQUEST['onoroff']!='true') ? '0' : '1');
	if ($isFormActive=='1') {
		$rvutil->enableRecordVersionForModule();
	} else {
		$rvutil->disableRecordVersionForModule();
	}
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Record Versioning', $currentModule));
$smarty->assign('MODULELIST', $rvutil::recverGetModuleinfo());
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/recordversioning.tpl');
?>
