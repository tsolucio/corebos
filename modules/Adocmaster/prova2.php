
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
include_once("include/database/PearDatabase.php");
global $adb;
require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');

//$hey='heythere';
//echo $hey;
//$hi=$_REQUEST['records'];

//$ha=json_decode($hi);
$hi2=$_REQUEST['result'];
$eri=$_REQUEST['value2'];
echo $eri;

//echo $hi2;

var_dump(json_decode($hi2));
$json_output=json_decode($hi2);

foreach ( $json_output as $var1 )
{
    if($var1->duration==-1){
   echo $var1->added21;
     include_once('modules/Adocdetail/Adocdetail.php');
global $current_user;
$focus = new Adocdetail();


    
   //$focus->column_fields["name"] = $var1->added7;
   $focus->column_fields["assigned_user_id"] =$current_user->id;
   
$focus->column_fields["adocdetailname"]=$var1->added9;
$focus->column_fields["adoc_quantity"]=$var1->added6;
$focus->column_fields["adoctomaster"] =$var1->added21;
   //$focus->column_fields["msgdescription"] = $var1->added7;
   $focus->column_fields["adoc_product"] =$eri;
  
  //$focus->column_fields["messagecategory"] ="Messaggio sollecito";
   $focus->save("Adocdetail");


   



    //echo "{$var1->id}\n";
    //echo "{$var1->added8}\n";
   // echo "{$var1->added4}\n";
    //echo "{$var1->duration}\n";
   // echo "{$var1->added27}\n";
    //$adb->pquery("update vtiger_adocdetail set adoc_price=? where adocdetailid=? ",array($var1->added22,$var1->id));
  // $adb->pquery("insert into vtiger_adocdetail(adocdetailid,adocdetailno,adocdetailname,nrline,adoctomaster,adoc_product,adoc_quantity,adoc_price,inout_docnr,poteknema,posupplier,riferimento,adoc_stock,chiave_adocdetailid,pcdetail,adocdetail_project) values (?,'','','',?,?,?,'','','','','','','','','')",array($var1->id,$var1->added21,$eri,$var1->added6));
   // $adb->pquery("update vtiger_adocdetail set nrline=? where adocdetailid=? ",array($var1->duration,$var1->id));
    //$adb->pquery("update vtiger_adocdetail set adoc_quantity=? where adocdetailid=? ",array($var1->duration,$var1->id));
 // $adb->pquery("update vtiger_adocdetail set adoc_quantity=? where adocdetailid=? ",array($var1->added6,$var1->id));
  // $adb->pquery("update vtiger_products set peso=? where productid=? ",array(0.006*$var1->added6,$var1->added21));
    
 //  $adb->pquery("update vtiger_adocdetail set adocdtax=?,adocdtotal=?,adocdtotalamount=? where adocdetailid=? ",array($var1->added27,$var1->added28,$var1->added29,$var1->id));
//    $adb->pquery("update vtiger_adocdetail,vtiger_products set adoc_price=? where adocdetailid=? and peso=? and productid=?",array($var1->added22,$var1->id,0.006*$var1->added6,$var1->added21));
    //$adb->pquery("update vtiger_adocdetail,vtiger_products set peso=?,adoc_price=? where adoc_quantity=? and adocdtax=? and adocdtotal=? and adocdtotalamount=? and adocdetailid=? and productid=?",
        // array(0.006*$var1->added6,$var1->added22,$var1->added6,$var1->added27,$var1->added28,$var1->added29,$var1->id,$var1->added21)   );
/// $adb->pquery("update vtiger_adocdetail set adoc_price=? where adocdetailid=? and adoc_quantity=?  ",array($var1->added22,$var1->id,$var1->added6));
    
}}

?>



