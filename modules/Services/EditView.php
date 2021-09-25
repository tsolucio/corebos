<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb;
require_once 'Smarty_setup.php';

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

if ($record) {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, $currentModule);
	$service_base_currency = getProductBaseCurrency($focus->id, $currentModule);
} else {
	$service_base_currency = fetchCurrency($current_user->id);
}
if ($isduplicate == 'true') {
	$focus->id = '';
	$focus->mode = '';
	$focus->column_fields['isduplicatedfromrecordid'] = $record; // in order to support duplicate workflows
	$smarty->assign('__cbisduplicatedfromrecordid', $record);
}
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

if (isset($_REQUEST['return_module'])) {
	$smarty->assign('RETURN_MODULE', vtlib_purify($_REQUEST['return_module']));
}
if (isset($_REQUEST['return_action'])) {
	$smarty->assign('RETURN_ACTION', vtlib_purify($_REQUEST['return_action']));
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

//Tax handling (get the available taxes only) - starts
if ($focus->mode == 'edit') {
	$retrieve_taxes = true;
	$serviceid = $focus->id;
	$tax_details = getTaxDetailsForProduct($serviceid, 'available_associated');
} elseif ($isduplicate == 'true') {
	$retrieve_taxes = true;
	$serviceid = $_REQUEST['record'];
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

//For Edit and Duplicate we have to retrieve the service associated taxes and show them
if ($retrieve_taxes) {
	for ($i=0; $i<count($tax_details); $i++) {
		$tax_value = getProductTaxPercentage($tax_details[$i]['taxname'], $serviceid);
		$tax_details[$i]['percentage'] = $tax_value;
		$tax_details[$i]['check_value'] = 1;
		//if the tax is not associated with the service then we should get the default value and unchecked
		if ($tax_value == '') {
			$tax_details[$i]['check_value'] = 0;
			$tax_details[$i]['percentage'] = getTaxPercentage($tax_details[$i]['taxname']);
		}
	}
}

$smarty->assign('TAX_DETAILS', $tax_details);
//Tax handling - ends

$unit_price = $focus->column_fields['unit_price'];
$price_details = getPriceDetailsForProduct($serviceid, $unit_price, 'available', $currentModule);
$smarty->assign("PRICE_DETAILS", $price_details);

$base_currency = 'curname' . $service_base_currency;
$smarty->assign('BASE_CURRENCY', $base_currency);
$smarty->assign('CONVERT_MODE', '');
$smarty->assign('SandRActive', GlobalVariable::getVariable('Application_SaveAndRepeatActive', 0, $currentModule));
$cbMapFDEP = Vtiger_DependencyPicklist::getFieldDependencyDatasource($currentModule);
$smarty->assign('FIELD_DEPENDENCY_DATASOURCE', json_encode($cbMapFDEP));
$smarty->assign('Application_Textarea_Style', GlobalVariable::getVariable('Application_Textarea_Style', 'height:140px;', $currentModule, $current_user->id));
$smarty->display('Inventory/InventoryEditView.tpl');
?>
