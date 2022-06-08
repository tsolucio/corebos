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

$qc_modules = getQuickCreateModules();
for ($i=0; $i<count($qc_modules); $i++) {
	$qcmod[$i]=$qc_modules[$i][1];
}
$smarty->assign('QCMODULEARRAY', $qcmod);
$suri=vtlib_purify($_SERVER['REQUEST_URI']);
$suri=substr($suri, strpos($suri, '?')+1);
$smarty->assign('POPUP', str_replace('&', '-a;', $suri).'-a;popqc=true');

if (!empty($_REQUEST['popqc']) && $_REQUEST['popqc'] = 'true' && empty($_REQUEST['advft_criteria']) && !empty($_REQUEST['record'])) {
	$fldrs = $adb->pquery(
		"SELECT vtiger_field.fieldlabel,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldname,vtiger_entityname.entityidfield
			FROM vtiger_field
			INNER JOIN vtiger_entityname on vtiger_field.tabid=vtiger_entityname.tabid and modulename=?
			WHERE vtiger_entityname.fieldname like concat('%',vtiger_field.columnname,'%')",
		array($currentModule)
	);
	$row = $adb->fetch_array($fldrs);
	$fieldLabelEscaped = str_replace(' ', '_', $row['fieldlabel']);
	$optionvalue = $row['tablename'].':'.$row['entityidfield'].':'.$row['entityidfield'].':'.$currentModule.'_'.$fieldLabelEscaped.':V';
	$fldval = vtlib_purify($_REQUEST['record']);
	$_REQUEST['searchtype']='advance';
	$_REQUEST['query'] = 'true';
	$_REQUEST['advft_criteria'] = '[{"groupid":"1","columnname":"'.$optionvalue.'","comparator":"e","value":"'.$fldval.'","columncondition":""}]';
}

// This is added to support the type of popup and callback
if (isset($_REQUEST['popupmode']) && isset($_REQUEST['callback'])) {
	$url = '&popupmode='.vtlib_purify($_REQUEST['popupmode']).'&callback='.vtlib_purify($_REQUEST['callback']);
	$smarty->assign('POPUPMODE', vtlib_purify($_REQUEST['popupmode']));
	$smarty->assign('CALLBACK', vtlib_purify($_REQUEST['callback']));
} else {
	$smarty->assign('POPUPMODE', '');
	$smarty->assign('CALLBACK', '');
}

$focus = CRMEntity::getInstance($currentModule);
$smarty->assign('CURR_ROW', 0);
$smarty->assign('FIELDNAME', '');
$smarty->assign('PRODUCTID', 0);
$smarty->assign('RECORDID', 0);
$smarty->assign('RETURN_MODULE', '');
$smarty->assign('SELECT', '');
switch ($currentModule) {
	case 'Contacts':
	case 'Accounts':
	case 'Leads':
	case 'Project':
	case 'Potentials':
	case 'Documents':
		if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='') {
			$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
		} else {
			$smarty->assign('RETURN_MODULE', 'Emails');
		}
		break;
	case 'Products':
	case 'Services':
		if (isset($_REQUEST['curr_row'])) {
			$curr_row = vtlib_purify($_REQUEST['curr_row']);
			$smarty->assign('CURR_ROW', $curr_row);
			$url_string .='&curr_row='.vtlib_purify($_REQUEST['curr_row']);
		}
		if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='') {
			$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
		}
		$smarty->assign('Service_Default_Units', GlobalVariable::getVariable('Inventory_Service_Default_Units', '1'));
		$smarty->assign('Product_Default_Units', GlobalVariable::getVariable('Inventory_Product_Default_Units', '1'));
		break;
	case 'PriceBooks':
		if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='') {
			$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
		}
		if (isset($_REQUEST['fldname']) && $_REQUEST['fldname'] !='') {
			$smarty->assign('FIELDNAME', vtlib_purify($_REQUEST['fldname']));
			$url_string .='&fldname='.vtlib_purify($_REQUEST['fldname']);
		}
		if (isset($_REQUEST['productid']) && $_REQUEST['productid'] !='') {
			$smarty->assign('PRODUCTID', vtlib_purify($_REQUEST['productid']));
			$url_string .='&productid='.vtlib_purify($_REQUEST['productid']);
		}
		break;
	default:
		if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='') {
			$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
		}
		break;
}
$smarty->assign('SINGLE_MOD', $currentModule);
$alphabetical = AlphabeticalSearch($currentModule, 'Popup', $focus->def_basicsearch_col, 'true', 'basic', $popuptype, '', '', $url);
if (isset($_REQUEST['select'])) {
	$smarty->assign('SELECT', 'enable');
}

$smarty->assign('RETURN_ACTION', isset($_REQUEST['return_action']) ? vtlib_purify($_REQUEST['return_action']) : '');

if ($currentModule == 'PriceBooks' && isset($_REQUEST['productid'])) {
	$productid= isset($_REQUEST['productid']) ? vtlib_purify($_REQUEST['productid']) : 0;
	$currency_id= isset($_REQUEST['currencyid']) ? vtlib_purify($_REQUEST['currencyid']) : fetchCurrency($current_user->id);
	$crmalias = CRMEntity::getcrmEntityTableAlias('PriceBooks');
	$pbpdorelcrmentity = CRMEntity::getcrmEntityTableAlias('pricebookproductrel', true);
	$query = 'select vtiger_pricebook.*, vtiger_pricebookproductrel.productid, vtiger_pricebookproductrel.listprice, ' .
		'vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime ' .
		'from vtiger_pricebook inner join vtiger_pricebookproductrel on vtiger_pricebookproductrel.pricebookid = vtiger_pricebook.pricebookid ' .
		"inner join $crmalias on vtiger_crmentity.crmid = vtiger_pricebook.pricebookid " .
		"inner join $pbpdorelcrmentity as pbpdocrm on pbpdocrm.crmid = vtiger_pricebookproductrel.pricebookproductrelid " .
		'where vtiger_crmentity.deleted=0 and vtiger_pricebook.currency_id='.$adb->sql_escape_string($currency_id).' and vtiger_pricebook.active=1';
	if (!empty($productid)) {
		$query.= ' and vtiger_pricebookproductrel.productid='.$adb->sql_escape_string($productid);
	}
	$smarty->assign('recid_var_value', '');
	$smarty->assign('mod_var_name', '');
	$smarty->assign('mod_var_value', '');
	$smarty->assign('recid_var_name', '');
} else {
	$where_relquery = '';
	if (!empty($_REQUEST['recordid'])) {
		$recid = vtlib_purify($_REQUEST['recordid']);
		$smarty->assign('RECORDID', $recid);
		$url_string .='&recordid='.$recid;
		$where_relquery = getRelCheckquery($currentModule, (isset($_REQUEST['return_module']) ? $_REQUEST['return_module'] : ''), $recid);
	}
	if (isset($_REQUEST['relmod_id']) || isset($_REQUEST['fromPotential'])) {
		if (isset($_REQUEST['relmod_id'])) {
			$mod = vtlib_purify($_REQUEST['parent_module']);
			$id = vtlib_purify($_REQUEST['relmod_id']);
		} else { // $_REQUEST['fromPotential'] != ''
			$mod = 'Accounts';
			$id= vtlib_purify($_REQUEST['acc_id']);
		}
		$smarty->assign('mod_var_name', 'parent_module');
		$smarty->assign('mod_var_value', $mod);
		$smarty->assign('recid_var_name', 'relmod_id');
		$smarty->assign('recid_var_value', $id);
		$where_relquery.= getPopupCheckquery($currentModule, $mod, $id);
	} elseif (isset($_REQUEST['query']) && isset($_REQUEST['search']) && $_REQUEST['query']=='true' && $_REQUEST['search']=='true') {
		// to show 'show all' button on search
		$smarty->assign('mod_var_name', '');
		$smarty->assign('mod_var_value', '');
		$smarty->assign('recid_var_name', '');
		$smarty->assign('recid_var_value', '0');
	} elseif (isset($_REQUEST['task_relmod_id'])) {
		$smarty->assign('mod_var_name', 'task_parent_module');
		$smarty->assign('mod_var_value', vtlib_purify($_REQUEST['task_parent_module']));
		$smarty->assign('recid_var_name', 'task_relmod_id');
		$smarty->assign('recid_var_value', vtlib_purify($_REQUEST['task_relmod_id']));
		$where_relquery.= getPopupCheckquery($currentModule, vtlib_purify($_REQUEST['task_parent_module']), vtlib_purify($_REQUEST['task_relmod_id']));
	} else {
		$smarty->assign('recid_var_value', '');
		$smarty->assign('mod_var_name', '');
		$smarty->assign('mod_var_value', '');
		$smarty->assign('recid_var_name', '');
	}
	if ($currentModule == 'Products' && empty($_REQUEST['record_id']) && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po')) {
		$showSubproducts = GlobalVariable::getVariable('Product_Show_Subproducts_Popup', 'no');
		if ($showSubproducts == 'yes') {
			$where_relquery.=' and vtiger_products.discontinued <> 0';
		} else {
			$crmalias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
			$where_relquery.=" and vtiger_products.discontinued<>0 AND vtiger_products.productid NOT IN (SELECT distinct topdo
				FROM vtiger_productcomponent
				INNER JOIN $crmalias ON vtiger_crmentity.crmid=vtiger_productcomponent.productcomponentid
				WHERE vtiger_crmentity.deleted = 0)";
		}
	} elseif ($currentModule == 'Products' && !empty($_REQUEST['record_id']) && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po')) {
		$crmalias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
		$where_relquery .= ' and vtiger_products.discontinued <> 0 AND (vtiger_products.productid IN '
			."(SELECT topdo FROM vtiger_productcomponent
				INNER JOIN $crmalias ON vtiger_crmentity.crmid=vtiger_productcomponent.productcomponentid
				WHERE vtiger_crmentity.deleted=0 AND frompdo=".$adb->sql_escape_string($_REQUEST['record_id']).'))';
	} elseif ($currentModule == 'Products' && (empty($_REQUEST['return_module']) || $_REQUEST['return_module'] != 'Products')) {
		$where_relquery .= ' and vtiger_products.discontinued <> 0';
	}

	if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Products' && $currentModule == 'Products' && $_REQUEST['recordid']) {
		$parentLikeSubProduct = GlobalVariable::getVariable('Product_Permit_Relate_Bundle_Parent', 'no');
		$SubProductBeParent = GlobalVariable::getVariable('Product_Permit_Subproduct_Be_Parent', 'no');
		if ($parentLikeSubProduct == 'yes' && $SubProductBeParent == 'no') {
			$where_relquery .=' and vtiger_products.discontinued <> 0 AND vtiger_crmentity.crmid NOT IN ('.$adb->sql_escape_string($_REQUEST['recordid']).')';
		} elseif ($parentLikeSubProduct == 'yes' && $SubProductBeParent == 'yes') {
			$crmalias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
			$where_relquery .=' and vtiger_products.discontinued <> 0 AND (vtiger_crmentity.crmid NOT IN ('.$adb->sql_escape_string($_REQUEST['recordid'])
				.") AND vtiger_crmentity.crmid NOT IN (SELECT distinct frompdo
					FROM vtiger_productcomponent
					INNER JOIN $crmalias ON vtiger_crmentity.crmid=vtiger_productcomponent.productcomponentid
					WHERE vtiger_crmentity.deleted=0 AND topdo=".$adb->sql_escape_string($_REQUEST['recordid']).'))';
		} else {
			$crmalias = CRMEntity::getcrmEntityTableAlias('ProductComponent');
			$where_relquery .=' and vtiger_products.discontinued <> 0 AND (vtiger_crmentity.crmid NOT IN ('.$adb->sql_escape_string($_REQUEST['recordid'])
				.") AND vtiger_crmentity.crmid NOT IN (SELECT distinct frompdo
					FROM vtiger_productcomponent
					INNER JOIN $crmalias ON vtiger_crmentity.crmid=vtiger_productcomponent.productcomponentid
					WHERE vtiger_crmentity.deleted=0
				) AND vtiger_crmentity.crmid NOT IN (SELECT distinct topdo
					FROM vtiger_productcomponent
					INNER JOIN $crmalias ON vtiger_crmentity.crmid=vtiger_productcomponent.productcomponentid
					WHERE vtiger_crmentity.deleted=0 AND frompdo=".$adb->sql_escape_string($_REQUEST['recordid']).'))';
		}
	}
	$smarty->assign('SHOW_SUBPRODUCTS', GlobalVariable::getVariable('Product_Show_Subproducts_Popup', 'no'));
	$smarty->assign('PRODUCT_PARENT_LIKE_SUBPRODUCT', GlobalVariable::getVariable('Product_Permit_Relate_Bundle_Parent', 'no'));
	$smarty->assign('SUBPRODUCT_BE_PARENT', GlobalVariable::getVariable('Product_Permit_Subproduct_Be_Parent', 'no'));
	if ($currentModule == 'Services' && $popuptype == 'inventory_service') {
		$where_relquery .=' and vtiger_service.discontinued <> 0';
	}

	if ($currentModule == 'PriceBooks') {
		$where_relquery .=' and vtiger_pricebook.active=1';
	}

	//Avoiding Current Record to show up in the popups When editing.
	if ($currentModule == 'Accounts' && !empty($_REQUEST['recordid'])) {
		$where_relquery .=' and vtiger_account.accountid!='.$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign('RECORDID', vtlib_purify($_REQUEST['recordid']));
	}

	if ($currentModule == 'Contacts' && !empty($_REQUEST['recordid'])) {
		$where_relquery .=' and vtiger_contactdetails.contactid!='.$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign('RECORDID', vtlib_purify($_REQUEST['recordid']));
	}

	if ($currentModule == 'Users' && !GlobalVariable::getVariable('Users_Select_Inactive', 1, 'Users')) {
		$where_relquery .= " and vtiger_users.status!='Inactive'";
	}
	if ($currentModule == 'Users' && !empty($_REQUEST['recordid'])) {
		$where_relquery .=' and vtiger_users.id!='.$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign('RECORDID', vtlib_purify($_REQUEST['recordid']));
	}

	$query = getListQuery($currentModule, $where_relquery);
}
$smarty->assign('RECORD_ID', '');
if ($currentModule == 'Products' && !empty($_REQUEST['record_id']) && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po')) {
	$product_name = getProductName(vtlib_purify($_REQUEST['record_id']));
	$smarty->assign('PRODUCT_NAME', $product_name);
	$smarty->assign('RECORD_ID', vtlib_purify($_REQUEST['record_id']));
}
$order_by = $focus->getOrderBy();
$sorder = $focus->getSortOrder();
$listview_header_search=getSearchListHeaderValues($focus, $currentModule, $url_string, $sorder, $order_by);
$smarty->assign('SEARCHLISTHEADER', $listview_header_search);
$smarty->assign('ALPHABETICAL', $alphabetical);
$queryGenerator = new QueryGenerator($currentModule, $current_user);
$controller = new ListViewController($adb, $current_user, $queryGenerator);
$fieldnames = $controller->getAdvancedSearchOptionString();
$fieldnames_array = $controller->getAdvancedSearchOptionArray();
$smarty->assign('FIELDNAMES', $fieldnames);
$smarty->assign('FIELDNAMES_ARRAY', $fieldnames_array);
$smarty->assign('CRITERIA_GROUPS', array());

if (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') {
	list($where, $ustring) = explode('#@@#', getWhereCondition($currentModule));
	$url_string .='&query=true'.$ustring;
}

if (isset($where) && $where != '') {
	$query .= ' and '.$where;
}

if (isset($order_by) && $order_by != '') {
	$tabname = getTableNameForField($currentModule, $order_by);
	if ($tabname !== '' && $tabname != null) {
		$query .= ' ORDER BY '.$tabname.'.'.$order_by.' '.$sorder;
	} else {
		$query .= ' ORDER BY '.$order_by.' '.$sorder;
	}
}

// vtlib customization: To override module specific popup query for a given field
$override_query = false;
if (method_exists($focus, 'getQueryByModuleField')) {
	$srcmodule=isset($_REQUEST['srcmodule']) ? vtlib_purify($_REQUEST['srcmodule']) : (isset($_REQUEST['return_module']) ? vtlib_purify($_REQUEST['return_module']) : '');
	$forrecord=isset($_REQUEST['forrecord']) ? vtlib_purify($_REQUEST['forrecord']) : (isset($_REQUEST['recordid']) ? vtlib_purify($_REQUEST['recordid']) : 0);
	$forfield =isset($_REQUEST['forfield']) ? vtlib_purify($_REQUEST['forfield']) : '';
	$override_query = $focus->getQueryByModuleField($srcmodule, $forfield, $forrecord, $query);
	if ($override_query) {
		$query = $override_query;
	}
}
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
$count_result = $adb->pquery(mkCountQuery($query), array());
$noofrows = $adb->query_result($count_result, 0, 'count');

if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
	$start = vtlib_purify($_REQUEST['start']);
	if ($start == 'last' && $noofrows > 0) {
		$start = ceil($noofrows/$list_max_entries_per_page);
	}
	if (!is_numeric($start) || $start < 1) {
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

$focus->initSortbyField($currentModule);
$focus->list_mode='search';
$focus->popup_type=$popuptype;
$url_string .='&popuptype='.$popuptype;
if (isset($_REQUEST['select']) && $_REQUEST['select'] == 'enable') {
	$url_string .='&select=enable';
}
if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '') {
	$url_string .='&return_module='.vtlib_purify($_REQUEST['return_module']);
}

if ($popuptype == 'set_return_emails') {
	$tabid = getTabid($currentModule);
	$mail_arr = getMailFields($tabid);

	if (!empty($mail_arr)) {
		$tablename = str_replace('vtiger_', '', $mail_arr['tablename']);
		$fieldname = $mail_arr['fieldname'];
		$fieldlabel = $mail_arr['fieldlabel'];
		$focus->search_fields[$fieldlabel] = array($tablename=>$fieldname);
		$focus->search_fields_name[$fieldlabel] = $fieldname;
	}
}

$listview_header = getSearchListViewHeader($focus, $currentModule, $url_string, $sorder, $order_by);
$smarty->assign('LISTHEADER', $listview_header);
$smarty->assign('HEADERCOUNT', count($listview_header)+1);

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
$validationData = getDBValidationData($focus->tab_name, $tabid);
$validationArray = split_validationdataArray($validationData);

$smarty->assign('VALIDATION_DATA_FIELDNAME', $validationArray['fieldname']);
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', $validationArray['datatype']);
$smarty->assign('VALIDATION_DATA_FIELDLABEL', $validationArray['fieldlabel']);

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
