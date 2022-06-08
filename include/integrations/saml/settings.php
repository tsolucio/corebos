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
 *  Module    : SAML Integration Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/integrations/saml/saml.php';

$smarty = new vtigerCRM_Smarty();
$wa = new corebos_saml();

$isadmin = is_admin($current_user);

if ($isadmin && !empty($_REQUEST['speid'])) {
	$isActive = ((empty($_REQUEST['saml_active']) || $_REQUEST['saml_active']!='on') ? '0' : '1');
	$isActiveWS = ((empty($_REQUEST['saml_activews']) || $_REQUEST['saml_activews']!='on') ? '0' : '1');
	$speid = (empty($_REQUEST['speid']) ? '' : vtlib_purify($_REQUEST['speid']));
	$spacs = (empty($_REQUEST['spacs']) ? '' : vtlib_purify($_REQUEST['spacs']));
	$spslo = (empty($_REQUEST['spslo']) ? '' : vtlib_purify($_REQUEST['spslo']));
	$spnid = (empty($_REQUEST['spnid']) ? '' : vtlib_purify($_REQUEST['spnid']));
	$ipeid = (empty($_REQUEST['ipeid']) ? '' : vtlib_purify($_REQUEST['ipeid']));
	$ipsso = (empty($_REQUEST['ipsso']) ? '' : vtlib_purify($_REQUEST['ipsso']));
	$ipslo = (empty($_REQUEST['ipslo']) ? '' : vtlib_purify($_REQUEST['ipslo']));
	$ip509 = (empty($_REQUEST['ip509']) ? '' : vtlib_purify($_REQUEST['ip509']));
	$rwurl = (empty($_REQUEST['rwurl']) ? '' : vtlib_purify($_REQUEST['rwurl']));
	$rwurl2 = (empty($_REQUEST['rwurl2']) ? '' : vtlib_purify($_REQUEST['rwurl2']));
	$rwurl3 = (empty($_REQUEST['rwurl3']) ? '' : vtlib_purify($_REQUEST['rwurl3']));
	$wa->saveSettings($isActive, $speid, $spacs, $spslo, $spnid, $ipeid, $ipsso, $ipslo, $ip509, $isActiveWS, $rwurl, $rwurl2, $rwurl3);
}

$smarty->assign('TITLE_MESSAGE', getTranslatedString('SAML Activation', $currentModule));
$wasettings = $wa->getSettings();
$smarty->assign('isActive', $wa->isActive());
$smarty->assign('isActiveWS', $wa->isActiveWS());
$smarty->assign('speid', $wasettings['SPentityId']);
$smarty->assign('spacs', $wasettings['SPACS']);
$smarty->assign('spslo', $wasettings['SPSLO']);
$smarty->assign('spnid', $wasettings['SPNameID']);
$smarty->assign('ipeid', $wasettings['IPentityId']);
$smarty->assign('ipsso', $wasettings['IPSSO']);
$smarty->assign('ipslo', $wasettings['IPSLO']);
$smarty->assign('ip509', $wasettings['IPx509']);
$smarty->assign('rwurl', $wasettings['WSRURL']);
$smarty->assign('rwurl2', $wasettings['WSRURL2']);
$smarty->assign('rwurl3', $wasettings['WSRURL3']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', $isadmin);
$smarty->display('modules/Utilities/saml.tpl');
?>
