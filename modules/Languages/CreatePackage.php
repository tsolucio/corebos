<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 ********************************************************************************/
require_once('database/DatabaseConnection.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');

global $app_strings,$mod_strings,$current_language,$theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smod_strings = return_module_language($current_language,'Settings');

$smarty = new vtigerCRM_smarty;

$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("THEME_PATH", $theme_path);
$smarty->assign("UMOD", $mod_strings);
$smarty->assign("PARENTTAB", $_REQUEST['parenttab']);

$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("ERROR", $_GET['error']);
$smarty->display("Settings/Languages/LanguagePackCreate.tpl");

?>