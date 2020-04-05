<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'include/MemoryLimitManager/MemoryLimitManager.php';

class fixMailScannerEmailparentid extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb, $current_user;
			$manager = new MemoryLimitManager();
			$phplimit = $manager->getPHPLimitInMegaBytes();
			$manager->setBufferInMegaBytes(100);
			$manager->setLimitInMegaBytes($phplimit);
			$batch = 10000;
			$cnt=1;
			$finished = true;
			$rs = $adb->query("select emailid,idlists from vtiger_emaildetails where idlists like '%@-1%'");
			while ($email = $rs->fetchRow()) {
				$refs = explode('|', $email['idlists']);
				$newref = array();
				$changed = false;
				foreach ($refs as $ref) {
					if (empty($ref)) {
						continue;
					}
					list($crmid, $fieldid) = explode('@', $ref);
					if ($fieldid=='-1') {
						$usrrs = $adb->pquery('select id from vtiger_users where id=?', array($crmid));
						if ($adb->num_rows($usrrs)==0) {
							$referenceHandler = vtws_getModuleHandlerFromId(vtws_getWSID($crmid), $current_user);
							$referenceMeta = $referenceHandler->getMeta();
							$relid = getEmailFieldId($referenceMeta, $crmid);
							$newref[] = "$crmid@$relid";
							$changed = true;
						} else {
							$newref[] = $ref;
						}
					} else {
						$newref[] = $ref;
					}
				}
				if (count($newref)>0 && $changed) {
					$newref = implode('|', $newref).'|';
					$adb->pquery('UPDATE vtiger_emaildetails SET idlists=? WHERE emailid=?', array($newref, $email['emailid']));
				}
				if ($cnt==$batch) {
					$this->sendMsg('BATCH PROCESSED '.$cnt);
				}
				$cnt++;
				if ($manager->isLimitReached()) {
					$this->sendMsgError('This changeset HAS NOT FINISHED. You must launch it again!');
					$finished = false;
					break;
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			if ($finished) {
				$this->markApplied(false);
			}
		}
		$this->finishExecution();
	}
}