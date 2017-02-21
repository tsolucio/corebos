<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class changeUitype68To10 extends cbupdaterWorker {
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$ui68rs = $adb->pquery("select fieldid,name
					from vtiger_field
					inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
					where uitype = '68'");
			while ($ui68 = $adb->fetch_array($ui68rs)) {
				$this->ExecuteQuery("insert into vtiger_fieldmodulerel (fieldid,module,relmodule,status,sequence) values (?,?,'Accounts',null,0)",
					array($ui68['fieldid'],$ui68['name']));
				$this->ExecuteQuery("insert into vtiger_fieldmodulerel (fieldid,module,relmodule,status,sequence) values (?,?,'Contacts',null,1)",
					array($ui68['fieldid'],$ui68['name']));
			}
			$this->ExecuteQuery("UPDATE vtiger_field SET uitype = '10' WHERE uitype = '68'");
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}