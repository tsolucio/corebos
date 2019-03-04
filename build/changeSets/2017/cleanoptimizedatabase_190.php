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

class cleanoptimizedatabase_190 extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery('ALTER TABLE vtiger_field MODIFY COLUMN tablename VARCHAR(100)', array());
			//Increasing Lead Status column size to 200 for Leads module
			$this->ExecuteQuery('ALTER TABLE vtiger_leaddetails MODIFY leadstatus VARCHAR(200)', array());

			// Increase tablabel and setype size
			$this->ExecuteQuery('ALTER TABLE vtiger_tab MODIFY tablabel VARCHAR(100)', array());
			$this->ExecuteQuery('SET foreign_key_checks = 0', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_tab MODIFY name VARCHAR(100)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_customview MODIFY entitytype VARCHAR(100)', array());
			$this->ExecuteQuery('SET foreign_key_checks = 1', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_crmentity MODIFY setype VARCHAR(100)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_portalinfo MODIFY user_password VARCHAR(255)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_mailscanner_ids ADD INDEX messageids_crmid_idx(crmid)', array());
			$this->ExecuteQuery('ALTER TABLE vtiger_activity MODIFY COLUMN subject VARCHAR(255)', array());
			// Change fieldLabel of description field to Description - Project modules.
			$fieldId = getFieldid(getTabid('Project'), 'description');
			if ($fieldId) {
				$fieldModel = Vtiger_Field::getInstance($fieldId);
				$fieldModel->label = 'Description';
				$fieldModel->save();
			}
			$fieldId = getFieldid(getTabid('ProjectMilestone'), 'description');
			if ($fieldId) {
				$fieldModel = Vtiger_Field::getInstance($fieldId);
				$fieldModel->label = 'Description';
				$fieldModel->save();
			}
			$fieldId = getFieldid(getTabid('ProjectTask'), 'description');
			if ($fieldId) {
				$fieldModel = Vtiger_Field::getInstance($fieldId);
				$fieldModel->label = 'Description';
				$fieldModel->save();
			}

			$columns = $adb->getColumnNames('vtiger_modcomments');
			if (in_array('parent_comments', $columns)) {
				$this->ExecuteQuery("UPDATE `vtiger_modcomments` SET `parent_comments`='0' WHERE `parent_comments` is null or `parent_comments` = ''", array());
				$this->ExecuteQuery('ALTER TABLE vtiger_modcomments MODIFY parent_comments INT(19) DEFAULT 0', array());
			}

			// Update Currency symbol for Egypt
			$this->ExecuteQuery('UPDATE vtiger_currencies SET currency_symbol=? WHERE currency_name=?', array('E£', 'Egypt, Pounds'));
			$this->ExecuteQuery('UPDATE vtiger_currency_info SET currency_symbol=? WHERE currency_name=?', array('E£', 'Egypt, Pounds'));

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
