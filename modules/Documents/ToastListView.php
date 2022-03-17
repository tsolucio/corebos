<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
require_once 'Smarty_setup.php';
require_once 'include/ListView/ListView.php';
require_once 'modules/CustomView/CustomView.php';
require_once 'include/DatabaseUtil.php';

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once "modules/$currentModule/$currentModule.php";
$url_string = '';
if (!isset($tool_buttons)) {
	$tool_buttons = Button_Check($currentModule);
}
$focus = new $currentModule();
$focus->initSortbyField($currentModule);
$list_buttons=$focus->getListButtons($app_strings, $mod_strings);
$smarty = new vtigerCRM_Smarty();
$list_buttons = array();
if (isPermitted('Documents', 'Delete', '') == 'yes') {
	$smarty->assign('MASS_DELETE', 'yes');
	$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
}
// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);
$smarty->assign('MAX_RECORDS', 20);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('BUTTONS', $list_buttons);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
if (empty($ERROR_MESSAGE) && !empty($_REQUEST['error_msg'])) {
	if (isset($_REQUEST['error_msgclass'])) {
		$ERROR_MESSAGE_CLASS = vtlib_purify($_REQUEST['error_msgclass']);
	}
	$ERROR_MESSAGE = vtlib_purify($_REQUEST['error_msg']);
}
if (!empty($ERROR_MESSAGE)) {
	$smarty->assign('ERROR_MESSAGE_CLASS', isset($ERROR_MESSAGE_CLASS) ? $ERROR_MESSAGE_CLASS : 'cb-alert-info');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString($ERROR_MESSAGE, $currentModule));
}
$customView = new CustomView($currentModule);
$viewid = $customView->getViewId($currentModule);
$viewinfo = $customView->getCustomViewByCvid($viewid);
$statusdetails = $customView->isPermittedChangeStatus($viewinfo['status'], $viewid);
$smarty->assign('CUSTOMVIEW_PERMISSION', $statusdetails);
$smarty->assign('VIEWID', $viewid);
if ($viewinfo['viewname'] == 'All') {
	$smarty->assign('ALL', 'All');
}
if ($viewid == 0) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	exit;
}
global $current_user;
$sql_error = false;
$queryGenerator = new QueryGenerator($currentModule, $current_user);
try {
	if ($viewid != '0') {
		$queryGenerator->initForCustomViewById($viewid);
	} else {
		$queryGenerator->initForDefaultCustomView();
	}
} catch (Exception $e) {
	$sql_error = true;
}
$smarty->assign('SQLERROR', $sql_error);
if ($sql_error) {
	$smarty->assign('ERROR', getTranslatedString('ERROR_GETTING_FILTER'));
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-error');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERROR_GETTING_FILTER', $currentModule));
	$smarty->assign('SEARCHLISTHEADER', array());
	$alphabetical = AlphabeticalSearch($currentModule, 'index', $focus->def_basicsearch_col, 'true', 'basic', '', '', '', '', $viewid);
	$smarty->assign('ALPHABETICAL', $alphabetical);
	$smarty->assign('FIELDNAMES', array());
	$smarty->assign('CRITERIA', array());
	$smarty->assign('SEARCH_URL', '');
	$smarty->assign('export_where', '');
	$smarty->assign('SELECTEDIDS', '');
	$smarty->assign('ALLSELECTEDIDS', '');
	$smarty->assign('CURRENT_PAGE_BOXES', '');
	$smarty->assign('NAVIGATION', '');
	$smarty->assign('recordListRange', '');
	$smarty->assign('CUSTOM_LINKS', '');
	$smarty->assign('LISTHEADER', '');
	$smarty->assign('LISTENTITY', array());
} else {
	$url_string = '';
	$noofrows = null;
	$smarty->assign('export_where', '');
	$smarty->assign('SQLERROR', $sql_error);
	$smarty->assign('NAVIGATION', '');
	$controller = new ListViewController($adb, $current_user, $queryGenerator);
	$smarty->assign('Document_Folder_View', 0);
	$smarty->assign('SEARCH_URL', '');
	$smarty->assign('NO_OF_FOLDERS', 0);
	$smarty->assign('FOLDERS', 0);
	$smarty->assign('EMPTY_FOLDERS', array());
	$smarty->assign('ALL_FOLDERS', array());
	$listview_header_search = $controller->getBasicSearchFieldInfoList();
	$smarty->assign('LISTHEADER', array());
	$smarty->assign('LISTENTITY', array());
	$smarty->assign('SEARCHLISTHEADER', $listview_header_search);
	$alphabetical = AlphabeticalSearch($currentModule, 'index', $focus->def_basicsearch_col, 'true', 'basic', '', '', '', '', $viewid);
	$fieldnames = $controller->getAdvancedSearchOptionString();
	$fieldnames_array = $controller->getAdvancedSearchOptionArray();
	$smarty->assign('ALPHABETICAL', $alphabetical);
	$smarty->assign('FIELDNAMES', $fieldnames);
	$smarty->assign('FIELDNAMES_ARRAY', $fieldnames_array);
	$smarty->assign('AVALABLE_FIELDS', getMergeFields($currentModule, 'available_fields'));
	$smarty->assign('FIELDS_TO_MERGE', getMergeFields($currentModule, 'fields_to_merge'));
	$smarty->assign('SELECTEDIDS', '');
	$smarty->assign('ALLSELECTEDIDS', '');
	$smarty->assign('CURRENT_PAGE_BOXES', '');
	include_once 'vtlib/Vtiger/Link.php';
	$customlink_params = array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']));
	$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));
}
$smarty->assign('IS_ADMIN', is_admin($current_user));
if (isset($listview_header_search) && is_array($listview_header_search)) {
	require_once 'include/utils/ListViewUtils.php';
	$tks_list = getListColumnSearch($listview_header_search, $currentModule);
	$smarty->assign('TKS_LIST_SEARCH', $tks_list);
	$smarty->assign('LVCSearchActive', GlobalVariable::getVariable('Application_ListView_SearchColumns', 0));
	$smarty->assign('LVCSearchAcTrigger', GlobalVariable::getVariable('Application_ListView_SearchColumns_AC_Trigger', 3));
}
// Search Panel Status
$DEFAULT_SEARCH_PANEL_STATUS = GlobalVariable::getVariable('Application_ListView_SearchPanel_Open', 1);
$smarty->assign('DEFAULT_SEARCH_PANEL_STATUS', ($DEFAULT_SEARCH_PANEL_STATUS ? 'display: block' : 'display: none'));
$smarty->assign('EDIT_FILTER_ALL', GlobalVariable::getVariable('Application_Filter_All_Edit', 1));
$smarty->assign('moduleView', GlobalVariable::getVariable('Application_ListView_Layout', 'table'));
$smarty->assign('Apache_Tika_URL', GlobalVariable::getVariable('Apache_Tika_URL', ''));

if (!empty($custom_list_include) && file_exists($custom_list_include)) {
	include $custom_list_include;
}
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '') {
	$smarty->display('ListViewEntries.tpl');
} elseif (isset($custom_list_template) && $custom_list_template != '') {
	$smarty->display($custom_list_template);
} else {
	$smarty->display('ListView.tpl');
}
?>