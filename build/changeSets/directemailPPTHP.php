<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class directemailPPTHP extends cbupdaterWorker {
	
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$em = new VTEventsManager($adb);
			$em->registerHandler('vtiger.entity.aftersave', 'modules/Emails/evcbrcHandler.php', 'evcbrcHandler');
			
			$modPrj = Vtiger_Module::getInstance('Project');
			$modPrjTsk = Vtiger_Module::getInstance('ProjectTask');
			$modPot = Vtiger_Module::getInstance('Potentials');
			$modHD = Vtiger_Module::getInstance('HelpDesk');
			$modEmail = Vtiger_Module::getInstance('Emails');
			
			$modPrj->setRelatedList($modEmail, 'Emails', Array('add'), 'get_emails');
			$modPrjTsk->setRelatedList($modEmail, 'Emails', Array('add'), 'get_emails');
			$modPot->setRelatedList($modEmail, 'Emails', Array('add'), 'get_emails');
			$modHD->setRelatedList($modEmail, 'Emails', Array('add'), 'get_emails');
			
			$block = VTiger_Block::getInstance('LBL_PROJECT_INFORMATION', $modPrj);
			$field = Vtiger_Field::getInstance('email',$modPrj);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$field = new Vtiger_Field();
				$field->name = 'email';
				$field->uitype = 13;
				$field->label = 'Email';
				$field->columntype = 'VARCHAR(150)';
				$field->typeofdata = 'E~O';
				$field->displaytype = 2;
				$block->addField($field);
			}

			$block = VTiger_Block::getInstance('LBL_PROJECT_TASK_INFORMATION', $modPrjTsk);
			$field = Vtiger_Field::getInstance('email',$modPrjTsk);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$field = new Vtiger_Field();
				$field->name = 'email';
				$field->uitype = 13;
				$field->label = 'Email';
				$field->columntype = 'VARCHAR(150)';
				$field->typeofdata = 'E~O';
				$field->displaytype = 2;
				$block->addField($field);
			}

			$block = VTiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $modPot);
			$field = Vtiger_Field::getInstance('email',$modPot);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$field = new Vtiger_Field();
				$field->name = 'email';
				$field->uitype = 13;
				$field->label = 'Email';
				$field->columntype = 'VARCHAR(150)';
				$field->typeofdata = 'E~O';
				$field->displaytype = 2;
				$block->addField($field);
			}

			$block = VTiger_Block::getInstance('LBL_TICKET_INFORMATION', $modHD);
			$field = Vtiger_Field::getInstance('email',$modHD);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$field = new Vtiger_Field();
				$field->name = 'email';
				$field->uitype = 13;
				$field->label = 'Email';
				$field->columntype = 'VARCHAR(150)';
				$field->typeofdata = 'E~O';
				$field->displaytype = 2;
				$block->addField($field);
			}

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}