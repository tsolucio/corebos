<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class undo_wsreferencetype31insert extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			// This changeset undoes an insert into vtiger_ws_referencetype which was incorrectly added in 
			// changeset cleandatabase_140: cleanoptimizedatabase_140.php and adds it into the correct place 
			// related to uitype 66 calendar related to
			// we also add vendors to uitype 66 because we added support for this a few commits back
			$this->ExecuteQuery('DELETE FROM vtiger_ws_referencetype where fieldtypeid=? and type=?', array(31,'Campaigns'));
			$rsft = $adb->query('select fieldtypeid from vtiger_ws_fieldtype where uitype=66');
			$ftype = $adb->query_result($rsft,0,0);
			$wscmp = $adb->query("select * from vtiger_ws_referencetype where fieldtypeid=$ftype and type='Campaigns'");
			if (!($wscmp and $adb->num_rows($wscmp)==1)) {
				$this->ExecuteQuery('INSERT INTO vtiger_ws_referencetype VALUES (?,?)', array($ftype,'Campaigns'));
			}
			$wscmp = $adb->query("select * from vtiger_ws_referencetype where fieldtypeid=$ftype and type='Vendors'");
			if (!($wscmp and $adb->num_rows($wscmp)==1)) {
				$this->ExecuteQuery('INSERT INTO vtiger_ws_referencetype VALUES (?,?)', array($ftype,'Vendors'));
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
