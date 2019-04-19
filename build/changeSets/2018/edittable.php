<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class edittable extends cbupdaterWorker {

	public function applyChange() {
		global $adb, $current_user;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$var=$adb->query("SELECT tabid FROM vtiger_tab WHERE vtiger_tab.name='ProductComponent'");
			$tid=$adb->query_result($var, 0, 'tabid');
			$sql='Update vtiger_relatedlists set related_tabid='.$tid
				.' where (vtiger_relatedlists.name="get_products" or vtiger_relatedlists.name="get_parent_products") and tabid=14';
			$adb->query($sql);

			include_once 'include/Webservices/Create.php';
			$var2=$adb->query("SELECT vtiger_seproductsrel.*
				FROM vtiger_seproductsrel
				INNER JOIN vtiger_crmentity AS c1 ON vtiger_seproductsrel.crmid = c1.crmid
				INNER JOIN vtiger_crmentity AS c2 ON vtiger_seproductsrel.productid = c2.crmid
				INNER JOIN vtiger_products ON vtiger_products.productid = vtiger_seproductsrel.crmid
				WHERE c1.deleted = 0 AND c2.deleted = 0 AND vtiger_seproductsrel.setype = 'Products'");
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$rec = array(
				'assigned_user_id' => $usrwsid,
				'relmode' => 'Required',
				'relfrom' => date('Y-m-d'),
				'relto' => '2030-01-01',
				'quantity' => '1',
				'instructions' => '',
			);
			$pdoWsId = vtws_getEntityId('Products').'x';
			while ($row=$adb->fetch_array($var2)) {
				$rec['frompdo'] = $pdoWsId.$row['productid'];
				$rec['topdo'] = $pdoWsId.$row['crmid'];
				if (isset($row['qty'])) {
					$rec['quantity'] = $row['qty'];
				}
				if (isset($row['quantity'])) {
					$rec['quantity'] = $row['quantity'];
				}
				vtws_create('ProductComponent', $rec, $current_user);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
?>