<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
header('X-Frame-Options: DENY');
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 150');
require_once 'vtlib/Vtiger/Module.php';
require_once 'Smarty_setup.php';
global $default_language, $current_language, $current_user;
if (empty($current_user)) {
	$current_user = Users::getActiveAdminUser();
}
if (empty($current_language)) {
	$current_language = $default_language;
}
$app_strings = return_application_language($current_language);
$mod_strings = return_module_language($current_language, 'Settings');
$smarty = new vtigerCRM_Smarty;
$date = new DateTimeField(null);
$smarty->assign('DATE', $date->getDisplayDateTimeValue());
$smarty->assign('CURRENT_USER_MAIL', $current_user->email1);
$smarty->assign('CURRENT_USER', $current_user->user_name);
$smarty->assign('CURRENT_USER_ID', $current_user->id);
$smarty->assign('LANGUAGE', $current_language);

// Pass on the Application Name
$smarty->assign('coreBOS_app_name', GlobalVariable::getVariable('Application_UI_Name', 'coreBOS'));

$companyDetails = retrieveCompanyDetails();
$smarty->assign('COMPANY_DETAILS', $companyDetails);

$smarty->assign('HELP_URL', GlobalVariable::getVariable('Application_Help_URL', 'http://corebos.org/documentation'));
getBrowserVariables($smarty);
$smarty->display('Maintenance.tpl');
?>
