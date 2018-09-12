<?php

//LoadAllMaps

require_once "GetAllMaps.php";
require_once 'All_functions.php';
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
require_once 'include/utils/utils.php';
require_once 'Smarty_setup.php';
// require_once ('include/database/PearDatabase.php');
// require_once('database/DatabaseConnection.php');
require_once 'include/CustomFieldUtil.php';
require_once 'data/Tracker.php';

try
{
    $TypeMaps = 'ALL';

    if (!empty($TypeMaps)) {
        if (!empty(GetMaps($TypeMaps))) {
            $smarty = new vtigerCRM_Smarty();
            $smarty->assign("MOD", $mod_strings);
            $smarty->assign("APP", $app_strings);
            $smarty->assign("AllMaps", GetMaps("ALL"));
            $output = $smarty->fetch('modules/MapGenerator/LoadAllMaps.tpl');
            echo $output;
        } else {
            echo showError("Something was wrong", "Missing the List of Maps ");
        }

    } else {
        echo showError("Information!!", "Not exist any map ");
    }
} catch (Exception $ex) {
    $log->debug(TypeOFErrors::ERRORLG . "Something was wrong check the Exception " . $ex);
    // echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
    echo showError("Something was wrong", "");
}
