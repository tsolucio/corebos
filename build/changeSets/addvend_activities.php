
<?php
class addVendorActivities extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'Vendors';
			$module = Vtiger_Module::getInstance($modname);
			
			$qtModule = Vtiger_Module::getInstance('Calendar');
			$relationLabel = 'Activities';
			$module->setRelatedList($qtModule , $relationLabel, Array("ADD"),'get_activities');

			$relationLabel = 'Activities History';
			$module->setRelatedList($qtModule , $relationLabel, Array("ADD"),'get_history');
			
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
			
		}
		$this->finishExecution();
	}
	
	function undoChange() {
	
	}
}

?>
