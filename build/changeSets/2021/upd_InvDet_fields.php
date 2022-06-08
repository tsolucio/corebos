<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class upd_InvDet_fields extends cbupdaterWorker {

	public function applyChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$fields = array('quantity', 'listprice', 'extgross', 'discount_amount', 'extnet', 'linetax', 'linetotal', 'units_delivered_received', 'cost_price', 'cost_gross', 'total_stock', 'remaining_units');
			foreach ($fields as $fieldname) {
				$fld = Vtiger_Field::getInstance($fieldname, $mod);
				if ($fld) {
					$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='NN~O' WHERE fieldid=?", array($fld->id));
				}
			}

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(); // this should not be done if changeset is Continuous
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		} else {
			if ($this->isApplied()) {
				$mod = Vtiger_Module::getInstance('InventoryDetails');
				$fields = array('quantity', 'listprice', 'extgross', 'discount_amount', 'extnet', 'linetax', 'linetotal', 'units_delivered_received', 'cost_price', 'cost_gross', 'total_stock');
				foreach ($fields as $fieldname) {
					$fld = Vtiger_Field::getInstance($fieldname, $mod);
					if ($fld) {
						$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldid=?", array($fld->id));
					}
				}
				$fieldsdec = array('discount_percent', 'tax_percent');
				foreach ($fieldsdec as $fieldname) {
					$fld = Vtiger_Field::getInstance($fieldname, $mod);
					if ($fld) {
						$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='N~O~3,2' WHERE fieldid=?", array($fld->id));
					}
				}
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone(); // this should not be done if changeset is Continuous
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}
}