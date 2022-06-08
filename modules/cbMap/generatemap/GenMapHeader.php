<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/utils/utils.php';

global $currentModule, $default_charset, $app_strings, $mod_strings, $theme, $current_user, $current_language;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$userName = getFullNameFromArray('Users', $current_user->column_fields);
$smarty = new vtigerCRM_Smarty;
$smarty->assign('THEME', $theme);
$smarty->assign('SET_CSS_PROPERTIES', GlobalVariable::getVariable('Application_CSS_Properties', 'include/LD/assets/styles/properties.php'));
$smarty->assign('IMAGEPATH', $image_path);
$smarty->assign('USER', $userName);

$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('LBL_CHARSET', $default_charset);

$smarty->assign('MODULE_NAME', $currentModule);
$smarty->assign('MODULE', $currentModule);
$date = new DateTimeField(null);
$smarty->assign('DATE', $date->getDisplayDateTimeValue());
$smarty->assign('CURRENT_USER_MAIL', $current_user->email1);
$smarty->assign('CURRENT_USER', $current_user->user_name);
$smarty->assign('CURRENT_USER_ID', $current_user->id);
$smarty->assign('CURRENT_USER_IMAGE', ($current_user->column_fields['imagenameimageinfo']!='' ? $current_user->column_fields['imagenameimageinfo']['path'] : ''));

// Pass on the authenticated user language
$smarty->assign('LANGUAGE', $current_language);

// Pass on the Application Name
$appUIName = GlobalVariable::getVariable('Application_UI_Name', 'coreBOS');
$smarty->assign('coreBOS_app_name', $appUIName);
$appUINameHTML = decode_html(vtlib_purify(GlobalVariable::getVariable('Application_UI_NameHTML', $appUIName)));
$smarty->assign('coreBOS_app_nameHTML', $appUINameHTML);

$companyDetails = retrieveCompanyDetails();
$smarty->assign('COMPANY_DETAILS', $companyDetails);
getBrowserVariables($smarty);
$smarty->display('modules/cbMap/GenMapHeader.tpl');
?>
