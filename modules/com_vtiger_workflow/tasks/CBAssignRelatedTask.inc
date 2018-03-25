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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';

class CBAssignRelatedTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('relmodule', 'username');
	}

	public function doTask(&$entity) {
		global $adb, $log, $current_user;
		list($cto,$cto_id) = explode('x', $entity->getId());
		if (!empty($cto_id) && !empty($this->relmodule) && !empty($this->username)) {
			$module = getSalesEntityType($cto_id);
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $this->relmodule);
			$handlerClass = $webserviceObject->getHandlerClass();
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$meta = $handler->getMeta();
			$refflds = $meta->getReferenceFieldDetails();
			$reffield = '';
			foreach ($refflds as $reffld => $refmods) {
				if (in_array($module, $refmods)) {
					$reffield = $reffld;
					break;
				}
			}
			if ($reffield != '') {
				$queryGenerator = new QueryGenerator($this->relmodule, $current_user);
				$queryGenerator->setFields(array('id'));
				$queryGenerator->addReferenceModuleFieldCondition($module, $reffield, 'id', $cto_id, 'e');
				$query = $queryGenerator->getQuery();
				if ($this->username=='assigneduser') {
					$usrquery = $adb->pquery('SELECT smownerid FROM vtiger_crmentity WHERE crmid=?', array($cto_id));
					$usr = $adb->query_result($usrquery, 0, 0);
				} else {
					$usr = $this->username;
				}
				$updquery = 'UPDATE vtiger_crmentity set smownerid=? where crmid=?';
				$relrecs = $adb->query($query);
				for ($relrec=0; $relrec<$adb->num_rows($relrecs); $relrec++) {
					// direct assignment to avoid relaunching workflows > I am not sure this is correct (!)
					$adb->pquery($updquery, array($usr, $adb->query_result($relrecs, $relrec, 0)));
				}
			}
		}
	}
}
?>