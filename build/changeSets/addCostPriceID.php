<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
class addCostPriceID extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'InventoryDetails';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_INVENTORYDETAILS_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('cost_price',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'cost_price';
				$field1->label= 'Cost Price';
				$field1->column = 'cost_price';
				$field1->columntype = 'DECIMAL(28,6)';
				$field1->uitype = 71;
				$field1->typeofdata = 'N~O';
				$field1->displaytype = 1;
				$field1->presence = 0;
				$block->addField($field1);
			}
			$field = Vtiger_Field::getInstance('cost_gross',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'cost_gross';
				$field1->label= 'Cost Total';
				$field1->column = 'cost_gross';
				$field1->columntype = 'DECIMAL(28,6)';
				$field1->uitype = 71;
				$field1->typeofdata = 'N~O';
				$field1->displaytype = 1;
				$field1->presence = 0;
				$block->addField($field1);
			}
		}

		$this->sendMsg('Changeset '.get_class($this).' applied!');
		$this->markApplied();
		$this->finishExecution();
	}
}

?>
