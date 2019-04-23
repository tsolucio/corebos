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

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log, $default_charset;

$smarty = new vtigerCRM_Smarty();

include 'modules/cbupdater/forcedButtons.php';
require_once 'modules/Vtiger/DetailView.php';
$flabel = getTranslatedString('Call From', 'PBXManager');
$idx = getFieldFromDetailViewBlockArray($blocks, $flabel);
$value = html_entity_decode($blocks[$idx['block_label']][$idx['field_key']][$flabel]['value'], ENT_QUOTES, $default_charset);
$blocks[$idx['block_label']][$idx['field_key']][$flabel]['value'] = $value;
$flabel = getTranslatedString('Call To', 'PBXManager');
$idx = getFieldFromDetailViewBlockArray($blocks, $flabel);
$value = html_entity_decode($blocks[$idx['block_label']][$idx['field_key']][$flabel]['value'], ENT_QUOTES, $default_charset);
$blocks[$idx['block_label']][$idx['field_key']][$flabel]['value'] = $value;
$smarty->assign('BLOCKS', $blocks);
$smarty->assign('NAME', html_entity_decode($recordName, ENT_QUOTES, $default_charset));
$singlepane_view = 'true';
$smarty->assign('SinglePane_View', $singlepane_view);
$smarty->assign('TODO_PERMISSION', 'no');
$smarty->assign('EVENT_PERMISSION', 'no');
$smarty->assign('EDIT_PERMISSION', 'no');
$smarty->assign('CREATE_PERMISSION', 'no');
$smarty->assign('DELETE', 'notpermitted');
$smarty->assign('CONTACT_PERMISSION', 'notpermitted');

$smarty->display('DetailView.tpl');
?>
