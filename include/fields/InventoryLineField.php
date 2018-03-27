<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : InventoryLineField
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'include/utils/InventoryUtils.php';
class InventoryLineField {

	private static $ILFieldsName = array(
		'productid' => array(
			'uitype' => 10,
			'fieldtype' => 'reference',
			'fieldname' => 'productid',
			'columnname' => 'productid',
			'fieldlabel' => 'Item Name',
			'tablename' => 'vtiger_inventoryproductrel',
			'typeofdata'=>'I~M',
			'mandatory'=>'true',
		),
		'quantity' => array(
			'uitype' => 7,
			'fieldtype' => 'double',
			'fieldname' => 'quantity',
			'columnname' => 'quantity',
			'fieldlabel' => 'Quantity',
			'tablename' => 'vtiger_inventoryproductrel',
			'typeofdata'=>'N~M',
			'mandatory'=>'true',
		),
		'listprice' => array(
			'uitype' => 71,
			'fieldtype' => 'currency',
			'fieldname' => 'listprice',
			'columnname' => 'listprice',
			'fieldlabel' => 'List Price',
			'tablename' => 'vtiger_inventoryproductrel',
			'typeofdata'=>'N~M',
			'mandatory'=>'true',
		),
		'comment' => array(
			'uitype' => 19,
			'fieldtype' => 'text',
			'fieldname' => 'comment',
			'columnname' => 'comment',
			'fieldlabel' => 'Item Comment',
			'tablename' => 'vtiger_inventoryproductrel',
			'typeofdata'=>'V~O',
			'mandatory'=>'false',
		),
		'discount_amount' => array(
			'uitype' => 71,
			'fieldtype' => 'currency',
			'fieldname' => 'discount_amount',
			'columnname' => 'discount_amount',
			'fieldlabel' => 'Item Discount Amount',
			'tablename' => 'vtiger_inventoryproductrel',
			'typeofdata'=>'N~O',
			'mandatory'=>'false',
		),
		'discount_percent' => array(
			'uitype' => 7,
			'fieldtype' => 'double',
			'fieldname' => 'discount_percent',
			'columnname' => 'discount_percent',
			'fieldlabel' => 'Item Discount Percent',
			'tablename' => 'vtiger_inventoryproductrel',
			'typeofdata'=>'N~O',
			'mandatory'=>'false',
		),
	);
	private static $ILFieldsLabel = array();
	private static $ILProductServiceNameFields = array(
		'productname' => array(
			'uitype' => 2,
			'module' => 'Products',
			'fieldtype' => 'string',
			'fieldname' => 'productname',
			'columnname' => 'productname',
			'fieldlabel' => 'Product Name',
			'tablename' => 'vtiger_products',
			'typeofdata'=>'V~M',
			'mandatory'=>'true',
			'presence' => 0,
		),
		'servicename' => array(
			'uitype' => 2,
			'module' => 'Services',
			'fieldtype' => 'string',
			'fieldname' => 'servicename',
			'columnname' => 'servicename',
			'fieldlabel' => 'Service Name',
			'tablename' => 'servicename',
			'typeofdata'=>'V~M',
			'mandatory'=>'true',
			'presence' => 0,
		),
	);
	public $fieldname;

	public function __construct($fieldname = '') {
		InventoryLineField::$ILFieldsLabel = array(
			'item name' => InventoryLineField::$ILFieldsName['productid'],
			'quantity' => InventoryLineField::$ILFieldsName['quantity'],
			'list price' => InventoryLineField::$ILFieldsName['listprice'],
			'item comment' => InventoryLineField::$ILFieldsName['comment'],
			'item discount amount' => InventoryLineField::$ILFieldsName['discount_amount'],
			'item discount percent' => InventoryLineField::$ILFieldsName['discount_percent'],
		);
		$taxes = getAllTaxes('all');
		foreach ($taxes as $tax) {
			$fieldlabel = strtolower($tax['taxlabel']);
			InventoryLineField::$ILFieldsLabel[$fieldlabel] = array(
				'uitype' => 7,
				'fieldtype' => 'double',
				'fieldname' => $tax['taxname'],
				'columnname' => $tax['taxname'],
				'fieldlabel' => $tax['taxlabel'],
				'tablename' => 'vtiger_inventoryproductrel',
				'typeofdata'=>'N~O',
				'mandatory'=>'false',
			);
			InventoryLineField::$ILFieldsName[$tax['taxname']] = InventoryLineField::$ILFieldsLabel[$fieldlabel];
		}
		$this->fieldname = $fieldname;
	}

	public function getInventoryLineFieldsByLabel() {
		return InventoryLineField::$ILFieldsLabel;
	}

	public function getInventoryLineFieldsByName() {
		return InventoryLineField::$ILFieldsName;
	}

	public function getInventoryLineProductServiceNameFields() {
		return InventoryLineField::$ILProductServiceNameFields;
	}

	public function getInventoryLineFieldsByObject() {
		$ilonjs = array();
		foreach (InventoryLineField::$ILFieldsName as $fname => $fdesc) {
			$ilonjs[$fname] = new InventoryLineField($fname);
		}
		return $ilonjs;
	}

	public function getFieldLabelKey() {
		return InventoryLineField::$ILFieldsName[$this->fieldname]['fieldlabel'];
	}

	public function isMandatory() {
		return InventoryLineField::$ILFieldsName[$this->fieldname]['mandatory'];
	}

	public function getFieldDataType() {
		return InventoryLineField::$ILFieldsName[$this->fieldname]['fieldtype'];
	}

	public function getUIType() {
		return InventoryLineField::$ILFieldsName[$this->fieldname]['uitype'];
	}

	public function getReferenceList() {
		return array('Products','Services');
	}

	public function getTableName() {
		return InventoryLineField::$ILFieldsName[$this->fieldname]['tablename'];
	}
}
?>