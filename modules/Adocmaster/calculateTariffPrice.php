<?php


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
    $moduleQuery = $adb->pquery("Select *,ptype.vatpercentage mastervat  
                        FROM  $module_table 
                        LEFT JOIN vtiger_account ON vtiger_account.accountid=$module_table.$account_field
                        LEFT JOIN vtiger_groupcompanies ON vtiger_groupcompanies.groupcompaniesid=vtiger_account.linktogroupcompanies
                        LEFT JOIN vtiger_payamentstype as ptype ON ptype.payamentstypeid=$module_table.$paytype_field
                        WHERE $module_table_id=?", array($elementid));
    $groupcompany = $adb->query_result($moduleQuery, 0, 'linktogroupcompanies');
    $mastervat=$adb->query_result($moduleQuery, 0,'mastervat');
    $paymenttype=$adb->query_result($moduleQuery, 0,$paytype_field);
 
    $productQuery=$adb->pquery("SELECT $price_field,linktoproductscategory,linktopay FROM vtiger_products WHERE productid=?",array($linktoprod));
    $unit_price=$adb->query_result($productQuery, 0, 0);
    $prodcategory = $adb->query_result($productQuery, 0, 1);
    $paymentid=$adb->query_result($productQuery, 0,2);
   $paymentQuery=$adb->pquery("SELECT vatpercentage as productvat,de_payament as paymentname FROM  vtiger_payamentstype WHERE payamentstypeid=?",array($paymentid));
    $vat=$adb->query_result($paymentQuery, 0,'productvat');
    $paymentname=$adb->query_result($paymentQuery, 0,'paymentname');
    $log->debug('provasot'.$paymentname);
   $tariffquery = $adb->query("SELECT * FROM vtiger_tariffs 
                                    INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_tariffs.tariffsid
                                    WHERE ce.deleted=0 AND product=$prodcategory AND account=$groupcompany AND initialqty<=$quantity AND finalqty>=$quantity");
    $tid=$adb->query_result($tariffquery,0,'tariffsid');
   $log->debug('tarifprova'.$tid);
    if ($adb->num_rows($tariffquery) > 0) {
        $percentage = $adb->query_result($tariffquery, 0, 'percentage');
       $log->debug('provaperqindja'.$percentage);
        $price_tariff = $adb->query_result($tariffquery, 0, 'finalpricefour');
$log->debug('cmimi'.$price_tariff);
        if($percentage=='0.00') $percentage='';
            if (!empty($percentage))
                $price_tariff = $unit_price * $percentage;
            else if(empty($price_tariff))
                $price_tariff = $unit_price;
    }
    else
   
     $price_tariff = $unit_price;   
        //$totalval=$price_tariff*$quantity;
//           $vat=$vatpercent;
//            if(!empty($paymenttype))
//            $vat=$mastervat*$vat;
       
        $totaltax=$price_tariff*$vat*$quantity;
        
        $adocdtotal=$price_tariff*$quantity+$totaltax;
        $adocdtotalamount=$price_tariff*$quantity;
        
        $foundRes=$paymentid."::".$paymentname."::".$price_tariff."::".$totaltax."::".$adocdtotal."::".$adocdtotalamount."::".$percentage."::".$unit_price."::".$vat."::".$prova;
        return $foundRes;
    
}
