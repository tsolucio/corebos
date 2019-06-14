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
 *  Module    : Hubspot Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
chdir('../../..');
include_once 'vtlib/Vtiger/Module.php';
include_once 'include/integrations/hubspot/HubSpot.php';
global $site_URL;

if (isset($_GET['code'])) {
	$hs = new corebos_hubspot();
	$acode = vtlib_purify($_GET['code']);
	coreBOS_Settings::setSetting(corebos_hubspot::KEY_ACCESSCODE, $acode);
	$err = $hs->getOAuthTokens($acode);
	if ($err == $hs::$ERROR_NONE) {
		header("Location: $site_URL/index.php?module=Utilities&action=integration&_op=Success&integration=Hubspot");
	} else {
		header("Location: $site_URL/index.php?module=Utilities&action=integration&_op=Error&integration=Hubspot&error_code=".$err.'&error_description=');
	}
} elseif (isset($_GET['error'])) {
	header("Location: $site_URL/index.php?module=Utilities&action=integration&_op=Error&integration=Hubspot&error_code=".
		urlencode(vtlib_purify($_GET['error'])).'&error_description='.urlencode(vtlib_purify($_GET['error_description'])));
}
?>
