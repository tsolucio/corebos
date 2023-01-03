<?php
/*************************************************************************************************
 * Copyright 2022 Spike. -- This file is a part of Spike coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. Spike. reserves all rights not expressly
* granted by the License. coreBOS distributed by Spike. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class add_workflow_addnotification extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
			$defaultModules = array('include' => array(), 'exclude' => array());
			$taskType= array(
				'name' => 'CBAddNotification',
				'label' => 'CBAddNotification',
				'classname' => 'CBAddNotification',
				'classpath' => 'modules/com_vtiger_workflow/tasks/CBAddNotification.php',
				'templatepath' => 'com_vtiger_workflow/taskforms/CBAddNotification.tpl',
				'modules' => $defaultModules,
				'sourcemodule' => ''
			);
			VTTaskType::registerTaskType($taskType);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}