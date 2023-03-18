<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class updateDetailViewWidgetSequence extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$rs = $adb->pquery('select businessactionsid, sequence from vtiger_businessactions inner join vtiger_crmentity on crmid=businessactionsid where deleted=0 and elementtype_action=?', array('DETAILVIEWWIDGET'));
			$noofrows = $adb->num_rows($rs);
			if ($noofrows > 0) {
				for ($i=0; $i < $noofrows; $i++) {
					$businessactionsid = $adb->query_result($rs, $i, 'businessactionsid');
					$sequence = $adb->query_result($rs, $i, 'sequence');
					$sequence = $sequence + 1;//increase seq + 1 due to condition changes, so we save the same order that the BA has actually
					$adb->pquery('update vtiger_businessactions set sequence=? where businessactionsid=?', array($sequence, $businessactionsid));
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}