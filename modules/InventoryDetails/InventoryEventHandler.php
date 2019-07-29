<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class InventoryEventHandler extends VTEventHandler {

	public function handleEvent($handlerType, $entityData) {
	}

	public function handleFilter($handlerType, $parameter) {
		global $currentModule, $adb;

		// I will get the Product id from line Item
		$query = "SELECT vtiger_products.unit_price FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid = 2633";
		// I will get the Dicount for the Product
		$query = "SELECT vtiger_discountline.discount FROM vtiger_discountline WHERE vtiger_discountline.discountlineid =(
			SELECT vtiger_crmentityrel.crmid FROM vtiger_crmentityrel WHERE vtiger_crmentityrel.module = 'DiscountLine' AND
			vtiger_crmentityrel.relmodule = 'Accounts' AND vtiger_crmentityrel.relcrmid = 'accountid' AND vtiger_crmentityrel.crmid =(
			SELECT vtiger_crmentityrel.crmid FROM vtiger_crmentityrel WHERE vtiger_crmentityrel.module = 'DiscountLine' AND 
			vtiger_crmentityrel.relmodule = 'Products' AND vtiger_crmentityrel.relcrmid = 'productid'))";
		$parameter['price'] = 0.0; // found in Products Module
		$parameter['discount'] = 0.0; //  Discount Line Module
		return $parameter;
	}
}
