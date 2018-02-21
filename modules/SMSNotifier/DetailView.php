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

// This might not be picked up in getBlocks as we do not have a field in it.
if (empty($blocks[getTranslatedString('StatusInformation', $currentModule)])) {
	$blocks[getTranslatedString('StatusInformation', $currentModule)] = array();
	$smarty->assign('BLOCKS', $blocks);
}

/** Removing Edit permissions */
$smarty->assign('DETAILVIEW_AJAX_EDIT', false);
$smarty->assign('CREATE_PERMISSION', 'notpermitted');

$smarty->display(vtlib_getModuleTemplate($currentModule, 'DetailView.tpl'));
?>
