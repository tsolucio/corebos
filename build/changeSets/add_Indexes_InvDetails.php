<?php
/*************************************************************************************************
 
  PUT YOUR LICENSE HERE
 
*************************************************************************************************/

class add_Indexes_InvDetails extends cbupdaterWorker {
	
	function applyChange() {
		global $adb;
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$indexes = array(
			  'productid',
			  'related_to',
			  'account_id',
			  'contact_id',
			  'vendor_id',
			  'lineitem_id'
			);
			foreach($indexes as $index){
			    $res = $adb->query("SHOW INDEX FROM vtiger_inventorydetails WHERE column_name = '$index'");
			    if($adb->num_rows($res) == 0){
				$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails ADD INDEX (`$index`)");
			    }
			}
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
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone(); // this should not be done if changeset is Continuous
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}
	
}