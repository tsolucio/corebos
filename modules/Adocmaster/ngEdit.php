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
 *  Module       : Adocmaster
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/


 require_once('modules/Adocmaster/Adocmaster.php');
 require_once('modules/Adocdetail/Adocdetail.php');

global $adb,$current_user;
$kaction=$_REQUEST['kaction'];
if($kaction=='retrieve1'){

$content=array();

require_once('Smarty_setup.php');
require_once("modules/Adocmaster/Adocmaster.php");
global $adb;

$id2 =$_REQUEST['record'];

require_once("modules/Adocmaster/calculateTariffPrice.php");



$adocquery=$adb->pquery("select vtiger_products.productid,vtiger_adocmaster.adocmasterid,vtiger_adocdetail.adocdtax,vtiger_adocdetail.adocdtotal,vtiger_adocdetail.adocdtotalamount,vtiger_adocdetail.adocdetailid,vtiger_adocdetail.adocdetailno,vtiger_adocdetail.adocdetailname,vtiger_adocdetail.adoc_product,vtiger_adocdetail.adoc_quantity,vtiger_adocdetail.adoc_price,vtiger_adocdetail.adoc_stock,vtiger_adocdetail.riferimento,vtiger_products.productname,vtiger_adocdetail.nrline from vtiger_adocdetail  join vtiger_crmentity on crmid=adocdetailid join vtiger_adocmaster on adocmasterid=adoctomaster left join vtiger_products on productid=adoc_product
    where deleted=0 and adocmasterid=?",array($id2));
$count=$adb->num_rows($adocquery);
for($i=0;$i<$count;$i++){
     $productid1=$adb->query_result($adocquery,$i,0);
     $query = 'select productname, vtiger_attachments.path,'
                                . ' vtiger_attachments.attachmentsid, '
                                . ' vtiger_attachments.name,vtiger_crmentity.setype '
                                . ' from vtiger_products '
                                . ' left join vtiger_seattachmentsrel '
                                . ' on vtiger_seattachmentsrel.crmid=vtiger_products.productid '
                                . ' inner join vtiger_attachments '
                                . ' on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid '
                                . ' inner join vtiger_crmentity '
                                . ' on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid '
                                . ' where vtiger_crmentity.setype="Products Image" and productid=?';
			$result_image = $adb->pquery($query, array($rowsz['adoc_product']));
                        $label_fld='';
                        //$image_array=array();$image_orgname_array=array();$imagepath_array=array();
			//for ($image_iter = 0; $image_iter < $adb->num_rows($result_image); $image_iter++) {
			if($adb->num_rows($result_image)>0){	
                        $image_id_array = $adb->query_result($result_image, '2', 'attachmentsid');

				//decode_html  - added to handle UTF-8   characters in file names
				//urlencode    - added to handle special characters like #, %, etc.,
				$image_array = urlencode(decode_html($adb->query_result($result_image, '2', 'name')));
				$image_orgname_array = decode_html($adb->query_result($result_image, '2', 'name'));

				$imagepath_array = $adb->query_result($result_image, '2', 'path');
			//}
			//if (count($image_array) > 1) {
				$label_fld =  $imagepath_array . $image_id_array . "_" . $image_array;

			//} elseif (count($image_array) == 1) {
			//	list($pro_image_width, $pro_image_height) = getimagesize($imagepath_array[0] . $image_id_array[0] . "_" . $image_orgname_array[0]);
			//	if ($pro_image_width > 450 || $pro_image_height > 300)
			//		$label_fld=  $imagepath_array[0] . $image_id_array[0] . "_" . $image_array[0] ;
			//				}else {
			//	$label_fld = '';
			//}
}
$adocid1=$adb->query_result($adocquery,$i,5);
$adocmasterid1=$adb->query_result($adocquery,$i,1);
$quantity1=$adb->query_result($adocquery,$i,9);

$foundRes2=calculatePrice('Adocdetail', $productid1, $adocmasterid1, $quantity1);
$foundRes3=explode("::",$foundRes2);
    $content[$i]['name']=$adb->query_result($adocquery,$i,6);
    $content[$i]['age']=$adb->query_result($adocquery,$i,14);
    $content[$i]['adoc_product_display']=$adb->query_result($adocquery,$i,13);
     $content[$i]['quantity']=$adb->query_result($adocquery,$i,9);
      $content[$i]['price']=$adb->query_result($adocquery,$i,10);
       $content[$i]['riferimento']=$adb->query_result($adocquery,$i,12);
       $content[$i]['adocdetailid']=$adb->query_result($adocquery,$i,5);
       $content[$i]['productid']=$adb->query_result($adocquery,$i,0);
       $content[$i]['stockid']=$adb->query_result($adocquery,$i,11);
        $content[$i]['adocid']=$adb->query_result($adocquery,$i,5);
        $content[$i]['adocdetailname']=$adb->query_result($adocquery,$i,7);
        $content[$i]['adocdtotal']=$adb->query_result($adocquery,$i,3);
         $content[$i]['adocdtax']=$adb->query_result($adocquery,$i,2);
          $content[$i]['adocdtotalamount']=$adb->query_result($adocquery,$i,4);
           $content[$i]['adocmasterid']=$adb->query_result($adocquery,$i,1);
           $content[$i]['precio']=$foundRes3[7];
           $content[$i]['total']=$foundRes3[2]*$adb->query_result($adocquery,$i,9);
           $content[$i]['newtax']=$foundRes3[3];
            $content[$i]['newadoctotal']=$foundRes3[4];
            $content[$i]['newadoctotalamount']=$foundRes3[5];
            $content[$i]['image']=$label_fld;
            $content[$i]['vat']=$foundRes3[8];
            $content[$i]['discount']=$foundRes3[6];
            $content[$i]['adoc_product']=$adb->query_result($adocquery,$i,8);
          
}
echo json_encode($content);

}

else{

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
   
    $quantity=$_REQUEST['quantity'];
    echo $quantity;
    $adocp=$_REQUEST['adocp'];
    echo $adocp;
    $focus=new Adocdetail();
  

   $adoc1=new Adocdetail();
  
   $adoc1->column_fields['adoc_quantity']=$quantity;
  $adoc1->column_fields['adoctomaster']=$laprueva;
  $adoc1->column_fields['assigned_user_id']=1;
  
   $adoc1->column_fields['adoc_product']=$adocp;
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
     
      
      

}
?>