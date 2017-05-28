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

class UserFailedLoginAttempts extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$moduleInstance = Vtiger_Module::getInstance('Users');
			$block = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $moduleInstance);
			$field = Vtiger_Field::getInstance('failed_login_attempts',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$user_field = new Vtiger_Field();
				$user_field->name = 'failed_login_attempts';
				$user_field->label = 'LBL_FAILED_LOGIN_ATTEMPTS';
				$user_field->table ='vtiger_users';
				$user_field->column = 'failed_login_attempts';
				$user_field->columntype = 'int(11)';
				$user_field->typeofdata = 'I~O';
				$user_field->uitype = '7';
				$user_field->masseditable = '0';
				$block->addField($user_field);
				$this->ExecuteQuery('update vtiger_users set failed_login_attempts=0');
				RecalculateSharingRules();
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}