<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/utils.php';

global $mod_strings, $app_strings, $adb, $theme, $theme_path, $image_path;

$smarty = new vtigerCRM_Smarty;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$field_module=getFieldModuleAccessArray();
// unset($field_module['Calendar']);
// unset($field_module['Events']);
uksort($field_module, function ($a, $b) {
	return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
});
$allfields=array();
foreach ($field_module as $fld_module => $mod_name) {
	$fieldListResult = getDefOrgFieldList($fld_module);
	$noofrows = $adb->num_rows($fieldListResult);
	$allfields[$fld_module] = getStdOutput($fieldListResult, $noofrows, $fld_module);
}

if (!empty($_REQUEST['fld_module'])) {
	$smarty->assign('DEF_MODULE', vtlib_purify($_REQUEST['fld_module']));
} else {
	$smarty->assign('DEF_MODULE', 'Leads');
}

/** Function to get the field label/permission array to construct the default orgnization field UI for the specified profile
 * @param $fieldListResult -- mysql query result that contains the field label and uitype:: Type array
 * @param $module -- module to translate with
 * @returns $standCustFld -- field label/permission array :: Type varchar
*/
function getStdOutput($fieldListResult, $noofrows, $module) {
	global $adb, $theme;
	$standCustFld = array();
	for ($i=0; $i<$noofrows; $i++) {
		$uitype = $adb->query_result($fieldListResult, $i, 'uitype');
		$fieldlabel = $adb->query_result($fieldListResult, $i, 'fieldlabel');
		$typeofdata = $adb->query_result($fieldListResult, $i, 'typeofdata');
		$fieldtype = explode('~', $typeofdata);
		$standCustFld[] = getTranslatedString($fieldlabel, $module);

		if ($adb->query_result($fieldListResult, $i, 'visible') == 0 || ($uitype == 117 && $fieldtype[1] == 'M')) {
			$visible ="<img src='" . vtiger_imageurl('prvPrfSelectedTick.gif', $theme) . "'>";
		} else {
			$visible = "<img src='" . vtiger_imageurl('no.gif', $theme) . "'>";
		}
		$standCustFld[]= $visible;
	}
	$standCustFld=array_chunk($standCustFld, 2);
	$standCustFld=array_chunk($standCustFld, 4);
	return $standCustFld;
}

$smarty->assign('FIELD_INFO', $field_module);
$smarty->assign('FIELD_LISTS', $allfields);
$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODE', 'view');
$smarty->display('FieldAccess.tpl');
?>