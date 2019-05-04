<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addfieldincbcvManagement extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->sendMsg('This changeset adds a new field to cbCVManagement module');
			global $adb;
			// Add Field in cbCVManagement module

			$fieldLayout=array(
				'cbCVManagement' => array(
					'LBL_CVMGMT_DEFAULTS'=> array(
						'setpublic' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>'56',
							'displaytype'=>'1',
							'label'=>'setpublic',
						),
					),
				),
			);
			$this->massCreateFields($fieldLayout);

			$cvManageWorkFlow = new VTWorkflowManager($adb);
			$cvWorkFlow = $cvManageWorkFlow->newWorkFlow('cbCVManagement');
			$cvWorkFlow->description = 'Send email on public filter';
			$cvWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
			$cvWorkFlow->defaultworkflow = 0;
			$cvWorkFlow->schtypeid = 0;
			$cvWorkFlow->test = '[{"fieldname":"setpublic","operation":"has changed to","value":"true:boolean","valuetype":"rawtext","joincondition":"and","groupid":"0"}]';
			$cvWorkFlow->schtime = '00:00:00';
			$cvWorkFlow->schdayofmonth = '';
			$cvWorkFlow->schdayofweek = '';
			$cvWorkFlow->schannualdates = '';
			$cvWorkFlow->schminuteinterval = '';
			$cvManageWorkFlow->save($cvWorkFlow);

			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTEmailTask', $cvWorkFlow->id);
			$task->active = false;
			$task->summary = 'Send email on public filter';
			$task->entity_type = 'cbCVManagement';
			$task->recepient = '$(assigned_user_id : (Users) email1)';
			$task->subject = '[View Notification]: Filter made public';
			$task->content = 'The filter with number $cvid has been set to public';
			$task->test = '';
			$task->reevaluate = 0;
			$tm->saveTask($task);

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}