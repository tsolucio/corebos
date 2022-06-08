<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';
require_once 'include/logging.php';
require_once 'include/ListView/ListView.php';
require_once 'include/utils/utils.php';
require_once 'modules/com_vtiger_workflow/VTWorkflow.php';
global $app_strings, $default_charset, $currentModule, $current_user, $theme, $adb;
$url_string = '';
$smarty = new vtigerCRM_Smarty;
if (!isset($where)) {
	$where = '';
}

$url = '';
$popuptype = '';
$popuptype = isset($_REQUEST['popuptype']) ? vtlib_purify($_REQUEST['popuptype']) : '';

// Pass on the authenticated user language
global $current_language;
$smarty->assign('LANGUAGE', $current_language);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('LBL_CHARSET', $default_charset);
$smarty->assign('THEME', $theme);
$smarty->assign('THEME_PATH', "themes/$theme/");
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('MODULE', $currentModule);
$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name));
getBrowserVariables($smarty);

// Gather the custom link information to display
include_once 'vtlib/Vtiger/Link.php';
$hdrcustomlink_params = array('MODULE'=>$currentModule);
$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, array('HEADERSCRIPT_POPUP', 'HEADERCSS_POPUP'), $hdrcustomlink_params);
$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT_POPUP']);
$smarty->assign('HEADERCSS', $COMMONHDRLINKS['HEADERCSS_POPUP']);
$smarty->assign('SET_CSS_PROPERTIES', GlobalVariable::getVariable('Application_CSS_Properties', 'include/LD/assets/styles/properties.php'));

$smarty->assign('QCMODULEARRAY', array());

// This is added to support the type of popup and callback
if (isset($_REQUEST['popupmode']) && isset($_REQUEST['callback'])) {
	$url = '&popupmode='.vtlib_purify($_REQUEST['popupmode']).'&callback='.vtlib_purify($_REQUEST['callback']);
	$smarty->assign('POPUPMODE', vtlib_purify($_REQUEST['popupmode']));
	$smarty->assign('CALLBACK', vtlib_purify($_REQUEST['callback']));
} else {
	$smarty->assign('POPUPMODE', '');
	$smarty->assign('CALLBACK', '');
}

$smarty->assign('CURR_ROW', 0);
$smarty->assign('FIELDNAME', '');
$smarty->assign('PRODUCTID', 0);
$smarty->assign('RECORDID', 0);
$smarty->assign('RETURN_MODULE', '');
$smarty->assign('SELECT', '');
$smarty->assign('SINGLE_MOD', $currentModule);
if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='') {
	$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
}
$focus = new Workflow();
$alphabetical = AlphabeticalSearch($currentModule, 'Popup', $focus->def_basicsearch_col, 'true', 'basic', $popuptype, '', '', $url);

if (isset($_REQUEST['select'])) {
	$smarty->assign('SELECT', 'enable');
}

$smarty->assign('RETURN_ACTION', isset($_REQUEST['return_action']) ? vtlib_purify($_REQUEST['return_action']) : '');

$where_relquery = '';
if (!empty($_REQUEST['recordid'])) {
	$recid = vtlib_purify($_REQUEST['recordid']);
	$smarty->assign('RECORDID', $recid);
	$url_string .='&recordid='.$recid;
	$where_relquery = getRelCheckquery($currentModule, (isset($_REQUEST['return_module']) ? $_REQUEST['return_module'] : ''), $recid);
}
if (isset($_REQUEST['query']) && isset($_REQUEST['search']) && $_REQUEST['query']=='true' && $_REQUEST['search']=='true') {
	// to show 'show all' button on search
	$smarty->assign('mod_var_name', '');
	$smarty->assign('mod_var_value', '');
	$smarty->assign('recid_var_name', '');
	$smarty->assign('recid_var_value', '0');
} else {
	$smarty->assign('recid_var_value', '');
	$smarty->assign('mod_var_name', '');
	$smarty->assign('mod_var_value', '');
	$smarty->assign('recid_var_name', '');
}

$query = 'select *,workflow_id as crmid from com_vtiger_workflows';

$smarty->assign('RECORD_ID', '');
$order_by = $focus->getOrderBy();
$sorder = $focus->getSortOrder();
$listview_header_search=getSearchListHeaderValues($focus, $currentModule, $url_string, $sorder, $order_by);
$smarty->assign('SEARCHLISTHEADER', $listview_header_search);
$smarty->assign('ALPHABETICAL', $alphabetical);
$smarty->assign('FIELDNAMES', $focus->list_fields);
$smarty->assign('NOADVANCEDSEARCH', 1);

if (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') {
	list($where, $ustring) = explode('#@@#', getWhereCondition($currentModule));
	$url_string .='&query=true'.$ustring;
}

if (isset($where) && $where != '') {
	$query .= ' where '.$where.$where_relquery;
} elseif ($where_relquery!='') {
	$query .= ' where true '.$where.$where_relquery;
}

if (isset($order_by) && $order_by != '') {
	$query .= ' ORDER BY '.$order_by.' '.$sorder;
}

// vtlib customization: To override module specific popup query for a given field
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
$count_result = $adb->pquery(mkCountQuery($query), array());
$noofrows = $adb->query_result($count_result, 0, 'count');

if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
	$start = vtlib_purify($_REQUEST['start']);
	if ($start == 'last' && $noofrows > 0) {
		$start = ceil($noofrows/$list_max_entries_per_page);
	}
	if (!is_numeric($start)) {
		$start = 1;
	} elseif ($start < 1) {
		$start = 1;
	}
	$start = ceil($start);
} else {
	$start = 1;
}
$limstart=($start-1)*$list_max_entries_per_page;
$query.=" LIMIT $limstart,$list_max_entries_per_page";
$list_result = $adb->pquery($query, array());
if (GlobalVariable::getVariable('Debug_Popup_Query', '0')=='1') {
	echo '<br>'.$query.'<br>';
}

$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);

$url_string .='&popuptype='.$popuptype;
if (isset($_REQUEST['select']) && $_REQUEST['select'] == 'enable') {
	$url_string .='&select=enable';
}
if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '') {
	$url_string .='&return_module='.vtlib_purify($_REQUEST['return_module']);
}

$listview_header = getSearchListViewHeader($focus, $currentModule, $url_string, $sorder, $order_by);
$smarty->assign('LISTHEADER', $listview_header);
$smarty->assign('HEADERCOUNT', count($listview_header)+1);
$focus->popup_type=$popuptype;
$listview_entries = getSearchListViewEntries($focus, $currentModule, $list_result, $navigation_array);
$smarty->assign('LISTENTITY', $listview_entries);
if (GlobalVariable::getVariable('Application_ListView_Compute_Page_Count', 0, $currentModule)) {
	$record_string = getRecordRangeMessage($list_result, $limstart, $noofrows);
} else {
	$record_string = '';
}

$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $currentModule, 'Popup');
$smarty->assign('NAVIGATION', $navigationOutput);
$smarty->assign('RECORD_STRING', $record_string);
$smarty->assign('RECORD_COUNTS', $noofrows);
$smarty->assign('POPUPTYPE', $popuptype);
$smarty->assign('PARENT_MODULE', isset($_REQUEST['parent_module']) ? vtlib_purify($_REQUEST['parent_module']) : '');

// Field Validation Information
$tabid = getTabid($currentModule);
$validationData = array();
$validationArray = array();

$smarty->assign('VALIDATION_DATA_FIELDNAME', '');
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', '');
$smarty->assign('VALIDATION_DATA_FIELDLABEL', '');

if (isset($_REQUEST['cbcustompopupinfo'])) {
	$cbcustompopupinfo = explode(';', $_REQUEST['cbcustompopupinfo']);
	$smarty->assign('CBCUSTOMPOPUPINFO_ARRAY', $cbcustompopupinfo);
	$smarty->assign('CBCUSTOMPOPUPINFO', $_REQUEST['cbcustompopupinfo']);
}

if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '') {
	$smarty->display('PopupContents.tpl');
} else {
	$smarty->display('Popup.tpl');
}
cbEventHandler::do_action('corebos.popup.footer');
?>
