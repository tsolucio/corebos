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

class addfieldstocal extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$res_google_apikey = $adb->pquery("SHOW COLUMNS FROM its4you_googlesync4you_access WHERE Field = ?",array('google_apikey'));
			if($adb->num_rows($res_google_apikey) == 0)
				$this->ExecuteQuery("ALTER TABLE `its4you_googlesync4you_access` ADD `google_apikey` VARCHAR( 250 ) NOT NULL ");
			
			$res_google_keyfile = $adb->pquery("SHOW COLUMNS FROM its4you_googlesync4you_access WHERE Field = ?",array('google_keyfile'));
			if($adb->num_rows($res_google_keyfile) == 0)
				$this->ExecuteQuery("ALTER TABLE `its4you_googlesync4you_access`ADD `google_keyfile` VARCHAR( 250 ) NOT NULL ");

			$res_google_clientid = $adb->pquery("SHOW COLUMNS FROM its4you_googlesync4you_access WHERE Field = ?",array('google_clientid'));
			if($adb->num_rows($res_google_clientid) == 0)
				$this->ExecuteQuery("ALTER TABLE `its4you_googlesync4you_access`ADD `google_clientid` VARCHAR( 250 ) NOT NULL");
				
			$this->ExecuteQuery("ALTER TABLE `its4you_googlesync4you_access` CHANGE `google_password` `google_password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
}