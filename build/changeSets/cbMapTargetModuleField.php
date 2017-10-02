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

class cbMapTargetModuleField extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$moduleInstance = Vtiger_Module::getInstance('cbMap');
			$block = Vtiger_Block::getInstance('LBL_MAP_INFORMATION', $moduleInstance);
			$field = Vtiger_Field::getInstance('targetname',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$field = new Vtiger_Field();
				$field->name = 'targetname';
				$field->label = 'Target Module';
				$field->table ='vtiger_cbmap';
				$field->column = 'targetname';
				$field->columntype = 'varchar(200)';
				$field->typeofdata = 'V~O';
				$field->uitype = '1613';
				$field->masseditable = '0';
				$field->sequence = 4;
				$block->addField($field);
			}
			$field = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set sequence=6 where fieldid=?',array($field->id));
			$field = Vtiger_Field::getInstance('createdtime',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set sequence=7 where fieldid=?',array($field->id));
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}