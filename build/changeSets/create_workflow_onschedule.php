<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class create_workflow_onschedule extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtypeid'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schtypeid INT(10)", array());
			}
			$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtime'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schtime TIME", array());
			} else {
				$this->ExecuteQuery('ALTER TABLE com_vtiger_workflows CHANGE schtime schtime TIME NULL DEFAULT NULL', array());
			}
			$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofmonth'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schdayofmonth VARCHAR(200)", array());
			}
			$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofweek'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schdayofweek VARCHAR(200)", array());
			}
			$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schannualdates'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schannualdates VARCHAR(200)", array());
			}
			$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('nexttrigger_time'));
			if (!($adb->num_rows($result))) {
				$this->ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD nexttrigger_time DATETIME", array());
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}