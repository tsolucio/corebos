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
if ($elementType != 'PurchaseOrder') {
	if (GlobalVariable::getVariable('Application_B2B', '1')=='1') {
		$acvid = isset($element['account_id']) ? $element['account_id'] : 0;
	} else {
		$acvid = isset($element['contact_id']) ? $element['contact_id'] : 0;
	}
} else {
	$acvid = $element['vendor_id'];
}
if (empty($element['taxtype'])) {
	if (empty($element['hdnTaxType'])) {
		$taxtype = 'group'; // Individual
	} else {
		$taxtype = $element['hdnTaxType'];
	}
} else {
	$taxtype=$element['taxtype'];
}
$_REQUEST['taxtype']=$taxtype;
$subtotal = 0;
$totalwithtax = 0;
$i = 0;
$pdoInformation=$element['pdoInformation'];
$skipCurDBConv = !empty($element['__cbws_skipcurdbconv_pdo']);
$lineitemAlreadyUsed = array();
foreach ($pdoInformation as $pdoline) {
	$i++;
	$_REQUEST['deleted'.$i]=(isset($pdoline['deleted']) ? $pdoline['deleted'] : 0);
	$_REQUEST['comment'.$i]=(isset($pdoline['comment']) ? $pdoline['comment'] : '');
	if (strpos($pdoline['productid'], 'x')>0) { // product is in webservice ID format
		list($void,$pdoline['productid']) = explode('x', $pdoline['productid']);
	}
	$_REQUEST['hdnProductId'.$i]=$pdoline['productid'];
	if (!empty($elementCRMID)) {
		$resIPR = $adb->pquery('select lineitem_id from vtiger_inventoryproductrel where id=? and productid=?', array($elementCRMID, $pdoline['productid']));
		$found=false;
		while (!$found && $IPRrow = $adb->fetch_array($resIPR)) {
			if (!in_array($IPRrow['lineitem_id'], $lineitemAlreadyUsed)) {
				$_REQUEST['lineitem_id'.$i] = intval($IPRrow['lineitem_id']);
				$lineitemAlreadyUsed[] = intval($IPRrow['lineitem_id']);
				$found = true;
			}
		}
	}
	$qty=$pdoline['qty'];
	$_REQUEST['qty'.$i]=$qty;
	$setype=getSalesEntityType($pdoline['productid']);
	$_REQUEST['listPrice'.$i] = $skipCurDBConv ? $pdoline['listprice'] : CurrencyField::convertToDBFormat($pdoline['listprice']);
	$discount=0;
	if (!empty($pdoline['discount'])) {
		$_REQUEST["discount$i"]='on';
		$_REQUEST["discount_type$i"]=$pdoline['discount_type'];
		if ($pdoline['discount_type']=='amount') {
			$_REQUEST["discount_amount$i"] = $skipCurDBConv ? $pdoline['discount_amount'] : CurrencyField::convertToDBFormat($pdoline['discount_amount']);
			$discount=$pdoline['discount_amount'];
		} else {
			$_REQUEST["discount_percentage$i"]=$pdoline['discount_percentage'];
			$discount=($qty * $_REQUEST['listPrice'.$i])*$pdoline['discount_percentage']/100;
		}
	}
	$subtotal = $subtotal + ($qty * $_REQUEST['listPrice'.$i]) - $discount;
	if ($taxtype == 'individual') {
		foreach (getTaxDetailsForProduct($pdoline['productid'], 'all', $acvid) as $productTax) {
			$tax_name = $productTax['taxname'];
			$tax_val = $productTax['percentage'];
			$request_tax_name = $tax_name.'_percentage'.$i;
			$_REQUEST[$request_tax_name] = $tax_val;
			$totalwithtax += ($qty * $_REQUEST['listPrice'.$i]) * ($tax_val/100);
		}
	}
	$cbMap = cbMap::getMapByName($elementType.'InventoryDetails', 'MasterDetailLayout');
	if ($cbMap!=null) {
		$cbMapFields = $cbMap->MasterDetailLayout();
		foreach ($cbMapFields['detailview']['fieldnames'] as $mdfield) {
			if (isset($pdoline[$mdfield]) && !is_null($pdoline[$mdfield])) {
				$_REQUEST[$mdfield.$i] = $pdoline[$mdfield];
			}
		}
	}
}
$_REQUEST['totalProductCount']=$i;
$_REQUEST['subtotal']=round($subtotal + $totalwithtax, 2);
if ($taxtype == 'individual') {
	$totaldoc=$subtotal+$totalwithtax;
	if (!empty($element['discount_type_final']) && $element['discount_type_final']=='amount') {
		$totaldoc=$totaldoc-$element['hdnDiscountAmount'];
	} elseif (!empty($element['discount_type_final']) && $element['discount_type_final']=='percentage') {
		$totaldoc=$totaldoc-($totaldoc*$element['hdnDiscountPercent']/100);
	}
} else {
	$totaldoc=$subtotal;
	if (!empty($element['discount_type_final']) && $element['discount_type_final']=='amount') {
		$totaldoc=$totaldoc-$element['hdnDiscountAmount'];
	} elseif (!empty($element['discount_type_final']) && $element['discount_type_final']=='percentage') {
		$totaldoc=$totaldoc-($totaldoc*$element['hdnDiscountPercent']/100);
	}
	$tax_val = 0;
	foreach (getAllTaxes('available', '') as $availableTax) {
		$keytax = 'tax'.$availableTax['taxid'].'_group_percentage';
		if (array_key_exists($keytax, $element)) {
			$tax_val += $element[$keytax];
			$_REQUEST[$keytax] = $element[$keytax];
		} else {
			$tax_val += $availableTax['percentage'];
		}
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
if (!empty($element['adjustmentType']) && $element['adjustmentType']=='add') {
	$totaldoc=$totaldoc+$element['adjustment'];
	$_REQUEST['adjustment']=$element['adjustment'];
} elseif (!empty($element['adjustmentType']) && $element['adjustmentType']=='deduct') {
	$totaldoc=$totaldoc-$element['adjustment'];
	$_REQUEST['adjustment']=$element['adjustment'];
}
$_REQUEST['total']=round($totaldoc, 2);
$_REQUEST['action']='Save';
?>
