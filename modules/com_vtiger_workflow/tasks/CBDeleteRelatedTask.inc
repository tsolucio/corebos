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
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');

class CBDeleteRelatedTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('relmodule');
	}

	public function doTask(&$entity) {
		list($wsid,$crmid) = explode('x', $entity->getId());
		if (!empty($crmid) and !empty($this->relmodule)) {
			self::deleteRelatedRecords($crmid, $this->relmodule);
		}
	}

	public static function deleteRelatedRecords($masterRecord, $relatedModule) {
		global $adb,$log,$current_user;
		$module = getSalesEntityType($masterRecord);
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $relatedModule);
		$handlerPath = $webserviceObject->getHandlerPath();
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
		$queryGenerator = new QueryGenerator($relatedModule, $current_user);
		$queryGenerator->setFields(array('id'));
		$queryGenerator->addReferenceModuleFieldCondition($module, $reffield, 'id', $masterRecord, 'e');
		$query = $queryGenerator->getQuery();
		$updquery = 'UPDATE vtiger_crmentity set deleted=1 where crmid=?';
		$relrecs = $adb->query($query);
		for ($relrec=0; $relrec<$adb->num_rows($relrecs); $relrec++) {
			// direct delete to avoid relaunching workflows > I am not sure this is correct (!)
			$adb->pquery($updquery, array($adb->query_result($relrecs, $relrec, 0)));
		}
	}
}

?>