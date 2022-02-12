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

class migrateDocumentFolders extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb, $current_user;
			$modulename = 'DocumentFolders';
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $modulename);
			if ($webserviceObject->getHandlerClass()=='VtigerActorOperation') {
				$this->sendMsgError('Changeset '.get_class($this).' CANNOT be applied! Please try again.');
			} else {
				$df = CRMEntity::getInstance($modulename);
				$fldrs = $adb->query('select * from vtiger_attachmentsfolder');
				while ($fldr = $adb->fetch_array($fldrs)) {
					$df->column_fields['foldername'] = $fldr['foldername'];
					$df->column_fields['sequence'] = $fldr['sequence'];
					$df->column_fields['description'] = $fldr['description'];
					$df->column_fields['assigned_user_id'] = $current_user->id;
					$df->mode='';
					unset($df->id);
					$df->save('DocumentFolders');
					$adb->pquery('update vtiger_crmentity set smcreatorid=? where crmid=?', array($fldr['createdby'], $df->id));
					$adb->pquery(
						'insert into vtiger_crmentityrel (crmid, module, relcrmid, relmodule) (select ?,?,notesid,? from vtiger_notes where folderid=?)',
						array($df->id, $modulename, 'Documents', $fldr['folderid'])
					);
				}
				$this->sendMsg('Changeset '.get_class($this).' applied!');
				$this->markApplied();
			}
		}
		$this->finishExecution();
	}
}
