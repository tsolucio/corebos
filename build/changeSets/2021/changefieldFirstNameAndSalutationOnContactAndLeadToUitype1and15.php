<?php
/*************************************************************************************************
 * Copyright 2021 Spike, JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class changefieldFirstNameAndSalutationOnContactAndLeadToUitype1and15 extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			$module1 = Vtiger_Module::getInstance('Contacts');
			$module2 = Vtiger_Module::getInstance('Leads');
			$field1 = Vtiger_Field::getInstance('firstname', $module1);
			$field2 = Vtiger_Field::getInstance('firstname', $module2);
			if ($field1 || $field2) {
				$this->ExecuteQuery("update vtiger_field set uitype=1 where fieldid = ? or fieldid = ?", array($field1->id, $field2->id));
			}
			// change all 255
			$this->ExecuteQuery('update vtiger_field set uitype=1 where uitype=?', array('255'));
			$field3 = Vtiger_Field::getInstance('salutationtype', $module1);
			$field4 = Vtiger_Field::getInstance('salutationtype', $module2);
			if ($field3 || $field4) {
				$this->ExecuteQuery("update vtiger_field set uitype=15 where fieldid = ? or fieldid = ?", array($field3->id, $field4->id));
			}
			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}