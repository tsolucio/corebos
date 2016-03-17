<?php
class addCostPrice extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'Products';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_PRICING_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('cost_price',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'cost_price';
				$field1->label= 'Cost Price';
				$field1->column = 'cost_price';
				$field1->columntype = 'DECIMAL(10,6)';
				$field1->uitype = 71;
				$field1->typeofdata = 'N~O';
				$field1->displaytype = 1;
				$field1->presence = 0;
				$block->addField($field1);
			}
			$modname = 'Services';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_PRICING_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('cost_price',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'cost_price';
				$field1->label= 'Cost Price';
				$field1->column = 'cost_price';
				$field1->columntype = 'DECIMAL(10,6)';
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
	
	function undoChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		}
		$this->finishExecution();
	}
}

?>
