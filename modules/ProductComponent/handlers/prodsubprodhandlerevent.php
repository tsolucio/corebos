<?php
class prodsubprodhandlerevent extends VTEventHandler {
	private function updateproductprice($id) {
		global $adb, $current_user;
		$qu = $adb->pquery("SELECT vtiger_productcomponent.frompdo,vtiger_productcomponent.productcomponentid 
		FROM vtiger_productcomponent where vtiger_productcomponent.productcomponentid=?", array($id));
		$v = $adb->query_result($qu, 0, 'frompdo');
		$query = $adb->pquery("SELECT sum(vtiger_productcomponent.quantity*vtiger_products.unit_price) AS total 
		FROM vtiger_productcomponent 
		INNER JOIN vtiger_products ON vtiger_productcomponent.topdo = vtiger_products.productid 
		INNER JOIN vtiger_crmentity AS c1 ON vtiger_productcomponent.productcomponentid = c1.crmid 
		INNER JOIN vtiger_crmentity AS c2 ON vtiger_products.productid= c2.crmid 
		WHERE c1.deleted = 0 AND c2.deleted = 0 AND vtiger_productcomponent.frompdo = ?", array($v));
		$res = $adb->query_result($query, 0, 'total');
		$query2 = $adb->pquery("update vtiger_products set unit_price=? where productid=?", array($res,$v));

		$prod_res = $adb->pquery("select unit_price, currency_id from vtiger_products where productid=?", array($v));
		$prod_unit_price = $adb->query_result($prod_res, 0, 'unit_price');
		$prod_base_currency = $adb->query_result($prod_res, 0, 'currency_id');

		$query3 = $adb->pquery("update vtiger_productcurrencyrel set actual_price=? where productid=? and 
		currencyid=?", array($prod_unit_price, $v, $prod_base_currency));
	}
	private function updatecostprice($id) {
		global $adb, $current_user;
		$qu2 = $adb->pquery("SELECT vtiger_productcomponent.frompdo,vtiger_productcomponent.productcomponentid 
		FROM vtiger_productcomponent where vtiger_productcomponent.productcomponentid=?", array($id));
		$v2 = $adb->query_result($qu2, 0, 'frompdo');
		$query_new = $adb->pquery("SELECT sum(vtiger_productcomponent.quantity*vtiger_products.cost_price) AS totalcost 
		FROM vtiger_productcomponent 
		INNER JOIN vtiger_products ON vtiger_productcomponent.topdo = vtiger_products.productid 
		INNER JOIN vtiger_crmentity AS c1 ON vtiger_productcomponent.productcomponentid = c1.crmid 
		INNER JOIN vtiger_crmentity AS c2 ON vtiger_products.productid= c2.crmid 
		WHERE c1.deleted = 0 AND c2.deleted = 0 AND vtiger_productcomponent.frompdo = ?", array($v2));
		$res2 = $adb->query_result($query_new, 0, 'totalcost');
		$query2 = $adb->pquery("update vtiger_products set cost_price=? where productid=?", array($res2,$v2));
	}
	public function handleEvent($eventName, $entityData) {
		global $adb, $current_user;
		if ($eventName == 'vtiger.entity.aftersave') {
			$moduleName = $entityData->getModuleName();
			$id = $entityData->getId();
			$Product_SubProduct_PriceRollUp = GlobalVariable::getVariable('Product_SubProduct_PriceRollUp', '', 'Products', '')==1;
			$Product_SubProduct_CostRollUp = GlobalVariable::getVariable('Product_SubProduct_CostRollUp', '', 'Products', '')==1;
			if ($moduleName == 'ProductComponent') {
				if ($Product_SubProduct_PriceRollUp) {
					$this->updateproductprice($id);
				}
				if ($Product_SubProduct_CostRollUp) {
					$this->updatecostprice($id);
				}
			}

			if ($moduleName == 'Products') {
				$r_check = $adb->pquery("SELECT vtiger_productcomponent.productcomponentid FROM vtiger_productcomponent WHERE topdo = ?", array($id));
				while ($row=$adb->fetch_array($r_check)) {
					if ($Product_SubProduct_PriceRollUp) {
						$this->updateproductprice($id);
					}
					if ($Product_SubProduct_CostRollUp) {
						$this->updatecostprice($id);
					}
				}
			}
		}
		if ($eventName == 'vtiger.entity.afterdelete') {
			$moduleName = $entityData->getModuleName();
			$id = $entityData->getId();
			if ($moduleName == 'ProductComponent') {
				$this->updateproductprice($id);

				$this->updatecostprice($id);
			}
		}
	}
}
?>