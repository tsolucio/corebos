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

class moveCalendarHomeWidget2cbCalendar extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$this->ExecuteQuery("update vtiger_homedefault set setype='cbCalendar' where setype='Calendar'", array());
			$this->ExecuteQuery("update vtiger_homemodule set setype='cbCalendar' where setype='Calendar'", array());
			$this->ExecuteQuery("update vtiger_homemodule set modulename='cbCalendar' where modulename='Calendar'", array());
			$this->ExecuteQuery("update vtiger_homemoduleflds set fieldname='vtiger_activity:dtstart:dtstart:cbCalendar_Start_Date_and_Time:DT'
				where fieldname='vtiger_activity:date_start:date_start:cbCalendar_Start_Date_and_Time:DT'", array());
			$this->ExecuteQuery("update vtiger_homemoduleflds set fieldname='vtiger_activity:dtend:dtend:cbCalendar_Due_Date:DT'
				where fieldname='vtiger_activity:date_end:date_end:cbCalendar_Due_Date:DT'", array());
			$this->ExecuteQuery("update vtiger_homemoduleflds set fieldname=replace(fieldname,':Calendar',':cbCalendar')", array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
?>
