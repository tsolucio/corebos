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

class create_workflow_taskstype extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
			$this->ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflow_tasktypes (
				id int(11) NOT NULL,
				tasktypename varchar(255) NOT NULL,
				label varchar(255),
				classname varchar(255),
				classpath varchar(255),
				templatepath varchar(255),
				modules text(500),
				sourcemodule varchar(255)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8", array());
			$taskTypes = array();
			$defaultModules = array('include' => array(), 'exclude'=>array());
			$createToDoModules = array('include' => array("Leads","Accounts","Potentials","Contacts","HelpDesk","Campaigns","Quotes","PurchaseOrder","SalesOrder","Invoice"), 'exclude'=>array("Calendar", "FAQ", "Events"));
			$createEventModules = array('include' => array("Leads","Accounts","Potentials","Contacts","HelpDesk","Campaigns"), 'exclude'=>array("Calendar", "FAQ", "Events"));
			
			$taskTypes[] = array("name"=>"VTEmailTask", "label"=>"Send Mail", "classname"=>"VTEmailTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTEmailTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTEmailTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
			$taskTypes[] = array("name"=>"VTEntityMethodTask", "label"=>"Invoke Custom Function", "classname"=>"VTEntityMethodTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTEntityMethodTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
			$taskTypes[] = array("name"=>"VTCreateTodoTask", "label"=>"Create Todo", "classname"=>"VTCreateTodoTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateTodoTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateTodoTask.tpl", "modules"=>$createToDoModules, "sourcemodule"=>'');
			$taskTypes[] = array("name"=>"VTCreateEventTask", "label"=>"Create Event", "classname"=>"VTCreateEventTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateEventTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateEventTask.tpl", "modules"=>$createEventModules, "sourcemodule"=>'');
			$taskTypes[] = array("name"=>"VTUpdateFieldsTask", "label"=>"Update Fields", "classname"=>"VTUpdateFieldsTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTUpdateFieldsTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTUpdateFieldsTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
			$taskTypes[] = array("name"=>"VTCreateEntityTask", "label"=>"Create Entity", "classname"=>"VTCreateEntityTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateEntityTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateEntityTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
			$taskTypes[] = array("name"=>"VTSMSTask", "label"=>"SMS Task", "classname"=>"VTSMSTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTSMSTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTSMSTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'SMSNotifier');
			
			foreach ($taskTypes as $taskType) {
				VTTaskType::registerTaskType($taskType);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		$this->finishExecution();
	}
	
}