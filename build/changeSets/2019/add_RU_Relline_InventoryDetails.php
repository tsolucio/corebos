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

class add_RU_Relline_InventoryDetails extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$block = Vtiger_Block::getInstance('LBL_INVENTORYDETAILS_INFORMATION', $mod);
			$field = new Vtiger_Field();
			$field->name = 'remaining_units';
			$field->label = 'Remaining Units';
			$field->column = 'remaining_units';
			$field->table = 'vtiger_inventorydetails';
			$field->columntype = 'DECIMAL(25,2)';
			$field->typeofdata = 'N~O';
			$field->uitype = '1';
			$block->addField($field);

			$field = new Vtiger_Field();
			$field->name = 'rel_lineitem_id';
			$field->label = 'Related Line Item ID';
			$field->column = 'rel_lineitem_id';
			$field->table = 'vtiger_inventorydetails';
			$field->columntype = 'INT(9)';
			$field->typeofdata = 'I~O';
			$field->uitype = '1';
			$block->addField($field);

			$mod = Vtiger_Module::getInstance('SalesOrder');
			$block = Vtiger_Block::getInstance('LBL_SO_INFORMATION', $mod);
			$field = new Vtiger_Field();
			$field->name = 'invoiced';
			$field->label = 'Invoiced';
			$field->column = 'invoiced';
			$field->columntype = 'VARCHAR(3)';
			$field->typeofdata = 'C~O';
			$field->uitype = '56';
			$block->addField($field);

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
