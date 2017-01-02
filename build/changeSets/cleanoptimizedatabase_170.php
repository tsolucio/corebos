<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class cleanoptimizedatabase_170 extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// module name and label
			$this->ExecuteQuery('ALTER TABLE `vtiger_tab` CHANGE `tablabel` `tablabel` VARCHAR(50) NOT NULL', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_ws_entity` CHANGE `name` `name` VARCHAR(50) NOT NULL', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_crmentity` CHANGE `setype` `setype` VARCHAR(50) NOT NULL', array());
			// SMS optimizations
			$this->ExecuteQuery('ALTER TABLE `vtiger_smsnotifier_status` ADD INDEX(`smsnotifierid`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_smsnotifier` ADD PRIMARY KEY(`smsnotifierid`)', array());
			// Audit table
			$this->ExecuteQuery('ALTER TABLE `vtiger_audit_trial` ADD INDEX(`actiondate`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_audit_trial` ADD INDEX(`module`)', array());
			$this->ExecuteQuery('ALTER TABLE `vtiger_audit_trial` ADD INDEX(`recordid`)', array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}