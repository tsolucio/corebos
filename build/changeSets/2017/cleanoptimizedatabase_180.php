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

class cleanoptimizedatabase_180 extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// Indexes after studying MySQL queries with no index
			$this->ExecuteQuery('ALTER TABLE `vtiger_entityname` ADD INDEX(`modulename`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_ws_entity` ADD UNIQUE(`name`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_evvtmenu` ADD INDEX(`mlabel`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_eventhandlers` ADD INDEX( `event_name`, `is_active`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_eventhandlers` ADD INDEX(`is_active`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_tab` ADD INDEX( `name`, `presence`, `isentitytype`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_users` ADD INDEX(`status`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_cbmap` ADD INDEX(`mapname`)', array());
			$this->ExecuteQuery('ALTER TABLE `its4you_calendar4you_event_fields` ADD INDEX( `userid`, `view`)', array());
			$this->ExecuteQuery('ALTER TABLE `its4you_calendar4you_settings` ADD PRIMARY KEY(`userid`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_notes` ADD INDEX(`folderid`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_inventorysubproductrel` ADD INDEX( `id`, `sequence_no`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_settings_blocks` ADD INDEX(`label`)', array());
			$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD INDEX( `module_name`, `execution_condition`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_ws_operation` ADD UNIQUE(` name `)', array());
			$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflow_tasktypes` ADD PRIMARY KEY(`id`)', array());
			$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflow_tasktypes` ADD UNIQUE(`tasktypename`)', array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}