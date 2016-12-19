<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/cbAuditTrail/AuditTrail.php');
require_once('modules/Users/Users.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require('user_privileges/audit_trail.php');

global $app_strings, $mod_strings, $current_language, $current_user, $adb, $theme, $currentModule;

$log = LoggerManager::getLogger('audit_trail');

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$focus = new AuditTrail();

$smarty = new vtigerCRM_Smarty();

$category = getParenttab();
$current_module_strings = return_module_language($current_language, 'Settings');

$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MOD", $current_module_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("CATEGORY",$category);
$smarty->assign("USERLIST", getUserslist(false));
$smarty->assign("LIST_HEADER",$focus->getAuditTrailHeader());
$smarty->assign("LIST_FIELDS",$focus->list_fields_name);
$smarty->assign("ATENABLED",$audit_trail);

$smarty->display('modules/cbAuditTrail/index.tpl');
?>