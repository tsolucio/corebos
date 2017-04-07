<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class GoogleContactChanges extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// Indexes after studying MySQL queries with no index
			$this->ExecuteQuery("ALTER TABLE `its4you_googlesync4you_access` ADD `service` varchar(255)  NULL ;");
			$this->ExecuteQuery("CREATE TABLE vtiger_google_sync_settings (user int(11) DEFAULT NULL, 
                                            module varchar(50) DEFAULT NULL , clientgroup varchar(255) DEFAULT NULL, 
                                            direction varchar(50) DEFAULT NULL)");
                        $this->ExecuteQuery("CREATE TABLE vtiger_google_sync_fieldmapping ( vtiger_field varchar(255) DEFAULT NULL,
                                            google_field varchar(255) DEFAULT NULL, google_field_type varchar(255) DEFAULT NULL,
                                            google_custom_label varchar(255) DEFAULT NULL, user int(11) DEFAULT NULL)");
                        //WSApp methods
                        $this->ExecuteQuery("INSERT INTO `vtiger_wsapp_handlerdetails` (`type`, `handlerclass`, `handlerpath`) VALUES ('vtigerSyncLib', 'WSAPP_VtigerSyncEventHandler', 'modules/WSAPP/synclib/handlers/VtigerSyncEventHandler.php');");
                        $this->ExecuteQuery("INSERT INTO `vtiger_wsapp_handlerdetails` (`type`, `handlerclass`, `handlerpath`) VALUES ('Google_vtigerHandler', 'Google_Vtiger_Handler', 'modules/Contacts/handlers/Vtiger.php');");
                        $this->ExecuteQuery("INSERT INTO `vtiger_wsapp_handlerdetails` (`type`, `handlerclass`, `handlerpath`) VALUES ('Google_vtigerSyncHandler', 'Google_VtigerSync_Handler', 'modules/Contacts/handlers/VtigerSync.php');");
                        $this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
