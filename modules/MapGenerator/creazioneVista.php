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
include "modules/MapGenerator/dbclass.php";

$connect = new MysqlClass();
$connect->connettiMysql();
echo "<div id='divView'>";
echo "<div class='allinea' id='labelNameView'><p>Nome della vista fffffffffff</p></div>";
echo "<div class='allinea' type='text' id='nameViewDiv'><input id='nameView' name='nameView'></div>";
echo "</div>";
echo showInstallationDb();
echo "<div id='tabForm'>";
echo "<ul id='selTab'></ul>";
echo "<button class='pulsante' id='sendTab' onclick='openMenuJoin2()'>Next</button>";
$connect->disconnettiMysql();
global $adb;
function makeDbList() {
    $res = mysql_query("SHOW DATABASES");
    $i=0;
    $dbList=array();
    while ($row = mysql_fetch_assoc($res)){
        $dbList[$i]=$row['Database'];
        $i++;
    }
    $picklist="<div class='selDataBase' id='selDb'>";
    $picklist=$picklist."<select class='dbList' id='dbList' name='dbList' onchange='selDB()' >";
    $picklist=$picklist."<option selected='selected' disabled='disabled'>Select Installation</option>";
    for($j=0; $j<count($dbList); $j++) {  
        $picklist=$picklist."<option id=\"".$dbList[$j]."\">".$dbList[$j]."</option>\"";
    } 
    $picklist=$picklist."</select></div>";
    return  $picklist;
    }
    
 function showInstallationDb() {
     global $adb;
    $res = $adb->query("Select * from vtiger_accountinstallation
                        join vtiger_crmentity on crmid =  accountinstallationid where deleted=0");
    $picklist="<div class='selDataBase' id='selDb'>";
    $picklist = $picklist."<select class='dbList' id='dbList' name='dbList' onchange='selDB(this)' >";
     $picklist=$picklist."<option selected='selected' disabled='disabled'>Select Installation:</option>";
    $nr = $adb->num_rows($res);
    for($i=0;$i<$nr;$i++){
        $dbname = $adb->query_result($res,$i,'dbname');
        $acin_no = $adb->query_result($res,$i,'acin_no');
        $acinstallationname = $adb->query_result($res,$i,'acinstallationname');
        $accountinstallationid = $adb->query_result($res,$i,'accountinstallationid');
        $picklist=$picklist."<option id=\"".$accountinstallationid."-".$acin_no.$dbname."\">".$acinstallationname."</option>\"";
    }
    
   
    $picklist=$picklist."</select></div>";
    return  $picklist;
    }       
    
 ?>
    

