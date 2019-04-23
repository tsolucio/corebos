<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM.
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
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
require_once 'Smarty_setup.php';

function get_loginpage($template, $language, $csrf, $user) {
	require 'vtigerversion.php';
	require 'modules/Settings/configod.php';
	global $currentModule, $adb, $current_language, $default_charset;
	$image_path='include/images/';

	$current_language = $language;
	$currentModule = 'Users';
	$app_strings = return_application_language($current_language);
	$mod_strings = return_module_language($current_language, $currentModule);
	$current_module_strings = return_module_language($current_language, $currentModule);

	$smarty=new vtigerCRM_Smarty;
	$smarty->assign('APP', $app_strings);
	$smarty->assign('LBL_CHARSET', $default_charset);
	$smarty->assign('IMAGE_PATH', $image_path);
	$smarty->assign('VTIGER_VERSION', $coreBOS_app_version);

	$companyDetails = retrieveCompanyDetails();
	$smarty->assign('COMPANY_DETAILS', $companyDetails);
	$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name, 'Users', Users::getActiveAdminId()));
	$smarty->assign('currentLoginIP', Vtiger_Request::get_ip());
	$smarty->assign('LOGIN_ERROR', '');
	$currentYear = date('Y');
	$smarty->assign('currentYear', $currentYear);
	if (empty($template) || !file_exists('Smarty/templates/Login/'.$template.'.tpl')) {
		$tpl2load = $cbodLoginPage;
	} else {
		$tpl2load = $template;
	}
	$smarty->assign('LoginPage', $tpl2load);
	$smarty->assign('CAN_UNBLOCK', (empty($_SESSION['can_unblock']) ? 'false' : 'true'));
	if ($csrf=='1' || strtolower($csrf)=='true') {
		//Initialise CSRFGuard library
		include_once 'include/csrfmagic/csrf-magic.php';
	}
	return $smarty->fetch('Login.tpl');
}
?>