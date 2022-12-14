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
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer, strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header("X-Content-Security-Policy: default-src 'self'; frame-ancestors 'self'; sandbox allow-forms allow-scripts allow-same-origin;");

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
$smarty->assign('MENUSEARCH', getFlatMenuJSON());
$header_array = getAdminevvtMenu();
$smarty->assign('evvtAdminMenu', $header_array);
$smarty->assign('HEADERS', $header_array);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGEPATH', $image_path);
$smarty->assign('USER', trim(gtltTagsToHTML($userName)));
$smarty->assign('CSRFNAME', $GLOBALS['csrf']['input-name']);

$qc_modules = getQuickCreateModules();
uasort($qc_modules, function ($a, $b) {
	return (strtolower($a[0]) < strtolower($b[0])) ? -1 : 1;
});
$smarty->assign('QCMODULE', $qc_modules);
$smarty->assign('SHOWQUICKCREATE', (count($qc_modules) && GlobalVariable::getVariable('Application_Display_QuickCreate', 1)));
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
$smarty->assign('CURRENT_USER_IMAGE', ($current_user->column_fields['imagenameimageinfo']!='' ? $current_user->column_fields['imagenameimageinfo']['path'] : ''));
$smarty->assign('CALC', get_calc($image_path));
$smarty->assign('USE_ASTERISK', get_use_asterisk($current_user->id));

if (is_admin($current_user)) {
	$smarty->assign('ADMIN_LINK', "<a href='index.php?module=Settings&action=index'>".$app_strings['LBL_SETTINGS'].'</a>');
}

$module_path='modules/'.$currentModule.'/';

//Assign the entered global search string to a variable and display it again
if (isset($_REQUEST['query_string']) && $_REQUEST['query_string'] != '') {
	$smarty->assign('QUERY_STRING', htmlspecialchars($_REQUEST['query_string'], ENT_QUOTES, $default_charset));//BUGIX Cross-Site-Scripting
} else {
	$smarty->assign('QUERY_STRING', $app_strings['LBL_SEARCH_STRING']);
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
$smarty->assign('coreBOS_app_coverimage', GlobalVariable::getVariable('Application_UI_CoverImage', 'themes/images/content-bg-1.png'));
$smarty->assign('Global_Search_PlaceHolder', GlobalVariable::getVariable('Application_Global_Search_PlaceHolder', $app_strings['LBL_SEARCH_TITLE'].$appUIName));
$NotificationSound = GlobalVariable::getVariable('Calendar_Notification_Sound', 'modules/cbCalendar/media/new_event.mp3');
if (!isInsideApplication($NotificationSound)) {
	$NotificationSound = 'modules/cbCalendar/media/new_event.mp3';
}
$smarty->assign('Calendar_Notification_Sound', $NotificationSound);

$companyDetails = retrieveCompanyDetails();
$smarty->assign('COMPANY_DETAILS', $companyDetails);
$getTitle = GlobalVariable::getVariable('Application_TitleInformation', getTranslatedString($currentModule, $currentModule).' - '.$appUIName, $currentModule, $current_user->id, $_REQUEST['action']);
if (is_numeric($getTitle)) {
	$getTitle = coreBOS_Rule::evaluate($getTitle, $_REQUEST['record']);
}
$smarty->assign('TITLE_HEADER', $getTitle);

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
$smarty->assign('Application_Menu_Direction', GlobalVariable::getVariable('Application_Menu_Direction', 'Horizontal'));

$smarty->assign('HELP_URL', GlobalVariable::getVariable('Application_Help_URL', 'https://corebos.org/documentation'));
$smarty->assign('SET_CSS_PROPERTIES', GlobalVariable::getVariable('Application_CSS_Properties', 'include/LD/assets/styles/properties.php'));
ob_start();
cbEventHandler::do_action('corebos.header.premenu');
$smarty->assign('COREBOS_HEADER_PREMENU', ob_get_clean());
getBrowserVariables($smarty);
$smarty->assign('Module_Popup_Edit', isset($_REQUEST['Module_Popup_Edit']) ? vtlib_purify($_REQUEST['Module_Popup_Edit']) : 0);

if (coreBOS_Settings::getSetting('onesignal_isactive', '0') == '1') {
	$smarty->assign('ONESIGNAL_APP_ID', coreBOS_Settings::getSetting('onesignal_app_id', ''));
	$smarty->assign('ONESIGNAL_IS_ACTIVE', true);
} else {
	$smarty->assign('ONESIGNAL_IS_ACTIVE', false);
}

// Checking for the Application_Focus_Element global variable
$ApplicationFocusElementValue = GlobalVariable::getVariable('Application_Focus_Element', '', '', '', $_REQUEST['action']);
if (!empty($ApplicationFocusElementValue)) {
	$smarty->assign('ApplicationFocusElementValue', $ApplicationFocusElementValue);
}

$smarty->display('Header.tpl');
?>
