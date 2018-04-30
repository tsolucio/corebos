<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/UserInfoUtil.php';
global $app_strings, $mod_strings, $theme, $adb, $log, $current_language;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$log->info('Inside Email Templates List View');

$result = $adb->pquery('select * from vtiger_emailtemplates order by templateid DESC', array());
$temprow = $adb->fetch_array($result);

$smarty = new vtigerCRM_Smarty;
$smarty->assign('UMOD', $mod_strings);
$smod_strings = return_module_language($current_language, 'Settings');
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $smod_strings);
$smarty->assign('MODULE', 'Settings');
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('PARENTTAB', getParentTab());

$return_data=array();
$cnt=1;
if ($temprow != null) {
	do {
		$templatearray=array();
		$templatearray['templatename'] = $temprow['templatename'];
		$templatearray['templateid'] = $temprow['templateid'];
		$templatearray['description'] = $temprow['description'];
		$templatearray['foldername'] = $temprow['foldername'];
		$return_data[]=$templatearray;
		$cnt++;
	} while ($temprow = $adb->fetch_array($result));
}

$log->info('Exiting Email Templates List View');

$smarty->assign('TEMPLATES', $return_data);
$smarty->display('ListEmailTemplates.tpl');
?>