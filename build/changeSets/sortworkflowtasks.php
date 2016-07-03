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

class sortworkflowtasks extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$result = $adb->pquery('show columns from com_vtiger_workflowtasks like ?', array('executionorder'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery('ALTER TABLE com_vtiger_workflowtasks ADD executionorder INT(10)', array());
				$this->ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks` ADD INDEX(`executionorder`)');
			}
			$result = $adb->pquery('select task_id,workflow_id from com_vtiger_workflowtasks order by workflow_id', array());
			$upd = 'update com_vtiger_workflowtasks set executionorder=? where task_id=?';
			$wfid = null;
			while ($task = $adb->fetch_array($result)) {
				if ($task['workflow_id']!=$wfid) {
					$order = 1;
					$wfid = $task['workflow_id'];
				}
				$adb->pquery($upd, array($order,$task['task_id']));
				$order++;
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}