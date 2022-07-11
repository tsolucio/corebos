<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Facebook Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/facebook/facebook.php';

$smarty = new vtigerCRM_Smarty();
$facebook = new corebos_facebook();

$isadmin = is_admin($current_user);

if ($isadmin && !empty($_REQUEST['fb_verification_code']) && !empty($_REQUEST['fb_destination_module'])) {
	$isActive = ((empty($_REQUEST['facebook_active']) || $_REQUEST['facebook_active']!='on') ? '0' : '1');
	$fb_verification_code = (empty($_REQUEST['fb_verification_code']) ? '' : vtlib_purify($_REQUEST['fb_verification_code']));
	$fb_destination_module = (empty($_REQUEST['fb_destination_module']) ? '' : vtlib_purify($_REQUEST['fb_destination_module']));
	$facebook->saveSettings($isActive, $fb_verification_code, $fb_destination_module);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Facebook Activation', $currentModule));
$facebookSettings = $facebook->getSettings();
$smarty->assign('isActive', $facebook->isActive());
$smarty->assign('fbVerificationCode', $facebookSettings['fb_verification_code']);
$smarty->assign('fbDestinationModule', $facebookSettings['fb_destination_module']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/facebook.tpl');
?>
