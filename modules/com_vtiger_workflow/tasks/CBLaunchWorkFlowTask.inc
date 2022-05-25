<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'data/CRMEntity.php';
require_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/ExecuteWorkflow.php';

class CBLaunchWorkFlowTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array(
			'workflowid', 'workflowid_display', 'record_filter_opt', 'relModlist', 'conditionexpressionmapid',
			'conditionexpressionmapid_display', 'recordsetmapid', 'recordsetmapid_display', 'crmids_list'
		);
	}

	public function doTask(&$entity) {
		global $adb, $currentModule, $from_wf, $logbg;
		$logbg->debug('LaunchWF '.$this->workflowid.' with type '.$this->record_filter_opt);
		switch ($this->record_filter_opt) {
			case 'filterByThese':
				$idslist = explode(',', $this->crmids_list);
				if (!empty($idslist)) {
					$entityids = array();
					foreach ($idslist as $entityid) {
						$entityid = vtws_getWSID($entityid);
						if ($this->validateCRMID($entityid)) {
							$entityids[] = $entityid;
						}
					}
					$this->executeWorkflow($this->workflowid, $entityids, '[]');
				}
				break;
			case 'filterByrelModuleAndConditionExpressionMap':
				list($wsid, $crmid) = explode('x', $entity->getId());
				$parentModule = getSalesEntityType($crmid);
				$focus = CRMEntity::getInstance($parentModule);
				$focus->id = $crmid;
				$focus->retrieve_entity_info($crmid, $parentModule, false, $from_wf);
				global $GetRelatedList_ReturnOnlyQuery;
				$holdValue = $GetRelatedList_ReturnOnlyQuery;
				$GetRelatedList_ReturnOnlyQuery = true;
				$rectabid = getTabid($parentModule);
				$relatedModuleId = getTabid($this->relModlist);
				$relationResult = $adb->pquery(
					'SELECT name FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? limit 1',
					array($rectabid, $relatedModuleId)
				);
				$relationInfo = $adb->fetch_array($relationResult);
				$params = array($crmid, $rectabid, $relatedModuleId);
				$holdCM = $currentModule;
				$currentModule = $parentModule;
				$relationData = call_user_func_array(array($focus, $relationInfo['name']), array_values($params));
				$GetRelatedList_ReturnOnlyQuery = $holdValue;
				$currentModule = $holdCM;
				if (isset($relationData['query'])) {
					$query = $relationData['query'];
					preg_match('/[\t ]from /i', $query, $matches, PREG_OFFSET_CAPTURE);
					$query = 'select vtiger_crmentity.crmid as id'.substr($query, $matches[0][1]);
					$result = $adb->query($query);
					$crmids = array();
					if ($result && $adb->num_rows($result) > 0) {
						$moduleId = vtws_getEntityId($this->relModlist);
						while ($related = $adb->fetch_array($result)) {
							if (!empty($this->conditionexpressionmapid)) {
								$cbMapObject = new cbMap();
								$cbMapObject->id = $this->conditionexpressionmapid;
								$cbMapObject->retrieve_entity_info($this->conditionexpressionmapid, 'cbMap');
								if ($cbMapObject->ConditionExpression($related['id'])) {
									$crmids[] = $moduleId.'x'.$related['id'];
								}
							} else {
								$crmids[] = $moduleId.'x'.$related['id'];
							}
						}
						$this->executeWorkflow($this->workflowid, $crmids, '[]');
					}
				}
				break;
			case 'filterByRecordSetMap':
				require_once 'modules/cbMap/cbMap.php';
				$focus = cbMap::getMapByID($this->recordsetmapid);
				if (!empty($focus)) {
					$maptype = $focus->column_fields['maptype'];
					switch ($maptype) {
						case 'Record Set Mapping':
							$rsm = $focus->RecordSetMapping();
							$modules = $rsm->getRecordSetModules();
							foreach ($modules as $module) {
								$include = $rsm->getRecordSetModule('include', $module);
								if (!empty($include)) {
									$moduleId = vtws_getEntityId($module);
									$crmids = array();
									foreach ($include as $id) {
										$entityid = $moduleId.'x'.$id;
										if ($this->validateCRMID($entityid)) {
											$crmids[] = $entityid;
										}
									}
									$this->executeWorkflow($this->workflowid, $crmids, '[]');
								}
							}
							break;
						default:
							$context = $entity->getData();
							if (!isset($context['record_id'])) {
								list($wsid, $crmid) = explode('x', $entity->getId());
								$context['record_id'] = $crmid;
							}
							$return = coreBOS_Rule::evaluate($this->recordsetmapid, $context);
							if (is_string($return)) {
								$crmids[] = $return;
							} else {
								foreach ($return as $ids) {
									$crmids[] = reset($ids);
								}
							}
							$this->executeWorkflow($this->workflowid, $crmids, '[]');
							break;
					}
				}
				break;
			case 'filterByCurrentRecord':
			default:
				$crmids = array();
				$crmids[] = $entity->getId();
				$this->executeWorkflow($this->workflowid, $crmids, '[]');
				break;
		}
	}

	private function validateCRMID($entityId) {
		global $adb;
		list($moduleId, $crmId) = explode('x', $entityId);
		$res = $adb->query('select 1 from vtiger_crmobject where crmid='.$crmId);
		return (is_numeric($moduleId) && is_numeric($crmId) && $adb->num_rows($res) > 0);
	}

	private function executeWorkflow($wfid, $crmids, $context) {
		global $current_user, $logbg;
		if (!empty($crmids)) {
			$logbg->debug('LaunchWF with', $crmids);
			cbwsExecuteWorkflowWithContext($wfid, json_encode($crmids), $context, $current_user);
		}
	}
}
?>