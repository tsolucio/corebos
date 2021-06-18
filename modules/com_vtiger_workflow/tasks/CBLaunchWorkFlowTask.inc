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
require_once 'include/Webservices/Retrieve.php';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'data/CRMEntity.php';

class CBLaunchWorkFlowTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array(
			'workflowid', 'workflowid_display', 'record_filter_opt', 'relModlist',
			'conditionexpressionmapid',  'conditionexpressionmapid_display',
			'recordsetmapid', 'recordsetmapid_display', 'crmids_list');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $currentModule, $log, $from_wf;

		switch ($this->record_filter_opt) {
			case "filterByCurrentRecord":
				$crmids = array();
				$crmids[] = $entity->getId();
				$this->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
				break;
			case "filterByThese":
				$idslist = explode(',', $this->crmids_list);
				if (count($idslist) > 0) {
					$entityids = array();
					foreach ($idslist as $entityid) {
						if ($this->validateCRMID($entityid)) {
							$entityids[] = $entityid;
						}
					}
					$this->executeWorkflow($this->workflowid, $entityids, '[]', $current_user);
				}
				break;
			case "filterByrelModuleAndConditionExpressionMap":
				include_once 'data/CRMEntity.php';
				list($wsid, $crmid) = explode('x', $entity->getId());
				$parentModule = getSalesEntityType($crmid);
				$focus = CRMEntity::getInstance($parentModule);
				$focus->id = $crmid;
				$focus->retrieve_entity_info($crmid, $parentModule, false, $from_wf);
				global $GetRelatedList_ReturnOnlyQuery, $adb;
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
				$relationData = call_user_func_array(array($focus, $relationInfo['name']), $params);
				$GetRelatedList_ReturnOnlyQuery = $holdValue;
				$currentModule = $holdCM;
				if (isset($relationData['query'])) {
					$query = $relationData['query'];
					preg_match('/[\t ]from /i', $query, $matches, PREG_OFFSET_CAPTURE);
					$query = 'select vtiger_crmentity.crmid as id'.substr($query, $matches[0][1]).$condition;
					$result = $adb->query($query);
					$crmids = array();
					if ($result && $adb->num_rows($result) > 0) {
						while ($related = $adb->fetch_array($result)) {
							if (!empty($this->conditionexpressionmapid)) {
								$cbMapObject = new cbMap();
								$cbMapObject->id = $this->conditionexpressionmapid;
								$cbMapObject->retrieve_entity_info($this->conditionexpressionmapid, 'cbMap');
								if ($cbMapObject->ConditionExpression($relatedModuleId.'x'.$related['id'])) {
									$crmids[] = $relatedModuleId.'x'.$related['id'];
								}
							} else {
								$crmids[] = $relatedModuleId.'x'.$related['id'];
							}
						}

						$this->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
					}
				}
				break;
			case "filterByRecordSetMap":
				require_once 'modules/cbMap/cbMap.php';
				$focus = new cbMap();
				$focus->id = $this->recordsetmapid;
				$focus->retrieve_entity_info($this->recordsetmapid, 'cbMap');
				$rsm = $focus->RecordSetMapping();
				$mapinfo = $rsm->getFullRecordSet();

				$include = $mapinfo['include'];
				$exclude = $mapinfo['exclude'];
				$group = $mapinfo['group'];
				$modules = $mapinfo['modules'];

				$crmids = array();

				if (count($include) > 0) {
					foreach ($modules as $module) {
						if (array_key_exists($module, $include)) {
							$idslist = $include[$module];
							$moduleId = vtws_getEntityId($module);
							$crmids = array();
							foreach ($idslist as $id) {
								if ($this->validateCRMID($moduleId.'x'.$id)) {
									$crmids[] = $moduleId.'x'.$id;
								}
							}
							$this->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
						}
					}
				} elseif (count($exclude) > 0) {
					foreach ($modules as $module) {
						if (array_key_exists($module, $exclude)) {
							$idslist = $exclude[$module];
							$moduleId = vtws_getEntityId($module);
							$crmids = array();
							foreach ($idslist as $id) {
								if ($this->validateCRMID($moduleId.'x'.$id)) {
									$crmids[] = $moduleId.'x'.$id;
								}
							}
							$this->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
						}
					}
				} elseif (count($group) > 0) {
					foreach ($modules as $module) {
						if (array_key_exists($module, $group)) {
							$idslist = $group[$module];
							$moduleId = vtws_getEntityId($module);
							$crmids = array();
							foreach ($idslist as $id) {
								if ($this->validateCRMID($moduleId.'x'.$id)) {
									$crmids[] = $moduleId.'x'.$id;
								}
							}
							$his->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
						}
					}
				}
				break;
		}
	}

	public function validateCRMID($entityId) {
		list($moduleId, $crmId) = explode('x', $entityId);
		$query = "select * from vtiger_crmentity where crmid={$crmId}";
		$res = $adb->query($query);

		if (is_numeric($moduleId) && is_numeric($crmId) && $adb->num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function executeWorkflow($wfid, $crmids, $context, $current_user) {
		include_once 'include/Webservices/ExecuteWorkflow.php';
		try {
			$ret = cbwsExecuteWorkflowWithContext($wfid, json_encode($crmids), $context, $current_user);
		} catch (Exception $e) {
			$ret = false;
		}
	}
}
?>