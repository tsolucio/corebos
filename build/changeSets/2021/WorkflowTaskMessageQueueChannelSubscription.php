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
include_once 'modules/com_vtiger_workflow/VTWorkflow.php';

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
			$this->ExecuteQuery('ALTER TABLE com_vtiger_workflow_activatedonce ADD pending tinyint default 0;');
			$result = $adb->pquery(
				'select com_vtiger_workflowtask_queue.task_id, entity_id, do_after, com_vtiger_workflows.workflow_id, execution_condition
				from com_vtiger_workflowtask_queue
				inner join com_vtiger_workflowtasks on com_vtiger_workflowtasks.task_id=com_vtiger_workflowtask_queue.task_id
				inner join com_vtiger_workflows on com_vtiger_workflows.workflow_id=com_vtiger_workflowtasks.workflow_id',
				array()
			);
			$it = new SqlResultIterator($adb, $result);
			foreach ($it as $row) {
				$msg = array(
					'taskId' => $row->task_id,
					'entityId' => $row->entity_id,
				);
				$delay = max($row->do_after-time(), 0);
				Workflow::pushWFTaskToQueue($row->workflow_id, $row->execution_condition, $row->entity_id, $msg, $delay);
			}
			$this->ExecuteQuery('delete from com_vtiger_workflowtask_queue', array());
			$this->ExecuteQuery('drop table com_vtiger_workflowtask_queue', array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}