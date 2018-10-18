<html>
<title>Join</title>
<INPUT id="nameView" type="hidden" name="<?php echo $_POST['nameView']; ?>" value="<?php echo $_POST['nameView']; ?>">
<INPUT id="nameDb" type="hidden" name="<?php echo $_POST['nameDb']; ?>" value="<?php echo $_POST['nameDb']; ?>">
<div id="menuJoinContainer">
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
    $tables=getTables();
    addSelTab($tables);
    ?>
    <div id="buttons">
        <ul>
            <li><button id="addJoin" value="Aggiungi Join" onclick="sendData()">Aggiungi Join</button> </li>
            <li><button id="deleteLast" value="Cancella l'ultimo Join" onClick="deleteLastJoin()" >Cancella l'ultimo Join</button> </li>    
            <li><button id="delete" value="Cancella query" onclick="deleteJoin()">Cancella query</button> </li>
            <li><button id="create" value="Crea vista materializzata" onclick="creaVista()" >Crea vista materializzata</button> </li>
        </ul>
    </div>
</div>
<div id="subTitleDiv"><h1 id="subTitle">Query:</h1></div>
<div id="results"></div>
<div id="null"></div>
<?php 
/*
 * Crea le <section> per la selezione delle tabelle del JOIN
 */
function addSelTab($array){
               ?>
<div id="sel1">
    <select id="selTab1" name="selTab1" onchange="updateSel('selTab1','selField1')" >
        <option SELECTED VALUE="Selezionare la tabella:">Selezionare la tabella:</option>
        <?php for($j=0; $j<count($array); $j++) {  ?>
        <option id="<?php echo $array[$j]; ?>" name="<?php echo $array[$j]; ?>"><?php echo $array[$j]; ?></option>
        <?php  } ?>
    </select>
    <select id="selField1" name="selField1" size="20"></select>
</div>
<div id="sel2">
    <select id="selTab2" name="selTab2" onchange="updateSel('selTab2','selField2')"  >
        <option SELECTED VALUE="Selezionare la tabella:">Selezionare la tabella:</option>
        <?php for($j=0; $j<count($array); $j++) {  ?>
        <option id="<?php echo $array[$j]; ?>" name="<?php echo $array[$j]; ?>"><?php echo $array[$j]; ?></option>
        <?php } ?>
    </select>
    <select id="selField2" name="selField2" size="20"></select>
 </div>             
<?php
}


/*
 * Restituisce un array contenente il nome di tutte le tabelle
 */
function getTables(){
    $mycheck = $_POST['mycheck']; //recupero array delle check
    $tot_mycheck = ""; //inizializzo la variabile
    foreach ($mycheck as $value){ 
        $tot_mycheck .= "$value,"; 
    }
    $array_value = explode(",",$mycheck);
    return $array_value;
}
?>

