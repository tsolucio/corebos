<?php
/*************************************************************************************************
 * Copyright 2023 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Electronic Invoice Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/electronicInvoice/electronicinvoice.php';

$smarty = new vtigerCRM_Smarty();
$electronicInvoice = new corebos_electronicInvoice();

$isadmin = is_admin($current_user);

if ($isadmin) {
	$isActive = ((empty($_REQUEST['electronicInvoice_active']) || $_REQUEST['electronicInvoice_active']!='on') ? '0' : '1');
	$pubkey = (empty($_REQUEST['publickey_display']) ? '' : vtlib_purify($_REQUEST['publickey_display']));
	$privkey = (empty($_REQUEST['privatekeyid_display']) ? '' : vtlib_purify($_REQUEST['privatekeyid_display']));
	$pkey = (empty($_REQUEST['pfkkeyid_display']) ? '' : vtlib_purify($_REQUEST['pfkkeyid_display']));
	$acenter = (empty($_REQUEST['admcenter_display']) ? '' : vtlib_purify($_REQUEST['admcenter_display']));
	$pphrase = (empty($_REQUEST['passphrase']) ? '' : vtlib_purify($_REQUEST['passphrase']));
	$accmap = (empty($_REQUEST['accountmap_display']) ? '' : vtlib_purify($_REQUEST['accountmap_display']));
	$contmap = (empty($_REQUEST['contactmap_display']) ? '' : vtlib_purify($_REQUEST['contactmap_display']));
	$face = (empty($_REQUEST['assigntypeFACe']) ? '' : vtlib_purify($_REQUEST['assigntypeFACe']));
	$facb2b = (empty($_REQUEST['assigntypeFACB2B']) ? '' : vtlib_purify($_REQUEST['assigntypeFACB2B']));
	$eibaseurl = (empty($_REQUEST['EI_baseurl']) ? '' : vtlib_purify($_REQUEST['EI_baseurl']));
	$eiuname = (empty($_REQUEST['EI_username']) ? '' : vtlib_purify($_REQUEST['EI_username']));
	$eipass = (empty($_REQUEST['EI_password']) ? '' : vtlib_purify($_REQUEST['EI_password']));
	$electronicInvoice->saveSettings($isActive, $pubkey, $privkey, $pkey, $acenter, $pphrase, $accmap, $contmap, $face, $facb2b, $eibaseurl, $eiuname, $eipass);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Electronic Invoice', $currentModule));
$eInvoiceSettings = $electronicInvoice->getSettings();
$smarty->assign('isActive', $electronicInvoice->isActive());
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/electronicInvoice.tpl');
?>