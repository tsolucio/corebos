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
include_once 'modules/AutoNumberPrefix/AutoNumberPrefix.php';

class migrateAutonumberInc extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$justInstalled = coreBOS_Settings::getSetting('AutoNumberPrefixJustInstalled', 0);
			if ($justInstalled) {
				coreBOS_Settings::delSetting('AutoNumberPrefixJustInstalled');
				$this->sendMsgError('Changeset '.get_class($this).' CANNOT be applied! Please try again.');
			} else {
				global $adb;
				$assignedtoid = Users::getActiveAdminId();

				$an = $adb->pquery(
					'SELECT 1 FROM vtiger_autonumberprefix WHERE semodule=? AND prefix=?',
					array('AutoNumberPrefix', 'ANPx-')
				);
				if ($an && $adb->num_rows($an)==0) {
					$focus = new AutoNumberPrefix();
					$focus->id ='';
					$focus->mode = '';
					$focus->column_fields['prefix'] = 'ANPx-';
					$focus->column_fields['semodule'] = 'AutoNumberPrefix';
					$focus->column_fields['format'] = '00001';
					$focus->column_fields['active'] = 1;
					$focus->column_fields['isworkflowexpression'] = 0;
					$focus->column_fields['current'] = 1;
					$focus->column_fields['default1'] = '1';
					$focus->column_fields['assigned_user_id'] = $assignedtoid;
					$focus->save('AutoNumberPrefix');
					$adb->pquery(
						'update vtiger_autonumberprefix set autonumberprefixno=? where autonumberprefixid=?',
						array('ANPx-00000', $focus->id)
					);
					$this->sendMsg('Prefix for AutoNumberPrefix created');
				}

				$res=$adb->query('SELECT num_id,prefix,semodule,start_id,active,cur_id from vtiger_modentity_num');
				while ($rows=$adb->fetch_array($res)) {
					$an = $adb->pquery(
						'SELECT 1 FROM vtiger_autonumberprefix WHERE semodule=? AND prefix=?',
						array($rows['semodule'], $rows['prefix'])
					);
					if ($an && $adb->num_rows($an)==0) {
						$focus = new AutoNumberPrefix();
						$focus->id ='';
						$focus->mode = '';
						$focus->column_fields['prefix'] = $rows['prefix'];
						$focus->column_fields['semodule'] = $rows['semodule'];
						$focus->column_fields['format'] = $rows['start_id'];
						$focus->column_fields['active'] = $rows['active'];
						$focus->column_fields['isworkflowexpression'] = 0;
						$focus->column_fields['current'] = $rows['cur_id'];
						$focus->column_fields['default1'] = '1';
						$focus->column_fields['assigned_user_id'] = $assignedtoid;
						$focus->save('AutoNumberPrefix');
						$this->sendMsg('Prefix for '.$rows['semodule'].' created');
					}
				}
				$this->sendMsg('Changeset '.get_class($this).' applied!');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}
