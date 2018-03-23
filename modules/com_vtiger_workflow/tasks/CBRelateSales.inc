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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

class CBRelateSales extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('relpdo','relsrv','withaccvnd','withcto');
	}

	public function doTask(&$entity) {
		global $adb;
		$invinfo = array(
			'Quotes' => array('id'=>'quoteid','account'=>'accountid','contact'=>'contactid'),
			'SalesOrder' => array('id'=>'salesorderid','account'=>'accountid','contact'=>'contactid'),
			'Invoice' => array('id'=>'invoiceid','account'=>'accountid','contact'=>'contactid'),
			'PurchaseOrder' => array('id'=>'purchaseorderid','account'=>'vendorid','contact'=>'contactid'),
		);
		list($inv,$inv_id) = explode('x', $entity->getId());
		if (!empty($inv_id)) {
			$setype = getSalesEntityType($inv_id);
			$query = 'select productid from vtiger_inventoryproductrel inner join vtiger_crmentity on crmid=productid where deleted=0 and id=?';
			$srvs = $adb->pquery($query, array($inv_id));
			if ($srvs && $adb->num_rows($srvs) > 0) {
				if ($setype=='PurchaseOrder') {
					$acc = CRMEntity::getInstance('Vendors');
					$accrs = $adb->pquery('select vendorid from vtiger_purchaseorder where purchaseorderid=?', array($inv_id));
					$accid = $adb->query_result($accrs, 0, 0);
					$accmodule = 'Vendors';
				} else {
					$acc = CRMEntity::getInstance('Accounts');
					$accrs = $adb->pquery('select accountid from vtiger_'.strtolower($setype).' where '.$invinfo[$setype]['id'].'=?', array($inv_id));
					$accid = $adb->query_result($accrs, 0, 0);
					$accmodule = 'Accounts';
				}
				$cto = CRMEntity::getInstance('Contacts');
				$ctors = $adb->pquery('select contactid from vtiger_'.strtolower($setype).' where '.$invinfo[$setype]['id'].'=?', array($inv_id));
				$ctoid = $adb->query_result($ctors, 0, 0);
				while ($srv = $adb->fetch_array($srvs)) {
					$pdosrv = getSalesEntityType($srv['productid']);
					if (($pdosrv == 'Products' && $this->relpdo) || (($pdosrv == 'Services' && $this->relsrv))) {
						if ($this->withaccvnd && !empty($accid)) {
							relateEntities($acc, $accmodule, $accid, $pdosrv, $srv['productid']);
						}
						if ($this->withcto && !empty($ctoid)) {
							relateEntities($cto, 'Contacts', $ctoid, $pdosrv, $srv['productid']);
						}
					}
				}
			}
		}
	}
}
?>