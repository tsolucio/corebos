<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $mod_strings,$app_strings,$theme,$currentModule,$current_user;

require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

$excludedRecords = vtlib_purify($_REQUEST['excludedRecords']);

$focus = CRMEntity::getInstance($currentModule);
$focus->mode = '';
$mode = 'mass_edit';

$disp_view = getView($focus->mode);
$idstring = vtlib_purify($_REQUEST['idstring']);

$smarty = new vtigerCRM_Smarty;
$smarty->assign('MODULE',$currentModule);
$smarty->assign('APP',$app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$storearray = getSelectedRecords($_REQUEST, $currentModule, vtlib_purify($_REQUEST['idstring']),$excludedRecords);
$idstringval=implode(';',$storearray);
$smarty->assign("IDS",$idstringval);
$smarty->assign('MASS_EDIT','1');
$smarty->assign('BLOCKS',getBlocks($currentModule,$disp_view,$mode,$focus->column_fields));
if ($currentModule=='Products') {
	$tax_details = getAllTaxes('available');
	for($i=0;$i<count($tax_details);$i++) {
		$tax_details[$i]['check_name'] = $tax_details[$i]['taxname'].'_check';
		$tax_details[$i]['check_value'] = 0;
	}
	$smarty->assign("TAX_DETAILS", $tax_details);
}
$smarty->assign("CATEGORY",getParentTab());

// Field Validation Information
$tabid = getTabid($currentModule);
$validationData = getDBValidationData($focus->tab_name,$tabid);
$validationArray = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);

$smarty->display('MassEditForm.tpl');

?>