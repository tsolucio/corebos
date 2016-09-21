<?php
class changeTypeOfDataUitype9 extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata = 'N~O~3,2' WHERE typeofdata = 'N~O'");
			$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata = 'M~O~3,2' WHERE typeofdata = 'M~O'");
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	

}

?>