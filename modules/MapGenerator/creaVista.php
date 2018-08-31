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
include "dbclass.php";
$db=$_POST['nameDb'];
$connect = new MysqlClass();
$connect->connetti($db);
$query = $_POST['query'];
$nomeVista = $_POST['nameView'];;
$result="";
$creazioneTabella="";
$inserimento="";
$queryInserimento="insert into viste (nome_vista, query) values ('$nomeVista', '$query')";

if(controlloQuery($query)){
     $result = mysql_query($query); 
     if (!table_exists("viste", $db)) {
	$queryCreazioneTabella="create table viste (nome_vista varchar(100), query varchar (9000));";
        $creazioneTabella=mysql_query($queryCreazioneTabella); 
    }
    $inserimento=mysql_query($queryInserimento);
    echo ' <b>La vista materializzata "'.$nomeVista.'" è stata creata con successo!</b>';
}
else{
    echo " <b>Attenzione la query inserita è errata, si prega di ricontrollare i parametri inseriti!</b>";
}
$connect->disconnettiMysql();

function controlloQuery($stringa){
    $position=strpos($stringa, "AS");
    $query =substr($stringa, $position+2);
    $result = mysql_query($query); 
    return $result;
}
function table_exists($table, $db) { 
	$tables = mysql_list_tables ($db); 
	while (list ($temp) = mysql_fetch_array ($tables)) {
		if ($temp == $table) {
			return TRUE;
		}
	}
	return FALSE;
}
?>
