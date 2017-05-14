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

class delSettingsBackupAnnouncements extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->sendMsg('This changeset eliminates Backup Settings section');
			$this->sendMsg('From now on you must go directly to the Backup module');
			$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('LBL_BACKUP_SERVER_SETTINGS'));
			$this->sendMsg('This changeset eliminates the Announcements Settings section');
			$this->sendMsg('From now on you must create an Application_Announcement Global Variable');
			$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('LBL_ANNOUNCEMENT'));
			global $adb, $current_user, $default_charset;
			$sql = 'select * from vtiger_announcement inner join vtiger_users on vtiger_announcement.creatorid=vtiger_users.id';
			$sql.=" AND vtiger_users.is_admin='on' AND vtiger_users.status='Active' AND vtiger_users.deleted = 0";
			$result = $adb->pquery($sql, array());
			if ($adb->num_rows($result)>0) {
				$announcement=$adb->query_result($result, 0, 'announcement');
				if ($announcement != '') {
					$announcement=html_entity_decode($announcement, ENT_QUOTES, $default_charset);
					include_once 'include/Webservices/Create.php';
					$usrwsid = vtws_getEntityId('Users').'x';
					vtws_create('GlobalVariable', array(
						'gvname' => 'Application_Announcement',
						'default_check' => '0',
						'value' => $announcement,
						'mandatory' => '0',
						'blocked' => '0',
						'module_list' => '',
						'category' => 'System',
						'in_module_list' => '',
						'assigned_user_id' => $usrwsid.$current_user->id,
					), $current_user);
				}
			}
			$this->ExecuteQuery('DROP TABLE vtiger_announcement');
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
