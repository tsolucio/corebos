<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;

require_once('Smarty_setup.php');

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();

$category = getParentTab($currentModule);
$record = $_REQUEST['record'];
$isduplicate = vtlib_purify($_REQUEST['isDuplicate']);

//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

if($record) {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, $currentModule);
	$service_base_currency = getProductBaseCurrency($focus->id,$currentModule);
} else {
	$service_base_currency = fetchCurrency($current_user->id);
}

if($isduplicate == 'true') {
	$focus->id = '';
	$focus->mode = '';
}
if(empty($_REQUEST['record']) && $focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}

//needed when creating a new service with a default vtiger_vendor name to passed
if (isset($_REQUEST['name']) && is_null($focus->name)) {
	$focus->name = $_REQUEST['name'];
}

$disp_view = getView($focus->mode);
	$smarty->assign('BLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields));

$smarty->assign('OP_MODE',$disp_view);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
// TODO: Update Single Module Instance name here.
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('CATEGORY', $category);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('CREATEMODE', vtlib_purify($_REQUEST['createmode']));

$smarty->assign('CHECK', Button_Check($currentModule));
$smarty->assign('DUPLICATE', $isduplicate);

if($focus->mode == 'edit' || $isduplicate) {
	$recordName = array_values(getEntityName($currentModule, $record));
	$recordName = $recordName[0];
	$smarty->assign('NAME', $recordName);
	$smarty->assign('UPDATEINFO',updateInfo($record));
}

if(isset($_REQUEST['return_module']))    $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if(isset($_REQUEST['return_action']))    $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if(isset($_REQUEST['return_id']))        $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));

// Field Validation Information
$tabid = getTabid($currentModule);
$validationData = getDBValidationData($focus->tab_name,$tabid);
$validationArray = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);

// In case you have a date field
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);

global $adb;
// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if($focus->mode != 'edit' && $mod_seq_field != null) {
		$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
		$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from vtiger_modentity_num where semodule = ? and active=1",array($currentModule));
        $mod_seq_prefix = $adb->query_result($mod_seq_string,0,'prefix');
        $mod_seq_no = $adb->query_result($mod_seq_string,0,'cur_id');
        if($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix.$mod_seq_no))
                echo '<br><font color="#FF0000"><b>'. getTranslatedString('LBL_DUPLICATE'). ' '. getTranslatedString($mod_seq_field['label'])
                	.' - '. getTranslatedString('LBL_CLICK') .' <a href="index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule='.$currentModule.'">'.getTranslatedString('LBL_HERE').'</a> '
                	. getTranslatedString('LBL_TO_CONFIGURE'). ' '. getTranslatedString($mod_seq_field['label']) .'</b></font>';
        else
                $smarty->assign("MOD_SEQ_ID",$autostr);
} else {
	$smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}
// END

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
// END

//Tax handling (get the available taxes only) - starts
if($focus->mode == 'edit')
{
	$retrieve_taxes = true;
	$serviceid = $focus->id;
	$tax_details = getTaxDetailsForProduct($serviceid,'available_associated');
}
elseif($_REQUEST['isDuplicate'] == 'true')
{
	$retrieve_taxes = true;
	$serviceid = $_REQUEST['record'];
	$tax_details = getTaxDetailsForProduct($serviceid,'available_associated');
}
else {
	$tax_details = getAllTaxes('available');
	$smarty->assign("PROD_MODE", "create");
}

for($i=0;$i<count($tax_details);$i++)
{
	$tax_details[$i]['check_name'] = $tax_details[$i]['taxname'].'_check';
	$tax_details[$i]['check_value'] = 0;
}

//For Edit and Duplicate we have to retrieve the service associated taxes and show them
if($retrieve_taxes)
{
	for($i=0;$i<count($tax_details);$i++)
	{
		$tax_value = getProductTaxPercentage($tax_details[$i]['taxname'],$serviceid);
		$tax_details[$i]['percentage'] = $tax_value;
		$tax_details[$i]['check_value'] = 1;
		//if the tax is not associated with the service then we should get the default value and unchecked
		if($tax_value == '')
		{
			$tax_details[$i]['check_value'] = 0;
			$tax_details[$i]['percentage'] = getTaxPercentage($tax_details[$i]['taxname']);
		}
	}
}

$smarty->assign("TAX_DETAILS", $tax_details);
//Tax handling - ends

$unit_price = $focus->column_fields['unit_price'];
$price_details = getPriceDetailsForProduct($serviceid, $unit_price, 'available',$currentModule);
$smarty->assign("PRICE_DETAILS", $price_details);

$base_currency = 'curname' . $service_base_currency;
$smarty->assign("BASE_CURRENCY", $base_currency);

$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($currentModule);
$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", Zend_Json::encode($picklistDependencyDatasource));

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));

if($focus->mode == 'edit') {
	$smarty->display('Inventory/InventoryEditView.tpl');
} else {
	$smarty->display('Inventory/InventoryCreateView.tpl');
}

?>