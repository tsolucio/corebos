<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class WorkflowTaskMessageQueueChannelSubscription extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// migrate existing work to new queue
			$cbmq = coreBOS_MQTM::getInstance();
			$result = $adb->pquery('select * from com_vtiger_workflowtask_queue', array());
			$it = new SqlResultIterator($adb, $result);
			foreach ($it as $row) {
				$msg = array(
					'taskId' => $row->task_id,
					'entityId' => $row->entity_id,
				);
				$delay = max($row->do_after-time(), 0);
				$cbmq->sendMessage('wfTaskQueueChannel', 'wftaskqueue', 'wftaskqueue', 'Data', '1:M', 0, Field_Metadata::FAR_FAR_AWAY_FROM_NOW, $delay, 0, json_encode($msg));
			}
			$this->ExecuteQuery('delete from com_vtiger_workflowtask_queue', array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}