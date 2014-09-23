<?php
 /*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Adecuaciones
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/


 require_once('modules/Adocmaster/Adocmaster.php');
 require_once('modules/Adocdetail/Adocdetail.php');

global $adb,$current_user;
$sot5=$_REQUEST['sot5'];

$sot3=$_REQUEST['sot3'];

$sot=$_REQUEST['sot'];

$sot2=$_REQUEST['sot2'];

$totiduhur=$_REQUEST['totiduhur'];

$taxiduhur=$_REQUEST['taxiduhur'];

$totali3=$_REQUEST['totali3'];

$discount2=$_REQUEST['discount2'];

$ageadding=$_REQUEST['ageadding'];

$quantityadding=$_REQUEST['quantityadding'];

$adocdelete=$_REQUEST['adocdelete'];

$price2=$_REQUEST['price2'];


$productadding=$_REQUEST['productadding'];

$pcprice2=$_REQUEST['pcprice2'];

$pcquantity2=$_REQUEST['pcquantity2'];

$pcdetailsid2=$_REQUEST['pcdetailsid2'];

$produkt=$_REQUEST['product2'];

$laprueva=$_REQUEST['record'];

$kaction=$_REQUEST['kaction'];


$prova=$_REQUEST['stato'];

$sasia=$_REQUEST['sasia'];

$idja=$_REQUEST['adocdetailid2'];

$adocmasterid=$_REQUEST['adocmasterid2'];

$tax2=$_REQUEST['newtax2'];
$adoc2=$_REQUEST['newadoctotal2'];
$amount2=$_REQUEST['newadoctotalamount2'];

$productid=$_REQUEST['productid2'];

if($kaction==adding){
    
    $focus=new Adocdetail();
  

   $adoc1=new Adocdetail();
  
   $adoc1->column_fields['adoc_quantity']=$sot;
  $adoc1->column_fields['adoctomaster']=$laprueva;
   $adoc1->column_fields['assigned_user_id']=$focus->column_fields['assigned_user_id'];
   $adoc1->column_fields['adoc_product']=$sot2;
  $adoc1->column_fields['adoc_price']=$sot5;
   $adoc1->save("Adocdetail");
}

if($kaction=='delete'){
   
     $adb->pquery("delete from vtiger_adocdetail where adocdetailid=?",array($adocdelete));
}


 



$adb->pquery("Update vtiger_adocdetail
    set nrline=?   where adocdetailid=?",array($prova,$idja));
$adb->pquery("Update vtiger_adocdetail
    set adoc_quantity=? where adocdetailid=?",array($sasia,$idja));
$adb->pquery("Update vtiger_adocdetail
    set adoc_product=? where adocdetailid=?",array($produkt,$idja));
 $adb->pquery("UPDATE vtiger_adocmaster set totalamount=?,taxamount=?,amount=?,impiva=?,nonimpiva=?
        WHERE adocmasterid=? ",array($totiduhur,$taxiduhur,$totali3,$totalIva,$totalNoIva,$adocmasterid));

require_once("modules/Adocmaster/calculateTariffPrice.php");
$foundRes2=calculatePrice('Adocdetail', $productid, $adocmasterid, $sasia);
$foundRes3=explode("::",$foundRes2);
$nuevoprecio=$foundRes3[2];

$nuevotax=$foundRes3[3];
$nuevototal=$foundRes3[4];
$nuevoamount=$foundRes3[5];

  $adb->pquery("update vtiger_adocdetail set adocdtax=?,adocdtotal=?,adocdtotalamount=? where adocdetailid=? ",array($nuevotax,$nuevototal,$nuevoamount,$idja));
  
       $adb->pquery("update vtiger_adocdetail set adoc_price=? where adocdetailid=?",array($price2,$idja));
     
      

?>
