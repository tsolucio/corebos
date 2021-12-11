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
 * Function to get field type name
 * @param integer uitype
 * @return string field type name
 */
function getCustomFieldTypeName($uitype) {
	global $mod_strings, $log;
	$log->debug('> getCustomFieldTypeName '.$uitype);
	$fldname = '';
	if ($uitype == 1 || $uitype == 2) {
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
 * @param string module
 * @return array customfields in key-value pairs
 */
function getCustomFieldArray($module) {
	global $log, $adb;
	$log->debug('> getCustomFieldArray '.$module);
	if (empty($module) || !vtlib_isModuleActive($module) || !vtlib_isEntityModule($module)) {
		return array();
	}
	$mod = CRMEntity::getInstance($module);
	$param = array($mod->customFieldTable[0]);
	$custresult = $adb->pquery('select tablename,fieldname from vtiger_field where tablename=? and vtiger_field.presence in (0,2)', $param);
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
 * Function to get a column from a vtiger_field table record
 * @param integer tab ID
 * @param integer field id
 * @param string column to return
 * @return string value of the given column in the row
 */
function getCustomFieldData($tab, $id, $datatype) {
	global $log, $adb;
	$log->debug('> getCustomFieldData '.$tab.','.$id.','.$datatype);
	$result = $adb->pquery('select * from vtiger_field where tabid=? and fieldid=? and vtiger_field.presence in (0,2)', array($tab, $id));
	$log->debug('< getCustomFieldData');
	return $adb->query_result($result, 0, $datatype);
}

/**
 * Function to get customfield table and index field from a given module
 * @param string Module name
 * @return the corresponding custom field table name and index
 */
function getCustomFieldTableInfo($module) {
	global $log;
	$log->debug('> getCustomFieldTableInfo '.$module);
	$cfinfo = array();
	if (empty($module) || !vtlib_isModuleActive($module) || !vtlib_isEntityModule($module)) {
		$log->debug('< getCustomFieldTableInfo');
		return $cfinfo;
	}
	$primary = CRMEntity::getInstance($module);
	if (isset($primary->customFieldTable)) {
		$cfinfo = $primary->customFieldTable;
	}
	$log->debug('< getCustomFieldTableInfo');
	return $cfinfo;
}

/**
 * Function to get Lead custom field Mapping entries
 * @param integer Lead custom field id
 * @return array custom field mapping
 */
function getListLeadMapping($cfid) {
	global $adb;
	$label = array();
	$result = $adb->pquery('select * from vtiger_convertleadmapping where cfmid=?', array($cfid));
	$noofrows = $adb->num_rows($result);
	$flabelsql = 'select fieldlabel from vtiger_field where fieldid=?';
	for ($i = 0; $i < $noofrows; $i++) {
		$accountid = $adb->query_result($result, $i, 'accountfid');
		$contactid = $adb->query_result($result, $i, 'contactfid');
		$potentialid = $adb->query_result($result, $i, 'potentialfid');
		if (empty($accountid)) {
			$label['accountlabel'] = '';
		} else {
			$flresult = $adb->pquery($flabelsql, array($accountid));
			$accountfield = $adb->query_result($flresult, 0, 'fieldlabel');
			$label['accountlabel'] = getTranslatedString($accountfield, 'Accounts');
		}
		if (empty($contactid)) {
			$label['contactlabel'] = '';
		} else {
			$flresult = $adb->pquery($flabelsql, array($contactid));
			$contactfield = $adb->query_result($flresult, 0, 'fieldlabel');
			$label['contactlabel'] = getTranslatedString($contactfield, 'Contacts');
		}
		if (empty($potentialid)) {
			$label['potentiallabel'] = '';
		} else {
			$flresult = $adb->pquery($flabelsql, array($potentialid));
			$potentialfield = $adb->query_result($flresult, 0, 'fieldlabel');
			$label['potentiallabel'] = getTranslatedString($potentialfield, 'Potentials');
		}
	}
	return $label;
}
?>
