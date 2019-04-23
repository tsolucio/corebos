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

class CalendarEnhanceFieldInfoRelatedModules extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$rdo = $adb->pquery('SELECT efid,event,fieldname FROM its4you_calendar4you_event_fields', array());
			while ($rev = $adb->fetch_array($rdo)) {
				$fidrs = $adb->pquery(
					'select fieldid from vtiger_field where tabid=? and fieldname=?',
					array(($rev['event']=='1'?16:9),$rev['fieldname'])
				);
				$fid = $adb->query_result($fidrs, 0, 0);
				$fname = $rev['fieldname'].':'.$fid;
				$this->ExecuteQuery(
					'update its4you_calendar4you_event_fields set fieldname=? where efid=?',
					array($fname,$rev['efid'])
				);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}