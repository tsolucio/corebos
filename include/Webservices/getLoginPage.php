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
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
require_once 'vtigerversion.php';
error_reporting(E_ALL);
function get_loginpage($template, $language, $csrf) {
	global $currentModule, $adb, $coreBOS_app_version, $current_language;
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

	// We check if we have the two new logo fields > if not we create them
	$cnorg=$adb->getColumnNames('vtiger_organizationdetails');
	if (!in_array('faviconlogo', $cnorg)) {
		$adb->query('ALTER TABLE `vtiger_organizationdetails` ADD `frontlogo` VARCHAR(150) NOT NULL, ADD `faviconlogo` VARCHAR(150) NOT NULL');
	}
	$sql="select * from vtiger_organizationdetails";
	$result = $adb->pquery($sql, array());
	//Handle for allowed organation logo/logoname likes UTF-8 Character
	$companyDetails = array();
	$companyDetails['name'] = $adb->query_result($result, 0, 'organizationname');
	$companyDetails['website'] = $adb->query_result($result, 0, 'website');
	$companyDetails['logo'] = decode_html($adb->query_result($result, 0, 'logoname'));
	if (decode_html($adb->query_result($result, 0, 'faviconlogo'))=='') {
		$favicon='themes/images/favicon.ico';
	} else {
		$favicon='test/logo/'.decode_html($adb->query_result($result, 0, 'faviconlogo'));
	}
	$companyDetails['favicon'] = $favicon;
	$smarty->assign('COMPANY_DETAILS', $companyDetails);
	$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name, 'Users', Users::getActiveAdminId()));
	$smarty->assign('currentLoginIP', Vtiger_Request::get_ip());
	$smarty->assign('LOGIN_ERROR', '');
	$currentYear = date('Y');
	$smarty->assign('currentYear', $currentYear);
	$smarty->assign('LoginPage', $cbodLoginPage);
	$smarty->assign('CAN_UNBLOCK', (empty($_SESSION['can_unblock']) ? 'false' : 'true'));
	$template = $smarty->fetch('Login.tpl');
	return $template;
}
?>