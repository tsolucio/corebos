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
		global $adb, $current_user, $currentModule, $log;

		switch ($this->record_filter_opt) {
			case "filterByCurrentRecord":
				$crmids = array();
				$crmids[] = $entity->getId();
				$this->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
				break;
			case "filterByContextVariable":
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
				require_once 'modules/cbMap/cbMap.php';
				$focus = new cbMap();
				$focus->id = $this->conditionexpressionmapid;
				$focus->retrieve_entity_info($this->conditionexpressionmapid, 'cbMap');
				list($moduleId, $crmId) = explode('x', $entity->getId());
				$parentModule = getSalesEntityType($crmId);
				$relatedModuleId = vtws_getEntityId($this->relModlist);

				if ((($parentModule == 'Leads') && ($this->relModlist == 'Products')) ||
					(($parentModule == 'Products' ) && ($this->relModlist == 'Accounts' || $this->relModlist == 'Contacts' ||
					$this->relModlist == 'Potentials' || $this->relModlist == 'Leads')) || (($parentModule == 'Potentials') &&
					($this->relModlist == 'Products'))) {
					$sql = 'select productid as id from vtiger_seproductsrel where crmid = ? and setype = ?';
					$result = $adb->pquery($sql, array($crmId, $parentModule));
				} elseif (($parentModule == 'Leads') && ($this->relModlist == 'Campaigns')) {
					$sql = 'select campaignid as id from vtiger_campaignleadrel where leadid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Vendors') && ($this->relModlist == 'Contacts')) {
					$sql = 'select contactid as id from vtiger_vendorcontactrel where vendorid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Vendors') && ($this->relModlist == 'Products')) {
					$sql = 'select productid as id from vtiger_products where vendor_id = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Potentials') && ($this->relModlist == 'Contacts')) {
					$sql = 'select contactid as id from vtiger_contpotentialrel where potentialid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Contacts') && ($this->relModlist == 'Products')) {
					$sql = 'select productid as id from vtiger_seproductsrel where crmid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Contacts') && ($this->relModlist == 'Campaigns')) {
					$sql = 'select campaignid as id from vtiger_campaigncontrel where contactid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Contacts') && ($this->relModlist == 'Potentials')) {
					$sql = 'select potentialid as id from vtiger_contpotentialrel where contactid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Contacts') && ($this->relModlist == 'Vendors')) {
					$sql = 'select vendorid as id from vtiger_vendorcontactrel where contactid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'cbCalendar') && ($this->relModlist == 'Contacts')) {
					$sql = 'select contactid as id from vtiger_vendorcontactrel where activityid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Campaigns') && ($this->relModlist == 'Leads')) {
					$sql = 'select leadid as id from vtiger_campaignleadrel where campaignid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Campaigns') && ($this->relModlist == 'Contacts')) {
					$sql = 'select contactid as id from vtiger_campaigncontrel where campaignid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Campaigns') && ($this->relModlist == 'Accounts')) {
					$sql = 'select accountid as id from vtiger_campaignaccountrel where campaignid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Accounts') && ($this->relModlist == 'Products')) {
					$sql = 'select productid as id from vtiger_seproductsrel where crmid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} elseif (($parentModule == 'Accounts') && ($this->relModlist == 'Campaigns')) {
					$sql = 'select campaignid as id from vtiger_campaignaccountrel where accountid = ?';
					$result = $adb->pquery($sql, array($crmId));
				} else {
					$sql = 'select relcrmid as id from vtiger_crmentityrel where crmid = ? and relmodule = ?';
					$result = $adb->pquery($sql, array($crmId, $this->relModlist));
				}

				$crmids = array();
				if ($result && $adb->num_rows($result) > 0) {
					while ($related = $adb->fetch_array($result)) {
						if ($focus->ConditionExpression($relatedModuleId.'x'.$related['id'])) {
							$crmids[] = $relatedModuleId.'x'.$related['id'];
						}
					}
					$this->executeWorkflow($this->workflowid, $crmids, '[]', $current_user);
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