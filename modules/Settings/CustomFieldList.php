<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************* */
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/CustomFieldUtil.php';

global $mod_strings, $app_strings, $theme;

$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$theme_path = 'themes/' . $theme . '/';
$image_path = 'themes/images/';
$smarty->assign('IMAGE_PATH', $image_path);
$module_array = getCustomFieldSupportedModules();

$cfimagecombo = array($image_path . 'text.gif',
	$image_path . 'number.gif',
	$image_path . 'percent.gif',
	$image_path . 'currency.gif',
	$image_path . 'date.gif',
	$image_path . 'email.gif',
	$image_path . 'phone.gif',
	$image_path . 'picklist.gif',
	$image_path . 'url.gif',
	$image_path . 'checkbox.gif',
	$image_path . 'text.gif',
	$image_path . 'picklist.gif');

$cftextcombo = array($mod_strings['Text'],
	$mod_strings['Number'],
	$mod_strings['Percent'],
	$mod_strings['Currency'],
	$mod_strings['Date'],
	$mod_strings['Email'],
	$mod_strings['Phone'],
	$mod_strings['PickList'],
	$mod_strings['LBL_URL'],
	$mod_strings['LBL_CHECK_BOX'],
	$mod_strings['LBL_TEXT_AREA'],
	$mod_strings['LBL_MULTISELECT_COMBO']
);

$smarty->assign('MODULES', $module_array);
$smarty->assign('CFTEXTCOMBO', $cftextcombo);
$smarty->assign('CFIMAGECOMBO', $cfimagecombo);
if (!empty($_REQUEST['fld_module'])) {
	$fld_module = vtlib_purify($_REQUEST['fld_module']);
} elseif (!empty($_REQUEST['formodule'])) {
	$fld_module = vtlib_purify($_REQUEST['formodule']);
} else {
	$fld_module = 'Leads';
}
$smarty->assign('MODULE', $fld_module);
$smarty->assign('CFENTRIES', getCFLeadMapping($fld_module));
if (isset($_REQUEST['duplicate']) && $_REQUEST['duplicate'] == 'yes') {
	$error = getTranslatedString('ERR_CUSTOM_FIELD_WITH_NAME', 'Settings') . vtlib_purify($_REQUEST['fldlabel']) . ' '
		. getTranslatedString('ERR_ALREADY_EXISTS', 'Settings') . ' ' . getTranslatedString('ERR_SPECIFY_DIFFERENT_LABEL', 'Settings');
	$smarty->assign('DUPLICATE_ERROR', $error);
} else {
	$smarty->assign('DUPLICATE_ERROR', '');
}

if (!empty($_REQUEST['mode'])) {
	$mode = vtlib_purify($_REQUEST['mode']);
} else {
	$mode = '';
}
$smarty->assign('MODE', $mode);

if (empty($_REQUEST['ajax']) || $_REQUEST['ajax'] != 'true') {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'CustomFieldList.tpl'));
} else {
	$smarty->display(vtlib_getModuleTemplate('Vtiger', 'CustomFieldEntries.tpl'));
}

/**
 * Function to get customfield entries for leads
 * @param string $module - Module name
 * return array  $cflist - customfield entries
 */
function getCFLeadMapping($module) {
	global $adb;
	$tabid = getTabid($module);
	$dbQuery = 'SELECT fieldid,columnname,fieldlabel,uitype,displaytype,block,vtiger_convertleadmapping.cfmid,vtiger_convertleadmapping.editable
		FROM vtiger_convertleadmapping
		LEFT JOIN vtiger_field ON vtiger_field.fieldid=vtiger_convertleadmapping.leadfid
		WHERE tabid IN (' . generateQuestionMarks($tabid) . ")
			AND vtiger_field.presence IN (0,2)
			AND generatedtype IN (1,2)
			AND vtiger_field.fieldname NOT IN('assigned_user_id','createdtime','modifiedtime','lead_no','modifiedby','campaignrelstatus')
			ORDER BY vtiger_field.fieldlabel";
	$result = $adb->pquery($dbQuery, array($tabid));
	$row = $adb->fetch_array($result);
	$count = 1;
	$cflist = array();
	if ($row != '') {
		do {
			$cf_element = array();
			$cf_element['map']['no'] = $count;
			$cf_element['map']['label'] = getTranslatedString($row['fieldlabel'], $module);
			$fld_type_name = getCustomFieldTypeName($row['uitype']);
			$cf_element['map']['type'] = $fld_type_name;
			$cf_element['cfmid'] = $row['cfmid'];
			$cf_element['editable']=$row['editable'];
			if ($module == 'Leads') {
				$mapping_details = getListLeadMapping($row['cfmid']);
				$cf_element['map'][] = $mapping_details['accountlabel'];
				$cf_element['map'][] = $mapping_details['contactlabel'];
				$cf_element['map'][] = $mapping_details['potentiallabel'];
			}
			$cflist[] = $cf_element;
			$count++;
		} while ($row = $adb->fetch_array($result));
	}
	return $cflist;
}

/**
 * Function to Lead customfield Mapping entries
 * @param integer  $cfid   - Lead customfield id
 * return array    $label  - customfield mapping
 */
function getListLeadMapping($cfid) {
	global $adb;
	$result = $adb->pquery('select * from vtiger_convertleadmapping where cfmid =?', array($cfid));
	$noofrows = $adb->num_rows($result);
	for ($i = 0; $i < $noofrows; $i++) {
		$accountid = $adb->query_result($result, $i, 'accountfid');
		$contactid = $adb->query_result($result, $i, 'contactfid');
		$potentialid = $adb->query_result($result, $i, 'potentialfid');

		$result2 = $adb->pquery('select fieldlabel from vtiger_field where fieldid =?', array($accountid));
		$accountfield = $adb->query_result($result2, 0, 'fieldlabel');
		$label['accountlabel'] = getTranslatedString($accountfield, 'Accounts');

		$result3 = $adb->pquery('select fieldlabel from vtiger_field where fieldid =?', array($contactid));
		$contactfield = $adb->query_result($result3, 0, 'fieldlabel');
		$label['contactlabel'] = getTranslatedString($contactfield, 'Contacts');
		$result4 = $adb->pquery('select fieldlabel from vtiger_field where fieldid =?', array($potentialid));
		$potentialfield = $adb->query_result($result4, 0, 'fieldlabel');
		$label['potentiallabel'] = getTranslatedString($potentialfield, 'Potentials');
	}
	return $label;
}

/* function to get the modules supports Custom Fields */
function getCustomFieldSupportedModules() {
	global $adb;
	$sql = 'SELECT distinct vtiger_field.tabid,name
		FROM vtiger_field
		INNER JOIN vtiger_tab ON vtiger_field.tabid=vtiger_tab.tabid
		WHERE vtiger_field.tabid NOT IN(9,10,16,29) AND vtiger_tab.presence != 1'; // Both 9 and 16 point to Calendar itself
	$result = $adb->pquery($sql, array());
	$modulelist = array();
	while ($moduleinfo = $adb->fetch_array($result)) {
		$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
	}
	return $modulelist;
}
?>