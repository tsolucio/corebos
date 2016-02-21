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

class add_workflow_delrelated extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
			$taskTypes = array();
			$defaultModules = array('include' => array(), 'exclude'=>array());
			$taskType= array("name"=>"CBDeleteRelatedTask", "label"=>"CBDeleteRelatedTask", "classname"=>"CBDeleteRelatedTask",
					 "classpath"=>"modules/com_vtiger_workflow/tasks/CBDeleteRelatedTask.inc",
					 "templatepath"=>"com_vtiger_workflow/taskforms/CBDeleteRelated.tpl",
					 "modules"=>$defaultModules,
					 "sourcemodule"=>'');
			VTTaskType::registerTaskType($taskType);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			global $adb;
			$result = $adb->pquery("SELECT * FROM `com_vtiger_workflowtasks` WHERE `task` like '%CBDeleteRelatedTask%'",array());
			if ($result and $adb->num_rows($result)>0) {
				$this->sendMsg('<span style="font-size:large;weight:bold;">Workflows that use this task exist!! Please eliminate them before undoing this change.</span>');
			} else {
				$adb->pquery("DELETE FROM com_vtiger_workflow_tasktypes WHERE
						tasktypename = 'CBDeleteRelatedTask' and label = 'CBDeleteRelatedTask' and classname = 'CBDeleteRelatedTask'",array());
				$this->markUndone(false);
				$this->sendMsg('Changeset '.get_class($this).' undone!');
			}
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
		}
		$this->finishExecution();
	}

	function isApplied() {
		$done = parent::isApplied();
		if (!$done) {
			global $adb;
			$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_tasktypes where tasktypename='CBDeleteRelatedTask'",array());
			$done = ($result and $adb->num_rows($result)==1);
		}
		return $done;
	}

}