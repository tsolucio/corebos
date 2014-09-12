<?php

/***************************
  Sample using a PHP array
****************************/

require('include/fpdm/fpdm.php');



global $adb,$log;
$inoutmaster=$_REQUEST['record'];
$str= $_SERVER['HTTP_REFERER'];

 $importantStuff = explode('DetailView&record=', $str);

 $record = $importantStuff[1];

$sql = "SELECT vtiger_accountbillads.bill_city as acc_city, vtiger_accountbillads.bill_state as acc_state, vtiger_accountbillads.bill_country as acc_country
FROM vtiger_adocdetail
LEFT JOIN vtiger_products ON vtiger_adocdetail.adoc_product = vtiger_products.productid
LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_adocdetail.adocdetailid
LEFT JOIN vtiger_adocmaster ON vtiger_adocdetail.adoctomaster = vtiger_adocmaster.adocmasterid
 LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_adocmaster.adoc_account
 LEFT JOIN vtiger_accountbillads ON vtiger_account.accountid=vtiger_accountbillads.accountaddressid
 LEFT JOIN vtiger_sites ON vtiger_account.accountid=vtiger_sites.accountid
WHERE deleted =0 AND adoctomaster =?

        ";
	
      
	$result = $adb->pquery($sql,array($record));
$sql1 = "SELECT * , vtiger_payamentstype.de_payament, vtiger_accountbillads.bill_city as acc_city, vtiger_accountbillads.bill_code as acc_code, vtiger_accountbillads.bill_state as acc_state, vtiger_accountbillads.bill_country as acc_country
FROM vtiger_adocdetail
LEFT JOIN vtiger_products ON vtiger_adocdetail.adoc_product = vtiger_products.productid
LEFT JOIN vtiger_payamentstype ON vtiger_adocdetail.paymentstype = vtiger_payamentstype.payamentstypeid
LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_adocdetail.adocdetailid
LEFT JOIN vtiger_adocmaster ON vtiger_adocdetail.adoctomaster = vtiger_adocmaster.adocmasterid
LEFT JOIN vtiger_project ON vtiger_project.projectid = vtiger_adocmaster.project
LEFT JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
 LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_adocmaster.adoc_account
 LEFT JOIN vtiger_accountbillads ON vtiger_account.accountid=vtiger_accountbillads.accountaddressid
 LEFT JOIN vtiger_sites ON vtiger_account.accountid=vtiger_sites.accountid
WHERE deleted =0 AND productname !='null' AND adoctomaster =?

        ";
	
 
	$result1 = $adb->pquery($sql1,array($record));
        $type=$adb->query_result($result1,0,'doctype');
       $acc=$adb->query_result($result1,0,'adoc_account');
        $acc_name=$adb->query_result($result1,0,'accountname');
$paymentlink =$adb->query_result($result1,0,'linkpayment'); 

$sql2 = "SELECT * FROM vtiger_payamentstype WHERE payamentstypeid =?";
$result2 = $adb->pquery($sql2,array($paymentlink));
$payment= $adb->query_result($result2,0,'de_payament');

        $bill_city=$adb->query_result($result,0,0);
        $bill_state=$adb->query_result($result,0,1);
        $bill_country=$adb->query_result($result,0,2);
        
        $bill_street=$adb->query_result($result1,0,'bill_street');
        $query=$adb->pquery("Select cityname from vtiger_cities
        where vtiger_cities.citiesid=?",array($bill_city));
     
        $cityname=$adb->query_result($query,0,'cityname');$log->debug('test5 ' .$cityname);
        $query=$adb->pquery("Select countyname from vtiger_counties
        where vtiger_counties.countiesid=?",array($bill_state));
        $countyname=$adb->query_result($query,0,'countyname');$log->debug('test5 ' .$countyname);
        $query=$adb->pquery("Select countriesname from vtiger_country
        where vtiger_country.countryid=?",array($bill_country));
        $countriesname=$adb->query_result($query,0,0);$log->debug('juli ' .$countriesname);

         $date=$adb->query_result($result1,$i,'docdate_from');
   $date1 = date('d-m-Y', strtotime($date));
   $date2=$adb->query_result($result1,$i,'docdate_to');
   $date2 = date('d-m-Y', strtotime($date2));
   $nrprod=$adb->num_rows($result1);
   for($i=0;$i<$nrprod;$i++){
       
  $quantity[$i]=$adb->query_result($result1,$i,'adoc_quantity');
  $nrline[$i] = $adb->query_result($result1,$i,'nrline')+1;
  $price[$i]=$adb->query_result($result1,$i,'adoc_price');
  $prodname[$i]=$adb->query_result($result1,$i,'productname');
   $poref[$i]=$adb->query_result($result1,$i,'riferimento');
  $prodname[$i]=$adb->query_result($result1,$i,'productname');
  $tot[$i]=$adb->query_result($result1,$i,'adocdtotalamount');
  $tot[$i] = '$ '.formatMoney($tot[$i],true);
  $parts = explode( ' ', $adb->query_result($result1,$i,'de_payament') );
if(strpos($adb->query_result($result1,$i,'de_payament'),"ALIQUOTA IVA")==false)
 $vat[$i]=$parts[2];
 
}
$totam=$adb->query_result($result1,0,'totalamount');
$subtot=$adb->query_result($result1,0,'taxamount');
$totiv=$adb->query_result($result1,0,'amount');
$impiva = $adb->query_result($result1,0,'impiva');
$nonimpiva = $adb->query_result($result1,0,'nonimpiva');
$nrdoc=$adb->query_result($result1,0,'nrdoc');
   $tot1 = $quantity[0]*$price[0];
    $tot1 = number_format($tot1, 2);
    $tot2 = $quantity[1]*$price[1];
    $tot2 = number_format($tot2, 2);
    $tot3 = $quantity[2]*$price[2];
    $tot3 = number_format($tot3, 2);
    $tot4 = $quantity[3]*$price[3];
    $tot4 = number_format($tot4, 2);
    $tot5 = $quantity[4]*$price[4];
    $tot5 = number_format($tot5, 2);
    $tot6 = $quantity[5]*$price[5];
    $tot6 = number_format($tot6, 2);
     $tot7 = $quantity[6]*$price[6];
    $tot7 = number_format($tot7, 2); 
    $tot8 = $quantity[7]*$price[7];
    $tot8 = number_format($tot8, 2);
    $sutot = $tot1 + $tot2 + $tot3+$tot4+$tot5+$tot6+$tot7+$tot8;
    $sutot = number_format($sutot, 2);
    $iva = $sutot*0.22;
    $iva = number_format($iva, 2);
     $totfat = $sutot+$iva;
   $totfat = number_format($totfat, 2);
   $name = $adb->query_result($result1,0,'accountname');
    function formatMoney($number, $fractional=false) { 
    if ($fractional) { 
             $number =  $nombre_format_francais = number_format($number, 2, ',', '.') ;
    } 
    while (true) { 
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 
        if ($replaced != $number) { 
            $number = $replaced; 
        } else { 
            break; 
        } 
    } 
    return $number; 
} 

   $partitaiva = $adb->query_result($result1,0,'partitaiva');
//   echo $nrprod;exit;
$doct= $adb->query_result($result1,0,'doctype');
if($doct ==='Ordine di Vendita'){
    
    $sqlt="SELECT * FROM vtiger_account LEFT JOIN vtiger_accountbillads ON vtiger_account.accountid=vtiger_accountbillads.accountaddressid WHERE accountid = 26584";
     $resultt = $adb->query($sqlt);
    $fields = array(
    'tipodocumento'  => $type,
    'accountname'    => $acc_name,
        'sellername' =>$adb->query_result($resultt,0,'accountname'),
        'nomecompleto'=>$adb->query_result($resultt,0,'briefdesc'),
        'selleraddress'=>$adb->query_result($resultt,0,'bill_street'),
        'tel'=>$adb->query_result($resultt,0,'phone'),
        'fax'=>$adb->query_result($resultt,0,'fax'),
        'selleremail'=>$adb->query_result($resultt,0,'email1'),
    //'trackingno'    => $adb->query_result($result1,0,'nrtracking'),
    'client_country' => $adb->query_result($result1,0,'acc_country'),
    'client_address'   =>  $bill_street,
    'accountcap_city_prov'   => $adb->query_result($result1,0,'acc_code').','.$adb->query_result($result1,0,'acc_city').','.$adb->query_result($result1,0,'acc_state'),
    'accountvat' => 'P.IVA:'.$partitaiva.'',
    'invoicenumber' =>'',
    'issuedate'   => $date1,
    'som_number'   => $adb->query_result($result1,0,'nrdoc'),
    'no1' => $nrline[0],
    'productname1'   => $prodname[0],
    'quantity1'   => $quantity[0],
    'unitprice1'   => $price[0],
    'code1' => $adb->query_result($result1,0,'codice_articolo'),
    'unit1' =>'',
    //'aliquota1'  => $vat[0],
    'amount1'   => $tot[0],
    'no2' => $nrline[1],
    'productname2'   => $prodname[1],
    'quantity2'   => $quantity[1],
    'unitprice2'   => $price[1],
    'code2' => $adb->query_result($result1,1,'codice_articolo'),
    'unit2' =>'',
    'amount2'   => $tot[1],
    'no3' => $nrline[2],
    'productname3'   => $prodname[2],
    'quantity3'   => $quantity[2],
    'unitprice3'   => $price[2],
    'code3' => $adb->query_result($result1,2,'codice_articolo'),
    'unit3' =>'',
    'amount3'   => $tot[2],
    'no4' => $nrline[3],
    'productname4'   => $prodname[3],
    'quantity4'   => $quantity[3],
    'unitprice4'   => $price[3],
    'code4' => $adb->query_result($result1,3,'codice_articolo'),
    'unit4' =>'',
    'amount4'   => $tot[3],
    'no5' => $nrline[4],
    'productname5'   => $prodname[4],
    'quantity5'   => $quantity[4],
    'unitprice5'   => $price[4],
    'code5' => $adb->query_result($result1,4,'codice_articolo'),
    'unit5' =>'',
    'amount5'   => $tot[4],
    'no6' => $nrline[5],
    'productname6'   => $prodname[5],
    'quantity6'   => $quantity[5],
    'unitprice6'   => $price[5],
    'code6' => $adb->query_result($result1,5,'codice_articolo'),
    'unit6' =>'',
    'amount6'   => $tot[5],
    'no7' => $nrline[6],
    'productname7'   => $prodname[6],
    'quantity7'   => $quantity[6],
    'unitprice7'   => $price[6],
    'code7' => $adb->query_result($result1,6,'codice_articolo'),
    'unit7' =>'',
    'amount7'   => $tot[6],
    'no8' => $nrline[7],
    'productname8'   => $prodname[7],
    'quantity8'   => $quantity[7],
    'unitprice8'   => $price[7],
    'code8' => $adb->query_result($result1,7,'codice_articolo'),
    'unit8' =>'',
    'amount8'   => $tot[7],
    'portofloading' =>$adb->query_result($result1,0,'pol'),
    'portofdischarge' =>$adb->query_result($result1,0,'pod'),
    'departuredate'=>$adb->query_result($result1,0,'dtvessel'),
    //'vesselname'=>$adb->query_result($result1,0,'vslname'),
    'billoflading'=>$adb->query_result($result1,0,'cf_1310'),
    'sayinwords'=>'',
    'imponibile'   => formatMoney($totiv,true),
    'vat_amount'   => formatMoney($subtot,true),
    'valore'   => formatMoney($totam,true),
    'paymenttype' => $payment,
    'totimponibilevat' => '$ '.formatMoney($impiva,true),
    'ivatotale'  => '$ '.formatMoney($subtot,true),
    'nonimponibilevat' => '$ '.formatMoney($nonimpiva,true),
    'description'   => $prodname[0].'          '.$poref[0].'
'.$prodname[1].'          '.$poref[1].'
'.$prodname[2].'          '.$poref[2].'
'.$prodname[3].'          '.$poref[3].'
'.$prodname[4].'          '.$poref[4].'
'.$prodname[5].'          '.$poref[5].'
'.$prodname[6].'          '.$poref[6].'
'.$prodname[7].'          '.$poref[7],
    
    
    
);

$pdf = new FPDM('include/fpdm/opt_salesorder.pdf');
}else{
$fields = array(
    'tipodocumento'  => $type,
    'accountname'    => $acc_name,
    'trackingno'    => $adb->query_result($result1,0,'nrtracking'),
    'client_country' => $adb->query_result($result1,0,'acc_country'),
    'client_address'   =>  $bill_street,
    'accountcap_city_prov'   => $adb->query_result($result1,0,'acc_code').','.$adb->query_result($result1,0,'acc_city').','.$adb->query_result($result1,0,'acc_state'),
    'accountvat' => 'P.IVA:'.$partitaiva.'',
    'invoicenumber' =>'',
    'issuedate'   => $date1,
    'som_number'   => $adb->query_result($result1,0,'nrdoc'),
    'no1' => $nrline[0],
    'productname1'   => $prodname[0],
    'quantity1'   => $quantity[0],
    'unitprice1'   => $price[0],
    'code1' => $adb->query_result($result1,0,'codice_articolo'),
    'unit1' =>'',
    //'aliquota1'  => $vat[0],
    'amount1'   => $tot[0],
    'no2' => $nrline[1],
    'productname2'   => $prodname[1],
    'quantity2'   => $quantity[1],
    'unitprice2'   => $price[1],
    'code2' => $adb->query_result($result1,1,'codice_articolo'),
    'unit2' =>'',
    'amount2'   => $tot[1],
    'no3' => $nrline[2],
    'productname3'   => $prodname[2],
    'quantity3'   => $quantity[2],
    'unitprice3'   => $price[2],
    'code3' => $adb->query_result($result1,2,'codice_articolo'),
    'unit3' =>'',
    'amount3'   => $tot[2],
    'no4' => $nrline[3],
    'productname4'   => $prodname[3],
    'quantity4'   => $quantity[3],
    'unitprice4'   => $price[3],
    'code4' => $adb->query_result($result1,3,'codice_articolo'),
    'unit4' =>'',
    'amount4'   => $tot[3],
    'no5' => $nrline[4],
    'productname5'   => $prodname[4],
    'quantity5'   => $quantity[4],
    'unitprice5'   => $price[4],
    'code5' => $adb->query_result($result1,4,'codice_articolo'),
    'unit5' =>'',
    'amount5'   => $tot[4],
    'no6' => $nrline[5],
    'productname6'   => $prodname[5],
    'quantity6'   => $quantity[5],
    'unitprice6'   => $price[5],
    'code6' => $adb->query_result($result1,5,'codice_articolo'),
    'unit6' =>'',
    'amount6'   => $tot[5],
    'no7' => $nrline[6],
    'productname7'   => $prodname[6],
    'quantity7'   => $quantity[6],
    'unitprice7'   => $price[6],
    'code7' => $adb->query_result($result1,6,'codice_articolo'),
    'unit7' =>'',
    'amount7'   => $tot[6],
    'no8' => $nrline[7],
    'productname8'   => $prodname[7],
    'quantity8'   => $quantity[7],
    'unitprice8'   => $price[7],
    'code8' => $adb->query_result($result1,7,'codice_articolo'),
    'unit8' =>'',
    'amount8'   => $tot[7],
    'portofloading' =>$adb->query_result($result1,0,'pol'),
    'portofdischarge' =>$adb->query_result($result1,0,'pod'),
    'departuredate'=>$adb->query_result($result1,0,'dtvessel'),
    'vesselname'=>$adb->query_result($result1,0,'vslname'),
    'billoflading'=>$adb->query_result($result1,0,'cf_1310'),
    'sayinwords'=>'',
    'imponibile'   => formatMoney($totiv,true),
    'vat_amount'   => formatMoney($subtot,true),
    'valore'   => formatMoney($totam,true),
    'paymenttype' => $payment,
    'totimponibilevat' => '$ '.formatMoney($impiva,true),
    'ivatotale'  => '$ '.formatMoney($subtot,true),
    'nonimponibilevat' => '$ '.formatMoney($nonimpiva,true),
    'description'   => $prodname[0].'          '.$poref[0].'
'.$prodname[1].'          '.$poref[1].'
'.$prodname[2].'          '.$poref[2].'
'.$prodname[3].'          '.$poref[3].'
'.$prodname[4].'          '.$poref[4].'
'.$prodname[5].'          '.$poref[5].'
'.$prodname[6].'          '.$poref[6].'
'.$prodname[7].'          '.$poref[7],
    
    
    
);

$pdf = new FPDM('include/fpdm/opt_fattura.pdf');}
//$pdf = new FPDM('include/fpdm/opt_salesorder.pdf');

       
$pdf->Load($fields, true); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
$pdf->Merge();
//$file = 'storage/opt_levico08.pdf';
//    
//    if (file_exists($file)) unlink($file);
//          
  	$pdf->Output();
   //     $pdf->Output('storage/opt_fattura_levico.pdf','D');



// just require TCPDF instead of FPDF
//require_once('include/fpdi/tcpdf.php');
//require_once('include/fpdi/fpdi.php');
// 
//class PDF extends FPDI {
// /**
// * "Remembers" the template id of the imported page
// */
// var $_tplIdx;
// 
// /**
// * inclu * include a background template for every page
//de a bac * include a background template for every page
//kground template for every page
// */
// function Header() {
// if (is_null($this->_tplIdx)) {
// $this->setSourceFile('test.pdf');
// $this->_tplIdx = $this->importPage(1);
// }
// $this->useTemplate($this->_tplIdx);
// 
// $this->SetFont('freesans', 'B', 9);
// $this->SetTextColor(255);
// $this->SetXY(60.5, 24.8);
// $this->Cell(0, 8.6, "TCPDF and FPDI");
// }
// 
// function Footer() {}
//}
// 
//// initiate PDF
//$pdf = new PDF();
//$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
//$pdf->SetAutoPageBreak(true, 40);
//$pdf->setFontSubsetting(false);
// 
//// add a page
//$pdf->AddPage();
// 
//// get esternal file content
//$utf8text = file_get_contents("cache/utf8test.txt", true);
// 
//$pdf->SetFont("freeserif", "", 12);
//// now write some text above the imported page
//$pdf->Write(5, $utf8text);
// 
//$pdf->Output('newpdf.pdf', 'D');

?>
