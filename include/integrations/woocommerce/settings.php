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
 *  Module    : Woocommerce Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/woocommerce/woocommerce.php';

$smarty = new vtigerCRM_Smarty();
$wc = new corebos_woocommerce();

$isadmin = is_admin($current_user);

if ($isadmin && isset($_REQUEST['ck']) && isset($_REQUEST['cs']) && isset($_REQUEST['wcurl'])) {
	$isActive = ((empty($_REQUEST['woocommerce_active']) || $_REQUEST['woocommerce_active']!='on') ? '0' : '1');
	$cs = (empty($_REQUEST['cs']) ? '' : vtlib_purify($_REQUEST['cs']));
	$ck = (empty($_REQUEST['ck']) ? '' : vtlib_purify($_REQUEST['ck']));
	$wcurl = (empty($_REQUEST['wcurl']) ? '' : vtlib_purify($_REQUEST['wcurl']));
	$wcsct = (empty($_REQUEST['wcsct']) ? '' : vtlib_purify($_REQUEST['wcsct']));
	$cm = ((empty($_REQUEST['woocommerce_customer']) || $_REQUEST['woocommerce_customer']!='on') ? '0' : '1');
	$pm = ((empty($_REQUEST['woocommerce_product']) || $_REQUEST['woocommerce_product']!='on') ? '0' : '1');
	$om = ((empty($_REQUEST['woocommerce_order']) || $_REQUEST['woocommerce_order']!='on') ? '0' : '1');
	$wc->saveSettings($isActive, $cs, $ck, $wcurl, $wcsct, $cm, $pm, $om);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Woocommerce Activation', $currentModule));
$wcsettings = $wc->getSettings();
$smarty->assign('isActive', $wc->isActive());
$smarty->assign('cs', $wcsettings['cs']);
$smarty->assign('ck', $wcsettings['ck']);
$smarty->assign('wcurl', $wcsettings['url']);
$smarty->assign('wcsct', $wcsettings['sct']);
$smarty->assign('isContact', $wcsettings['cm']);
$smarty->assign('isProduct', $wcsettings['pm']);
$smarty->assign('isSalesOrder', $wcsettings['om']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/woocommerce.tpl');
?>
