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
require_once 'modules/cbMap/cbMap.php';

class CBDeleteRelatedTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('relmodule', 'bmapid', 'bmapid_display');
	}

	public function doTask(&$entity) {
		list($wsid,$crmid) = explode('x', $entity->getId());
		if (!empty($crmid) && !empty($this->relmodule)) {
			self::deleteRelatedRecords($crmid, $this->relmodule, $this->bmapid);
		}
	}

	public static function deleteRelatedRecords($masterRecord, $relatedModule, $bmapid) {
		global $adb,$log,$current_user;
		$module = getSalesEntityType($masterRecord);

		// Business Map
		$focus = new cbMap();
		$focus->id = $bmapid;
		$focus->mode = '';
		$focus->retrieve_entity_info($bmapid, 'cbMap');

		$businessMap = $focus->column_fields['maptype'];

		$crmids = array();

		// Checking relation
		$moduleId = getTabid($module);
		$relatedModuleId = getTabid($relatedModule);

		// WebserviceID
		$entityId = vtws_getEntityId($relatedModule);

		$sql = "select name from vtiger_relatedlists where tabid = ? and related_tabid = ? and relationtype = 'N:N'";
		$result = $adb->pquery($sql, array($moduleId, $relatedModuleId));

		if ($result && $adb->num_rows($result) > 0) {
			$relation_result_row = $adb->fetch_array($result);
			switch ($relation_result_row['name']) {
				case 'get_related_list':
					if ($businessMap == "Condition Query") {
						$crmids = $focus->ConditionQuery($masterRecord);
						$adb->pquery(
							'delete from vtiger_crmentityrel where (crmid in ('. generateQuestionMarks($crmids) .') and relmodule = ?) or (relcrmid = ? and module = ?)',
							array($crmids, $relatedModule, $masterRecord, $relatedModule)
						);
					} else if ($businessMap == "Condition Expression") {
						// Get related Modules
						$sql = "select relcrmid from vtiger_crmentityrel where crmid = ? and relmodule = ?";
						$result = $adb->pquery($sql, array($masterRecord, $relatedModule));
						if ($result && $adb->num_rows($result) > 0) {
							while ($related = $adb->fetch_array($result)) {
								if ($focus->ConditionExpression($entityId.'x'.$related['relcrmid'])) {
									$adb->pquery(
										'delete from vtiger_crmentityrel where relcrmid = ? and module = ?',
										array($related['relcrmid'], $relatedModule)
									);
								}
							}
						}
					}
					else {
						$adb->pquery(
							'delete from vtiger_crmentityrel where (crmid = ? and relmodule = ?) or (relcrmid = ? and module = ?)',
							array($masterRecord, $relatedModule, $masterRecord, $relatedModule)
						);
					}
					break;

				case 'get_attachments':
					if ($businessMap == "Condition Query") {
						$crmids = $focus->ConditionQuery($masterRecord);
						$adb->pquery('delete from vtiger_senotesrel where crmid in ('. generateQuestionMarks($crmids) .')', array($crmids));
					} else if ($businessMap == "Condition Expression") {
						// Get related Documents
						$sql = "select notesid from vtiger_senotesrel where crmid = ?";
						$result = $adb->pquery($sql, array($masterRecord));
						if ($result && $adb->num_rows($result) > 0) {
							while ($related = $adb->fetch_array($result)) {
								if ($focus->ConditionExpression($entityId.'x'.$related['notesid'])) {
									$adb->pquery('delete from vtiger_senotesrel where notesid = ?', array($related['notesid']));
								}
							}
						}
					} else {
						$adb->pquery('delete from vtiger_senotesrel where crmid = ?', array($masterRecord));
					}
					break;

				case 'get_activities':
					if ($businessMap == "Condition Query") {
						$crmids = $focus->ConditionQuery($masterRecord);
						$adb->pquery('delete from vtiger_seactivityrel where crmid in ('. generateQuestionMarks($crmids) .')', array($crmids));
					} else if ($businessMap == "Condition Expression") {
						// Get related Activities
						$sql = "select activityid from vtiger_seactivityrel where crmid = ?";
						$result = $adb->pquery($sql, array($masterRecord));
						if ($result && $adb->num_rows($result) > 0) {
							while ($related = $adb->fetch_array($result)) {
								if ($focus->ConditionExpression($entityId.'x'.$related['activityid'])) {
									$adb->pquery('delete from vtiger_seactivityrel where activityid = ?', array($related['activityid']));
								}
							}
						}
					} else {
						$adb->pquery('delete from vtiger_seactivityrel where crmid = ?', array($masterRecord));
					}
					break;

				default:
				// To do.
					break;
			}
		} else {
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $relatedModule);
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
			$relrecs = $adb->query($query);

			for ($relrec=0; $relrec<$adb->num_rows($relrecs); $relrec++) {
				// direct delete to avoid relaunching workflows > I am not sure this is correct (!)
				if ($businessMap == "Condition Query") {
					$crmids = $focus->ConditionQuery($masterRecord);
					if (in_array($adb->query_result($relrecs, $relrec, 0), $crmids)) {
						$adb->pquery($updquery, array($adb->query_result($relrecs, $relrec, 0)));
					}
				} else if ($businessMap == "Condition Expression") {
					if ($focus->ConditionExpression($webserviceObject->getEntityId().'x'.$adb->query_result($relrecs, $relrec, 0))) {
						$adb->pquery($updquery, array($adb->query_result($relrecs, $relrec, 0)));
					}
				} else {
					$adb->pquery($updquery, array($adb->query_result($relrecs, $relrec, 0)));
				}
			}
		}
	}
}
?>