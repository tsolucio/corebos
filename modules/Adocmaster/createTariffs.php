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
include_once('vtlib/Vtiger/Module.php');
include_once('modules/Tariffs/Tariffs.php');
global $adb,$current_user;
$adocmasterid=$_REQUEST['adocmasterid'];
$adocdetailsQuery=$adb->pquery("SELECT adoc.adocmastername,
            adocdet.adoc_price, adocdet.adoc_quantity,adocdet.adoc_product,
            acc.accountid,acc.accountname,prod.codice_articolo
            FROM vtiger_adocdetail adocdet
            INNER JOIN vtiger_crmentity ce ON ce.crmid=adocdet.adocdetailid
            INNER JOIN vtiger_adocmaster adoc ON adoc.adocmasterid=adocdet.adoctomaster
            INNER JOIN vtiger_products prod ON prod.productid=adocdet.adoc_product
            INNER JOIN vtiger_project pro ON pro.projectid=adoc.project
            INNER JOIN vtiger_account acc ON acc.accountid=adoc.adoc_account
            WHERE ce.deleted=0 AND adoc.adocmasterid=?",array($adocmasterid));
for($i=0;$i<$adb->num_rows($adocdetailsQuery);$i++){
$price=$adb->query_result($adocdetailsQuery,$i,'adoc_price');
$adoc_quantity=$adb->query_result($adocdetailsQuery,$i,'adoc_quantity');
$adoc_product=$adb->query_result($adocdetailsQuery,$i,'adoc_product');
$codice=$adb->query_result($adocdetailsQuery,$i,'codice_articolo');
$accountid=$adb->query_result($adocdetailsQuery,$i,'accountid');
$accountname=$adb->query_result($adocdetailsQuery,$i,'accountname');
$adocmastername=$adb->query_result($adocdetailsQuery,$i,'adocmastername');

$tariffQuery=$adb->pquery("SELECT * FROM vtiger_tariffs tariff
                           INNER JOIN vtiger_crmentity ce ON ce.crmid=tariff.tariffsid
                           WHERE ce.deleted=0 AND tariff.account=? AND tariff.product=? 
                           AND tariff.initialqty=1 AND tariff.finalqty=1000000 ",array($accountid,$adoc_product));
if($adb->num_rows($tariffQuery)>0){
    $tariffid=$adb->query_result($tariffQuery,0,'tariffsid');
 $adb->query_result("UPDATE vtiger_tariffs SET tariffsname=?,finalpricefour=? WHERE tariffsid=?",array($accountname." X ".$codice,$price,$tariffid));
}
else{
    $focus=new Tariffs();
    $focus->column_fields['tariffsname']=$accountname." X ".$codice;
    $focus->column_fields['account']=$accountid;
    $focus->column_fields['product']=$adoc_product;
    $focus->column_fields['finalpricefour']=$price;
    $focus->column_fields['initialqty']=1;
    $focus->column_fields['finalqty']=1000000;
//    $focus->column_fields['linktopcategories']=
//    $focus->column_fields['linktogcompanies']=        
    $_REQUEST['assigntype'] = 'U';
    $focus->column_fields['assigned_user_id']=$current_user->id;
    $focus->save("Tariffs");
            
}
    

}
?>
