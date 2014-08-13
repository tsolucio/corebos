<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

// Product line support
$taxtype=$element['taxtype'];
if (empty($taxtype)) $taxtype = 'group'; // Individual
$_REQUEST['taxtype']=$taxtype;
$subtotal = 0;
$totalwithtax = 0;
$i = 0;
$pdoInformation=$element['pdoInformation'];
foreach ($pdoInformation as $pdoline) {
	$i++;
	$_REQUEST['deleted'.$i]=0;
	$_REQUEST['comment'.$i]=$pdoline['comment'];
	$_REQUEST['hdnProductId'.$i]=$pdoline['productid'];
	$qty=$pdoline['qty'];
	$_REQUEST['qty'.$i]=$qty;
	$setype=getSalesEntityType($pdoline['productid']);
	$_REQUEST['listPrice'.$i]=$pdoline['listprice']; // getUnitPrice($pdoline['productid'],$setype);
	$discount=0;
	if (!empty($pdoline['discount'])) {
		$_REQUEST["discount$i"]="on";
		$_REQUEST["discount_type$i"]=$pdoline['discount_type'];
		if ($pdoline['discount_type']=='amount') {
			$_REQUEST["discount_amount$i"]=$pdoline['discount_amount'];
			$discount=$pdoline['discount_amount'];
		} else {
			$_REQUEST["discount_percentage$i"]=$pdoline['discount_percentage'];
			$discount=($qty * $_REQUEST['listPrice'.$i])*$pdoline['discount_percentage']/100;
		}
	}
	$subtotal = $subtotal + ($qty * $_REQUEST['listPrice'.$i]) - $discount;
	if($taxtype == "individual") {
		$taxes_for_product = getTaxDetailsForProduct($pdoline['productid'],'all');
		for($tax_count=0;$tax_count<count($taxes_for_product);$tax_count++) {
			$tax_name = $taxes_for_product[$tax_count]['taxname'];
			$tax_val = $taxes_for_product[$tax_count]['percentage'];
			$request_tax_name = $tax_name."_percentage".$i;
			$_REQUEST[$request_tax_name] = $tax_val;
			$totalwithtax += ($qty * $_REQUEST['listPrice'.$i]) * ($tax_val/100);
		}
	}
}
$_REQUEST['totalProductCount']=$i;
$_REQUEST['subtotal']=round($subtotal + $totalwithtax,2);
if($taxtype == "individual") {
	$totaldoc=$subtotal+$totalwithtax;
	if ($element['discount_type_final']=='amount') {
		$totaldoc=$totaldoc-$element['hdnDiscountAmount'];
	} elseif ($element['discount_type_final']=='percentage') {
		$totaldoc=$totaldoc-($totaldoc*$element['hdnDiscountPercent']/100);
	}
} else {
	$totaldoc=$subtotal;
	if ($element['discount_type_final']=='amount') {
		$totaldoc=$totaldoc-$element['hdnDiscountAmount'];
	} elseif ($element['discount_type_final']=='percentage') {
		$totaldoc=$totaldoc-($totaldoc*$element['hdnDiscountPercent']/100);
	}
	$all_available_taxes = getAllTaxes('available','');
	for($tax_count=0;$tax_count<count($all_available_taxes);$tax_count++) {
		$tax_val += $all_available_taxes[$tax_count]['percentage'];
	}
	$totaldoc=$totaldoc+($totaldoc*$tax_val/100);
}
if (!empty($element['shipping_handling_charge'])) {
$_REQUEST['shipping_handling_charge']=$element['shipping_handling_charge'];
	$totaldoc=$totaldoc+$element['shipping_handling_charge'];
	$shtaxes=$adb->query('select taxname from vtiger_shippingtaxinfo where deleted=0');
	while ($sht=$adb->fetch_array($shtaxes)) {
		$shname=$sht['taxname'];
		if (!empty($element[$shname])) {
			$_REQUEST[$shname.'_sh_percent'] = $element[$shname];
			$totaldoc=$totaldoc+($element['shipping_handling_charge']*$element[$shname]/100);
		}
	}
}
if ($element['adjustmentType']=='add') {
	$totaldoc=$totaldoc+$element['adjustment'];
	$_REQUEST['adjustment']=$element['adjustment'];
} elseif ($element['adjustmentType']=='deduct') {
	$totaldoc=$totaldoc-$element['adjustment'];
	$_REQUEST['adjustment']=$element['adjustment'];
}
$_REQUEST['total']=round($totaldoc,2);
?>