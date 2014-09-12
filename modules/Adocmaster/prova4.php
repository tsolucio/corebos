<?php

global $adb,$log,$current_user;

 require_once('modules/Adocmaster/Adocmaster.php');
 require_once('modules/Adocdetail/Adocdetail.php');


 $laprueva=$_REQUEST['record'];
$sot=$_REQUEST['sot'];

$sot2=$_REQUEST['sot2'];
$kaction=$_REQUEST['kaction'];


if($kaction==doc1){
   
    require_once("modules/Adocmaster/calculateTariffPrice.php");
$foundRes2=calculatePrice('Adocdetail', $sot2, $laprueva, $sot);
$foundRes3=explode("::",$foundRes2);
 //echo $laprueva; echo $sot2; echo $sot;
 //echo 'okokokok';
 //koment
   echo $foundRes3[7];
 $log->debug("cmimiiriiriiririri".$foundRes3[7]);
}

?>
