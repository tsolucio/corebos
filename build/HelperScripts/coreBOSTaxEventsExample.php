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
 *  Module       : coreBOS Tax Events Example helper script
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
class coreBOSTaxEventsExample extends VTEventHandler {
	private $_moduleCache = array();

	/**
	 * @param $handlerType
	 * @param $entityData VTEntityData
	 */
	public function handleEvent($handlerType, $entityData) {
	}

	public function handleFilter($handlerType, $parameter) {
		global $currentModule;
		switch($handlerType) {
			case 'corebos.filter.TaxCalculation.getTaxDetailsForProduct':
				$tax_details = array();
				$tax_details[0]['productid'] = $parameter[0];
				$tax_details[0]['taxid'] = 22;
				$tax_details[0]['taxname'] = '22';
				$tax_details[0]['taxlabel'] = '22';
				$tax_details[0]['percentage'] = 0.22;
				$tax_details[0]['deleted'] = 0;
				$tax_details[1]['productid'] = $parameter[0];
				$tax_details[1]['taxid'] = 23;
				$tax_details[1]['taxname'] = '23';
				$tax_details[1]['taxlabel'] = '22';
				$tax_details[1]['percentage'] = 0.23;
				$tax_details[1]['deleted'] = 0;
				$parameter[2] = $tax_details;
				break;
			case 'corebos.filter.TaxCalculation.getProductTaxPercentage':
				break;
			case 'corebos.filter.TaxCalculation.getAllTaxes':
				break;
			case 'corebos.filter.TaxCalculation.getTaxPercentage':
				break;
			case 'corebos.filter.TaxCalculation.getTaxId':
				break;
		}
		return $parameter;
	}
}
