<?php
include "modules/MapGenerator/dbclass.php";
$db=$_POST['nameDb'];
$connect = new MysqlClass();
$connect->connetti($db);
$nameView=$_POST['nameView'];
$updateString="select viste.query from viste where nome_vista='$nameView'";
$res= mysql_query($updateString);
 while ($r = mysql_fetch_array($res, MYSQL_BOTH)){ 
    $queryDiCreazione=$r['query'];

 }
 
$dropQuery="drop table ".$nameView;
mysql_query($dropQuery);
$creazione=mysql_query( $queryDiCreazione);
$connect->disconnettiMysql();

if(($creazione)){
    echo 'La vista "'.$nameView.'" é stata aggiornata con successo.';
}
else{
    echo "Aggiornamento non riuscito!";
}
?>
