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

class databasechangesToMoveTocbCalendar2 extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("UPDATE vtiger_activity_reminder_popup SET semodule='cbCalendar' where semodule='Calendar';");
			$this->ExecuteQuery("UPDATE vtiger_globalvariable SET module_list=REPLACE(module_list, ' Calendar ', ' cbCalendar ') where module_list LIKE '% Calendar %';");
			$this->ExecuteQuery("DELETE FROM vtiger_field WHERE tabid=9 AND uitype='10';");
			$this->ExecuteQuery("UPDATE com_vtiger_workflowtasks SET workflow_id = -1*workflow_id WHERE task regexp 'O:[0-9]*:\"VTCreateEventTask\":.*';");
			$this->ExecuteQuery("UPDATE com_vtiger_workflowtasks SET workflow_id = -1*workflow_id WHERE task regexp 'O:[0-9]*:\"VTCreateTodoTask\":.*';");
			$this->ExecuteQuery('DELETE FROM com_vtiger_workflow_tasktypes WHERE tasktypename=? OR tasktypename=?', array('VTCreateEventTask', 'VTCreateTodoTask'));
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
