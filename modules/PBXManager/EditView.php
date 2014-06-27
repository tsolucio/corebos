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

require_once('Smarty_setup.php');
require_once("modules/$currentModule/$currentModule.php");

$focus = new $currentModule();
$smarty = new vtigerCRM_Smarty();

$category = getParentTab($currentModule);

$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);

$smarty->assign('SINGLE_MOD', "CallHistory");
$smarty->assign('CATEGORY', $category);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

$smarty->assign('CHECK', array());

$smarty->display(vtlib_getModuleTemplate('PBXManager', 'EditView.tpl'));

?>
