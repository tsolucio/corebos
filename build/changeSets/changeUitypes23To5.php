<?php
class changeUitypes23To5 extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("UPDATE vtiger_field SET uitype = '5' WHERE uitype = '23' AND fieldname IN ('closingdate','startdate','targetenddate','actualenddate')");
			$this->ExecuteQuery("UPDATE vtiger_field SET uitype = '5' WHERE uitype = '23' AND fieldname = 'due_date' AND tablename = 'vtiger_servicecontracts'");
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	

}

?>