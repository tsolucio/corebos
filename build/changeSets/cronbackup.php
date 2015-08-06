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

class cbcronbackup extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$res_crons = $adb->pquery("SELECT name FROM vtiger_cron_task WHERE name ='Native Backup'",array());
			if($adb->num_rows($res_crons) == 0)
				$this->ExecuteQuery("INSERT INTO vtiger_cron_task (name ,handler_file ,frequency ,laststart ,lastend ,status ,module ,sequence ,description) VALUES ('Native Backup', 'cron/modules/VtigerBackup/VtigerBackup.service', '86400', '0', '0', '0', 'VtigerBackup', '7', 'Backup with no external tools. Can easily run into memory limitations and really slow down the server. Good for smaller sets of information.')");
			$res_crons = $adb->pquery("SELECT name FROM vtiger_cron_task WHERE name ='External Backup'",array());
			if($adb->num_rows($res_crons) == 0)
				$this->ExecuteQuery("INSERT INTO vtiger_cron_task (name ,handler_file ,frequency ,laststart ,lastend ,status ,module ,sequence ,description) VALUES ('External Backup', 'cron/modules/VtigerBackup/ExternalBackup.service', '86400', '0', '0', '0', 'VtigerBackup', '7', 'Backup with external tools. mysqldump and zip must be available on server. Fast and good for big sets of information.')");
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}