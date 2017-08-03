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

class syncSeactivityRelWithAllRelIdOnCbCalendar extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$res_cal = $adb->pquery("SELECT activityid,rel_id FROM vtiger_activity",array());
			$noofrows = $adb->num_rows($res_cal);
			for ($i = 0; $i < $noofrows; $i++) {
				$activityid = '0';
				$rel_id = '0';
				$activityid = $adb->query_result($res_cal,$i,'activityid');
				$rel_id = $adb->query_result($res_cal,$i,'rel_id');
				//Insert into seactivity rel
				if($rel_id != '' && $rel_id != '0') {
					$res_rel = $adb->pquery('SELECT * FROM vtiger_seactivityrel WHERE activityid = ?',array($activityid));
					if($adb->num_rows($res_rel) > 0) {
						$adb->pquery('UPDATE vtiger_seactivityrel SET crmid = ? WHERE activityid = ?',array($rel_id,$activityid));
						$this->sendMsg('UPDATE RELATION ACTIVITYID: '.$activityid.' WITH REL_ID: '.$rel_id);
					} else {
						$adb->pquery('insert into vtiger_seactivityrel(crmid,activityid) values(?,?)',array($rel_id,$activityid));
						$this->sendMsg('ADD RELATION ACTIVITYID: '.$activityid.' WITH REL_ID: '.$rel_id);
					}
				} elseif ($rel_id=='' || $rel_id=='0') {
					$res_rel = $adb->pquery('SELECT * FROM vtiger_seactivityrel WHERE activityid = ?',array($activityid));
					if($adb->num_rows($res_rel) > 0) {
						$adb->pquery("DELETE from vtiger_seactivityrel where activityid = ?", array($activityid));
						$this->sendMsg('DELETE RELATION ACTIVITYID: '.$activityid);
					}
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
