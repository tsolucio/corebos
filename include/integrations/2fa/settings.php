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
 *  Module    : Two Factor Authentification Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
global $current_user, $adb;
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/authTypes/TwoFactorAuth/autoload.php';
use \RobThree\Auth\TwoFactorAuth;

$smarty = new vtigerCRM_Smarty();

$userid = isset($_REQUEST['user_list']) ? vtlib_purify($_REQUEST['user_list']) : '';
$do2FA = GlobalVariable::getVariable('User_2FAAuthentication', 0, 'Users', $userid);
$isAppActive = ($do2FA==1);
if (!empty($userid) && $_REQUEST['_op']=='setconfig2fa') {
	$isFormActive = ((empty($_REQUEST['2faactive']) || $_REQUEST['2faactive']!='on') ? '0' : '1');
	$recexists = $adb->pquery('select globalvariableid
		from vtiger_globalvariable
		inner join vtiger_crmentity on crmid=globalvariableid
		where deleted=0 and gvname=? and smownerid=?', array('User_2FAAuthentication',$userid));
	if ($isFormActive=='1') {
		$tfa = new TwoFactorAuth('coreBOSWebApp');
		$FASecret = $tfa->createSecret(160);
		$smarty->assign('FASecret', chunk_split($FASecret, 4, ' '));
		coreBOS_Settings::setSetting('coreBOS_2FA_Secret_'.$userid, $FASecret);
		$smarty->assign('QRCODE', $tfa->getQRCodeImageAsDataUri('coreBOSWebApp', $FASecret));
		if ($recexists && $adb->num_rows($recexists)==1) {
			$gvid = $adb->query_result($recexists, 0, 0);
			$adb->pquery('update vtiger_globalvariable set value=1 where globalvariableid=?', array($gvid));
		} else {
			vtws_create('GlobalVariable', array(
				'gvname' => 'User_2FAAuthentication',
				'default_check' => '0',
				'value' => '1',
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => '',
				'category' => 'System',
				'in_module_list' => '',
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$userid,
			), $current_user);
		}
		$isAppActive = true;
	} else {
		if ($recexists && $adb->num_rows($recexists)==1) {
			$gvid = $adb->query_result($recexists, 0, 0);
			$adb->pquery('update vtiger_globalvariable set value=0 where globalvariableid=?', array($gvid));
		}
		$smarty->assign('FASecret', '');
		coreBOS_Settings::delSetting('coreBOS_2FA_Secret_'.$userid);
		$smarty->assign('QRCODE', '');
		$isAppActive = false;
	}
} else {
	$smarty->assign('FASecret', '');
	$smarty->assign('QRCODE', '');
}
$smarty->assign('isActive', $isAppActive);

$smarty->assign('TITLE_MESSAGE', getTranslatedString('2FA Activation', $currentModule));
$smarty->assign('USERLIST', getUserslist(true, $userid));
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/2fa.tpl');
?>
