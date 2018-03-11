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
class inventoryDetailsTaxBlock extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;

			// Install new block and fields
			require_once('vtlib/Vtiger/Module.php');
			$mod = Vtiger_Module::getInstance('InventoryDetails');

			$block = new Vtiger_Block();
			$block->label = 'InventoryDetailsTaxBlock';
			$block->sequence = 3;
			$mod->addBlock($block);

			$r = $adb->pquery('SELECT * FROM vtiger_inventorytaxinfo WHERE deleted=?', array(0));
			while($tax=$adb->fetch_array($r)) {
				$field = new Vtiger_Field();
				$field->label = $tax['taxlabel'];
				$field->name = 'id_tax' . $tax['taxid'] . '_perc';
				$field->column = 'id_tax' . $tax['taxid'] . '_perc';
				$field->columntype = 'INT(11)';
				$field->uitype = 9;
				$field->typeofdata = 'V~O';
				$block->addField($field);
			}

			// Install event handler for when taxes are created, changed and renamed
			require_once('include/events/include.inc');
			$em = new VTEventsManager($adb);
			$eventName = 'corebos.add.tax';
			$fileName = 'modules/InventoryDetails/taxHandler.php';
			$className = 'addTaxHandler';
			$em->registerHandler($eventName, $fileName, $className);

			$eventName = 'corebos.changestatus.tax';
			$fileName = 'modules/InventoryDetails/taxHandler.php';
			$className = 'changeStatusTaxHandler';
			$em->registerHandler($eventName, $fileName, $className);

			$eventName = 'corebos.changelabel.tax';
			$fileName = 'modules/InventoryDetails/taxHandler.php';
			$className = 'changeLabelTaxHandler';
			$em->registerHandler($eventName, $fileName, $className);

			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			global $adb;
			require_once('vtlib/Vtiger/Module.php');

			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$block = Vtiger_Block::getInstance('InventoryDetailsTaxBlock', $mod);
			$block->delete(true);

			// Remove the registered events
			require_once('include/events/include.inc');
			$em = new VTEventsManager($adb);
			$em->unregisterHandler('addTaxHandler');
			$em->unregisterHandler('changeStatusTaxHandler');
			$em->unregisterHandler('changeLabelTaxHandler');

			$this->markUndone(false);

		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
		}
		$this->finishExecution();
	}
	
	function isApplied() {
		$done = parent::isApplied();
		if (!$done) {

		}
		return $done;
	}
	
}