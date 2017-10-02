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

class UserHourStartFieldsList extends cbupdaterWorker {
	
	// on some installs the fields are present but not the picklist so I force the picklist creation for those
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$moduleInstance = Vtiger_Module::getInstance('Users');
			$block = Vtiger_Block::getInstance('LBL_CALENDAR_SETTINGS', $moduleInstance);
			if (!$block) {
				$block = new Vtiger_Block();
				$block->label = 'LBL_CALENDAR_SETTINGS';
				$block->sequence = 2;
				$moduleInstance->addBlock($block);
			}
			$this->ExecuteQuery('drop table if exists vtiger_hour_format');
			$this->ExecuteQuery('drop table if exists vtiger_start_hour');
			$field = Vtiger_Field::getInstance('hour_format',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2,uitype=16 where fieldid=?',array($field->id));
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hour_format';
				$field->label = 'Calendar Hour Format';
				$field->table ='vtiger_users';
				$field->column = 'hour_format';
				$field->columntype = 'varchar(4)';
				$field->typeofdata = 'V~O';
				$field->uitype = '16';
				$field->masseditable = '0';
				$block->addField($field);
			}
			$field->setPicklistValues(array('12','24'));
			$start_hour = Vtiger_Field::getInstance('start_hour',$moduleInstance);
			if ($start_hour) {
				$this->ExecuteQuery('update vtiger_field set presence=2,uitype=16 where fieldid=?',array($start_hour->id));
			} else {
				$start_hour = new Vtiger_Field();
				$start_hour->name = 'start_hour';
				$start_hour->label = 'Day starts at';
				$start_hour->table ='vtiger_users';
				$start_hour->column = 'start_hour';
				$start_hour->columntype = 'varchar(5)';
				$start_hour->typeofdata = 'V~O';
				$start_hour->uitype = '16';
				$start_hour->masseditable = '0';
				$block->addField($start_hour);
			}
			$start_hour->setPicklistValues(array('00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00'
				,'12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00'));
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}