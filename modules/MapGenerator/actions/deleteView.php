<?php
include "modules/MapGenerator/dbclass.php";
$db=$_POST['nameDb'];
$connect = new MysqlClass();
$connect->connetti($db);
$nameView=$_POST['nameView'];
$delete="delete from viste where nome_vista='$nameView'";
$query= mysql_query($delete);
$dropQuery="drop table ".$nameView;
mysql_query($dropQuery);
if($query){
    echo 'La vista "'.$nameView.'" è stata cancellata.';
}

?>
