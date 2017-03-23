<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
global $app_strings,$mod_strings, $currentModule, $theme, $current_language, $current_user;
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
$smarty = new vtigerCRM_Smarty();
$category = getParentTab();

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/Vtiger/layout_utils.php');

$idstring = getSelectedRecords($_REQUEST,$currentModule,(isset($_REQUEST['idstring']) ? $_REQUEST['idstring'] : ''),(isset($_REQUEST['excludedRecords']) ? $_REQUEST['excludedRecords'] : ''));
$idstring = join(';',$idstring);

$smarty->assign("SESSION_WHERE",(isset($_SESSION['export_where']) ? $_SESSION['export_where'] : ''));
$tool_buttons = array(
'EditView' => 'no',
'CreateView' => 'no',
'index' => 'yes',
'Import' => 'no',
'Export' => 'no',
'Merge' => 'no',
'DuplicatesHandling' => 'no',
'Calendar' => 'no',
'moduleSettings' => 'no',
);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('CUSTOM_MODULE', false);

$smarty->assign('APP',$app_strings);
$smarty->assign('MOD',$mod_strings);
$smarty->assign("THEME", $theme_path);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("CATEGORY",$category);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("MODULELABEL",getTranslatedString($currentModule));
$smarty->assign("IDSTRING",$idstring);
$smarty->assign("PERPAGE",$list_max_entries_per_page);

if(!is_admin($current_user) && (isPermitted($currentModule, 'Export') != 'yes')) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));
} else {
	$smarty->display('ExportRecords.tpl');
}
?>
