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
class addPdoSrvDivisible extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$modname = 'Products';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_STOCK_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('divisible',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'divisible';
				$field1->label= 'Divisible';
				$field1->column = 'divisible';
				$field1->columntype = 'varchar(3)';
				$field1->uitype = 56;
				$field1->typeofdata = 'C~O';
				$field1->displaytype = 1;
				$field1->presence = 0;
				$block->addField($field1);
			}
			$modname = 'Services';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_PRICING_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('divisible',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'divisible';
				$field1->label= 'Divisible';
				$field1->column = 'divisible';
				$field1->columntype = 'varchar(3)';
				$field1->uitype = 56;
				$field1->typeofdata = 'C~O';
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
