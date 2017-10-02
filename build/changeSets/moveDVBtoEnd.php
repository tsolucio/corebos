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

class moveDVBtoEnd extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$rs = $adb->query("select * from vtiger_links where linktype='DETAILVIEWWIDGET' and linkurl like 'block://%' order by tabid,sequence");
			$tabid = 0;
			while ($link = $adb->fetch_array($rs)) {
				if ($link['tabid']!=$tabid) {
					$tabid = $link['tabid'];
					$rsseq = $adb->pquery('select max(sequence) from vtiger_blocks where tabid=?',array($tabid));
					$cnt = $adb->query_result($rsseq,0,0)+1;
				}
				$this->ExecuteQuery('update vtiger_links set sequence=? where linkid=?',array($cnt,$link['linkid']));
				$cnt++;
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(); // this should not be done if changeset is Continuous
		}
		$this->finishExecution();
	}
}