<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';

/**
 * Function to get vtiger_field typename
 * @param $uitype :: uitype -- Type integer
 * returns the vtiger_field type name -- Type string
 */
function getCustomFieldTypeName($uitype) {
	global $mod_strings, $log;
	$log->debug('> getCustomFieldTypeName '.$uitype);
	$fldname = '';

	/*
	 * salutation type is an exception where the uitype 55 is considered to be as text.
	 */

	if ($uitype == 1 || $uitype == 2 || $uitype == 55 || $uitype == 255) {
		$fldname = $mod_strings['Text'];
	} elseif ($uitype == 7) {
		$fldname = $mod_strings['Number'];
	} elseif ($uitype == 9) {
		$fldname = $mod_strings['Percent'];
	} elseif ($uitype == 5 || $uitype == 23) {
		$fldname = $mod_strings['Date'];
	} elseif ($uitype == 13) {
		$fldname = $mod_strings['Email'];
	} elseif ($uitype == 11) {
		$fldname = $mod_strings['Phone'];
	} elseif ($uitype == 15) {
		$fldname = $mod_strings['PickList'];
	} elseif ($uitype == 17) {
		$fldname = $mod_strings['LBL_URL'];
	} elseif ($uitype == 56) {
		$fldname = $mod_strings['LBL_CHECK_BOX'];
	} elseif ($uitype == 71) {
		$fldname = $mod_strings['Currency'];
	} elseif ($uitype == 21 || $uitype == 19) {
		$fldname = $mod_strings['LBL_TEXT_AREA'];
	} elseif ($uitype == 33) {
		$fldname = $mod_strings['LBL_MULTISELECT_COMBO'];
	} elseif ($uitype == 85) {
		$fldname = $mod_strings['Skype'];
	}
	$log->debug('< getCustomFieldTypeName');
	return $fldname;
}

/**
 * Function to get custom fields
 * @param $module :: Type string
 * returns customfields in key-value pair array format
 */
function getCustomFieldArray($module) {
	global $log, $adb;
	$log->debug('> getCustomFieldArray '.$module);
	$custquery = 'select tablename,fieldname from vtiger_field where tablename=? and vtiger_field.presence in (0,2) order by tablename';
	$mod = CRMEntity::getInstance($module);
	$param = array($mod->customFieldTable[0]);
	$custresult = $adb->pquery($custquery, $param);
	$custFldArray = array();
	$noofrows = $adb->num_rows($custresult);
	for ($i=0; $i<$noofrows; $i++) {
		$colName=$adb->query_result($custresult, $i, 'fieldname');
		$custFldArray[$colName] = $i;
	}
	$log->debug('< getCustomFieldArray');
	return $custFldArray;
}

/**
 * Function to get columnname and vtiger_fieldlabel from vtiger_field vtiger_table
 * @param $module :: module name -- Type string
 * @param $trans_array :: translated column vtiger_fields -- Type array
 * returns trans_array in key-value pair array format
 */
function getCustomFieldTrans($module, $trans_array) {
	global $log, $adb;
	$log->debug('> getCustomFieldTrans '.$module.','. $trans_array);
	$tab_id = getTabid($module);
	$custquery = 'select columnname,fieldlabel from vtiger_field where generatedtype=2 and vtiger_field.presence in (0,2) and tabid=?';
	$custresult = $adb->pquery($custquery, array($tab_id));
	$noofrows = $adb->num_rows($custresult);
	for ($i=0; $i<$noofrows; $i++) {
		$colName=$adb->query_result($custresult, $i, 'columnname');
		$fldLbl = $adb->query_result($custresult, $i, 'fieldlabel');
		$trans_array[$colName] = $fldLbl;
	}
	$log->debug('< getCustomFieldTrans');
}

/**
 * Function to get customfield record from vtiger_field vtiger_table
 * @param $tab :: Tab ID -- Type integer
 * @param $datatype :: vtiger_field name -- Type string
 * @param $id :: vtiger_field Id -- Type integer
 * returns the data result in string format
 */
function getCustomFieldData($tab, $id, $datatype) {
	global $log, $adb;
	$log->debug('> getCustomFieldData '.$tab.','.$id.','.$datatype);
	$query = 'select * from vtiger_field where tabid=? and fieldid=? and vtiger_field.presence in (0,2)';
	$result = $adb->pquery($query, array($tab, $id));
	$return_data=$adb->fetch_array($result);
	$log->debug('< getCustomFieldData');
	return $return_data[$datatype];
}

/**
 * Function to get customfield table and index field from a given module
 * @param $module :: Module name -- Type string
 * returns the corresponding custom field table name and index
 */
function getCustomFieldTableInfo($module) {
	global $log;
	$log->debug('> getCustomFieldTableInfo '.$module);
	$primary = CRMEntity::getInstance($module);
	if (isset($primary->customFieldTable)) {
		$cfinfo = $primary->customFieldTable;
	} else {
		$cfinfo = '';
	}
	$log->debug('< getCustomFieldTableInfo');
	return $cfinfo;
}

/**
 * Function to get customfield type,length value,decimal value and picklist value
 * @param $label :: vtiger_field typename -- Type string
 * @param $typeofdata :: datatype -- Type string
 * returns the vtiger_field type,length,decimal
 * and picklist value in ';' separated array format
 */
function getFldTypeandLengthValue($label, $typeofdata) {
	global $log, $mod_strings;
	$log->debug('> getFldTypeandLengthValue '.$label.','.$typeofdata);
	if ($label == $mod_strings['Text']) {
		$types = explode('~', $typeofdata);
		$data_array=array('0',$types[3]);
		$fieldtype = implode(';', $data_array);
	} elseif ($label == $mod_strings['Number']) {
		$types = explode('~', $typeofdata);
		$data_decimal = explode(',', $types[2]);
		$data_array=array('1',$data_decimal[0],$data_decimal[1]);
		$fieldtype = implode(';', $data_array);
	} elseif ($label == $mod_strings['Percent']) {
		$types = explode('~', $typeofdata);
		$data_array=array('2','5',$types[3]);
		$fieldtype = implode(';', $data_array);
	} elseif ($label == $mod_strings['Currency']) {
		$types = explode('~', $typeofdata);
		$data_decimal = explode(',', $types[2]);
		$data_array=array('3',$data_decimal[0],$data_decimal[1]);
		$fieldtype = implode(';', $data_array);
	} elseif ($label == $mod_strings['Date']) {
		$fieldtype = '4';
	} elseif ($label == $mod_strings['Email']) {
		$fieldtype = '5';
	} elseif ($label == $mod_strings['Phone']) {
		$fieldtype = '6';
	} elseif ($label == $mod_strings['PickList']) {
		$fieldtype = '7';
	} elseif ($label == $mod_strings['LBL_URL']) {
		$fieldtype = '8';
	} elseif ($label == $mod_strings['LBL_CHECK_BOX']) {
		$fieldtype = '9';
	} elseif ($label == $mod_strings['LBL_TEXT_AREA']) {
		$fieldtype = '10';
	} elseif ($label == $mod_strings['LBL_MULTISELECT_COMBO']) {
		$fieldtype = '11';
	} elseif ($label == $mod_strings['Skype']) {
		$fieldtype = '12';
	}
	$log->debug('< getFldTypeandLengthValue');
	return $fieldtype;
}
?>
