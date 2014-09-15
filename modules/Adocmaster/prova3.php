<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/ 
require_once('modules/Adocmaster/Adocmaster.php');

global $adb,$current_user;
$kaction=$_REQUEST['kaction'];
$content=array();
echo $kaction;
$prova=$_REQUEST['stato'];
echo $prova;
$sasia=$_REQUEST['sasia'];
echo $sasia;
$idja=$_REQUEST['adocdetailid2'];
echo $idja;
$adb->pquery("Update vtiger_adocdetail
    set nrline=?   where adocdetailid=?",array($prova,$idja));
$adb->pquery("Update vtiger_adocdetail
    set adoc_quantity=? where adocdetailid=?",array($sasia,$idja));
?>
