<?php
include "modules/MapGenerator/dbclass.php";
$db=$_POST['nameDb'];
$data = new MysqlClass();
$data->connetti($db);
$i=0;
$elencoTabelle = mysql_list_tables ($db);
while ($i < mysql_num_rows ($elencoTabelle)){
    $tabella[$i] = mysql_tablename ($elencoTabelle, $i);
    $i++;
}
for($i=0;$i<count($tabella);$i++){ 
    $selTab=$selTab."<li><label for=".$tabella[$i]."><input type='checkbox' name='myCheck[]' id='myCheck' value='".$tabella[$i]."' class='checkbox'>".$tabella[$i]."</label></li>";      
} 
echo $selTab;
?>