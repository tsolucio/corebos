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

class addfieldstocaltwo extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$res_crons = $adb->pquery("SELECT name FROM vtiger_cron_task WHERE name ='Calendar4You - GoogleSync Insert'",array());
			if($adb->num_rows($res_crons) == 0)
				$this->ExecuteQuery("INSERT INTO vtiger_cron_task (name ,handler_file ,frequency ,laststart ,lastend ,status ,module ,sequence ,description) VALUES ('Calendar4You - GoogleSync Insert', 'modules/Calendar4You/cron/InsertEvents.service', '60', '1421927705', '1421927730', '1', 'Calendar4You', '7', '')");
			
			$res_refres_token = $adb->pquery("SHOW COLUMNS FROM its4you_googlesync4you_access WHERE Field = ?",array('refresh_token'));
			if($adb->num_rows($res_refres_token) == 0)
				$this->ExecuteQuery("ALTER TABLE  its4you_googlesync4you_access ADD  refresh_token VARCHAR( 250 ) NOT NULL");
			
			$res_synctoken = $adb->pquery("SHOW COLUMNS FROM its4you_googlesync4you_access WHERE Field = ?",array('synctoken'));
			if($adb->num_rows($res_synctoken) == 0)
				$this->ExecuteQuery("ALTER TABLE  its4you_googlesync4you_access ADD  synctoken VARCHAR( 250 ) NOT NULL");
	
			$res_googleinsert = $adb->pquery("SHOW COLUMNS FROM its4you_googlesync4you_access WHERE Field = ?",array('googleinsert'));
			if($adb->num_rows($res_googleinsert) == 0)
				$this->ExecuteQuery("ALTER TABLE  its4you_googlesync4you_access ADD  googleinsert VARCHAR( 10 ) NOT NULL ");
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
}