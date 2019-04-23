<?php
/*************************************************************************************************
 * Copyright 2018 TSolucio -- This file is a part of TSolucio coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. TSolucio reserves all rights not expressly
* granted by the License. coreBOS distributed by TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
class inventoryDetailsTaxBlockDValue extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$module = Vtiger_Module::getInstance('InventoryDetails');
			$cols = $adb->query("show columns from vtiger_inventorydetails like 'id_tax%_perc'");
			while ($tax=$adb->fetch_array($cols)) {
				$this->ExecuteQuery('ALTER TABLE vtiger_inventorydetails CHANGE '.$tax['field'].' '.$tax['field']." DECIMAL(7,3) NULL DEFAULT '0'");
				$taxf = Vtiger_Field::getInstance($tax['field'], $module);
				$this->ExecuteQuery('UPDATE vtiger_field set typeofdata=? WHERE fieldid=?', array('N~O', $taxf->id));
			}
			$this->markApplied();
		}
		$this->finishExecution();
	}
}