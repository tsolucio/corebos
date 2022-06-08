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
require_once 'include/Webservices/SetRelation.php';

class setManyToManyRelationwf extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('relAction', 'idlist');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg;
		if (empty($entity->WorkflowContext['SetManyToManyRelation_Records'])) {
			$idlistArr = explode(',', $this->idlist);
		} else {
			if (is_string($entity->WorkflowContext['SetManyToManyRelation_Records'])) {
				$idlistArr = explode(',', $entity->WorkflowContext['SetManyToManyRelation_Records']);
			} else {
				$idlistArr = $entity->WorkflowContext['SetManyToManyRelation_Records'];
			}
		}
		if (empty($entity->WorkflowContext['SetManyToManyRelation_Record'])) {
			list($modid, $recid) = explode('x', $entity->getId());
			$recmod = $entity->getModuleName();
		} else {
			$recid = vtws_getCRMID($entity->WorkflowContext['SetManyToManyRelation_Record']);
			$recmod = getSalesEntityType($recid);
		}
		$logbg->debug($this->relAction.' '.$recid.' with '.print_r($idlistArr, true));
		if ($this->relAction == 'addrel') {
			$idlistNewArr = array();
			foreach ($idlistArr as $recId) {
				$idlistNewArr[] = vtws_getWSID($recId);
			}
			vtws_setrelation(vtws_getWSID($recid), $idlistNewArr, $current_user);
		} elseif ($this->relAction == 'delrel') {
			$modobj = CRMEntity::getInstance($recmod);
			foreach ($idlistArr as $rid) {
				$rid = vtws_getCRMID($rid);
				$return_module = getSalesEntityType($rid);
				$modobj->unlinkRelationship($recid, $return_module, $rid);
				$modobj->trackUnLinkedInfo($return_module, $rid, $recmod, $recid);
			}
		} elseif ($this->relAction == 'delAllrel') {
			$rectabid = getTabid($recmod);
			$modobj = CRMEntity::getInstance($recmod);
			$done = array();
			foreach ($idlistArr as $rid) {
				$rid = vtws_getCRMID($rid);
				$mod2unrelate = getSalesEntityType($rid);
				if (!in_array($mod2unrelate, $done)) {
					$done[] = $mod2unrelate;
					$relatedModuleId = getTabid($mod2unrelate);
					$relationResult = $adb->pquery(
						'SELECT name FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?',
						array($rectabid, $relatedModuleId)
					);
					$relationInfo = $adb->fetch_array($relationResult);
					global $GetRelatedList_ReturnOnlyQuery, $currentModule;
					$holdCM = $currentModule;
					$currentModule = $recmod;
					$holdValue = $GetRelatedList_ReturnOnlyQuery;
					$GetRelatedList_ReturnOnlyQuery = true;
					$params = array($recid, $rectabid, $relatedModuleId);
					$relationData = call_user_func_array(array($modobj, $relationInfo['name']), array_values($params));
					$GetRelatedList_ReturnOnlyQuery = $holdValue;
					$currentModule = $holdCM;
					if (isset($relationData['query'])) {
						$query = $relationData['query'];
						preg_match('/[\t ]from /i', $query, $matches, PREG_OFFSET_CAPTURE);
						$query = 'select vtiger_crmentity.crmid'.substr($query, $matches[0][1]);
						$rs = $adb->query($query);
						while ($row = $adb->fetch_array($rs)) {
							$modobj->unlinkRelationship($recid, $mod2unrelate, $row['crmid']);
							$modobj->trackUnLinkedInfo($mod2unrelate, $row['crmid'], $recmod, $recid);
						}
					}
				}
			}
		}
	}
}
?>
