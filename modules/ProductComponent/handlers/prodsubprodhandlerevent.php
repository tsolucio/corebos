<?php
/*************************************************************************************************
 * Copyright 2018 TSolucio -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : coreBOS Product Component
 *************************************************************************************************/

class prodsubprodhandlerevent extends VTEventHandler {

	private function updateproductprice($id, $isproduct = false) {
		global $adb;
		if ($isproduct) {
			$v = $id;
		} else {
			$qu = $adb->pquery('SELECT vtiger_productcomponent.frompdo FROM vtiger_productcomponent where vtiger_productcomponent.productcomponentid=?', array($id));
			$v = $adb->query_result($qu, 0, 'frompdo');
		}
		$query = $adb->pquery(
			'SELECT sum(vtiger_productcomponent.quantity*vtiger_products.unit_price) AS total
				FROM vtiger_productcomponent
				INNER JOIN vtiger_products ON vtiger_productcomponent.topdo = vtiger_products.productid
				INNER JOIN vtiger_crmentity AS c1 ON vtiger_productcomponent.productcomponentid = c1.crmid
				INNER JOIN vtiger_crmentity AS c2 ON vtiger_products.productid= c2.crmid
				WHERE c1.deleted = 0 AND c2.deleted = 0 AND vtiger_productcomponent.frompdo = ?',
			array($v)
		);
		$res = $adb->query_result($query, 0, 'total');
		$adb->pquery('update vtiger_products set unit_price=? where productid=?', array($res, $v));

		$prod_res = $adb->pquery('select unit_price, currency_id from vtiger_products where productid=?', array($v));
		$prod_unit_price = $adb->query_result($prod_res, 0, 'unit_price');
		$prod_base_currency = $adb->query_result($prod_res, 0, 'currency_id');

		$adb->pquery('update vtiger_productcurrencyrel set actual_price=? where productid=? and currencyid=?', array($prod_unit_price, $v, $prod_base_currency));
	}

	private function updatecostprice($id, $isproduct = false) {
		global $adb;
		if ($isproduct) {
			$v = $id;
		} else {
			$qu = $adb->pquery('SELECT vtiger_productcomponent.frompdo FROM vtiger_productcomponent where vtiger_productcomponent.productcomponentid=?', array($id));
			$v = $adb->query_result($qu, 0, 'frompdo');
		}
		$query_new = $adb->pquery(
			'SELECT sum(vtiger_productcomponent.quantity*vtiger_products.cost_price) AS totalcost
				FROM vtiger_productcomponent
				INNER JOIN vtiger_products ON vtiger_productcomponent.topdo = vtiger_products.productid
				INNER JOIN vtiger_crmentity AS c1 ON vtiger_productcomponent.productcomponentid = c1.crmid
				INNER JOIN vtiger_crmentity AS c2 ON vtiger_products.productid= c2.crmid
				WHERE c1.deleted = 0 AND c2.deleted = 0 AND vtiger_productcomponent.frompdo = ?',
			array($v)
		);
		$res2 = $adb->query_result($query_new, 0, 'totalcost');
		$adb->pquery('update vtiger_products set cost_price=? where productid=?', array($res2, $v));
	}

	public function handleEvent($eventName, $entityData) {
		global $adb;
		if ($eventName == 'vtiger.entity.aftersave' || $eventName == 'vtiger.entity.afterdelete') {
			$moduleName = $entityData->getModuleName();
			$id = $entityData->getId();
			$entityDelta = new VTEntityDelta();
			if ($moduleName == 'ProductComponent') {
				$Product_SubProduct_PriceRollUp = GlobalVariable::getVariable('Product_SubProduct_PriceRollUp', '', 'Products', '')=='1';
				$Product_SubProduct_CostRollUp = GlobalVariable::getVariable('Product_SubProduct_CostRollUp', '', 'Products', '')=='1';
				if ($entityDelta->hasChanged($moduleName, $id, 'frompdo')) {
					$oldFromPdo = $entityDelta->getOldEntityValue($moduleName, $id, 'frompdo');
					if ($Product_SubProduct_PriceRollUp) {
						$this->updateproductprice($oldFromPdo, true);
						$this->updateproductprice($id);
					}
					if ($Product_SubProduct_CostRollUp) {
						$this->updatecostprice($oldFromPdo, true);
						$this->updatecostprice($id);
					}
				}
				if ($entityDelta->hasChanged($moduleName, $id, 'quantity') || $entityDelta->hasChanged($moduleName, $id, 'topdo')) {
					if ($Product_SubProduct_PriceRollUp) {
						$this->updateproductprice($id);
					}
					if ($Product_SubProduct_CostRollUp) {
						$this->updatecostprice($id);
					}
				}
			}

			if ($moduleName == 'Products' && ($entityDelta->hasChanged($moduleName, $id, 'cost_price') || $entityDelta->hasChanged($moduleName, $id, 'unit_price'))) {
				$Product_SubProduct_PriceRollUp = GlobalVariable::getVariable('Product_SubProduct_PriceRollUp', '', 'Products', '')=='1';
				$Product_SubProduct_CostRollUp = GlobalVariable::getVariable('Product_SubProduct_CostRollUp', '', 'Products', '')=='1';
				$r_check = $adb->pquery('SELECT vtiger_productcomponent.productcomponentid FROM vtiger_productcomponent WHERE topdo = ?', array($id));
				while ($row=$adb->fetch_array($r_check)) {
					if ($Product_SubProduct_PriceRollUp) {
						$this->updateproductprice($row['productcomponentid']);
					}
					if ($Product_SubProduct_CostRollUp) {
						$this->updatecostprice($row['productcomponentid']);
					}
				}
			}
		}
	}
}
?>