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

class PotentialForecastAmount extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$chktbl = $adb->query('select 1 from com_vtiger_workflow_tasktypes limit 1');
			if ($chktbl) {
			$moduleInstance = Vtiger_Module::getInstance('Potentials');
			$block = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $moduleInstance);
			$field = Vtiger_Field::getInstance('forecast_amount',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$forecast_field = new Vtiger_Field();
				$forecast_field->name = 'forecast_amount';
				$forecast_field->label = 'Forecast Amount';
				$forecast_field->table ='vtiger_potential';
				$forecast_field->column = 'forecast_amount';
				$forecast_field->columntype = 'decimal(25,4)';
				$forecast_field->typeofdata = 'N~O';
				$forecast_field->uitype = '71';
				$forecast_field->masseditable = '0';
				$block->addField($forecast_field);
			}
			
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Calculate or Update forecast amount'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Potentials");
				$potentailsWorkFlow->test = '';
				$potentailsWorkFlow->description = "Calculate or Update forecast amount";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
				$potentailsWorkFlow->defaultworkflow = 1;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTUpdateFieldsTask', $potentailsWorkFlow->id);
				$task->active = true;
				$task->summary = 'update forecast amount';
				$task->field_value_mapping = '[{"fieldname":"forecast_amount","valuetype":"expression","value":"amount * probability / 100"}]';
				$taskManager->saveTask($task);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
			} else {
				$this->sendMsgError('This changeset could not be applied because it depends on create_workflow_tasktype which probably has not been applied yet. Apply that changeset and try this one again.');
			}
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			// undo your magic here
			$moduleInstance=Vtiger_Module::getInstance('Potentials');
			$field = Vtiger_Field::getInstance('forecast_amount',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=1 where fieldid=?',array($field->id));
			}
			global $adb;
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Calculate or Update forecast amount'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$wfid = $adb->query_result($wfrs,0,0);
				$this->deleteWorkflow($wfid);
				$this->sendMsg('Workflow deleted!');
			}
			$this->sendMsg('Changeset '.get_class($this).' undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}
	
}