<?php

global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
require_once 'include/utils/utils.php';
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
// require_once('database/DatabaseConnection.php');
require_once 'include/CustomFieldUtil.php';
require_once 'data/Tracker.php';

$dataa = $_POST["data"];
$dataid = $_POST["dataid"];
$savehistory = $_POST["savehistory"];
$saveasfunction = $_POST["anotherfunction"];

$smarty = new vtigerCRM_Smarty();
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("Datas", $dataa);
$smarty->assign("dataid", $dataid);
$smarty->assign("savehistory", $savehistory);
$smarty->assign("anotherfunction", $saveasfunction);
$output = $smarty->fetch('modules/MapGenerator/Modal.tpl');
echo $output;
