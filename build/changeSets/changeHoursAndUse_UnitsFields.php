<?php
class changeHoursAndUse_UnitsFields extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			//Fix types to hours in HelpDesk
			$modname = 'HelpDesk';
			$module = Vtiger_Module::getInstance($modname);
			$field = Vtiger_Field::getInstance('hours',$module);
			if($field){
				$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='N~O', uitype='7' WHERE fieldid=?",array($field->id));
				$this->ExecuteQuery("ALTER TABLE `vtiger_troubletickets` CHANGE `hours` `hours` DECIMAL( 28, 6 ) NULL DEFAULT NULL ;");
			}
			//Fix types to Used_units in ServiceContracts
			$modname = 'ServiceContracts';
			$module = Vtiger_Module::getInstance($modname);
			$field = Vtiger_Field::getInstance('used_units',$module);
			if($field){
				$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldid=?",array($field->id));
				$this->ExecuteQuery("ALTER TABLE `vtiger_servicecontracts` CHANGE `used_units` `used_units` DECIMAL( 28, 6 ) NULL DEFAULT NULL ;");
			}
			$field = Vtiger_Field::getInstance('total_units',$module);
			if($field){
				$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldid=?",array($field->id));
				$this->ExecuteQuery("ALTER TABLE `vtiger_servicecontracts` CHANGE `total_units` `total_units` DECIMAL( 28, 6 ) NULL DEFAULT NULL ;");
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
