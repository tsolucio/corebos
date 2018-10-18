<?php
/*************************************************************************************************
 * Copyright 2018 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. MajorLabel reserves all rights not expressly
* granted by the License. coreBOS distributed by MajorLabel S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
class inactiveTaxesInInventoryDetails extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;

			require_once 'vtlib/Vtiger/Module.php';
			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$block = Vtiger_Block::getInstance('InventoryDetailsTaxBlock', $mod);

			$r = $adb->query('SELECT * FROM vtiger_inventorytaxinfo');
			while ($tax=$adb->fetch_array($r)) {
				$field = Vtiger_Field::getInstance('id_tax' . $tax['taxid'] . '_perc', $mod);
				if (!$field) {
					$field = new Vtiger_Field();
					$field->label = $tax['taxlabel'];
					$field->name = 'id_tax' . $tax['taxid'] . '_perc';
					$field->column = 'id_tax' . $tax['taxid'] . '_perc';
					$field->columntype = 'DECIMAL(7,3)';
					$field->uitype = 9;
					$field->typeofdata = 'N~O';
					$field->presence = 1;
					$block->addField($field);
				}
			}
			$this->markApplied();
		}
		$this->finishExecution();
	}
}