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
$allacc=array();
$installations = array();
global $adb;
    $res = $adb->pquery("SELECT * from  vtiger_accountinstallation
                        INNER JOIN vtiger_crmentity ON crmid=accountinstallationid 
                        INNER JOIN vtiger_account on vtiger_accountinstallation.linktoacsd = vtiger_account.accountid
                        WHERE vtiger_crmentity.smownerid=?  
                        AND  deleted=0",array($current_user->id));

    $nr = $adb->num_rows($res);
    for($i=0;$i<$nr;$i++){
        $dbname = $adb->query_result($res,$i,'dbname');
        $acin_no = $adb->query_result($res,$i,'acin_no');
        $acinstallationname = $adb->query_result($res,$i,'acinstallationname');
        $accountinstallationid = $adb->query_result($res,$i,'accountinstallationid');
        $installations[$i]['dbname'] = $dbname;
        $installations[$i]['acin_no'] = $acin_no;
        $installations[$i]['acinstallationname'] = $acinstallationname;
        $installations[$i]['accountinstallationid'] = $accountinstallationid;
    }
    



//for($i=0;$i<$adb->num_rows($res);$i++){
//    $name=$adb->query_result($res,$i,"acinstallationname").' '.$adb->query_result($res,$i,"accountname");
//    $id=$adb->query_result($res,$i,"accountinstallationid");
//    $dbname = $adb->query_result($res,$i,'dbname');
//    $acin_no = $adb->query_result($res,$i,'acin_no');
//    $name1=str_replace("'","",$name);
//    $allacc[$id."-".$name1.'-'.$acin_no.$dbname]=$name;               
//}

//$smarty->assign("allacc",$allacc);
$smarty->assign("INSTALLATIONS", $installations);
if(isset($_REQUEST['todo'])){
if($_REQUEST['todo'] == "querygenerator")
$smarty->display('modules/MapGenerator/createView.tpl');
else if($_REQUEST['todo'] == "FSscript")
$smarty->display('modules/MapGenerator/FSscript.tpl');
else if($_REQUEST['todo'] == "createReportTable")
$smarty->display('modules/MapGenerator/ReportTable.tpl');
else if($_REQUEST['todo'] == "createReportTable2")
$smarty->display('modules/MapGenerator/ReportNameTable.tpl');
}