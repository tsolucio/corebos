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

class mysqlstrictNO_ZERO_IN_DATE extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$smrs = $adb->query('SELECT @@SESSION.sql_mode');
			$sm = $adb->query_result($smrs,0,0);
			$adb->query("SET SESSION sql_mode = ''");
			$this->ExecuteQuery('ALTER TABLE `vtiger_email_access` CHANGE `accesstime` `accesstime` TIME NULL DEFAULT NULL');
			$this->ExecuteQuery('ALTER TABLE `vtiger_users` CHANGE `date_entered` `date_entered` DATETIME NOT NULL');
			$this->ExecuteQuery('ALTER TABLE `vtiger_users` CHANGE `date_modified` `date_modified` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;');
			$this->ExecuteQuery('ALTER TABLE `vtiger_import_maps` CHANGE `date_entered` `date_entered` DATETIME NOT NULL');
			$this->ExecuteQuery('ALTER TABLE `vtiger_import_maps` CHANGE `date_modified` `date_modified` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;');
			$this->ExecuteQuery('ALTER TABLE `vtiger_loginhistory` CHANGE `login_time` `login_time` TIMESTAMP NULL DEFAULT NULL;');
			$this->ExecuteQuery('ALTER TABLE `vtiger_loginhistory` CHANGE `logout_time` `logout_time` TIMESTAMP NULL DEFAULT NULL');
			$this->ExecuteQuery("UPDATE `vtiger_users` set date_modified=date_entered");
			$this->ExecuteQuery("UPDATE `vtiger_import_maps` set date_modified=date_entered");
			$this->ExecuteQuery("UPDATE `vtiger_loginhistory` set login_time=null where login_time='0000-00-00 00:00:00'");
			$this->ExecuteQuery("UPDATE `vtiger_loginhistory` set logout_time=null where logout_time='0000-00-00 00:00:00'");
			$adb->query("SET SESSION sql_mode = '$sm'");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}