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

class contacts_hierarchylink extends cbupdaterWorker {
	private $label = 'LBL_SHOW_CONTACT_HIERARCHY';
	private $link_module = 'Contacts';
	private $link_type = 'DETAILVIEWBASIC';
	private $link_url = 'index.php?module=Contacts&action=ContactHierarchy&contactid=$RECORD$';
	private $link_order = 5;
	private $link_image = 'themes/images/hierarchy_color16.png';
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$moduleInstance = Vtiger_Module::getInstance($this->link_module);
			$moduleInstance->addLink($this->link_type, $this->label, $this->link_url, $this->link_image,$this->link_order);
			$this->ExecuteQuery("UPDATE vtiger_links SET linkicon='themes/images/hierarchy_color16.png' WHERE linklabel=? and linktype=? and tabid=6",
				array('LBL_SHOW_ACCOUNT_HIERARCHY','DETAILVIEWBASIC'));
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			// undo your magic here
			$moduleInstance = Vtiger_Module::getInstance($this->link_module);
			$moduleInstance->deleteLink($this->link_type, $this->label, $this->link_url);
			$this->sendMsg('Changeset '.get_class($this).' undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}

}