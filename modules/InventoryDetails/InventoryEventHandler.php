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
		if ($handlerType == 'corebos.filter.inventory.getprice') {
			if (coreBOS_Settings::getSetting('KEY_DISCOUNT_MODULE_STATUS', '')) {
				global $adb;
				$search_in = (1 == GlobalVariable::getVariable('Application_B2C', '0')) ? 3 : 2;

				if ($parameter[5] == "--None--") {
					$query_string = 'SELECT *FROM vtiger_discountline INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = discountlineid INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid) WHERE deleted=0 AND (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=? OR vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)';
					$params = array($parameter[$search_in], $parameter[$search_in],
					$parameter[4], $parameter[4]);
				} else {
					$query_string = 'SELECT *FROM vtiger_discountline INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = discountlineid INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid) WHERE deleted=0 AND vtiger_discountline.dlcategory =? AND (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=? OR vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)';
					$params = array($parameter[5], $parameter[$search_in], $parameter[$search_in],
					$parameter[4], $parameter[4]);
				}

				$query_result = $adb->pquery($query_string, $params);
				if ($adb->num_rows($query_result) > 0) {
					if (!empty($adb->query_result($query_result, 0, "cbmapid"))) {
						$mapid = $adb->query_result($query_result, 0, "cbmapid");
						$context = array('recordid' => $inventory_line['productid']);
						$value = coreBOS_Rule::evaluate($mapid, $context);

						if ($adb->query_result($query_result, 0, "returnvalue") == 'Cost+Margin') {
							$result_cost_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($parameter[4]));
							$unitprice = $adb->query_result($result_cost_price, 0, "unit_price");
							$unitprice = $unitprice + (1 + $value);
						} elseif ($adb->query_result($query_result, 0, "returnvalue") == 'Unit+Discount') {
							$result_unit_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($parameter[4]));
							$unitprice = $adb->query_result($result_unit_price, 0, "unit_price");
							return array($unitprice , $value);
						}
					} else {
						$value = $adb->query_result($query_result, 0, "discount");
						if ($adb->query_result($query_result, 0, "returnvalue") == 'Cost+Margin') {
							$result_cost_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($parameter[4]));
							$unitprice = $adb->query_result($result_cost_price, 0, "unit_price");
							$unitprice = $unitprice + (1 + $value);
							return array($unitprice, 0);
						} elseif ($adb->query_result($query_result, 0, "returnvalue") == 'Unit+Discount') {
							$result_unit_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($parameter[4]));
							$unitprice = $adb->query_result($result_unit_price, 0, "unit_price");
							return array($unitprice , $value);
						}
					}
				} else {
					return array($parameter[0], $parameter[1]);
				}
			} else {
				return array($parameter[0], $parameter[1]);
			}
		}
	}
}
