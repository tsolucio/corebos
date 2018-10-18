<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
$MapId = "";
if (isset($_POST['MapID'])) {
    $MapId = $_POST['MapID'];
}
if(isset($_POST['queryid'])){
$queryid=$_POST['queryid'];
}
else
$queryid=md5(date("Y-m-d H:i:s").uniqid(rand(), true));
//echo "<h2>".$MapId."</h2>";
global $app_strings, $mod_strings, $current_language, $currentModule, $theme,$adb,$root_directory,$current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once ('include/utils/utils.php');
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
//require_once('database/DatabaseConnection.php');
require_once ('include/CustomFieldUtil.php');
require_once ('data/Tracker.php');
$smarty = new vtigerCRM_Smarty();
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("MapID", $MapId);
$smarty->assign("queryid", $queryid);
$smarty->assign("NameView", $NameView);
$output = $smarty->fetch('modules/MapGenerator/createJoinCondition.tpl');
echo $output;
?>
