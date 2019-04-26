<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class migrateUIType7581to10 extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$rs75 = $adb->pquery('select fieldid from vtiger_field where uitype=?', array('75'));
			while ($ui75 = $adb->fetch_row($rs75)) {
				//$mod = Vtiger_Module::getInstance($ui75['tabid']);
				$fld = Vtiger_Field::getInstance($ui75['fieldid']);
				$this->ExecuteQuery('update vtiger_field set uitype=? where fieldid=?', array('10', $ui75['fieldid']));
				$fld->setRelatedModules(array('Vendors'));
			}
			$rsws75 = $adb->pquery('select fieldtypeid from vtiger_ws_fieldtype where uitype=?', array('75'));
			$fldtid = $adb->query_result($rsws75, 0, 0);
			$this->ExecuteQuery("DELETE FROM `vtiger_ws_fieldtype` WHERE `vtiger_ws_fieldtype`.`uitype` = 75;", array());
			$this->ExecuteQuery(
				"DELETE FROM `vtiger_ws_referencetype` WHERE `vtiger_ws_referencetype`.`fieldtypeid` = ? AND `vtiger_ws_referencetype`.`type` = 'Vendors'",
				array($fldtid)
			);
			$rs81 = $adb->pquery('select fieldid from vtiger_field where uitype=?', array('81'));
			while ($ui81 = $adb->fetch_row($rs81)) {
				//$mod = Vtiger_Module::getInstance($ui81['tabid']);
				$fld = Vtiger_Field::getInstance($ui81['fieldid']);
				$this->ExecuteQuery('update vtiger_field set uitype=? where fieldid=?', array('10', $ui81['fieldid']));
				$fld->setRelatedModules(array('Vendors'));
			}
			$rsws81 = $adb->pquery('select fieldtypeid from vtiger_ws_fieldtype where uitype=?', array('81'));
			$fldtid = $adb->query_result($rsws81, 0, 0);
			$this->ExecuteQuery("DELETE FROM `vtiger_ws_fieldtype` WHERE `vtiger_ws_fieldtype`.`uitype` = 81;", array());
			$this->ExecuteQuery(
				"DELETE FROM `vtiger_ws_referencetype` WHERE `vtiger_ws_referencetype`.`fieldtypeid` = ? AND `vtiger_ws_referencetype`.`type` = 'Vendors'",
				array($fldtid)
			);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
