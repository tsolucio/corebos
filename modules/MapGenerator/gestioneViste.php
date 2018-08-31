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
echo makeDbList();
echo "<div id='divManage'>";
echo "<select id='selViews' name='selViews' size='10' ></select>";
echo "<div id='buttonsManage'><button class='pulsante' id='updateView' value='Aggiorna vista' onclick='updateView()'>Aggiorna vista</button>";   
echo "<button class='pulsante' id='deleteVista' value='EliminaVista' onclick='deleteView()'>Cancella Vista</button></div>";
echo "<div id='resultView'>";
echo "<div class='subTitleDiv'>Reporting message</div>";
echo "<textarea id='textmessage' readonly></textarea>";
    

echo "</div></div>";
$connect->disconnettiMysql();

function makeDbList() {
    $res = mysql_query("SHOW DATABASES");
    $i=0;
    while ($row = mysql_fetch_assoc($res)){
        $dbList[$i]=$row['Database'];
        $i++;
    }
    $picklist="<div class='selDataBase' id='selDbViews'>";
    $picklist=$picklist."<select class='dbList' id='dbListViews' name='dbListViews' onchange='selDBViews()' >";
    $picklist=$picklist."<option selected='selected' disabled='disabled'>Selezionare il database:</option>";
    for($j=0; $j<count($dbList); $j++) {  
        $picklist=$picklist."<option id=\"".$dbList[$j]."\">".$dbList[$j]."</option>\"";
    } 
    $picklist=$picklist."</select></div>";
    return  $picklist;
    }
      
?>