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

$isAppActive = (coreBOS_Settings::getSetting('LOGALL_ACTIVE', '0')=='1');

if ($_REQUEST['_op']=='setconfiglogall' && is_admin($current_user)) {
	$isAppActive = ((empty($_REQUEST['logall_active']) || $_REQUEST['logall_active']!='on') ? '0' : '1');
	coreBOS_Settings::setSetting('LOGALL_ACTIVE', $isAppActive);

	$em = new VTEventsManager($adb);
	if ($isAppActive) {
		$em->registerHandler('corebos.audit.action', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('corebos.audit.authenticate', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('corebos.audit.login', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('corebos.audit.logout', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('corebos.audit.login.attempt', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('vtiger.entity.aftersave.final', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('vtiger.entity.beforedelete', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('vtiger.entity.afterrestore', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('corebos.entity.link.after', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
		$em->registerHandler('corebos.entity.link.delete', 'include/integrations/logall/logallhandler.php', 'cbLogAllHandler');
	} else {
		$em->unregisterHandler('cbLogAllHandler');
	}
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('LogAll Activation', $currentModule));
$smarty->assign('isActive', $isAppActive);
$smarty->assign('LAActive', $isAppActive ? 'LogAll_Active' : 'LogAll_Inactive');
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/logall.tpl');
?>
