<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('Smarty_setup.php');

include_once dirname(__FILE__) . '/SMSNotifier.php';

global $theme, $currentModule, $mod_strings, $app_strings, $current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty();
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);

$mode = vtlib_purify($_REQUEST['mode']);
$record = vtlib_purify($_REQUEST['record']);

if($mode == 'query') {
	SMSNotifier::smsquery($record);
}

$results = SMSNotifier::getSMSStatusInfo($record);

$smarty->assign("RESULTS", $results);
$smarty->display(vtlib_getModuleTemplate($currentModule, 'StatusWidget.tpl'));

?>