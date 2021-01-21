<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addWorkflowMassActionsColumns extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$cnmsg = $adb->getColumnNames('com_vtiger_workflows');
			if (!in_array('options', $cnmsg)) {
				$this->ExecuteQuery('ALTER TABLE com_vtiger_workflows ADD options VARCHAR(100);');
				$this->ExecuteQuery('ALTER TABLE com_vtiger_workflows ADD cbquestion INT(11);');
				$this->ExecuteQuery('ALTER TABLE com_vtiger_workflows ADD recordset INT(11);');
				$this->ExecuteQuery('ALTER TABLE com_vtiger_workflows ADD onerecord INT(11);');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied! You can safely ignore errors in this changeset, it is a redundant check to make sure the fields are there.');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}