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

class delSettingsAuditLogin extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$this->sendMsg('This changeset eliminates Audit Trail and Login History from Settings section');
			$this->sendMsg('From now on you must use the corresponding extensions to accomplish these tasks');
			$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('LBL_AUDIT_TRAIL'));
			$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('LBL_LOGIN_HISTORY_DETAILS'));
			$toinstall = array('cbAuditTrail','cbLoginHistory');
			foreach ( $toinstall as $module ) {
				if ($this->isModuleInstalled($module)) {
					vtlib_toggleModuleAccess( $module, true );
					$this->sendMsg( "$module activated!" );
				} else {
					$this->installManifestModule( $module );
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
