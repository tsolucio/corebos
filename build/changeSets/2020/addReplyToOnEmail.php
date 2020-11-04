<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addReplyToOnEmail extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$modname = 'Emails';
			$module = Vtiger_Module::getInstance($modname);
			if ($module) {
				$fromemail = Vtiger_Field::getInstance('from_email', $module);
				$field = Vtiger_Field::getInstance('replyto', $module);
				if (!$field) {
					$field = new Vtiger_Field();
					$field->name = 'replyto';
					$field->label= 'replyto';
					$field->table = 'vtiger_emaildetails';
					$field->column = 'replyto';
					$field->columntype = 'VARCHAR(500)';
					$field->uitype = 12;
					$field->typeofdata = 'V~O';
					$field->displaytype = 1;
					$field->presence = 0;
					$fromemail->block->addField($field);
				}
				$this->ExecuteQuery('ALTER TABLE `vtiger_mailmanager_mailrecord` ADD `mreplyto` VARCHAR(500) NOT NULL');
				$this->sendMsg('Changeset '.get_class($this).' applied!');
				$this->markApplied();
			} else {
				$this->sendMsgError('Changeset '.get_class($this).' NOT applied! cbQuestion module not found');
			}
		}
		$this->finishExecution();
	}
}