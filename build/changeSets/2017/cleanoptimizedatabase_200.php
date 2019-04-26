<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class cleanoptimizedatabase_200 extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// permit mass edit of ticket solution field
			$this->ExecuteQuery("UPDATE vtiger_field SET masseditable = '2' WHERE vtiger_field.tabid = 13 and columnname = 'solution'", array());
			$this->ExecuteQuery("UPDATE vtiger_blocks SET create_view = '0' WHERE vtiger_blocks.tabid = 13 and blocklabel = 'LBL_TICKET_RESOLUTION'", array());

			// permit mass edit of customer portal activation
			$this->ExecuteQuery("UPDATE vtiger_field SET masseditable = '2' WHERE vtiger_field.tabid = 4 and columnname = 'portal'", array());

			// Convert attachment path to varchar instead of text
			$this->ExecuteQuery('ALTER TABLE `vtiger_attachments` CHANGE `path` `path` VARCHAR(550) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;', array());

			// Convert reportsto foreign key field to integer
			$this->ExecuteQuery('ALTER TABLE `vtiger_contactdetails` CHANGE `reportsto` `reportsto` INT(11) NULL DEFAULT NULL;', array());

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
