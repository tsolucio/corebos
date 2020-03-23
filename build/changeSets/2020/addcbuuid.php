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

class addcbuuid extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$cncrm = $adb->getColumnNames('vtiger_crmentity');
			if (!in_array('cbuuid', $cncrm)) {
				$this->ExecuteQuery('ALTER TABLE `vtiger_crmentity` ADD `cbuuid` char(40) default "";');
			}
			$manager = new MemoryLimitManager();
			$phplimit = $manager->getPHPLimitInMegaBytes();
			$manager->setBufferInMegaBytes(100);
			$manager->setLimitInMegaBytes($phplimit);
			$batch = 10000;
			$f = CRMEntity::getInstance('Accounts');
			$rs = $adb->query('select count(*) as cnt from vtiger_crmentity inner join vtiger_tab on setype=name and isentitytype=1 where cbuuid=""');
			$cnt = $rs->fields['cnt'];
			$finished = true;
			for ($loop=0; $loop<=($cnt/$batch); $loop++) {
				$rs=$adb->query('select crmid,setype,smcreatorid,smownerid,createdtime from vtiger_crmentity inner join vtiger_tab on setype=name and isentitytype=1 where cbuuid="" limit '.$batch);
				while ($row = $adb->fetch_array($rs)) {
					$f->column_fields['record_module'] = $row['setype'];
					$f->column_fields['record_id'] = $row['crmid'];
					$f->column_fields['created_user_id'] = $row['smcreatorid'];
					$f->column_fields['assigned_user_id'] = $row['smownerid'];
					//$f->column_fields['description'] = $row['description'];
					$f->column_fields['createdtime'] = $row['createdtime'];
					$adb->query('UPDATE vtiger_crmentity SET cbuuid="'.$f->getUUID().'" WHERE crmid='.$row['crmid']);
				}
				unset($rs);
				$this->sendMsg('CBUUID BATCH PROCESSED '.($loop+1).'x'.$batch);
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