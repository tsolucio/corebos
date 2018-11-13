<?php
class cbquestion extends cbupdaterWorker{

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$toinstall = array('cbQuestion');
			foreach ($toinstall as $module) {
				if ($this->isModuleInstalled($module)) {
					vtlib_toggleModuleAccess($module, true);
					$this->sendMsg("$module activated!");
				} else {
					$this->installManifestModule($module);
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}
?>