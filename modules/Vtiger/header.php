<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
header('X-Frame-Options: DENY');
require_once 'Smarty_setup.php';
require_once 'include/utils/utils.php';
require_once 'include/calculator/Calc.php';

global $currentModule, $default_charset, $app_strings, $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$userName = getFullNameFromArray('Users', $current_user->column_fields);
$smarty = new vtigerCRM_Smarty;
require_once 'modules/evvtMenu/evvtMenuUtils.php';
$smarty->assign('MENU', getMenuArray(0));
$header_array = getAdminevvtMenu();
$smarty->assign('evvtAdminMenu', $header_array);
$smarty->assign('HEADERS', $header_array);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGEPATH', $image_path);
$smarty->assign('USER', $userName);

$qc_modules = getQuickCreateModules();
uasort($qc_modules, function ($a, $b) {
	return (strtolower($a[0]) < strtolower($b[0])) ? -1 : 1;
});
$smarty->assign('QCMODULE', $qc_modules);
$smarty->assign('APP', $app_strings);
$smarty->assign('LBL_CHARSET', $default_charset);
$cnt = count($qc_modules);
$smarty->assign('CNT', $cnt);

$smarty->assign('MODULE_NAME', $currentModule);
$date = new DateTimeField(null);
$smarty->assign('DATE', $date->getDisplayDateTimeValue());
$smarty->assign('CURRENT_USER_MAIL', $current_user->email1);
$smarty->assign('CURRENT_USER', $current_user->user_name);
$smarty->assign('CURRENT_USER_ID', $current_user->id);
$smarty->assign('CATEGORY', getParentTab());
$smarty->assign('CALC', get_calc($image_path));
$smarty->assign('ANNOUNCEMENT', get_announcements());
$smarty->assign('USE_ASTERISK', get_use_asterisk($current_user->id));

if (is_admin($current_user)) {
	$smarty->assign('ADMIN_LINK', "<a href='index.php?module=Settings&action=index'>".$app_strings['LBL_SETTINGS'].'</a>');
}

$module_path="modules/".$currentModule."/";

//Assign the entered global search string to a variable and display it again
if (isset($_REQUEST['query_string']) && $_REQUEST['query_string'] != '') {
	$smarty->assign("QUERY_STRING", htmlspecialchars($_REQUEST['query_string'], ENT_QUOTES, $default_charset));//BUGIX " Cross-Site-Scripting "
} else {
	$smarty->assign("QUERY_STRING", "$app_strings[LBL_SEARCH_STRING]");
}

require_once 'data/Tracker.php';
$tracFocus=new Tracker();
$list = $tracFocus->get_recently_viewed($current_user->id);
$smarty->assign('TRACINFO', $list);

// Gather the custom link information to display
include_once 'vtlib/Vtiger/Link.php';
$hdrcustomlink_params = array('MODULE'=>$currentModule);
$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, array('HEADERLINK','HEADERSCRIPT', 'HEADERCSS'), $hdrcustomlink_params);
$smarty->assign('HEADERLINKS', $COMMONHDRLINKS['HEADERLINK']);
$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT']);
$smarty->assign('HEADERCSS', $COMMONHDRLINKS['HEADERCSS']);

// Pass on the authenticated user language
global $current_language;
$smarty->assign('LANGUAGE', $current_language);

// Pass on the Application Name
$appUIName = GlobalVariable::getVariable('Application_UI_Name', 'coreBOS');
$smarty->assign('coreBOS_app_name', $appUIName);
$appUINameHTML = decode_html(vtlib_purify(GlobalVariable::getVariable('Application_UI_NameHTML', $appUIName)));
$smarty->assign('coreBOS_app_nameHTML', $appUINameHTML);

$companyDetails = retrieveCompanyDetails();
$smarty->assign('COMPANY_DETAILS', $companyDetails);

//Global Search Autocomplete Mapping
$bmapname = 'GlobalSearchAutocomplete';
$cbMapGS = array();
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$cbMapGS = $cbMap->GlobalSearchAutocomplete();
	$cbMapGS['entityfield']='query_string';
}
$smarty->assign('GS_AUTOCOMP', $cbMapGS);
$Application_Global_Search_Active = GlobalVariable::getVariable('Application_Global_Search_Active', 1);
$smarty->assign('Application_Global_Search_Active', $Application_Global_Search_Active);

$smarty->assign('HELP_URL', GlobalVariable::getVariable('Application_Help_URL', 'http://corebos.org/documentation'));
ob_start();
cbEventHandler::do_action('corebos.header.premenu');
$smarty->assign('COREBOS_HEADER_PREMENU', ob_get_clean());
getBrowserVariables($smarty);
$smarty->assign('Module_Popup_Edit', isset($_REQUEST['Module_Popup_Edit']) ? vtlib_purify($_REQUEST['Module_Popup_Edit']) : 0);

$smarty->display('Header.tpl');
cbEventHandler::do_action('corebos.header');
?>
