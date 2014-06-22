<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class document_relatedlist extends cbupdaterWorker {
	
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// Relation with Entities
			$newrelid = $adb->getUniqueID("vtiger_relatedlists");
			$this->ExecuteQuery("INSERT INTO vtiger_relatedlists (relation_id, tabid, related_tabid, name, sequence, label, presence, actions) VALUES ($newrelid, 8, 0, 'getEntities', '1', 'Related To',0,'SELECT');");
			//Create a new table to support custom fields in Documents module
			$adb->query("CREATE TABLE IF NOT EXISTS vtiger_notescf (notesid INT(19), FOREIGN KEY fk_1_vtiger_notescf(notesid) REFERENCES vtiger_notes(notesid) ON DELETE CASCADE);");
			$this->sendMsg('Document relations added.');
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			// undo your magic here
			$this->ExecuteQuery("DELETE FROM vtiger_relatedlists where tabid=8 and related_tabid=0 and name='getEntities' and label='Related To';");
			$this->sendMsg('Changeset '.get_class($this).' undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}
	
}