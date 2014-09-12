<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 
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
    set adoc_quantity=? where adocdetailid=?",array($sasia,$idja));*/

 require_once('modules/Adocmaster/Adocmaster.php');
 require_once('modules/Adocdetail/Adocdetail.php');

global $adb,$current_user;
$sot5=$_REQUEST['sot5'];
echo 'sot5'.$sot5;
$sot3=$_REQUEST['sot3'];
echo $sot3;
$sot=$_REQUEST['sot'];
echo 'sot'.$sot;
$sot2=$_REQUEST['sot2'];
echo 'sot2'.$sot2;
$totiduhur=$_REQUEST['totiduhur'];
echo 'total'.$totiduhur;
$taxiduhur=$_REQUEST['taxiduhur'];
echo 'tax'.$taxiduhur;
$totali3=$_REQUEST['totali3'];
echo 'totali3'.$totali3;
$discount2=$_REQUEST['discount2'];
echo 'discount'.$discount2;
$ageadding=$_REQUEST['ageadding'];
echo 'ageadding'.$ageadding.'ageadding';
$quantityadding=$_REQUEST['quantityadding'];
echo 'quantityadding'.$quantityadding.'quantityadding';
$adocdelete=$_REQUEST['adocdelete'];
echo 'fshi'.$adocdelete.'fshi';
$price2=$_REQUEST['price2'];
echo $price2*$discount2;
echo 'cmimiedit'.$price2.'cmimiedit';
$productadding=$_REQUEST['productadding'];
echo 'productadding'.$productadding.'productadding';
$pcprice2=$_REQUEST['pcprice2'];
echo 'cmimi'.$pcprice2.'cmimi';
$pcquantity2=$_REQUEST['pcquantity2'];
echo 'totali'.$pcquantity2.'totali';
$pcdetailsid2=$_REQUEST['pcdetailsid2'];
echo 'pcid'.$pcdetailsid2.'pcid';
$produkt=$_REQUEST['product2'];
echo 'produktii'.$produkt.'produktii';
$laprueva=$_REQUEST['record'];
echo 'rekordi'.$laprueva.'rekordi';
$kaction=$_REQUEST['kaction'];
echo 'kaction'.$kaction.'kaction';
$content=array();
echo $kaction;
$prova=$_REQUEST['stato'];
echo '<br>';
echo 'prova'.$prova;
$sasia=$_REQUEST['sasia'];
echo '<br>';
echo 'sasia'.$sasia;
$idja=$_REQUEST['adocdetailid2'];
echo '<br>';
echo 'eriid'.$idja;
$adocmasterid=$_REQUEST['adocmasterid2'];
echo '<br>';
echo 'id'.$adocmasterid;
$tax2=$_REQUEST['newtax2'];
$adoc2=$_REQUEST['newadoctotal2'];
$amount2=$_REQUEST['newadoctotalamount2'];
echo '<br><br><br>';
echo $tax2;
echo '<br><br><br>';
echo $adoc2;
echo '<br><br><br>';
echo $amount2;
$productid=$_REQUEST['productid2'];
echo '<br><br><br>';
echo 'PRODUCTID'.$productid;
//if ( $prova==-1){
    //$focus = CRMEntity::getInstance('PCDetails');
  //  echo 'okhere';
   //$focus=new Adocdetail();
  

   //$adoc1=new Adocdetail();
  
 //  $adoc1->column_fields['adoc_quantity']=$sasia;
  //$adoc1->column_fields['adoctomaster']=$laprueva;
   //$adoc1->column_fields['assigned_user_id']=$focus->column_fields['assigned_user_id'];
   //$adoc1->column_fields['adoc_product']=$produkt;
   
 //  $adoc1->save("Adocdetail");
    
    
    
//}
if($kaction==adding){
    echo 'addingnewitem';
    echo 'aha'.$sot5.'aha';
    $focus=new Adocdetail();
  

   $adoc1=new Adocdetail();
  
   $adoc1->column_fields['adoc_quantity']=$sot;
  $adoc1->column_fields['adoctomaster']=$laprueva;
   $adoc1->column_fields['assigned_user_id']=$focus->column_fields['assigned_user_id'];
   $adoc1->column_fields['adoc_product']=$sot2;
  $adoc1->column_fields['adoc_price']=$sot5;
   $adoc1->save("Adocdetail");
}
/*if($kaction==doc1){
   
    require_once("modules/Adocmaster/calculateTariffPrice.php");
$foundRes2=calculatePrice('Adocdetail', $sot2, $laprueva, $sot);
$foundRes3=explode("::",$foundRes2);
 //echo $laprueva; echo $sot2; echo $sot;
 //echo 'okokokok';
 
   echo $foundRes3[2];
}*/
if($kaction=='delete'){
    echo 'deleteit';
     $adb->pquery("delete from vtiger_adocdetail where adocdetailid=?",array($adocdelete));
}

$taxquery=$adb->pquery("Select sum(a.adocdtax) as tax,sum(a.adocdtotal) as total,
                    sum(a.adocdtotalamount) as totalimponibile,vtiger_payamentstype.vatpercentage mastervat
                    from vtiger_adocdetail a 
                    join vtiger_crmentity ce on a.adocdetailid=ce.crmid 
                    INNER JOIN vtiger_adocmaster ON a.adoctomaster=vtiger_adocmaster.adocmasterid
                    INNER JOIN vtiger_payamentstype ON vtiger_adocmaster.linkpayment=vtiger_payamentstype.payamentstypeid
                    where ce.deleted=0 and a.adoctomaster=?",array($adocmasterid));
$tax=round($adb->query_result($taxquery,0,'tax'),3);
$adocdtotal=round($adb->query_result($taxquery,0,'total'),3);
$adocdtotalamount=round($adb->query_result($taxquery,0,'totalimponibile'),3);
echo '<br>';
echo $tax;
echo '<br>';
echo $adocdtotal;
echo '<br>';
echo $adocdtotalamount;
 $queryIva=$adb->pquery('Select sum(a.adocdtax) as tax,sum(a.adocdtotal) as total,
                    sum(a.adocdtotalamount) as totalimponibile,vtiger_payamentstype.vatpercentage mastervat
                    from vtiger_adocdetail a 
                    join vtiger_crmentity ce on a.adocdetailid=ce.crmid 
                    INNER JOIN vtiger_adocmaster ON a.adoctomaster=vtiger_adocmaster.adocmasterid
                    INNER JOIN vtiger_payamentstype ON vtiger_adocmaster.linkpayment=vtiger_payamentstype.payamentstypeid
                    where ce.deleted=0 and a.adoctomaster=? and a.adocdtax>0',array($adocmasterid));
        $totalIva=round($adb->query_result($queryIva,0,'totalimponibile'),3);
        $queryNoIva=$adb->pquery("Select sum(a.adocdtax) as tax,sum(a.adocdtotal) as total,
                    sum(a.adocdtotalamount) as totalimponibile,vtiger_payamentstype.vatpercentage mastervat
                    from vtiger_adocdetail a 
                    join vtiger_crmentity ce on a.adocdetailid=ce.crmid 
                    INNER JOIN vtiger_adocmaster ON a.adoctomaster=vtiger_adocmaster.adocmasterid
                    INNER JOIN vtiger_payamentstype ON vtiger_adocmaster.linkpayment=vtiger_payamentstype.payamentstypeid
                    where ce.deleted=0 and a.adoctomaster=? and a.adocdtax=0",array($adocmasterid));
        $totalNoIva=round($adb->query_result($queryNoIva,0,'totalimponibile'),3);
        echo '<br>';
        echo $totalIva;
 



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
echo 'eri3'.$nuevoprecio.'eri3';
echo 'eri4'.$foundRes3[6].'eri4';
echo 'eri5'.$foundRes3[7].'eri5';
echo 'vat'.$foundRes3[8].'vat';
$nuevotax=$foundRes3[3];
$nuevototal=$foundRes3[4];
$nuevoamount=$foundRes3[5];
echo 'eri2'.'<br>'.$nuevotax; echo '<br>';
echo 'eri2'.'<br>'.$nuevototal; echo '<br>';
echo 'eri2'.'<br>'.$nuevoamount; echo '<br>';
  $adb->pquery("update vtiger_adocdetail set adocdtax=?,adocdtotal=?,adocdtotalamount=? where adocdetailid=? ",array($nuevotax,$nuevototal,$nuevoamount,$idja));
  
       $adb->pquery("update vtiger_adocdetail set adoc_price=? where adocdetailid=?",array($price2,$idja));
     
      
// $adb->pquery("update vtiger_pcdetails set price=?,quantity=?,totalprice=? where pcdetailsid=?",array($pcprice2,$pcquantity2,$pcprice2*$pcquantity2,$pcdetailsid2));
      //koment   
 //$adb->pquery("update vtiger_products set unit_price=? where productid=?",array($price2,$productid));
//koment
?>
