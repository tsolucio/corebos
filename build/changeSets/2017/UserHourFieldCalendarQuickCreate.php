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

class UserHourFieldCalendarQuickCreate extends cbupdaterWorker {

	// on some installs the fields are present but not the picklist so I force the picklist creation for those
	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
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
			$movethese = array('activity_view','lead_view','hour_format','end_hour','start_hour');
			foreach ($movethese as $fieldname) {
				$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
				$this->ExecuteQuery('update vtiger_field set block=? where fieldid=?', array($block->id,$field->id));
			}
			$this->ExecuteQuery("ALTER TABLE `vtiger_hour_format` CHANGE `picklist_valueid` `sortorderid` INT(11) NOT NULL DEFAULT '0'");
			$field = Vtiger_Field::getInstance('hour_format', $moduleInstance);
			$this->ExecuteQuery('update vtiger_field set presence=0,uitype=16,displaytype=1 where fieldid=?', array($field->id));
			$this->ExecuteQuery("update `vtiger_users` set hour_format='24' where hour_format = ''");
			$this->ExecuteQuery("update `vtiger_users` set hour_format='12' where hour_format != '24'");
			// Calendar Quick Create
			$moduleInstance = Vtiger_Module::getInstance('cbCalendar');
			$activatethese = array('activitytype','subject','assigned_user_id','dtstart','dtend','rel_id','cto_id','eventstatus');
			foreach ($activatethese as $fieldname) {
				$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
				$this->ExecuteQuery('update vtiger_field set quickcreate=0 where quickcreate=1 and fieldid=?', array($field->id));
			}
			$deactivatethese = array('time_end','due_date','time_start','date_start','recurringtype','reminder_time','sendnotification');
			foreach ($deactivatethese as $fieldname) {
				$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
				if ($field) {
					$this->ExecuteQuery("update vtiger_field set quickcreate=1,typeofdata=REPLACE(typeofdata,'~M','~O') where fieldid=?", array($field->id));
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}