<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class updateMobileModuleToCrmNow2 extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$module = 'Mobile';
			if ($this->isModuleInstalled($module)) {
				global $adb;
				$Mobilers = $adb->query("SELECT version FROM vtiger_tab WHERE name = 'Mobile'");
				$version = $adb->query_result($Mobilers, 0, 'version');
				if($version == '3.0'){
					//Update module
					$package = new Vtiger_Package();

					$moduleInstance = Vtiger_Module::getInstance($module);
					$package->loadManifestFromFile('modules/'.$module.'/manifest.xml');
					$rdo = $package->update_Module($moduleInstance);
					//delete unused table
					$this->ExecuteQuery("DROP TABLE vtiger_mobile_alerts");
					$this->sendMsg('Module updated: '.$module);
				}else{
					$this->sendMsg($module.' was updated before ');
				}
			} else {
				$this->installManifestModule($module);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			vtlib_toggleModuleAccess('Mobile',false);
			$this->sendMsg('Mobile deactivated!');
			$this->markUndone(false);
			$this->sendMsg('Changeset '.get_class($this).' undone!');
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
		}
		$this->finishExecution();
	}
	
}