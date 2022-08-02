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

class deactivateBCaseAutocalculations extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			global $adb;
			// Update fields
			$modname = 'cbBCase';
			$fieldArray = array('actualcost', 'actualrevenue', 'actualroi');
			$module = Vtiger_Module::getInstance($modname);
			foreach ($fieldArray as $field) {
				$fld_ref = Vtiger_Field::getInstance($field, $module);
				$this->ExecuteQuery("UPDATE vtiger_field SET displaytype='4' WHERE fieldid={$fld_ref->id}");
			}
			// delete Link
			$action = array(
				'menutype' => 'item',
				'title' => 'Recalculate',
				'href' => 'javascript:cbbcrecalculate($RECORD$);',
				'icon' => '{"library":"utility", "icon":"formula"}',
			);
			BusinessActions::deleteLink($module->id, 'DETAILVIEWBASIC', $action['title'], $action['href'], $action['icon'], 0, null, true, 0);

			// unregister Handlers
			$ev = new VTEventsManager($adb);
			$ev->unregisterHandler('cbBCaseHandler');
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			global $adb;
			//revert fields
			$modname = 'cbBCase';
			$fieldArray = array('actualcost', 'actualrevenue', 'actualroi');
			$module = Vtiger_Module::getInstance($modname);
			foreach ($fieldArray as $field) {
				$fld_ref = Vtiger_Field::getInstance($field, $module);
				$this->ExecuteQuery("UPDATE vtiger_field SET displaytype='2' WHERE fieldid={$fld_ref->id}");
			}

			// add Link
			$action = array(
				'menutype' => 'item',
				'title' => 'Recalculate',
				'href' => 'javascript:cbbcrecalculate($RECORD$);',
				'icon' => '{"library":"utility", "icon":"formula"}',
			);
			BusinessActions::addLink($module->id, 'DETAILVIEWBASIC', $action['title'], $action['href'], $action['icon'], 0, null, true, 0);

			// register Handlers
			$ev = new VTEventsManager($adb);
			$ev->registerHandler('vtiger.entity.aftersave', 'modules/cbBCase/cbBCaseHandler.php', 'cbBCaseHandler');
			$ev->registerHandler('corebos.entity.link.after', 'modules/cbBCase/cbBCaseHandler.php', 'cbBCaseHandler');

			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
		} else {
			$this->sendMsg('Changeset ' . get_class($this) . ' not applied!');
		}
		$this->finishExecution();
	}
}
