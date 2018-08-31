<?php

/**
 * @Author: edmondi kacaj
 * @Date:   2017-12-18 11:23:34
 * @Last Modified by:   edmondi kacaj
 * @Last Modified time: 2017-12-22 16:29:12
 */

global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
require_once ('include/utils/utils.php');
require_once ('Smarty_setup.php');
require_once ('include/database/PearDatabase.php');
// require_once('database/DatabaseConnection.php');
require_once ('include/CustomFieldUtil.php');
require_once ('data/Tracker.php');
require_once ('All_functions.php');

    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("MapName", $mapName);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("Allmaps",SelectallMaps());
    $output = $smarty->fetch('modules/MapGenerator/CreateMaps.tpl');
    echo $output;
