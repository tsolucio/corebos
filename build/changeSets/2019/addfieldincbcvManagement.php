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
			// Fields preparations
			$moduleInstance = Vtiger_Module::getInstance('cbCVManagement');
			//var_dump($moduleInstance);
			$block = Vtiger_Block::getInstance('LBL_DAFAULT_VALUE', $moduleInstance );
			//var_dump($block);
			//die();
            if (!$block) {
                $block = new Vtiger_Block();
                $block->label = 'LBL_DAFAULT_VALUE';
                $block->sequence = 5;
                $moduleInstance->addBlock($block);
            }
			$fieldLayout = array(
				'cbCVManagement' => array(
					'Default Value' => array(
						'LBL_DAFAULT_VALUE' => array(
							'columntype'=>'checkbox',
							'typeofdata'=>'V~O',
							'uitype'=>56,
							'label' => 'Set Public',
							'displaytype'=>'1',
						),
					),
				),
			);
			$cvManageWorkFlow = new VTWorkflowManager($adb);
			$cvManageWorkFlow = $cvManageWorkFlow->newWorkFlow("cbCVManagement");
			$cvManageWorkFlow->test = '';
			$cvManageWorkFlow->description = "Send email on public filter";
			$invWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
			$cvManageWorkFlow->defaultworkflow = 1;
			$cvManageWorkFlow->schtypeid = 0;
			$cvManageWorkFlow->test = '';
			$cvManageWorkFlow->schtime = '00:00:00';
			$cvManageWorkFlow->schdayofmonth = '';
			$cvManageWorkFlow->schdayofweek = '';
			$cvManageWorkFlow->schannualdates = '';
			$cvManageWorkFlow->schminuteinterval = '';
			$cvManageWorkFlow->save($cvManageWorkFlow);

			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTEmailTask', $cvManageWorkFlow->id);
			$task->active = false;
			$task->summary = $caltk['summary'];
			$task->entity_type = "cbCVManagement";
			$task->recepient = "admin user";
			$task->subject = "Send email on public filter";
			$task->content = 'A filter named $cvid has been set to public';
			$task->test = '';
			$task->reevaluate = 1;
			$tm->saveTask($task);

            $this->massCreateFields($fieldLayout);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
