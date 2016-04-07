<?php
class add_halfyear_recurring_invoice extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'SalesOrder';
			$module = Vtiger_Module::getInstance($modname);
			$field = Vtiger_Field::getInstance('recurring_frequency',$module);
                        $field->setPicklistValues(array('half-year'));
                        $this->ExecuteQuery("UPDATE vtiger_recurring_frequency SET sortorderid=5 WHERE recurring_frequency='half-year'");
                        $this->ExecuteQuery("UPDATE vtiger_recurring_frequency SET sortorderid=6 WHERE recurring_frequency='Yearly'");
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
