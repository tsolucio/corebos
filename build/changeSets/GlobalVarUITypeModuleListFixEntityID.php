<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class GlobalVarUITypeModuleListFixEntityID extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			// change uitype and label
			$field = Vtiger_Field::getInstance('module_list',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery("update vtiger_field set uitype=3313,fieldlabel='Module List' where fieldid=?",array($field->id));
			}
			// convert all existing records to new format
			$gvrs = $adb->pquery('select globalvariableid,module_list from vtiger_globalvariable', array());
			$updsql = 'update vtiger_globalvariable set module_list=? where globalvariableid=?';
			while ($gv = $adb->fetch_array($gvrs)) {
				if (trim($gv['module_list'])!='') {
					$ml = array_map('trim', explode(',', $gv['module_list']));
					$ml = implode(' |##| ', $ml);
					$this->ExecuteQuery($updsql,array($ml,$gv['globalvariableid']));
				}
			}
			// fix incorrect entiyidentifier
			$updsql = "UPDATE `vtiger_entityname` SET 
				`fieldname`='globalno',
				`entityidfield`='globalvariableid',
				`entityidcolumn`='globalvariableid'
				WHERE `modulename`='GlobalVariable' and `tablename`='vtiger_globalvariable'";
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}