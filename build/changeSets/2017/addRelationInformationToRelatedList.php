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

class addRelationInformationToRelatedList extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$columns = $adb->getColumnNames('vtiger_relatedlists');
			if (!in_array('relationfieldid', $columns)) {
				$this->ExecuteQuery('ALTER TABLE vtiger_relatedlists ADD COLUMN relationfieldid INT(11) DEFAULT NULL', array());
			}
			if (!in_array('relationtype', $columns)) {
				$this->ExecuteQuery('ALTER TABLE vtiger_relatedlists ADD COLUMN relationtype varchar(3) DEFAULT NULL', array());
			}

			// Update relation field for existing relationships
			$ignoreRelationFieldMapping = array('Emails');
			$query = 'SELECT * FROM vtiger_relatedlists ORDER BY tabid';
			$result = $adb->pquery($query, array());
			$num_rows = $adb->num_rows($result);
			$updateQuery = 'UPDATE vtiger_relatedlists SET relationfieldid=? WHERE relation_id=?';
			for ($i=0; $i<$num_rows; $i++) {
				$tabId = $adb->query_result($result, $i, 'tabid');
				$relatedTabid = $adb->query_result($result, $i, 'related_tabid');
				$relationId = $adb->query_result($result, $i, 'relation_id');
				$primaryModuleInstance = Vtiger_Module::getInstance($tabId);
				$relatedModuleInstance = Vtiger_Module::getInstance($relatedTabid);

				if (empty($relatedModuleInstance)) {
					continue;
				}

				$primaryModuleName = $primaryModuleInstance->name;
				$relatedModuleName = $relatedModuleInstance->name;

				if (in_array($relatedModuleName, $ignoreRelationFieldMapping)) {
					continue;
				}
				$relatedModuleReferenceFields = $relatedModuleInstance->getFieldsByType('reference');
				foreach ($relatedModuleReferenceFields as $fieldModel) {
					$referenceList = $fieldModel->getReferenceList(false);
					if (in_array($primaryModuleName, $referenceList)) {
						$this->ExecuteQuery($updateQuery, array($fieldModel->id, $relationId));
						break;
					}
				}
			}
			// Migrating existing relations to N:N or 1:N based on relation fieldid
			$query = "UPDATE vtiger_relatedlists SET relationtype='N:N' WHERE relationfieldid IS NULL";
			$result = $this->ExecuteQuery($query, array());

			$query = "UPDATE vtiger_relatedlists SET relationtype='1:N' WHERE relationfieldid IS NOT NULL";
			$result = $this->ExecuteQuery($query, array());

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
