<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of coreBOS CRM Customizations.
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
*  Author	   : JPL TSolucio, S. L.
*************************************************************************************************/

$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';

function setVAT($entity) {
	global $log,$adb;
	if (is_object($entity)) {
		list($mod,$entid) = explode('x', $entity->data['id']);
		$mod = getSalesEntityType($entid);
	} else {
		list($mod,$entid) = explode('x', $entity);
	}
	if (!in_array($mod, getInventoryModules()) || !inventoryCanSaveProductLines($_REQUEST, $mod)) {
		return true;
	}
	$modules = array(
		'Invoice' => array(
			'table' => 'vtiger_invoice',
			'id' => 'invoiceid'
		),
		'SalesOrder' => array(
			'table' => 'vtiger_salesorder',
			'id' => 'salesorderid'
		),
		'Quotes' => array(
			'table' => 'vtiger_quotes',
			'id' => 'quoteid'
		),
		'PurchaseOrder' => array(
			'table' => 'vtiger_purchaseorder',
			'id' => 'purchaseorderid'
		)
	);
	if ($mod != 'PurchaseOrder') {
		if (GlobalVariable::getVariable('B2B', '1')=='1') {
			$acvid = $entity->data['account_id'];
		} else {
			$acvid = $entity->data['contact_id'];
		}
	} else {
		$acvid = $entity->data['vendor_id'];
	}
	$ipr_cols = $adb->getColumnNames('vtiger_inventoryproductrel');
	$taxsum=$taxinfo=array();
	$taxsum['taxtotal']=0;
	$taxinfo['taxtotal']=array('label'=>'Tax Total','field'=>'sum_taxtotal');
	$taxinfo['taxtotalret']=array('label'=>'Total Tax Retention', 'field'=>'sum_taxtotalretention');
	$ent = $adb->pquery(
		"select subtotal,discount_percent,discount_amount,adjustment,total,taxtype,s_h_amount from {$modules[$mod]['table']} where {$modules[$mod]['id']}=?",
		array($entid)
	);
	$taxinfo['pl_grand_total'] = array('label'=>'Grand Total', 'field' => 'pl_grand_total');
	$taxsum['pl_grand_total'] = $adb->query_result($ent, 0, 'total');
	$taxinfo['pl_adjustment'] = array('label'=>'Final Adjustment', 'field' => 'pl_adjustment');
	$taxsum['pl_adjustment'] = $adb->query_result($ent, 0, 'adjustment');
	$netTotal = $adb->query_result($ent, 0, 'subtotal');
	$hdnDiscountPercent = $adb->query_result($ent, 0, 'discount_percent');
	$hdnDiscountAmount = $adb->query_result($ent, 0, 'discount_amount');
	$finalDiscount = 0;
	if ($hdnDiscountPercent != 0) {
		$finalDiscount = $hdnDiscountAmount = ($netTotal*$hdnDiscountPercent/100);
	} elseif ($hdnDiscountAmount != 0) {
		$finalDiscount = $hdnDiscountAmount;
	}
	$taxinfo['pl_dto_global'] = array('label'=>'Global Discount', 'field' => 'pl_dto_global');
	$taxsum['pl_dto_global'] = (is_null($hdnDiscountAmount) == true ? 0 : $hdnDiscountAmount);
	$taxinfo['pl_sh_total'] = array('label'=>'SH Total', 'field' => 'pl_sh_total');
	$taxsum['pl_sh_total'] = $adb->query_result($ent, 0, 's_h_amount');
	$taxinfo['pl_sh_tax'] = array('label'=>'SH Tax', 'field' => 'pl_sh_tax');
	$taxsum['pl_sh_tax'] = 0;
	foreach (getAllTaxes('available', 'sh', 'edit', $entid) as $taxItem) {
		$shtax_percent = getInventorySHTaxPercent($entid, $taxItem['taxname']);
		$taxsum['pl_sh_tax'] = $taxsum['pl_sh_tax'] + ($taxsum['pl_sh_total'] * $shtax_percent / 100);
	}
	$query = 'SELECT sum(quantity * listprice) AS extgross,
		sum(COALESCE( discount_amount, COALESCE( discount_percent * quantity * listprice /100, 0 ) )) AS dtoamount
		FROM vtiger_inventoryproductrel
		WHERE id = ?';
	$igrs = $adb->pquery($query, array($entid));
	$taxinfo['pl_gross_total'] = array('label'=>'Gross Total', 'field' => 'pl_gross_total');
	$taxsum['pl_gross_total'] = $adb->query_result($igrs, 0, 'extgross');
	$taxinfo['pl_dto_line'] = array('label'=>'Line Discount', 'field' => 'pl_dto_line');
	$taxsum['pl_dto_line'] = $adb->query_result($igrs, 0, 'dtoamount');
	$taxinfo['pl_dto_total'] = array('label'=>'Total Discount', 'field' => 'pl_dto_total');
	$taxsum['pl_dto_total'] = $taxsum['pl_dto_line']+$hdnDiscountAmount;
	$taxinfo['pl_net_total'] = array('label'=>'Net Total (aGD)', 'field' => 'pl_net_total');
	$taxsum['pl_net_total'] = $taxsum['pl_gross_total']-$taxsum['pl_dto_total'];

	$result = $adb->pquery('select * from vtiger_inventoryproductrel where id=?', array($entid));
	$num_rows=$adb->num_rows($result);
	$taxtotal = $taxtotalret = 0;
	$taxtype = $adb->query_result($ent, 0, 'taxtype');
	if ($taxtype == 'group') {
		$final_totalAfterDiscount = $netTotal - $finalDiscount;
		$tax_details = getAllTaxes('available', '', 'edit', $entid);
		foreach ($tax_details as $taxItem) {
			$tax_name = $taxItem['taxname'];
			if (in_array($tax_name, $ipr_cols)) {
				$tax_value = $adb->query_result($result, 0, $tax_name);
			} else {
				$tax_value = $taxItem['percentage'];
			}
			if ($tax_value == '' || $tax_value == 'NULL') {
				$tax_value = 0;
			}
			$taxamount = $final_totalAfterDiscount*$tax_value/100;
			if ($taxItem['retention']) {
				$taxtotalret = $taxtotalret + $taxamount;
			} else {
				$taxtotal = $taxtotal + $taxamount;
			}
			if (!isset($taxsum[$tax_name])) {
				$taxsum[$tax_name] = 0;
			}
			$taxsum[$tax_name]+=$taxamount;
			if (!isset($taxinfo[$tax_name])) {
				$fname = preg_replace("/[^a-zA-Z0-9\s]/", '', $tax_name);
				$fname = preg_replace('/\s/', '_', $fname);
				$fname = strtolower($fname);
				$taxinfo[$tax_name]=array('label'=>$taxItem['taxlabel'], 'field'=>'sum_'.$fname);
			}
		}
		if (!isset($taxsum['nettotal'])) {
			$taxsum['nettotal'] = 0;
		}
		$taxsum['nettotal'] = $netTotal;
	}
	if ($taxtype == 'individual') {
		for ($i=1; $i<=$num_rows; $i++) {
			$discount_percent=$adb->query_result($result, $i-1, 'discount_percent');
			$discount_amount=$adb->query_result($result, $i-1, 'discount_amount');
			$productid=$adb->query_result($result, $i-1, 'productid');
			$qty=$adb->query_result($result, $i-1, 'quantity');
			$listprice=$adb->query_result($result, $i-1, 'listprice');
			$total = $qty*$listprice;
			$totalAfterDiscount=$total;
			$productDiscount = 0;
			if ($discount_percent != 'NULL' && $discount_percent != '') {
				$productDiscount = $total*$discount_percent/100;
				$totalAfterDiscount = $total-$productDiscount;
			} elseif ($discount_amount != 'NULL' && $discount_amount != '') {
				$productDiscount = $discount_amount;
				$totalAfterDiscount = $total-$productDiscount;
			}
			$tax_details = getAllTaxes('available', '', 'edit', $entid);
			foreach ($tax_details as $taxItem) {
				$tax_name = $taxItem['taxname'];
				$tax_value = getInventoryProductTaxValue($entid, $productid, $tax_name);
				$individual_taxamount = $totalAfterDiscount*$tax_value/100;
				if ($taxItem['retention']) {
					$taxtotalret = $taxtotalret + $individual_taxamount;
				} else {
					$taxtotal = $taxtotal + $individual_taxamount;
				}
				if (!isset($taxsum[$tax_name])) {
					$taxsum[$tax_name] = 0;
				}
				$taxsum[$tax_name]+=$individual_taxamount;
				if (!isset($taxinfo[$tax_name])) {
					$fname = preg_replace("/[^a-zA-Z0-9\s]/", '', $tax_name);
					$fname = preg_replace('/\s/', '_', $fname);
					$fname = strtolower($fname);
					$taxinfo[$tax_name]=array('label'=>$taxItem['taxlabel'],'field'=>'sum_'.$fname);
				}
			}
			if (!isset($taxsum['nettotal'])) {
				$taxsum['nettotal'] = 0;
			}
			$taxsum['nettotal'] += $total;
		}
	}
	$taxinfo['nettotal']=array('label'=>'Net Total (bGD)', 'field'=>'sum_nettotal');
	$taxsum['taxtotal']=$taxtotal;
	$taxsum['taxtotalret']=$taxtotalret;
	$inv_cols = $adb->getColumnNames($modules[$mod]['table']);
	$upd = "update {$modules[$mod]['table']} set ";
	foreach ($taxsum as $idx => $val) {
		if (!in_array($taxinfo[$idx]['field'], $inv_cols)) {
			$mod_ent = VTiger_Module::getInstance($mod);
			$block_ent = VTiger_Block::getInstance('LBL_'.$mod.'_FINANCIALINFO', $mod_ent);
			$field1 = new Vtiger_Field();
			$field1->name = $taxinfo[$idx]['field'];
			$field1->label= $taxinfo[$idx]['label'];
			$field1->column = $taxinfo[$idx]['field'];
			$field1->columntype = 'DECIMAL(25,6)';
			$field1->uitype = 7;
			$field1->typeofdata = 'NN~O';
			$field1->displaytype = 2;
			$field1->presence = 0;
			$block_ent->addField($field1);
		}
		$upd.=$taxinfo[$idx]['field'].'=?,';
	}
	$upd=trim($upd, ',');
	$upd.=" where {$modules[$mod]['id']}=".$entid;
	$adb->pquery($upd, $taxsum);
}
?>