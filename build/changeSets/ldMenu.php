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

class ldMenu extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			$toinstall = array('evvtMenu');
			foreach ($toinstall as $module) {
				if ($this->isModuleInstalled($module)) {
					vtlib_toggleModuleAccess($module, true);
					$this->sendMsg("$module activated!");
				} else {
					$this->ExecuteQuery("CREATE TABLE IF NOT EXISTS `vtiger_evvtmenu` (
							`evvtmenuid` int(11) NOT NULL AUTO_INCREMENT,
							`mtype` varchar(25) NOT NULL,
							`mvalue` varchar(200) NOT NULL,
							`mlabel` varchar(200) NOT NULL,
							`mparent` int(11) NOT NULL,
							`mseq` smallint(6) NOT NULL,
							`mvisible` tinyint(4) NOT NULL,
							`mpermission` varchar(250) NOT NULL,
							PRIMARY KEY (`evvtmenuid`),
							KEY `mparent` (`mparent`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
					$this->installManifestModule($module);
				}
			}
			$this->sendMsg('This changeset eliminates the Menu Settings section');
			$this->sendMsg("From now on you must go to Menu Editor link either in the Settings Menu or on the administrator's quick access dropdown menu");
			$this->ExecuteQuery('UPDATE vtiger_settings_field SET active=? WHERE vtiger_settings_field.name = ?', array('1', 'LBL_MENU_EDITOR'));
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}