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
require_once 'include/utils/CommonUtils.php';

global $mod_strings, $current_user, $app_strings, $currentModule, $theme;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty;
$focus = CRMEntity::getInstance($currentModule);

$qcreate_array = QuickCreate($currentModule);
$validationData = $qcreate_array['data'];
$data = split_validationdataArray($validationData);
$smarty->assign('QUICKCREATE', $qcreate_array['form']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('ACTIVITY_MODE', (isset($_REQUEST['activity_mode']) ? vtlib_purify($_REQUEST['activity_mode']) : ''));
$smarty->assign('FROM', (isset($_REQUEST['from']) ? vtlib_purify($_REQUEST['from']) : ''));
$smarty->assign('URLPOPUP', (isset($_REQUEST['pop']) ? str_replace('-a;', '&', $_REQUEST['pop']) : ''));
$smarty->assign('MASS_EDIT', '0');
$smarty->assign('QCMODULE', getTranslatedString('SINGLE_'.$currentModule, $currentModule));
$smarty->assign('USERID', $current_user->id);
$smarty->assign('VALIDATION_DATA_FIELDNAME', $data['fieldname']);
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', $data['datatype']);
$smarty->assign('VALIDATION_DATA_FIELDLABEL', $data['fieldlabel']);
$smarty->assign('MODULE', $currentModule);
//Start - Add multi currency
$service_base_currency = fetchCurrency($current_user->id);
$price_details = getPriceDetailsForProduct('', '', 'available', $currentModule);
$smarty->assign('PRICE_DETAILS', $price_details);
$base_currency = 'curname' . $service_base_currency;
$smarty->assign('BASE_CURRENCY', $base_currency);
//End - add multi currency

//Tax handling (get the available taxes only) - starts
$smarty->assign('MODE', $focus->mode);
if ($focus->mode == 'edit') {
	$retrieve_taxes = true;
	$serviceid = $focus->id;
	$tax_details = getTaxDetailsForProduct($serviceid, 'available_associated');
} elseif (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$retrieve_taxes = true;
	$serviceid = vtlib_purify($_REQUEST['record']);
	$tax_details = getTaxDetailsForProduct($serviceid, 'available_associated');
} else {
	$retrieve_taxes = false;
	$serviceid = 0;
	$tax_details = getAllTaxes('available');
}

for ($i=0; $i<count($tax_details); $i++) {
	$tax_details[$i]['check_name'] = $tax_details[$i]['taxname'].'_check';
	$tax_details[$i]['check_value'] = 0;
}

//For Edit and Duplicate we have to retrieve the services associated taxes and show them
if ($retrieve_taxes) {
	for ($i=0; $i<count($tax_details); $i++) {
		$tax_value = getProductTaxPercentage($tax_details[$i]['taxname'], $serviceid);
		$tax_details[$i]['percentage'] = $tax_value;
		$tax_details[$i]['check_value'] = 1;
		//if the tax is not associated with the services then we should get the default value and unchecked
		if ($tax_value == '') {
			$tax_details[$i]['check_value'] = 0;
			$tax_details[$i]['percentage'] = getTaxPercentage($tax_details[$i]['taxname']);
		}
	}
}
$smarty->assign('TAX_DETAILS', $tax_details);
//Tax handling - ends

$smarty->display('QuickCreate.tpl');
?>
