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

class UserHourStartFieldsPL16 extends cbupdaterWorker {

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
			$this->ExecuteQuery("delete from vtiger_picklist where name='hour_format'");
			$this->ExecuteQuery("delete from vtiger_picklist where name='start_hour'");
			$field = Vtiger_Field::getInstance('hour_format',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set presence=2,uitype=16 where fieldid=?',array($field->id));
			$field->setPicklistValues(array('am/pm','12','24'));
			$start_hour = Vtiger_Field::getInstance('start_hour',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set presence=2,uitype=16 where fieldid=?',array($start_hour->id));
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}