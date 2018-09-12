
<?php


global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";
// require_once ('include/utils/utils.php');
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
// require_once('database/DatabaseConnection.php');
require_once 'include/CustomFieldUtil.php';
require_once 'data/Tracker.php';
require_once 'All_functions.php';
include_once 'Staticc.php';
$mapName = $_POST['NameView'];
if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "SQL") {
    $MapId = "";
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    // echo "<h2>".$MapId."</h2>";

    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("MapName", $mapName);
    $smarty->assign("NameView", $NameView);
    $output = $smarty->fetch('modules/MapGenerator/createJoinCondition.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "Mapping") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/MappingView.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "MasterDetail") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/MasterDetail.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "ListColumns") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/ListColumns.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "ConditionQuery") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $output = $smarty->fetch('modules/MapGenerator/createJoinCondition.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "Module_Set") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/Module_Set.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "IOMap") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/IOMap.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "FieldDependency") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/FieldDependency.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "FieldDependencyPortal") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/FieldDependencyPortal.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "GlobalSearchAutocomplete") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/GlobalSearchAutocomplete.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "ConditionExpression") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/ConditionExpression.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "CREATEVIEWPORTAL") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/CREATEVIEWPORTAL.tpl');
    echo $output;
} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "DETAILVIEWBLOCKPORTAL") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/DETAILVIEWBLOCKPORTAL.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "MENUSTRUCTURE") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/MENUSTRUCTURE.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "RecordAccessControl") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/RecordAccessControl.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "DuplicateRecords") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/DuplicateRecords.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "RendicontaConfig") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/RendicontaConfig.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "ImportBusinessMapping") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/ImportBusinessMapping.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "RecordSetMapping") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/RecordSetMapping.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "ExtendedFieldInformationMapping") {
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $output = $smarty->fetch('modules/MapGenerator/ExtendedFieldInformationMapping.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "WS") {
    $listdtat = CheckIfExistResponseTypeTable(TypeOFErrors::HttpresponseTypeTable);
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $smarty->assign("listdtat", $listdtat);
    $output = $smarty->fetch('modules/MapGenerator/WS.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "WS Validation") {
    $listdtat = CheckIfExistResponseTypeTable(TypeOFErrors::HttpresponseTypeTable);
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $smarty->assign("listdtat", $listdtat);
    $output = $smarty->fetch('modules/MapGenerator/WSValidation.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "RelatedPanes") {
    $listdtat = CheckIfExistResponseTypeTable(TypeOFErrors::HttpresponseTypeTable);
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $smarty->assign("listdtat", $listdtat);
    $output = $smarty->fetch('modules/MapGenerator/relatedpanes.tpl');
    echo $output;

} else if (isset($_POST['ObjectType']) && $_POST['ObjectType'] == "FieldSet") {
    $listdtat = CheckIfExistResponseTypeTable(TypeOFErrors::HttpresponseTypeTable);
    $queryid = md5(date("Y-m-d H:i:s") . uniqid(rand(), true));
    //echo "<h2>".$MapId."</h2>";
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign("MOD", $mod_strings);
    $smarty->assign("APP", $app_strings);
    $smarty->assign("MapID", $MapId);
    $smarty->assign("queryid", $queryid);
    $smarty->assign("NameView", $NameView);
    $smarty->assign("MapName", $mapName);
    $smarty->assign("listdtat", $listdtat);
    $output = $smarty->fetch('modules/MapGenerator/fieldset.tpl');
    echo $output;

} else {
    require_once 'All_functions.php';

    echo showError("An error has occurred", "Not exists a map with  Type " . $_POST['ObjectType']);
}