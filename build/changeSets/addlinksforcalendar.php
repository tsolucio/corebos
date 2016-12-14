<?php
class addlinksforcalendar extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$link = Vtiger_Link::addlink(55, 'HEADERSCRIPT', 'Calendar4You_HeaderScript3', 'modules/Calendar4You/fullcalendar/lib/moment.min.js');
                       	$link2 = Vtiger_Link::addlink(55, 'HEADERCSS', 'Calendar4You_HeaderStyle2', 'modules/Calendar4You/fullcalendar/themes/cupertino/jquery-ui.min.css');
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
