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

class fixCreateFollowupWorkflowTasks extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("UPDATE `vtiger_field` SET `displaytype`=3 WHERE `fieldname` = 'duration_hours' and `tablename`='vtiger_activity';", array());
			global $adb;
			$rs = $adb->query("select 1 from com_vtiger_workflows where summary='Create Calendar Follow Up on create'");
			if ($rs && $adb->num_rows($rs)==0) {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				$calendarWorkflow = $workflowManager->newWorkFlow("cbCalendar");
				$calendarWorkflow->test = '[{"fieldname":"followupcreate","operation":"is","value":"true:boolean","valuetype":"rawtext","joincondition":"and","groupid":"0"}]';
				$calendarWorkflow->description = "Create Calendar Follow Up on create";
				$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_FIRST_SAVE;
				$calendarWorkflow->defaultworkflow = 1;
				$workflowManager->save($calendarWorkflow);
				$task = $taskManager->createTask('VTCreateEntityTask', $calendarWorkflow->id);
				$task->active = true;
				$task->summary = 'Create Calendar Follow Up';
				$task->entity_type = "cbCalendar";
				$task->reference_field = "relatedwith";
				$task->field_value_mapping = '[{"fieldname":"subject","modulename":"cbCalendar","valuetype":"expression","value":"concat('."'Follow up: '".',subject )"},{"fieldname":"assigned_user_id","modulename":"cbCalendar","valuetype":"fieldname","value":"assigned_user_id "},{"fieldname":"dtstart","modulename":"cbCalendar","valuetype":"fieldname","value":"followupdt  "},{"fieldname":"dtend","modulename":"cbCalendar","valuetype":"fieldname","value":"followupdt "},{"fieldname":"eventstatus","modulename":"cbCalendar","valuetype":"rawtext","value":"Planned"},{"fieldname":"taskpriority","modulename":"cbCalendar","valuetype":"rawtext","value":"Medium"},{"fieldname":"sendnotification","modulename":"cbCalendar","valuetype":"rawtext","value":"true:boolean"},{"fieldname":"activitytype","modulename":"cbCalendar","valuetype":"fieldname","value":"followuptype "},{"fieldname":"visibility","modulename":"cbCalendar","valuetype":"rawtext","value":"Private"},{"fieldname":"location","modulename":"cbCalendar","valuetype":"fieldname","value":"location "},{"fieldname":"reminder_time","modulename":"cbCalendar","valuetype":"rawtext","value":"0"},{"fieldname":"recurringtype","modulename":"cbCalendar","valuetype":"rawtext","value":"--None--"},{"fieldname":"description","modulename":"cbCalendar","valuetype":"fieldname","value":"description "},{"fieldname":"followupcreate","modulename":"cbCalendar","valuetype":"rawtext","value":"false:boolean"},{"fieldname":"date_start","modulename":"cbCalendar","valuetype":"fieldname","value":"dtstart "},{"fieldname":"time_start","modulename":"cbCalendar","valuetype":"rawtext","value":"00:00"},{"fieldname":"due_date","modulename":"cbCalendar","valuetype":"fieldname","value":"dtend "},{"fieldname":"time_end","modulename":"cbCalendar","valuetype":"rawtext","value":"00:00"}]';
				$task->test = '';
				$task->reevaluate = 0;
				$taskManager->saveTask($task);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}