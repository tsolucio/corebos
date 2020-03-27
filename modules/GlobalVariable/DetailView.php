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
require_once 'include/Webservices/getmaxloadsize.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

require_once 'modules/Vtiger/DetailView.php';
require_once 'modules/GlobalVariable/LoadGlobalVariableDefinitions.php';
$fieldlabel = getTranslatedString('Name', $currentModule);
$kk = getFieldFromDetailViewBlockArray($blocks, $fieldlabel);
$gvnamearray = $blocks[$kk['block_label']][$kk['field_key']][$fieldlabel]['options'];
uasort($gvnamearray, function ($a, $b) {
	return strtolower($a[0]) < strtolower($b[0]) ? -1 : 1;
});
$blocks[$kk['block_label']][$kk['field_key']][$fieldlabel]['options'] = $gvnamearray;
$smarty->assign('BLOCKS', $blocks);
if ($focus->column_fields['gvname']=='Application_Upload_MaxSize') {
	$phpmaxsize = get_maxloadsize();
	$phpmaxsizeMB = readableBytes($phpmaxsize, 'MB');
	$warning = '<b>'.getTranslatedString('VTLIB_LBL_WARNING', 'Settings').':</b> '.getTranslatedString('PHP_MAX_UPLOAD', 'GlobalVariable');
	$warning .= ': <b>'.$phpmaxsizeMB.' ('.$phpmaxsize.')</b>';
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
	$smarty->assign('ERROR_MESSAGE', $warning);
}
$smarty->display('DetailView.tpl');
?>
