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

class addCreatedByField extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError ())
			$this->sendError ();
		if ($this->isApplied ()) {
			$this->sendMsg ( 'Changeset ' . get_class ( $this ) . ' already applied!' );
		} else {
			global $adb;
			$sql = "SELECT tabid,name FROM vtiger_tab WHERE name not in ('Calendar','Events','PBXManager','Webmails','Emails','Integration','Dashboard','ModComments') and isentitytype=1";
			$rs = $adb->pquery($sql, array());
			while ($tab=$adb->fetch_array($rs)) {
				$module = $tab['name'];
				$moduleInstance = Vtiger_Module::getInstance($tab['tabid']);
				if ($moduleInstance) {
					$result = $adb->pquery('select blocklabel from vtiger_blocks where tabid=? and sequence = 1', array($tab['tabid']));
					$block = $adb->query_result($result, 0, 'blocklabel');
					if ($block) {
						$blockInstance = Vtiger_Block::getInstance($block, $moduleInstance);
						$field = Vtiger_Field::getInstance('created_user_id',$moduleInstance);
						if ($field) {
							$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid='.$field->id);
						} else {
						$field = new Vtiger_Field ();
						$field->name = 'created_user_id';
						$field->label = 'Created By';
						$field->table = 'vtiger_crmentity';
						$field->column = 'smcreatorid';
						$field->uitype = 52;
						$field->typeofdata = 'V~O';
						$field->displaytype = 2;
						$field->quickcreate = 3;
						$field->masseditable = 0;
						$blockInstance->addField($field);
						}
						$this->sendMsg("Creator field added for $module <br>");
					}
				} else {
					$this->sendMsg("Unable to find $module instance<br>");
				}
			}
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied ();
		}
		$this->finishExecution ();
	}
}