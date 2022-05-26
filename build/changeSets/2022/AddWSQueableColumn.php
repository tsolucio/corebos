<?php
/*************************************************************************************************
 * Copyright 2022 Spike, JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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

class AddWSQueableColumn extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			global $adb;
			$cnmsg = $adb->getColumnNames('vtiger_ws_operation');
			if (!in_array('queable', $cnmsg)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_ws_operation` ADD `queable` int DEFAULT 0;');
			}
			$qwsops = "('create','update','describe','convertlead','revise','getRelatedModulesInfomation','ExecuteWorkflow','MassDelete','upsert','ExecuteWorkflowWithContext','gendoc_convert','MassUpdate','MassCreate')";
			$this->ExecuteQuery('UPDATE `vtiger_ws_operation` set queable=1 where name in '.$qwsops);
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}