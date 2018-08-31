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
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
//require_once('database/DatabaseConnection.php');
require_once('include/CustomFieldUtil.php');
require_once('data/Tracker.php');



//global $log, $adb;

//if (isset($_POST['MapID'])){
//    $mapid=$_POST['MapID'];
//    $XmlConvertertoarray=array();
//    $query=$adb->query("select description from vtiger_crmentity where crmid=$mapid");
//    $description=$adb->query_result($query,0,"description");
//    $movies = new SimpleXMLElement($description);
//    $FirstSecondModule=array();
//    $Fields=array();
//    $FirstSecondModule[]=array(
//        'FmoduleID'=>$movies->Fmodule[0]->FmoduleID,
//        'FmoduleName'=>$movies->Fmodule[0]->FmoduleName,
//        'SecmoduleID'=>$movies->Secmodule[0]->SecmoduleID,
//        'SecmoduleName'=>$movies->Secmodule[0]->SecmoduleName,
//
//    );
//     foreach($movies->fields->field as $field) {
//         $Fields[]=array(
//                'fieldname' => $field->fieldname,
//                'fieldID'=>$field->fieldID,
//                );
//
//     }
//
//}

function takeFirstMOduleFromXMLMap($MapID)
{
    global $log, $adb;
  if (isset($MapID)) {
      $FmoduleID = "";
      $FmoduleName = "";
      $query = $adb->query("select description from vtiger_crmentity where crmid=$MapID");
      $description = $adb->query_result($query, 0, "description");
      $movies = new SimpleXMLElement($description);
      $FmoduleID = $movies->Fmodule[0]->FmoduleID;
      $FmoduleName = $movies->Fmodule[0]->FmoduleName;
      return $FmoduleName;
      //return $FmoduleID;
  }else{
      return "";
  }

}




function takeSecondMOduleFromXMLMap($MapID)
{
    global $log, $adb;
   if (isset($MapID)) {
       $SecmoduleID = "";
       $SecmoduleName = "";
       $query = $adb->query("select description from vtiger_crmentity where crmid=$MapID");
       $description = $adb->query_result($query, 0, "description");
       $movies = new SimpleXMLElement($description);
       $SecmoduleID = $movies->Secmodule[0]->SecmoduleID;
       $SecmoduleName = $movies->Secmodule[0]->SecmoduleName;
       return $SecmoduleName;
       //return $FmoduleID;
   }
   else{
       return "";
   }

}

function takeAllFileds($MapID)
{
    global $log, $adb;
    if (isset($MapID)) {
        $query = $adb->query("select description from vtiger_crmentity where crmid=$MapID");
        $description = $adb->query_result($query, 0, "description");
        $movies = new SimpleXMLElement($description);
        $Fields=array();
        foreach($movies->fields->field as $field => $value) {
            $Fields[]=  $value;//  $field->fieldname;


        }
        return $Fields;
    }else{
        return "";
    }

}


//$theme_path="themes/".$theme."/";
//$image_path=$theme_path."images/";
//$smarty = new vtigerCRM_Smarty();
//$smarty->assign("MOD", $mod_strings);
//$smarty->assign("APP", $app_strings);
//$smarty->assign("FirstSecModule", $FirstSecondModule);
//$smarty->assign("Fields", $Fields);
//$smarty->assign("MapID", $mapid);
//$output = $smarty->fetch('modules/MVCreator/createJoinCondition.tpl');
//echo $output;
?>

