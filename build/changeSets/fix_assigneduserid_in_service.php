<?php
/*************************************************************************************************
 
  PUT YOUR LICENSE HERE
 
*************************************************************************************************/

class fix_assigneduserid_in_service extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("UPDATE vtiger_field INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_field.tabid and vtiger_tab.name = 'Services'
			 SET typeofdata = 'V~M' WHERE uitype = '53'");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(); // this should not be done if changeset is Continuous
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		} else {
			if ($this->isApplied()) {
				// ***
				// undo your magic here
				// ***
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone(); // this should not be done if changeset is Continuous
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}
	
}