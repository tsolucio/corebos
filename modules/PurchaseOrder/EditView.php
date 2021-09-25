<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $log, $current_user;
require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';
require_once 'include/CustomFieldUtil.php';
require_once 'include/utils/utils.php';

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();

$massedit1x1 = isset($_REQUEST['massedit1x1']) ? vtlib_purify($_REQUEST['massedit1x1']) : '0';
if ($massedit1x1=='s') { // mass edit 1x1 start
	$idstring = getSelectedRecords(
		$_REQUEST,
		$currentModule,
		(isset($_REQUEST['allselectedboxes']) ? trim($_REQUEST['allselectedboxes'], ';') : ''),
		(isset($_REQUEST['excludedRecords']) ? trim($_REQUEST['excludedRecords'], ';') : '')
	);
	coreBOS_Session::set('ME1x1Info', array(
		'complete' => $idstring,
		'processed' => array(),
		'pending' => $idstring,
		'next' => $idstring[0],
	));
}
if (coreBOS_Session::has('ME1x1Info')) {
	$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
	$smarty->assign('MED1x1MODE', 1);
	$smarty->assign('CANCELGO', 'index.php?action=ListView&massedit1x1=c&module='.$currentModule);
	$_REQUEST['record'] = $ME1x1Info['next'];
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
	$memsg = getTranslatedString('LBL_MASS_EDIT').':&nbsp;'.getTranslatedString('LBL_RECORD').(count($ME1x1Info['processed'])+1).'/'.count($ME1x1Info['complete']);
	$smarty->assign('ERROR_MESSAGE', $memsg);
	$smarty->assign('gobackBTN', count($ME1x1Info['processed'])==0);
} else {
	$smarty->assign('MED1x1MODE', 0);
}
if (!empty($_REQUEST['saverepeat'])) {
	$_REQUEST = array_merge($_REQUEST, coreBOS_Session::get('saverepeatRequest', array()));
	if (isset($_REQUEST['CANCELGO'])) {
		$smarty->assign('CANCELGO', vtlib_purify($_REQUEST['CANCELGO']));
	}
} else {
	coreBOS_Session::set('saverepeatRequest', $_REQUEST);
}
$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);

$record = isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : null;
$isduplicate = isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : null;

$searchurl = getBasic_Advance_SearchURL();
$smarty->assign('SEARCH', $searchurl);

$currencyid = fetchCurrency($current_user->id);
$rate_symbol = getCurrencySymbolandCRate($currencyid);
$rate = $rate_symbol['rate'];
$associated_prod = array();
$smarty->assign('CONVERT_MODE', '');
if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, 'PurchaseOrder');
	$focus->name=$focus->column_fields['subject'];
}
if ($isduplicate == 'true') {
	$smarty->assign('DUPLICATE_FROM', $focus->id);
	$PO_associated_prod = getAssociatedProducts($currentModule, $focus);
	$inventory_cur_info = getInventoryCurrencyInfo($currentModule, $focus->id);
	$currencyid = $inventory_cur_info['currency_id'];
	$focus->id = '';
	$focus->mode = '';
	$smarty->assign('__cbisduplicatedfromrecordid', $record);
}
// add products when converting from a salesorder
if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'SalesOrder' && $_REQUEST['salesorderid'] != '' && $_REQUEST['createmode'] == 'link') {
	// Created from a sales order
	require_once 'modules/SalesOrder/SalesOrder.php';

	$so_focus = new SalesOrder();
	$so_focus->id = $_REQUEST['salesorderid'];
	$so_focus->retrieve_entity_info($_REQUEST['salesorderid'], 'SalesOrder');

	$associated_prod = getAssociatedProducts('SalesOrder', $so_focus);
	$txtTax = ((!empty($so_focus->column_fields['txtTax'])) ? $so_focus->column_fields['txtTax'] : '0.000');
	$txtAdj = ((!empty($so_focus->column_fields['txtAdjustment'])) ? $so_focus->column_fields['txtAdjustment'] : '0.000');
	$smarty->assign('ASSOCIATEDPRODUCTS', $associated_prod);
	$smarty->assign('MODE', $so_focus->mode);
	$smarty->assign('AVAILABLE_PRODUCTS', 'true');
}
// End addition to get products when coming from a salesorder
$focus->preEditCheck($_REQUEST, $smarty);
if (!empty($_REQUEST['save_error']) && $_REQUEST['save_error'] == 'true') {
	if (!empty($_REQUEST['encode_val'])) {
		global $current_user;
		$encode_val = vtlib_purify($_REQUEST['encode_val']);
		$decode_val = base64_decode($encode_val);
		$explode_decode_val = explode('&', trim($decode_val, '&'));
		$tabid = getTabid($currentModule);
		foreach ($explode_decode_val as $fieldvalue) {
			$value = explode('=', $fieldvalue);
			$field_name_val = $value[0];
			$field_value =urldecode($value[1]);
			$finfo = VTCacheUtils::lookupFieldInfo($tabid, $field_name_val);
			if ($finfo !== false) {
				switch ($finfo['uitype']) {
					case '56':
						$field_value = $field_value=='on' ? '1' : '0';
						break;
					case '7':
					case '9':
					case '72':
						$field_value = CurrencyField::convertToDBFormat($field_value, null, true);
						break;
					case '71':
						$field_value = CurrencyField::convertToDBFormat($field_value);
						break;
					case '33':
					case '3313':
					case '3314':
						if (is_array($field_value)) {
							$field_value = implode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $field_value);
						}
						break;
				}
			}
			$focus->column_fields[$field_name_val] = $field_value;
		}
	}
	$errormessageclass = isset($_REQUEST['error_msgclass']) ? vtlib_purify($_REQUEST['error_msgclass']) : '';
	$errormessage = isset($_REQUEST['error_msg']) ? vtlib_purify($_REQUEST['error_msg']) : '';
	$smarty->assign('ERROR_MESSAGE_CLASS', $errormessageclass);
	$smarty->assign('ERROR_MESSAGE', $errormessage);
} elseif ($focus->mode != 'edit') {
	setObjectValuesFromRequest($focus);
}
if (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] !='') {
	$focus->column_fields['product_id'] = $_REQUEST['product_id'];
	$associated_prod = getAssociatedProducts('Products', $focus, $focus->column_fields['product_id']);
	for ($i=1; $i<=count($associated_prod); $i++) {
		$associated_prod_id = $associated_prod[$i]['hdnProductId'.$i];
		$associated_prod_prices = getPricesForProducts($currencyid, array($associated_prod_id), 'Products', false);
		$associated_prod[$i]['listPrice'.$i] = $associated_prod_prices[$associated_prod_id];
	}
	$smarty->assign('ASSOCIATEDPRODUCTS', $associated_prod);
	$smarty->assign('AVAILABLE_PRODUCTS', 'true');
	$smarty->assign('MODE', $focus->mode);
	$result = $adb->pquery('select vendor_id from vtiger_products where productid=?', array($_REQUEST['product_id']));
	if ($result && $adb->num_rows($result)>0) {
		$_REQUEST['vendor_id'] = $adb->query_result($result, 0, 'vendor_id');
	}
}
if (!empty($_REQUEST['parent_id']) && !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Services') {
	$focus->column_fields['product_id'] = vtlib_purify($_REQUEST['parent_id']);
	$associated_prod = getAssociatedProducts('Services', $focus, $focus->column_fields['product_id']);
	for ($i=1; $i<=count($associated_prod); $i++) {
		$associated_prod_id = $associated_prod[$i]['hdnProductId'.$i];
		$associated_prod_prices = getPricesForProducts($currencyid, array($associated_prod_id), 'Services');
		$associated_prod[$i]['listPrice'.$i] = $associated_prod_prices[$associated_prod_id];
	}
	$smarty->assign('ASSOCIATEDPRODUCTS', $associated_prod);
	$smarty->assign('AVAILABLE_PRODUCTS', 'true');
}

if (!empty($_REQUEST['vendor_id']) && $_REQUEST['record']=='') {
	$focus->column_fields['vendor_id'] = vtlib_purify($_REQUEST['vendor_id']);
}
$smarty->assign('MASS_EDIT', '0');
$disp_view = getView($focus->mode);
$blocks = getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields);
$smarty->assign('BLOCKS', $blocks);
$basblocks = getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields, 'BAS');
$smarty->assign('BASBLOCKS', $basblocks);
$advblocks = getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields, 'ADV');
$smarty->assign('ADVBLOCKS', $advblocks);

$custom_blocks = getCustomBlocks($currentModule, $disp_view);
$smarty->assign('CUSTOMBLOCKS', $custom_blocks);
$smarty->assign('FIELDS', $focus->column_fields);

$smarty->assign('OP_MODE', $disp_view);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('CREATEMODE', isset($_REQUEST['createmode']) ? vtlib_purify($_REQUEST['createmode']) : '');

$smarty->assign('CHECK', Button_Check($currentModule));
$smarty->assign('DUPLICATE', $isduplicate);

if ($focus->mode == 'edit' || $isduplicate == 'true') {
	$recordName = array_values(getEntityName($currentModule, $record));
	$recordName = isset($recordName[0]) ? $recordName[0] : '';
	$smarty->assign('NAME', $recordName);
	$smarty->assign('UPDATEINFO', updateInfo($record));
}

if ($focus->mode == 'edit') {
	$associated_prod = getAssociatedProducts('PurchaseOrder', $focus);
	$smarty->assign('ASSOCIATEDPRODUCTS', $associated_prod);
	$smarty->assign('AVAILABLE_PRODUCTS', (empty($associated_prod) ? 'false' : 'true'));
} elseif ($isduplicate == 'true') {
	$associated_prod = $PO_associated_prod;
	$smarty->assign('AVAILABLE_PRODUCTS', 'true');
	$smarty->assign('MODE', $focus->mode);
}
if (empty($associated_prod) && $isduplicate != 'true') { // creating
	include_once 'modules/cbMap/processmap/MasterDetailLayout.php';
	$associated_prod = MasterDetailLayout::setCreateAsociatedProductsValue($currentModule, $smarty);
}

list($v1, $v2, $associated_prod, $customtemplatename) = cbEventHandler::do_filter('corebos.filter.inventory.itemrow.edit', array($currentModule, $focus, $associated_prod, ''));
$smarty->assign('customtemplaterows', $customtemplatename);
$smarty->assign('ASSOCIATEDPRODUCTS', $associated_prod);

if (isset($_REQUEST['return_module'])) {
	$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
} else {
	$smarty->assign('RETURN_MODULE', 'PurchaseOrder');
}
if (isset($_REQUEST['return_action'])) {
	$smarty->assign('RETURN_ACTION', vtlib_purify($_REQUEST['return_action']));
} else {
	$smarty->assign('RETURN_ACTION', 'index');
}
if (isset($_REQUEST['return_id'])) {
	$smarty->assign('RETURN_ID', vtlib_purify($_REQUEST['return_id']));
}
if (isset($_REQUEST['return_viewname'])) {
	$smarty->assign('RETURN_VIEWNAME', vtlib_purify($_REQUEST['return_viewname']));
}
$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000, $currentModule);
$smarty->assign('UPLOADSIZE', $upload_maxsize/1000000); //Convert to MB
$smarty->assign('UPLOAD_MAXSIZE', $upload_maxsize);

// Field Validation Information
$tabid = getTabid($currentModule);
$validationData = getDBValidationData($focus->tab_name, $tabid);
$validationArray = split_validationdataArray($validationData);

$smarty->assign('VALIDATION_DATA_FIELDNAME', $validationArray['fieldname']);
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', $validationArray['datatype']);
$smarty->assign('VALIDATION_DATA_FIELDLABEL', $validationArray['fieldlabel']);

// In case you have a date field
$smarty->assign('CALENDAR_LANG', $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign('CALENDAR_DATEFORMAT', parse_calendardate($app_strings['NTC_DATE_FORMAT']));

// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($focus->mode != 'edit' && $mod_seq_field != null) {
	$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
	list($mod_seq_string, $mod_seq_prefix, $mod_seq_no, $doNative) = cbEventHandler::do_filter('corebos.filter.ModuleSeqNumber.get', array('', '', '', true));
	if ($doNative) {
		$mod_seq_string = $adb->pquery('SELECT prefix, cur_id from vtiger_modentity_num where semodule = ? and active=1', array($currentModule));
		$mod_seq_prefix = $adb->query_result($mod_seq_string, 0, 'prefix');
		$mod_seq_no = $adb->query_result($mod_seq_string, 0, 'cur_id');
	}
	if ($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix.$mod_seq_no)) {
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		$smarty->assign('ERROR_MESSAGE', '<b>'. getTranslatedString($mod_seq_field['label']). ' '. getTranslatedString('LBL_NOT_CONFIGURED')
			.' - '. getTranslatedString('LBL_PLEASE_CLICK') .' <a href="index.php?module=Settings&action=CustomModEntityNo&selmodule='
			.$currentModule.'">'.getTranslatedString('LBL_HERE').'</a> '.getTranslatedString('LBL_TO_CONFIGURE').' '.getTranslatedString($mod_seq_field['label']).'</b>');
	} else {
		$smarty->assign('MOD_SEQ_ID', $autostr);
	}
} else {
	if (!empty($mod_seq_field) && !empty($mod_seq_field['name']) && !empty($focus->column_fields[$mod_seq_field['name']])) {
		$smarty->assign('MOD_SEQ_ID', $focus->column_fields[$mod_seq_field['name']]);
	} else {
		$smarty->assign('MOD_SEQ_ID', '');
	}
}

//if create PO, get all available product taxes and shipping & Handling taxes
if ($focus->mode != 'edit') {
	$tax_details = getAllTaxes('available');
	$sh_tax_details = getAllTaxes('available', 'sh');
} else {
	$tax_details = getAllTaxes('available', '', $focus->mode, $focus->id);
	$sh_tax_details = getAllTaxes('available', 'sh', 'edit', $focus->id);
}
$smarty->assign('GROUP_TAXES', $tax_details);
$smarty->assign('SH_TAXES', $sh_tax_details);

$smarty->assign('CURRENCIES_LIST', getAllCurrencies());
if ($focus->mode == 'edit') {
	$inventory_cur_info = getInventoryCurrencyInfo('PurchaseOrder', $focus->id);
	$smarty->assign('INV_CURRENCY_ID', $inventory_cur_info['currency_id']);
} else {
	$smarty->assign('INV_CURRENCY_ID', $currencyid);
}

// Gather the custom link information to display
include_once 'vtlib/Vtiger/Link.php';
$customlink_params = array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$smarty->assign(
	'CUSTOM_LINKS',
	Vtiger_Link::getAllByType($tabid, array('EDITVIEWBUTTON','EDITVIEWBUTTONMENU','EDITVIEWWIDGET'), $customlink_params, null, $focus->id)
);
// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
$smarty->assign('Module_Popup_Edit', isset($_REQUEST['Module_Popup_Edit']) ? vtlib_purify($_REQUEST['Module_Popup_Edit']) : 0);
$smarty->assign('SandRActive', GlobalVariable::getVariable('Application_SaveAndRepeatActive', 0, $currentModule));
$cbMapFDEP = Vtiger_DependencyPicklist::getFieldDependencyDatasource($currentModule);
$smarty->assign('FIELD_DEPENDENCY_DATASOURCE', json_encode($cbMapFDEP));
//Get Service or Product by default when create
$smarty->assign('PRODUCT_OR_SERVICE', GlobalVariable::getVariable('Inventory_ProductService_Default', 'Products', $currentModule, $current_user->id));
$smarty->assign('Inventory_ListPrice_ReadOnly', GlobalVariable::getVariable('Inventory_ListPrice_ReadOnly', '0', $currentModule, $current_user->id));
$smarty->assign('Inventory_Comment_Style', GlobalVariable::getVariable('Inventory_Comment_Style', 'width:70%;height:40px;', $currentModule, $current_user->id));
$smarty->assign('Application_Textarea_Style', GlobalVariable::getVariable('Application_Textarea_Style', 'height:140px;', $currentModule, $current_user->id));
//Set taxt type group or individual by default when create
$smarty->assign('TAX_TYPE', GlobalVariable::getVariable('Inventory_Tax_Type_Default', 'individual', $currentModule, $current_user->id));
$smarty->assign('TAXFILLINMODE', GlobalVariable::getVariable('Inventory_Tax_FillInMode', 'All', $currentModule, $current_user->id));
//Show or not the Header to copy address to left or right
$smarty->assign('SHOW_COPY_ADDRESS', GlobalVariable::getVariable('Application_Show_Copy_Address', 1, $currentModule, $current_user->id));
$smarty->assign('SHOW_SHIPHAND_CHARGES', GlobalVariable::getVariable('Inventory_Show_ShippingHandlingCharges', 1, $currentModule, $current_user->id));
$smarty->assign('ShowInventoryLines', strpos(GlobalVariable::getVariable('Inventory_DoNotUseLines', '', $currentModule, $current_user->id), $currentModule)===false);

$smarty->display('Inventory/InventoryEditView.tpl');
?>
