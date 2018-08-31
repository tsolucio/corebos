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
 $db=$_POST['nameDbViews'];
// istanza della classe
$connect = new MysqlClass();
// chiamata alla funzione di connessione
$vista='';
$selTab='';
$connect->connetti($db);
$i=0;
$query="select nome_vista from viste;";
$risultato = mysql_query($query);

while ($riga = mysql_fetch_array($risultato, MYSQL_ASSOC)) {
   $vista[$i]=$riga["nome_vista"];
   $i++;
}

if ($vista!=''){
for($i=0;$i<count($vista);$i++){ 
    $selTab=$selTab."<option id='".$vista[$i]."' name='".$vista[$i]."'>".$vista[$i]."</option>";      
} 
$connect->disconnettiMysql();
echo $selTab;
}     
?>



