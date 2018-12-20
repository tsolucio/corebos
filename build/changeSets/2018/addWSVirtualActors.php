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

class addWSVirtualActors extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			// workflow
			$wsid = $adb->getUniqueID('vtiger_ws_entity');
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity` (`id`, `name`, `handler_path`, `handler_class`, `ismodule`)
				VALUES (?, 'Workflow', 'include/Webservices/VtigerActorOperation.php', 'VtigerActorOperation', '0')", array($wsid));
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity_tables` (`webservice_entity_id`, `table_name`) VALUES (?, 'com_vtiger_workflows');", array($wsid));
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity_name` (`entity_id`, `name_fields`, `index_field`, `table_name`)
				VALUES (?, 'summary', 'workflow_id', 'com_vtiger_workflows');", array($wsid));
			// Audit Trail
			$wsid = $adb->getUniqueID('vtiger_ws_entity');
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity` (`id`, `name`, `handler_path`, `handler_class`, `ismodule`)
				VALUES (?, 'AuditTrail', 'include/Webservices/VtigerActorOperation.php', 'VtigerActorOperation', '0')", array($wsid));
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity_tables` (`webservice_entity_id`, `table_name`) VALUES (?, 'vtiger_audit_trial');", array($wsid));
			// Login History
			$wsid = $adb->getUniqueID('vtiger_ws_entity');
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity` (`id`, `name`, `handler_path`, `handler_class`, `ismodule`)
				VALUES (?, 'LoginHistory', 'include/Webservices/VtigerActorOperation.php', 'VtigerActorOperation', '0')", array($wsid));
			$this->ExecuteQuery("INSERT INTO `vtiger_ws_entity_tables` (`webservice_entity_id`, `table_name`) VALUES (?, 'vtiger_loginhistory');", array($wsid));
			///////
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}