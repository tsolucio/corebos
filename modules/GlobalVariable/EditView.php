<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Vtiger/EditView.php';
require_once 'modules/GlobalVariable/LoadGlobalVariableDefinitions.php';
$fieldlabel = getTranslatedString('Name', $currentModule);
$kk = getFieldFromEditViewBlockArray($blocks, $fieldlabel);
$gvnamearray = $blocks[$kk['block_label']][$kk['row_key']][$kk['field_key']][3][0];
uasort($gvnamearray, function ($a, $b) {
	return strtolower($a[0]) < strtolower($b[0]) ? -1 : 1;
});
$blocks[$kk['block_label']][$kk['row_key']][$kk['field_key']][3][0] = $gvnamearray;
$basblocks[$kk['block_label']][$kk['row_key']][$kk['field_key']][3][0] = $gvnamearray;
$smarty->assign('BLOCKS', $blocks);
$smarty->assign('BASBLOCKS', $basblocks);
$smarty->display('salesEditView.tpl');
?>