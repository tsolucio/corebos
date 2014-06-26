<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class cbupdate_example extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset cbupdate_example already applied!');
		} else {
			// do your magic here
			$this->ExecuteQuery('select 1 from vtiger_cbupdater');
			$this->ExecuteQuery('select 1 from vtiger_cbupder');
			$package = new Vtiger_Package();
			ob_start();
			$rdo = $package->importManifest('build/French/manifest.xml');
			$out = ob_get_contents();
			ob_end_clean();
			$this->sendMsg($out);
			if ($rdo) $this->sendMsg('french installed!');
			else $this->sendMsg('NO french!');
			$this->sendMsg('Changeset cbupdate_example applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			// undo your magic here
			// Deactivate French modules
			vtlib_toggleLanguageAccess('fr_fr',false);
			$this->sendMsg('NO french!');
			$this->sendMsg('Changeset cbupdate_example undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset cbupdate_example not applied!');
		}
		$this->finishExecution();
	}
	
}