<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class EmailField2DisplayType4andReviewFailedLoginAttempts extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			foreach (['Project', 'ProjectTask', 'Potentials', 'HelpDesk'] as $mod) {
				$module = Vtiger_Module::getInstance($mod);
				$field = Vtiger_Field::getInstance('email', $module);
				if ($field) {
					$this->ExecuteQuery('update vtiger_field set displaytype=4 where fieldid=?', array($field->id));
					$this->ExecuteQuery('ALTER TABLE '.$module->basetable.' CHANGE `email` `email` VARCHAR(250);');
				}
			}
			$this->ExecuteQuery(
				'update vtiger_eventhandlers set event_name=? where handler_path=? and handler_class=?;',
				array('vtiger.entity.aftersave.first', 'modules/Emails/evcbrcHandler.php', 'evcbrcHandler')
			);
			$cncrm = $adb->getColumnNames('vtiger_portalinfo');
			if (in_array('failed_login_attempts', $cncrm)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_portalinfo` CHANGE `failed_login_attempts` `failed_login_attempts` INT NOT NULL DEFAULT 0;');
			} else {
				$this->ExecuteQuery('ALTER TABLE `vtiger_portalinfo` ADD `failed_login_attempts` int NOT NULL DEFAULT 0;');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}