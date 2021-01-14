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
 *  Module    : SendGrid Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/sendgrid/sendgrid.php';

$smarty = new vtigerCRM_Smarty();
$sd = new corebos_sendgrid();

$isadmin = is_admin($current_user);

if ($isadmin && $_REQUEST['_op']=='setconfigsendgrid') {
	$isActive = ((empty($_REQUEST['sendgrid_active']) || $_REQUEST['sendgrid_active']!='on') ? '0' : '1');
	$usesg_transactional = (empty($_REQUEST['usesg_transactional']) ? '' : vtlib_purify($_REQUEST['usesg_transactional']));
	$srv_transactional = (empty($_REQUEST['srv_transactional']) ? '' : vtlib_purify($_REQUEST['srv_transactional']));
	$user_transactional = (empty($_REQUEST['user_transactional']) ? '' : vtlib_purify($_REQUEST['user_transactional']));
	$pass_transactional = (empty($_REQUEST['pass_transactional']) ? '' : vtlib_purify($_REQUEST['pass_transactional']));
	$usesg_marketing = (empty($_REQUEST['usesg_marketing']) ? '' : vtlib_purify($_REQUEST['usesg_marketing']));
	$srv_marketing = (empty($_REQUEST['srv_marketing']) ? '' : vtlib_purify($_REQUEST['srv_marketing']));
	$user_marketing = (empty($_REQUEST['user_marketing']) ? '' : vtlib_purify($_REQUEST['user_marketing']));
	$pass_marketing = (empty($_REQUEST['pass_marketing']) ? '' : vtlib_purify($_REQUEST['pass_marketing']));
	$apiurl_transactional = (empty($_REQUEST['apiurl_transactional']) ? '' : vtlib_purify($_REQUEST['apiurl_transactional']));
	$sd->saveSettings(
		$isActive,
		$usesg_transactional,
		$srv_transactional,
		$user_transactional,
		$pass_transactional,
		$usesg_marketing,
		$srv_marketing,
		$user_marketing,
		$pass_marketing,
		$apiurl_transactional
	);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('SendGrid Activation', $currentModule));
$sdsettings = $sd->getSettings();
$smarty->assign('isActive', $sd->isActive());
$smarty->assign('usesg_transactional', $sdsettings['usesg_transactional']);
$smarty->assign('srv_transactional', $sdsettings['srv_transactional']);
$smarty->assign('user_transactional', $sdsettings['user_transactional']);
$smarty->assign('pass_transactional', $sdsettings['pass_transactional']);
$smarty->assign('usesg_marketing', $sdsettings['usesg_marketing']);
$smarty->assign('srv_marketing', $sdsettings['srv_marketing']);
$smarty->assign('user_marketing', $sdsettings['user_marketing']);
$smarty->assign('pass_marketing', $sdsettings['pass_marketing']);
$smarty->assign('apiurl_transactional', $sdsettings['apiurl_transactional']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/sendgrid.tpl');