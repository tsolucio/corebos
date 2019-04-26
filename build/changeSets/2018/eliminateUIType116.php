<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class eliminateUIType116 extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("CREATE TABLE `vtiger_end_hour` (
				`end_hourid` int(11) NOT NULL,
				`end_hour` varchar(20) NOT NULL,
				`presence` int(1) NOT NULL DEFAULT '1',
				`sortorderid` int(11) NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$this->ExecuteQuery(
				"INSERT INTO `vtiger_end_hour` (`end_hourid`, `end_hour`, `presence`, `sortorderid`) VALUES
				(1, '00:00', 1, 363),
				(2, '01:00', 1, 364),
				(3, '02:00', 1, 365),
				(4, '03:00', 1, 366),
				(5, '04:00', 1, 367),
				(6, '05:00', 1, 368),
				(7, '06:00', 1, 369),
				(8, '07:00', 1, 370),
				(9, '08:00', 1, 371),
				(10, '09:00', 1, 372),
				(11, '10:00', 1, 373),
				(12, '11:00', 1, 374),
				(13, '12:00', 1, 375),
				(14, '13:00', 1, 376),
				(15, '14:00', 1, 377),
				(16, '15:00', 1, 378),
				(17, '16:00', 1, 379),
				(18, '17:00', 1, 380),
				(19, '18:00', 1, 381),
				(20, '19:00', 1, 382),
				(21, '20:00', 1, 383),
				(22, '21:00', 1, 384),
				(23, '22:00', 1, 385),
				(24, '23:00', 1, 386);"
			);
			$this->ExecuteQuery("ALTER TABLE `vtiger_end_hour` ADD PRIMARY KEY (`end_hourid`);");
			$this->ExecuteQuery("ALTER TABLE `vtiger_end_hour` MODIFY `end_hourid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;");
			$this->ExecuteQuery("UPDATE `vtiger_field` SET uitype='16' WHERE columnname = 'end_hour' and tablename='vtiger_users' and uitype='116';");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
