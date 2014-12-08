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

function test($entity_id,$date){
global $adb,$log,$current_user;
$paymentid=$entity_id;//$_REQUEST['paymentid']
$datefrom=date('Y-m-d',strtotime($date));//$_REQUEST['start_date']
$dat_fmt = $current_user->date_format;
$dateFormat=($dat_fmt == 'dd-mm-yyyy') ? 'd-m-Y' : (($dat_fmt == 'mm-dd-yyyy') ? 'm-d-Y' : (($dat_fmt == 'yyyy-mm-dd') ? 'Y-m-d' : ''));

$res = $adb->pquery("Select * from vtiger_payamentstype WHERE payamentstypeid=?", array($paymentid));
        //$datefrom = $adb->query_result($res, 0, 'docdate_from');
        $diffdays = $adb->query_result($res, 0, 'differdays');
        $endofmonth = $adb->query_result($res, 0, 'endofmonth');
        $extradays = $adb->query_result($res, 0, 'extradays');

        $calc_date = $datefrom;
        if ($diffdays <> 0) {
            if ($diffdays == 1)
                $s = "";
            else
                $s = "s";
            $calc_date = date("Y-m-d", strtotime($calc_date . " + $diffdays day$s"));
        }
        if ($endofmonth > 0) {
            $calc_date = date('Y-m-d', strtotime($calc_date));
        }
        if ($extradays <> 0) {
            if ($extradays == 1)
                $s = "";
            else
                $s = "s";
            $calc_date = date("Y-m-d", strtotime($calc_date . " + $extradays day$s"));
        }
        $calc_date=date($dateFormat,strtotime($calc_date));
        return $calc_date;
}

function calculatePrice($selmodule,$linktoprod,$elementid,$quantity){
    global $adb,$log;
    if($selmodule=='Adocdetail'){
        $module_table="vtiger_adocmaster";
        $module_table_id="adocmasterid";
        $account_field="adoc_account";
        $paytype_field="linkvatexempt";
        $price_field="unit_price";
    }
    elseif($selmodule=='PurchaseOrderLines'){
        $module_table="vtiger_purchaseordermaster";
        $module_table_id="purchaseordermasterid";
        $account_field="account_id";
        $paytype_field="payamentstype";
        $price_field="purchaseprice";
    }
    elseif($selmodule=='SalesOrderLines'){
        $module_table="vtiger_salesordermaster";
        $module_table_id="salesordermasterid";
        $account_field="somtoacc";
        $paytype_field="paymentstype";
        $price_field="unit_price";
 }
  
 
    $productQuery=$adb->pquery("SELECT $price_field FROM vtiger_products WHERE productid=?",array($linktoprod));
    $unit_price=$adb->query_result($productQuery, 0, 0);
 
    $log->debug("cmimprodukti".$unit_price);
   
       
        $totaltax=$unit_price*$quantity;
        
        $adocdtotal=$unit_price*$quantity+$totaltax;
        $adocdtotalamount=$unit_price*$quantity;
        
        $foundRes=$paymentid."::".$paymentname."::".$price_tariff."::".$totaltax."::".$adocdtotal."::".$adocdtotalamount."::".$percentage."::".$unit_price."::".$prova;
     $log->debug("gjithevlerat".$foundRes);   return $foundRes;
        
        
        
        
    }
    

?>
