<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class Addmodulemessages extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$module = 'Messages';
			if ($this->isModuleInstalled($module)) {
				vtlib_toggleModuleAccess($module, true);
				$cnmsg = $adb->getColumnNames('vtiger_messages');
				if (in_array('messagename', $cnmsg) && in_array('messagetype', $cnmsg) && in_array('messageno', $cnmsg)) {
					$msgModuleWithS = '0';
					$blockName = 'LBL_MESSAGE_INFORMATION';
				} else {
					$msgModuleWithS = '1';
					$blockName = 'LBL_MESSAGES_INFORMATION';
				}
				$fields = array(
					$module => array(
						$blockName => array(
							'lasteventtime' => array(
								'columntype'=>'datetime',
								'typeofdata'=>'DT~O',
								'uitype'=>'70',
								'displaytype'=>'2',
								'label'=>'lasteventtime', // optional, if empty fieldname will be used
								'massedit' => 1,  // optional, if empty 0 will be set
							),
							'email_tplid' => array(
								'columntype'=>'int(11)',
								'typeofdata'=>'I~O',
								'uitype'=>'10',
								'displaytype'=>'1',
								'label'=>'email_tplid', // optional, if empty fieldname will be used
								'massedit' => 0,  // optional, if empty 0 will be set
								'mods'=>array('Actions'), // used if uitype 10
							),
							'messagesrelatedto' => array(
								'columntype'=>'int(11)',
								'typeofdata'=>'I~O',
								'uitype'=>'10',
								'displaytype'=>'1',
								'label'=>'Related To', // optional, if empty fieldname will be used
								'massedit' => 1,  // optional, if empty 0 will be set
								'mods'=>array('Accounts', 'Contacts', 'Potentials', 'Leads', 'HelpDesk', 'Vendors', 'Project', 'ProjectTask',), // used if uitype 10
							),
							'messagesuniqueid' => array(
								'columntype'=>'varchar(250)',
								'typeofdata'=>'V~O',
								'uitype'=>'1',
								'displaytype'=>'1',
								'label'=>'messagesuniqueid', // optional, if empty fieldname will be used
								'massedit' => 1,  // optional, if empty 0 will be set
							),
						)
					),
				);
				$this->massCreateFields($fields);
				$this->sendMsg("$module activated!");
			} else {
				$msgModuleWithS = '0';
				$this->installManifestModule($module);
			}
			coreBOS_Settings::setSetting('coreBOSMessageModuleWithS', $msgModuleWithS);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}