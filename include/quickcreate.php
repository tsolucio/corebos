<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/utils/CommonUtils.php';

global $mod_strings, $current_user, $app_strings, $currentModule, $theme;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty;

if (empty($QCFIELDS_QUERY)) {
	$qcreate_array = QuickCreate($currentModule);
} else {
	$result = $adb->query($QCFIELDS_QUERY);
	$qcreate_array = QuickCreateFieldInformation($result, $currentModule, (empty($QCDEFAULTFIELDVALUES) ? array() : $QCDEFAULTFIELDVALUES));
}
$validationData = $qcreate_array['data'];
$data = split_validationdataArray($validationData);
$smarty->assign('QUICKCREATE', $qcreate_array['form']);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MODE', '');
$smarty->assign('MOD_SEQ_ID', '');
$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000, $currentModule);
$smarty->assign('UPLOADSIZE', $upload_maxsize/1000000); //Convert to MB
$smarty->assign('UPLOAD_MAXSIZE', $upload_maxsize);
$smarty->assign('Application_Textarea_Style', GlobalVariable::getVariable('Application_Textarea_Style', 'height:140px;', $currentModule, $current_user->id));
$smarty->assign('ACTIVITY_MODE', (isset($_REQUEST['activity_mode']) ? vtlib_purify($_REQUEST['activity_mode']) : ''));
$smarty->assign('FROM', (isset($_REQUEST['from']) ? vtlib_purify($_REQUEST['from']) : ''));
$smarty->assign('URLPOPUP', (isset($_REQUEST['pop']) ? str_replace('-a;', '&', $_REQUEST['pop']) : ''));
$smarty->assign('MASS_EDIT', '0');
$smarty->assign('QCMODULE', getTranslatedString('SINGLE_'.$currentModule, $currentModule));
$smarty->assign('USERID', $current_user->id);
$smarty->assign('VALIDATION_DATA_FIELDNAME', $data['fieldname']);
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE', $data['datatype']);
$smarty->assign('VALIDATION_DATA_FIELDLABEL', $data['fieldlabel']);
$smarty->assign('MODULE', $currentModule);
$smarty->display('QuickCreate.tpl');
?>