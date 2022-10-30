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

class addQueryTypePicklistTocbQuestion extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$modname = 'cbQuestion';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_cbQuestion_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('querytype', $module);
			if (!$field) {
				$fieldInstance = new Vtiger_Field();
				$fieldInstance->name = 'querytype';
				$fieldInstance->label = 'querytype';
				$fieldInstance->columntype = 'varchar(128)';
				$fieldInstance->uitype = 15;
				$fieldInstance->displaytype = 1;
				$fieldInstance->typeofdata = 'V~O';
				$fieldInstance->quickcreate = 0;
				$fieldInstance->defaultvalue='Web Service';
				$block->addField($fieldInstance);
				$pickListValues = array('Web Service', 'SQL', 'ClickHouse');
				$fieldInstance->setPicklistValues($pickListValues);
			}

			// Updating records
			$adb->pquery('UPDATE vtiger_cbquestion SET querytype=? WHERE sqlquery=?', array('SQL', '1'));

			// Deleting field
			$field = Vtiger_Field::getInstance('sqlquery', $module);
			if ($field) {
				$field->delete();
			}

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
?>
