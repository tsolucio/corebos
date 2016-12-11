<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once("data/Tracker.php");
require_once('Smarty_setup.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');

global $log, $app_strings, $app_list_strings, $mod_strings,$current_user;
$current_module_strings = return_module_language($current_language, 'Reports');

$log = LoggerManager::getLogger('report_list');

global $currentModule, $image_path, $theme;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$list_report_form = new vtigerCRM_Smarty;
$list_report_form->assign("MOD", $mod_strings);
$list_report_form->assign("APP", $app_strings);
$list_report_form->assign("APPLIST", $app_list_strings);
$tool_buttons = array(
'EditView' => 'no',
'CreateView' => 'no',
'index' => 'no',
'Import' => 'no',
'Export' => 'no',
'Merge' => 'no',
'DuplicatesHandling' => 'no',
'Calendar' => 'no',
'moduleSettings' => 'no',
);
$list_report_form->assign('CHECK', $tool_buttons);
$list_report_form->assign('CUSTOM_MODULE', false);
$list_report_form->assign("THEME", $theme);
$list_report_form->assign("IMAGE_PATH", $image_path);
$list_report_form->assign("CATEGORY",getParentTab());
$list_report_form->assign("MODULE",$currentModule);
$repObj = new Reports();
$list_report_form->assign("REPT_FLDR",$repObj->sgetRptFldr('SAVED'));
$cusFldrDtls = Array();
$cusFldrDtls = $repObj->sgetRptFldr('CUSTOMIZED');
$list_report_form->assign("REPT_CUSFLDR",$cusFldrDtls);
$fldrids_lists = array();
foreach($cusFldrDtls as $entries)
{
	$fldrids_lists[] =$entries['id'];
}

$list_report_form->assign("FOLDE_IDS",implode(',',$fldrids_lists));
$list_report_form->assign("REPT_MODULES",getReportsModuleList($repObj));
$list_report_form->assign("REPT_FOLDERS",$repObj->sgetRptFldr());
$list_report_form->assign("DEL_DENIED",isset($_REQUEST['del_denied']) ? vtlib_purify($_REQUEST['del_denied']) : '');

$list_report_form->assign('ISADMIN',is_admin($current_user));

if(isset($_REQUEST['mode']) and ($_REQUEST['mode'] == 'ajax' or $_REQUEST['mode'] == 'ajaxdelete'))
	$list_report_form->display("ReportContents.tpl");
else
	$list_report_form->display("Reports.tpl");
?>
