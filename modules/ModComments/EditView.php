<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
require_once('Smarty_setup.php');

// Forcefully disable Editing For Any User
if (!empty($_REQUEST['record'])) {
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign("MOD",$mod_strings);
	$smarty->assign("APP",$app_strings);
	$smarty->assign("THEME", "$theme");
	$smarty->assign("IMAGE_PATH", "themes/$theme/images/");

	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));
	exit;
}	
// END

require_once 'modules/Vtiger/EditView.php';

$smarty->display('CreateView.tpl');

?>