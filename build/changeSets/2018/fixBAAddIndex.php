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

class fixBAAddIndex extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$idxrs = $adb->query('SHOW INDEX FROM vtiger_businessactions');
			$idxs = array();
			while ($idx = $adb->fetch_array($idxrs)) {
				$idxs[] = $idx['column_name'];
			}
			if (!in_array('elementtype_action', $idxs)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_businessactions` ADD INDEX(`elementtype_action`);', array());
			}
			if (!in_array('module_list', $idxs)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_businessactions` ADD INDEX(`module_list`);', array());
			}
			if (!in_array('active', $idxs)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_businessactions` ADD INDEX(`active`);', array());
			}
			if (!in_array('acrole', $idxs)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_businessactions` ADD INDEX(`acrole`);', array());
			}
			$mod = Vtiger_Module::getInstance('BusinessActions');
			$fld = Vtiger_Field::getInstance('brmap', $mod);
			if ($fld) {
				$fld->setRelatedModules(array('cbMap'));
				$this->ExecuteQuery('ALTER TABLE `vtiger_businessactions` CHANGE `brmap` `brmap` INT(11) NULL DEFAULT NULL;', array());
			}
			$fld = Vtiger_Field::getInstance('sequence', $mod);
			if ($fld) {
				$this->ExecuteQuery("update vtiger_field set typeofdata='N~O~3~0' where fieldid=?", array($fld->id));
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
