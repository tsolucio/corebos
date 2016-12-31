<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
global $app_strings, $default_charset, $currentModule, $current_user, $theme, $adb;
$url_string = '';
$smarty = new vtigerCRM_Smarty;
if (!isset($where)) $where = "";

$parent_tab=getParentTab();
$smarty->assign("CATEGORY",$parent_tab);

$url = '';
$popuptype = '';
$popuptype = isset($_REQUEST['popuptype']) ? vtlib_purify($_REQUEST['popuptype']) : '';

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
// Pass on the authenticated user language
global $current_language;
$smarty->assign('LANGUAGE', $current_language);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("THEME_PATH",$theme_path);
$smarty->assign("MODULE",$currentModule);
$smarty->assign('coreBOS_uiapp_name', GlobalVariable::getVariable('Application_UI_Name',$coreBOS_app_name));
// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$hdrcustomlink_params = Array('MODULE'=>$currentModule);
$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, Array('HEADERSCRIPT_POPUP', 'HEADERCSS_POPUP'), $hdrcustomlink_params);
$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT_POPUP']);
$smarty->assign('HEADERCSS', $COMMONHDRLINKS['HEADERCSS_POPUP']);
// END

$qc_modules = getQuickCreateModules();
for ($i=0;$i<count($qc_modules);$i++)  $qcmod[$i]=$qc_modules[$i][1];
$smarty->assign("QCMODULEARRAY", $qcmod);
$suri=vtlib_purify($_SERVER["REQUEST_URI"]);
$suri=substr($suri,strpos($suri,'?')+1);
$smarty->assign("POPUP", str_replace('&','-a;',$suri).'-a;popqc=true');

if (!empty($_REQUEST['popqc']) and $_REQUEST['popqc'] = 'true' and empty($_REQUEST['advft_criteria']) and !empty($_REQUEST['record'])) {
	$fldrs = $adb->query("SELECT vtiger_field.fieldlabel,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldname,vtiger_entityname.entityidfield
			FROM vtiger_field
			INNER JOIN vtiger_entityname on vtiger_field.tabid=vtiger_entityname.tabid and modulename='$currentModule' WHERE uitype=4");
	$row = $adb->fetch_array($fldrs);
	$fieldLabelEscaped = str_replace(" ","_",$row['fieldlabel']);
	$optionvalue = $row['tablename'].":".$row['columnname'].":".$row['fieldname'].":".$currentModule."_".$fieldLabelEscaped.":V";
	$fldvalrs = $adb->query('select '.$row['columnname'].' from '.$row['tablename'].' inner join vtiger_crmentity on crmid = '.$row['entityidfield'].' where '.$row['entityidfield'].'='.$_REQUEST['record'].' ORDER BY createdtime DESC LIMIT 1');
	$fldval = $adb->query_result($fldvalrs,0,0);
	$_REQUEST['searchtype']='advance';
	$_REQUEST['query'] = 'true';
	$_REQUEST['advft_criteria'] = '[{"groupid":"1","columnname":"'.$optionvalue.'","comparator":"e","value":"'.$fldval.'","columncondition":""}]';
}

$form = vtlib_purify($_REQUEST['form']);
//added to get relatedto field value for todo, while selecting from the popup list, after done the alphabet or basic search.
if(isset($_REQUEST['maintab']) && $_REQUEST['maintab'] != '')
{
	$act_tab = vtlib_purify($_REQUEST['maintab']);
	$url = "&maintab=".$act_tab;
} else {
	$act_tab = '';
}
$smarty->assign("MAINTAB",$act_tab);

// This is added to support the type of popup and callback
if(isset($_REQUEST['popupmode']) && isset($_REQUEST['callback'])) {
	$url = "&popupmode=".vtlib_purify($_REQUEST['popupmode'])."&callback=".vtlib_purify($_REQUEST['callback']);
	$smarty->assign("POPUPMODE", vtlib_purify($_REQUEST['popupmode']));
	$smarty->assign("CALLBACK", vtlib_purify($_REQUEST['callback']));
}

$focus = CRMEntity::getInstance($currentModule);
$smarty->assign('CURR_ROW', 0);
$smarty->assign('FIELDNAME', '');
$smarty->assign('PRODUCTID', 0);
$smarty->assign('RECORDID', 0);
$smarty->assign('SELECT', '');
switch($currentModule)
{
	case 'Contacts':
		$log = LoggerManager::getLogger('contact_list');
		$smarty->assign("SINGLE_MOD",'Contact');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','lastname','true','basic',$popuptype,"","",$url);
		break;
	case 'Campaigns':
		$log = LoggerManager::getLogger('campaign_list');
		$smarty->assign("SINGLE_MOD",'Campaign');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','campaignname','true','basic',$popuptype,"","",$url);
		break;
	case 'Accounts':
		$log = LoggerManager::getLogger('account_list');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$smarty->assign("SINGLE_MOD",'Account');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','accountname','true','basic',$popuptype,"","",$url);
		break;
	case 'Leads':
		$log = LoggerManager::getLogger('contact_list');
		$smarty->assign("SINGLE_MOD",'Lead');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','lastname','true','basic',$popuptype,"","",$url);
		break;
	case 'Potentials':
		$log = LoggerManager::getLogger('potential_list');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$smarty->assign("SINGLE_MOD",'Opportunity');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','potentialname','true','basic',$popuptype,"","",$url);
		break;
	case 'Quotes':
		$log = LoggerManager::getLogger('quotes_list');
		$smarty->assign("SINGLE_MOD",'Quote');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		break;
	case 'Invoice':
		$smarty->assign("SINGLE_MOD",'Invoice');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'Products':
		$smarty->assign("SINGLE_MOD",getTranslatedString('SINGLE_'.$currentModule));
		if(isset($_REQUEST['curr_row']))
		{
			$curr_row = vtlib_purify($_REQUEST['curr_row']);
			$smarty->assign("CURR_ROW", $curr_row);
			$url_string .="&curr_row=".vtlib_purify($_REQUEST['curr_row']);
		}
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','productname','true','basic',$popuptype,"","",$url);
		$smarty->assign('Product_Default_Units', GlobalVariable::getVariable('Product_Default_Units', ''));
		break;
	case 'Vendors':
		$smarty->assign("SINGLE_MOD",'Vendor');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','vendorname','true','basic',$popuptype,"","",$url);
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		break;
	case 'SalesOrder':
		$smarty->assign("SINGLE_MOD",'SalesOrder');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'PurchaseOrder':
		$smarty->assign("SINGLE_MOD",'PurchaseOrder');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'PriceBooks':
		$smarty->assign("SINGLE_MOD",'PriceBook');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		if(isset($_REQUEST['fldname']) && $_REQUEST['fldname'] !='')
		{
			$smarty->assign("FIELDNAME",vtlib_purify($_REQUEST['fldname']));
			$url_string .="&fldname=".vtlib_purify($_REQUEST['fldname']);
		}
		if(isset($_REQUEST['productid']) && $_REQUEST['productid'] !='')
		{
			$smarty->assign("PRODUCTID",vtlib_purify($_REQUEST['productid']));
			$url_string .="&productid=".vtlib_purify($_REQUEST['productid']);
		}
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','bookname','true','basic',$popuptype,"","",$url);
		break;
	case 'Users':
		$smarty->assign("SINGLE_MOD", 'Users');
		if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '')
			$smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule, 'Popup', 'user_name', 'true', 'basic', $popuptype, "", "", $url);
		if (isset($_REQUEST['select']))
			$smarty->assign("SELECT", 'enable');
		break;
	case 'HelpDesk':
		$smarty->assign("SINGLE_MOD",'HelpDesk');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','ticket_title','true','basic',$popuptype,"","",$url);
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		break;

	case 'Documents':
		$smarty->assign("SINGLE_MOD",'Document');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','notes_title','true','basic',$popuptype,"","",$url);
		break;

	// Special case handling (for curr_row value) for Services module
	case 'Services':
		if(isset($_REQUEST['curr_row']))
		{
			$curr_row = vtlib_purify($_REQUEST['curr_row']);
			$smarty->assign("CURR_ROW", $curr_row);
			$url_string .="&curr_row=".vtlib_purify($_REQUEST['curr_row']);
		}
		$smarty->assign('Service_Default_Units', GlobalVariable::getVariable('Service_Default_Units', ''));
	// vtlib customization: Generic hook for Popup selection
	default:
		$smarty->assign("SINGLE_MOD", $currentModule);
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup',$focus->def_basicsearch_col,'true','basic',$popuptype,"","",$url);
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		break;
	// END
}
// vtlib customization: Initialize focus to get generic popup
if($_REQUEST['form'] == 'vtlibPopupView' or $_REQUEST['form'] == 'DetailView') {
	vtlib_setup_modulevars($currentModule, $focus);
}
// END

$smarty->assign('RETURN_ACTION',isset($_REQUEST['return_action']) ? vtlib_purify($_REQUEST['return_action']) : '');

//Retreive the list from Database
if($currentModule == 'PriceBooks' && isset($_REQUEST['productid']))
{
	$productid= isset($_REQUEST['productid']) ? vtlib_purify($_REQUEST['productid']) : 0;
	$currency_id= isset($_REQUEST['currencyid']) ? vtlib_purify($_REQUEST['currencyid']) : fetchCurrency($current_user->id);
	$query = 'select vtiger_pricebook.*, vtiger_pricebookproductrel.productid, vtiger_pricebookproductrel.listprice, ' .
		'vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime ' .
		'from vtiger_pricebook inner join vtiger_pricebookproductrel on vtiger_pricebookproductrel.pricebookid = vtiger_pricebook.pricebookid ' .
		'inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_pricebook.pricebookid ' .
		'where vtiger_crmentity.deleted=0 and vtiger_pricebook.currency_id='.$adb->sql_escape_string($currency_id).' and vtiger_pricebook.active=1';
	if (!empty($productid)) {
		$query.= ' and vtiger_pricebookproductrel.productid='.$adb->sql_escape_string($productid);
	}
}
else
{
	$where_relquery = '';
	if(isset($_REQUEST['recordid']) && $_REQUEST['recordid'] != '')
	{
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
		$url_string .='&recordid='.vtlib_purify($_REQUEST['recordid']);
		$where_relquery = getRelCheckquery($currentModule,$_REQUEST['return_module'],$_REQUEST['recordid']);
	}
	if(isset($_REQUEST['relmod_id']) || isset($_REQUEST['fromPotential']))
	{
		if($_REQUEST['relmod_id'] !='')
		{
			$mod = vtlib_purify($_REQUEST['parent_module']);
			$id = vtlib_purify($_REQUEST['relmod_id']);
		}
		else if($_REQUEST['fromPotential'] != '')
		{
			$mod = "Accounts";
			$id= vtlib_purify($_REQUEST['acc_id']);
		}

		$smarty->assign("mod_var_name", "parent_module");
		$smarty->assign("mod_var_value", $mod);
		$smarty->assign("recid_var_name", "relmod_id");
		$smarty->assign("recid_var_value",$id);
		$where_relquery.= getPopupCheckquery($currentModule,$mod,$id);
	}
	else if(isset($_REQUEST['task_relmod_id']))
	{
		$smarty->assign("mod_var_name", "task_parent_module");
		$smarty->assign("mod_var_value", vtlib_purify($_REQUEST['task_parent_module']));
		$smarty->assign("recid_var_name", "task_relmod_id");
		$smarty->assign("recid_var_value",vtlib_purify($_REQUEST['task_relmod_id']));
		$where_relquery.= getPopupCheckquery($currentModule, vtlib_purify($_REQUEST['task_parent_module']),  vtlib_purify($_REQUEST['task_relmod_id']));
	} else {
		$smarty->assign('recid_var_value', '');
		$smarty->assign('mod_var_name', '');
		$smarty->assign('mod_var_value', '');
		$smarty->assign('recid_var_name', '');
		$smarty->assign('recid_var_value', 0);
	}
	if($currentModule == 'Products' && !$_REQUEST['record_id'] && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po')){
		$showSubproducts = GlobalVariable::getVariable('Product_Show_Subproducts_Popup', 'no');
		if($showSubproducts == 'yes'){
			$where_relquery .=" and vtiger_products.discontinued <> 0";
		}else{
			$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_products.productid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE setype='Products'))";
		}
	}elseif($currentModule == 'Products' && $_REQUEST['record_id'] && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po'))
		$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_products.productid IN (SELECT crmid FROM vtiger_seproductsrel WHERE setype='Products' AND productid=".$adb->sql_escape_string($_REQUEST['record_id'])."))";
	elseif($currentModule == 'Products' && $_REQUEST['return_module'] != 'Products')
		$where_relquery .=" and vtiger_products.discontinued <> 0";

	if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Products' && $currentModule == 'Products' && $_REQUEST['recordid']){
		$parentLikeSubProduct = GlobalVariable::getVariable('Product_Permit_Relate_Bundle_Parent', 'no');
		$SubProductBeParent = GlobalVariable::getVariable('Product_Permit_Subproduct_Be_Parent', 'no');
		if($parentLikeSubProduct == 'yes' && $SubProductBeParent == 'no'){
			$where_relquery .=" and vtiger_products.discontinued <> 0 AND vtiger_crmentity.crmid NOT IN (".$adb->sql_escape_string($_REQUEST['recordid']).")";
		}elseif($parentLikeSubProduct == 'yes' && $SubProductBeParent == 'yes'){
			$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_crmentity.crmid NOT IN (".$adb->sql_escape_string($_REQUEST['recordid']).") AND vtiger_crmentity.crmid NOT IN (SELECT productid FROM vtiger_seproductsrel WHERE setype='Products' AND crmid=".$adb->sql_escape_string($_REQUEST['recordid'])."))";
		}else{
			$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_crmentity.crmid NOT IN (".$adb->sql_escape_string($_REQUEST['recordid']).") AND vtiger_crmentity.crmid NOT IN (SELECT productid FROM vtiger_seproductsrel WHERE setype='Products') AND vtiger_crmentity.crmid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE setype='Products' AND productid=".$adb->sql_escape_string($_REQUEST['recordid'])."))";
		}
	}
		$smarty->assign("SHOW_SUBPRODUCTS", GlobalVariable::getVariable('Product_Show_Subproducts_Popup', 'no'));
		$smarty->assign("PRODUCT_PARENT_LIKE_SUBPRODUCT", GlobalVariable::getVariable('Product_Permit_Relate_Bundle_Parent', 'no'));
		$smarty->assign("SUBPRODUCT_BE_PARENT", GlobalVariable::getVariable('Product_Permit_Subproduct_Be_Parent', 'no'));
	if($currentModule == 'Services' && $popuptype == 'inventory_service') {
		$where_relquery .=" and vtiger_service.discontinued <> 0";
	}

	if($currentModule == 'PriceBooks') {
		$where_relquery .=' and vtiger_pricebook.active=1';
	}

	//Avoiding Current Record to show up in the popups When editing.
	if($currentModule == 'Accounts' && !empty($_REQUEST['recordid'])){
		$where_relquery .=" and vtiger_account.accountid!=".$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
	}

	if($currentModule == 'Contacts' && !empty($_REQUEST['recordid'])){
		$where_relquery .=" and vtiger_contactdetails.contactid!=".$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
	}

	if($currentModule == 'Users' && !empty($_REQUEST['recordid'])){
		$where_relquery .=" and vtiger_users.id!=".$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
	}

	$query = getListQuery($currentModule,$where_relquery);
}
$smarty->assign('RECORD_ID', 0);
if($currentModule == 'Products' && !empty($_REQUEST['record_id']) && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po'))
{
	$product_name = getProductName(vtlib_purify($_REQUEST['record_id']));
	$smarty->assign("PRODUCT_NAME", $product_name);
	$smarty->assign("RECORD_ID", vtlib_purify($_REQUEST['record_id']));
}
//Added to fix the issue #2307
$order_by = $focus->getOrderBy();
$sorder = $focus->getSortOrder();
$listview_header_search=getSearchListHeaderValues($focus,$currentModule,$url_string,$sorder,$order_by);
$smarty->assign("SEARCHLISTHEADER", $listview_header_search);
$smarty->assign("ALPHABETICAL", $alphabetical);
$queryGenerator = new QueryGenerator($currentModule, $current_user);
$controller = new ListViewController($adb, $current_user, $queryGenerator);
$fieldnames = $controller->getAdvancedSearchOptionString();
$criteria = getcriteria_options();
$smarty->assign("CRITERIA", $criteria);
$smarty->assign("FIELDNAMES", $fieldnames);

if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true')
{
	list($where, $ustring) = explode("#@@#",getWhereCondition($currentModule));
	$url_string .="&query=true".$ustring;
}

if(isset($where) && $where != '')
{
	$query .= ' and '.$where;
}

if(isset($order_by) && $order_by != '')
{
	$query .= ' ORDER BY '.$order_by.' '.$sorder;
}

// vtlib customization: To override module specific popup query for a given field
$override_query = false;
if(method_exists($focus, 'getQueryByModuleField')) {
	$srcmodule = isset($_REQUEST['srcmodule']) ? vtlib_purify($_REQUEST['srcmodule']) : vtlib_purify($_REQUEST['return_module']);
	$forrecord = isset($_REQUEST['forrecord']) ? vtlib_purify($_REQUEST['forrecord']) : (isset($_REQUEST['recordid']) ? vtlib_purify($_REQUEST['recordid']) : 0);
	$forfield = isset($_REQUEST['forfield']) ? vtlib_purify($_REQUEST['forfield']) : '';
	$override_query = $focus->getQueryByModuleField($srcmodule, $forfield, $forrecord, $query);
	if($override_query) {
		$query = $override_query;
	}
}

$count_result = $adb->pquery(mkCountQuery($query), array());
$noofrows = $adb->query_result($count_result,0,'count');
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
//Retreiving the start value from request
if(isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
	$start = vtlib_purify($_REQUEST['start']);
	if($start == 'last'){
		//$count_result = $adb->pquery( mkCountQuery($query), array());
		//$noofrows = $adb->query_result($count_result,0,'count');
		if($noofrows > 0){
			$start = ceil($noofrows/$list_max_entries_per_page);
		}
	}
	if(!is_numeric($start)){
		$start = 1;
	}elseif($start < 1){
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

//Retreive the Navigation array
$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);

//Retreive the List View Table Header
$focus->initSortbyField($currentModule);
$focus->list_mode="search";
$focus->popup_type=$popuptype;
$url_string .='&popuptype='.$popuptype;
if(isset($_REQUEST['select']) && $_REQUEST['select'] == 'enable')
	$url_string .='&select=enable';
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '')
	$url_string .='&return_module='.vtlib_purify($_REQUEST['return_module']);

if($popuptype == 'set_return_emails'){
	$tabid = getTabid($currentModule);
	$mail_arr = getMailFields($tabid);

	if(!empty($mail_arr)){
		$tablename = str_replace("vtiger_","",$mail_arr['tablename']);
		$fieldname = $mail_arr['fieldname'];
		$fieldlabel = $mail_arr['fieldlabel'];
		$focus->search_fields[$fieldlabel] = Array($tablename=>$fieldname);
		$focus->search_fields_name[$fieldlabel] = $fieldname;
	}
}

$listview_header = getSearchListViewHeader($focus,"$currentModule",$url_string,$sorder,$order_by);
$smarty->assign("LISTHEADER", $listview_header);
$smarty->assign("HEADERCOUNT",count($listview_header)+1);

$listview_entries = getSearchListViewEntries($focus,"$currentModule",$list_result,$navigation_array,$form);
$smarty->assign("LISTENTITY", $listview_entries);
if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
	$record_string = getRecordRangeMessage($list_result, $limstart, $noofrows);
} else {
	$record_string = '';
}

$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string,$currentModule,"Popup");
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("RECORD_STRING", $record_string);
$smarty->assign("RECORD_COUNTS", $noofrows);
$smarty->assign("POPUPTYPE", $popuptype);
$smarty->assign("PARENT_MODULE", isset($_REQUEST['parent_module']) ? vtlib_purify($_REQUEST['parent_module']) : '');

// Field Validation Information
$tabid = getTabid($currentModule);
$validationData = getDBValidationData($focus->tab_name,$tabid);
$validationArray = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("PopupContents.tpl");
else
	$smarty->display("Popup.tpl");

?>
