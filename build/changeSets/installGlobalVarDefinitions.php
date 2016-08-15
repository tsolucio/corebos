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

class installGlobalVarDefinitions extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$global_variables = array(
				'preload_prototype',
				'preload_jquery',
			);
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			$block = new Vtiger_Block();
			$block->label = 'GVarDefinitions';
			$moduleInstance->addBlock($block);
			if (!empty($block->id)) {
				$this->ExecuteQuery('update vtiger_blocks set display_status=0 where blockid=?',array($block->id));
			}
			$moduleInstance->addLink('LISTVIEWBASIC', 'Definitions', "javascript:gotourl('index.php?module=GlobalVariable&action=GlobalVariableDefinitions&parenttab=Tools')",'',4);
			$field = Vtiger_Field::getInstance('gvname',$moduleInstance);
			if ($field) {
				foreach ($global_variables as $gvar) {
					$sql = 'select * from vtiger_gvname where gvname=?';
					$result = $adb->pquery($sql, array($gvar));
					$origPicklistID = $adb->query_result($result, 0, 'picklist_valueid');
					$sql = 'delete from vtiger_gvname where gvname=?';
					$this->ExecuteQuery($sql, array($gvar));
					$sql = 'delete from vtiger_role2picklist where picklistvalueid=?';
					$this->ExecuteQuery($sql, array($origPicklistID));
					$sql = 'DELETE FROM vtiger_picklist_dependency WHERE sourcevalue=? AND sourcefield=? AND tabid=?';
					$this->ExecuteQuery($sql, array($gvar, 'gvname', $moduleInstance->id));
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
}