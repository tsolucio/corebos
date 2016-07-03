<?php
class addRecurringSO extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
                    global $adb;
                    $modname = 'SalesOrder';
                    $module = Vtiger_Module::getInstance($modname);
                    $field = Vtiger_Field::getInstance('recurring_frequency', $module);
                    if ($field) {
                        $field->setPicklistValues(array('2years','3years','4years','5years'));
                        $this->ExecuteQuery("UPDATE vtiger_recurring_frequency SET sortorderid=7 WHERE recurring_frequency='2years'");
                        $this->ExecuteQuery("UPDATE vtiger_recurring_frequency SET sortorderid=8 WHERE recurring_frequency='3years'");
                        $this->ExecuteQuery("UPDATE vtiger_recurring_frequency SET sortorderid=9 WHERE recurring_frequency='4years'");
                        $this->ExecuteQuery("UPDATE vtiger_recurring_frequency SET sortorderid=10 WHERE recurring_frequency='5years'");
                    }

                    $this->sendMsg('Changeset '.get_class($this).' applied!');
                    $this->markApplied();
		}
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
