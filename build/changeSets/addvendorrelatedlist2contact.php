<?php
class addvendorrelatedlist2contact extends cbupdaterWorker {
	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'Vendors';
			$module = Vtiger_Module::getInstance($modname);
			$ctModule = Vtiger_Module::getInstance('Contacts');
			$ctModule->setRelatedList($module, $modname, array('SELECT'), 'get_vendors');
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		} else {
			if ($this->isApplied()) {
				// undo your magic here
				$modname = 'Vendors';
				$module = Vtiger_Module::getInstance($modname);
				$ctModule = Vtiger_Module::getInstance('Contacts');
				$module->unsetRelatedList($ctModule, $modname, 'get_vendors');
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone();
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}
}
?>