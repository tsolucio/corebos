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

class separateCreateAndEditPermissions extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$this->ExecuteQuery("INSERT INTO vtiger_actionmapping values(7,'CreateView',0)");
			$tabid = Array(); 
			$tab_res = $adb->query('SELECT distinct tabid,isentitytype FROM vtiger_tab');
			$noOfTabs = $adb->num_rows($tab_res);
			for($i=0;$i<$noOfTabs;$i++) {
				$tabid[] = array(
					'tabid'=>$adb->query_result($tab_res,$i,'tabid'),
					'entity'=>$adb->query_result($tab_res,$i,'isentitytype'),
					);
			}
			$profile_sql = $adb->query("select profileid from vtiger_profile");
			$num_profile = $adb->num_rows($profile_sql);
			for($i=0;$i<$num_profile;$i++) {
				$profile_id = $adb->query_result($profile_sql,$i,'profileid');
				for($j=0;$j<$noOfTabs;$j++) {
					if ($tabid[$j]['entity']) {
						$this->ExecuteQuery('insert into vtiger_profile2standardpermissions values(?,?,?,?)', array($profile_id, $tabid[$j]['tabid'], 7, 0));
					}
				}
			}
			create_tab_data_file();
			$this->ExecuteQuery("DROP TABLE IF EXISTS its4you_calendar4you_profilespermissions");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}
