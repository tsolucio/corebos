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
 *  Module    : Third Party Integration Access point
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

if (!isset($_REQUEST['_op'])) {
	$_REQUEST['_op'] = 'HELP';
}
switch ($_REQUEST['_op']) {
	case 'Success':
		$smarty = new vtigerCRM_Smarty();
		$titlemessage = getTranslatedString('SUCCESSFUL_REGISTRATION_TITLE', $currentModule);
		$smarty->assign('TITLE_MESSAGE', $titlemessage);
		$smarty->assign('MESSAGE', sprintf(getTranslatedString('SUCCESSFUL_REGISTRATION_MESSAGE', $currentModule), vtlib_purify($_REQUEST['integration'])));
		$smarty->assign('ERROR_CLASS', '');
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('THEME', $theme);
		include 'modules/cbupdater/forcedButtons.php';
		$smarty->assign('CHECK', $tool_buttons);
		$smarty->display('modules/Utilities/integration.tpl');
		break;
	case 'Error':
		$smarty = new vtigerCRM_Smarty();
		$titlemessage = getTranslatedString('UNSUCCESSFUL_REGISTRATION_TITLE', $currentModule);
		$smarty->assign('TITLE_MESSAGE', $titlemessage);
		$smarty->assign('MESSAGE', sprintf(getTranslatedString('UNSUCCESSFUL_REGISTRATION_MESSAGE', $currentModule), vtlib_purify($_REQUEST['integration'])).
			'<br>'.vtlib_purify($_REQUEST['error_description']).' ('.vtlib_purify($_REQUEST['error_code']).')');
		$smarty->assign('ERROR_CLASS', 'slds-theme_error');
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('THEME', $theme);
		include 'modules/cbupdater/forcedButtons.php';
		$smarty->assign('CHECK', $tool_buttons);
		$smarty->display('modules/Utilities/integration.tpl');
		break;
	case 'getconfighubspot':
	case 'setconfighubspot':
		include_once 'include/integrations/hubspot/settings.php';
		break;
	case 'getconfigzendesk':
	case 'setconfigzendesk':
		include_once 'include/integrations/zendesk/settings.php';
		break;
	case 'getconfig2fa':
	case 'setconfig2fa':
		include_once 'include/integrations/2fa/settings.php';
		break;
	case 'getconfiggcontact':
	case 'setconfiggcontact':
		include_once 'include/integrations/GContacts/settings.php';
		break;
	default:
		$smarty = new vtigerCRM_Smarty();
		$titlemessage = getTranslatedString('Available Integrations', $currentModule);
		$smarty->assign('TITLE_MESSAGE', $titlemessage);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('THEME', $theme);
		include 'modules/cbupdater/forcedButtons.php';
		$smarty->assign('CHECK', $tool_buttons);
		$smarty->display('modules/Utilities/integrationhelp.tpl');
		break;
}