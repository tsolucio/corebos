<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
require_once 'Smarty_setup.php';
require_once 'modules/CustomerPortal/PortalUtils.php';

$smarty = new vtigerCRM_Smarty();
if (isPermitted('CustomerPortal', '')!='yes') {
	echo '<br><br>';
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('applicationmessage.tpl');
	exit;
}

$mode = $_REQUEST['mode'];
if ($mode !='' && $mode == 'save') {
	cp_saveAdvancedSettings($_REQUEST);
}
$category = getParentTab();
$smarty->assign('THEME', $theme);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('BUTTONS', $list_buttons);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('MODULE_VIEWALL', cp_getContactsViewInfo());

$smarty->assign('USERS', cp_getUsers());
$smarty->assign('USERID', cp_getCurrentUser());
$smarty->display(vtlib_getModuleTemplate($currentModule, 'AdvancedSettings.tpl'));
?>
