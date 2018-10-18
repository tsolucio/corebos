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
global $log;
include_once("modules/cbMap/cbMap.php");
$focust = new cbMap();
if (isset($_POST["nameView"]) && isset($_POST["QueryGenerate"])){
  try {
      //$stringaselField1 = $_POST['selField1'];//stringa con tutte i campi scelti in selField1
      $queryGenerate = $_POST['QueryGenerate'];//stringa con tutte i campi scelti in selField1
      $nameView = $_POST['nameView'];//nome della vista
      //echo "value are not empty";
      $focust->column_fields['assigned_user_id'] = 1;
      $focust->column_fields['mapname'] = $nameView;
      $focust->column_fields['content']=$queryGenerate;
      $focust->column_fields['description'] = $queryGenerate;
      $focust->column_fields['maptype'] = "SQL";
      if (!$focust->saveentity("cbMap")) {
          echo "success!!! The map is created.";
      } else {
          echo "Error!!! something went wrong.";
      }
  }catch (Exception $e){
      $log->error("errors from catch "+$e->getMessage());
    }
}
else{
    echo "value are empty";
}

?>