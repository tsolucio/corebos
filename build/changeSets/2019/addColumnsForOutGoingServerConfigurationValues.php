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

class AddColumnsForOutgoingServerConfigurationValues extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  server VARCHAR(100) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  server_port INT(19) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  server_username VARCHAR(100) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  server_password VARCHAR(100) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  server_type VARCHAR(20) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  smtp_auth VARCHAR(5) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  server_path VARCHAR(256) NOT NULL");
            $this->ExecuteQuery("ALTER TABLE  vtiger_mail_account  ADD  from_email_field VARCHAR(50) NOT NULL");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
