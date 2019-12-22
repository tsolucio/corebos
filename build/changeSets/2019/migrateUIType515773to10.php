<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class migrateUIType515773to10 extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$uitypes = array('51'=>'Accounts', '57'=>'Contacts', '73'=>'Accounts');
			foreach ($uitypes as $uitype => $mod) {
				$rsuit = $adb->pquery('select fieldid from vtiger_field where uitype=?', array($uitype));
				while ($uiuit = $adb->fetch_row($rsuit)) {
					$fld = Vtiger_Field::getInstance($uiuit['fieldid']);
					$this->ExecuteQuery('update vtiger_field set uitype=? where fieldid=?', array('10', $uiuit['fieldid']));
					$fld->setRelatedModules(array($mod));
				}
				$rswsuit = $adb->pquery('select fieldtypeid from vtiger_ws_fieldtype where uitype=?', array($uitype));
				$fldtid = $adb->query_result($rswsuit, 0, 0);
				$this->ExecuteQuery('DELETE FROM `vtiger_ws_fieldtype` WHERE `vtiger_ws_fieldtype`.`uitype` = ?;', array($uitype));
				$this->ExecuteQuery(
					"DELETE FROM `vtiger_ws_referencetype` WHERE `vtiger_ws_referencetype`.`fieldtypeid` = ? AND `vtiger_ws_referencetype`.`type` = '$mod'",
					array($fldtid)
				);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
