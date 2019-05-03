<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
			$this->sendMsg('This changeset add new fields to cbCVManagement module');
			global $adb;
			// Add Field in cbCVManagement module

			$fieldLayout=array(
				'cbCVManagement' => array(
					'LBL_CVMGMT_DEFAULTS'=> array(
						'Set Public' => array(
							'columntype'=>'checkbox',
							'typeofdata'=>'V~O',
							'uitype'=>'56',
							'displaytype'=>'1',
							'label'=>'setpublic',
						),
					),
				),
			);
			$this->massCreateFields($fieldLayout);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);

			$cvManageWorkFlow = new VTWorkflowManager($adb);
			$cvManageWorkFlow = $cvManageWorkFlow->newWorkFlow("cbCVManagement");
			$cvManageWorkFlow->description = "Send email on public filter";
			$invWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
			$cvManageWorkFlow->defaultworkflow = 1;
			$cvManageWorkFlow->schtypeid = 0;
			$cvManageWorkFlow->test = '{"fieldname":"set_public","operation":"has changed to","value":"true:boolean","valuetype":"rawtext","joincondition":"and","groupid":"0"}]';
			$cvManageWorkFlow->schtime = '00:00:00';
			$cvManageWorkFlow->schdayofmonth = '';
			$cvManageWorkFlow->schdayofweek = '';
			$cvManageWorkFlow->schannualdates = '';
			$cvManageWorkFlow->schminuteinterval = '';
			$cvManageWorkFlow->save($cvManageWorkFlow);

			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTEmailTask', $cvManageWorkFlow->id);
			$task->active = false;
			$task->entity_type = "cbCVManagement";
			$task->recepient = "$(assigned_user_id : (Users) email1)";
			$task->subject = "Filter made public notification";
			$task->content = 'A filter named $cvid has been set to public';
			$task->test = '';
			$task->reevaluate = 0;
			$tm->saveTask($task);
		}
		$this->finishExecution();
	}
}
