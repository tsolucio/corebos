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

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

require_once 'modules/Vtiger/DetailView.php';
$fieldlabel = getTranslatedString('linktype', $currentModule);
$kk = getFieldFromDetailViewBlockArray($blocks, $fieldlabel);
$batypearray = $blocks[$kk['block_label']]['__fields'][$kk['field_key']][$fieldlabel]['options'];
uasort($batypearray, function ($a, $b) {
	return strtolower($a[0]) < strtolower($b[0]) ? -1 : 1;
});
$blocks[$kk['block_label']]['__fields'][$kk['field_key']][$fieldlabel]['options'] = $batypearray;
$smarty->assign('BLOCKS', $blocks);
$smarty->display('DetailView.tpl');
?>