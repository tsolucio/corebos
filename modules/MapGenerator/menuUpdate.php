<html>
<head><title>Aggiorna viste</title></head>
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
$res = mysql_query("SHOW DATABASES");
$i=0;
while ($row = mysql_fetch_assoc($res)){
    $dbList[$i]=$row['Database'];
    $i++;
}
?>
<div id="divUpdate">
    <ul id="elements">
        <li>
           <select id="dbListViews" name="dbListViews" onchange="selDBViews()" >
                <option SELECTED VALUE="Selezionare la tabella:">Selezionare il database:</option>
                <?php for($j=0; $j<count($dbList); $j++) {  ?>
                    <option id="<?php echo $dbList[$j]; ?>" name="<?php echo $dbList[$j]; ?>"><?php echo $dbList[$j]; ?></option>
                <?php  } ?>
           </select>  
        </li>
        <li><select id="selViews" name="selViews" size="10" ></select></li>
        <li> <button id="updateView" value="Aggiorna vista" onclick="updateView()">Aggiorna vista</button></li>   
        <li><div id="immagine" style="display:none"><img src="modules/MapGenerator/image/ajax-loader.gif" /></div></li>
        <li><div id="resultView"></div></li>
    </ul>
</div>
</html>