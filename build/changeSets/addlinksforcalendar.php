<?php
class addlinksforcalendar extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
                        $query=$adb->query("select tabid from vtiger_tab where name='Calendar4You'");
                        $tabid=$adb->query_result($query,0,0);
			$link = Vtiger_Link::addlink($tabid, 'HEADERSCRIPT', 'Calendar4You_HeaderScript3', 'modules/Calendar4You/fullcalendar/lib/moment.min.js');
                       	$link2 = Vtiger_Link::addlink($tabid, 'HEADERCSS', 'Calendar4You_HeaderStyle2', 'modules/Calendar4You/fullcalendar/themes/cupertino/jquery-ui.min.css');
                        $adb->query("UPDATE vtiger_links SET sequence = '1' WHERE  linkurl='modules/Calendar4You/fullcalendar/fullcalendar.js'");
                        $adb->query("UPDATE vtiger_links SET sequence = '2' WHERE  linkurl='modules/Calendar4You/Calendar4You.js'");
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
