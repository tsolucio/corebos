<?php
/*************************************************************************************************
 * Copyright 2019 TSolucio -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************/
require_once 'include/Webservices/ExecuteWorkflow.php';
class WorkflowRelationEventHandler extends VTEventHandler {

	public function handleEvent($eventName, $entityData) {
		global $adb, $current_user, $log;
		$sourceModule = $entityData['sourceModule'];
		$sourceRecordId = $entityData['sourceRecordId'];
		$destinationModule = $entityData['destinationModule'];
		$destinationRecordId = $entityData['destinationRecordId'];
		$wsid = vtws_getEntityId(getSalesEntityType($sourceRecordId)).'x';
		$crmids = $wsid.$sourceRecordId;

		if ($eventName == 'corebos.entity.link.after') {
			$relate_excution_condition = 9;
			// for source module
			$wsid = vtws_getEntityId(getSalesEntityType($sourceRecordId)).'x';
			$crmids = $wsid.$sourceRecordId;
			$result = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE execution_condition=? AND module_name=? AND 
			(module_to_relate=? OR module_to_relate="Any")', array($relate_excution_condition, $sourceModule, $destinationModule));
			if ($result || $adb->num_rows($result)!= 0) {
				while ($row = $adb->fetch_array($result)) {
					cbwsExecuteWorkflow($row['workflow_id'], json_encode(array($crmids)), $current_user, $destinationRecordId, $destinationModule);
				}
			}

			// for destination module
			$wsid = vtws_getEntityId(getSalesEntityType($destinationRecordId)).'x';
			$crmids = $wsid.$destinationRecordId;
			$result = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE execution_condition=? AND module_name=? AND 
			(module_to_relate=? OR module_to_relate="Any")', array($relate_excution_condition, $destinationModule, $sourceModule));
			if ($result || $adb->num_rows($result)!= 0) {
				while ($row = $adb->fetch_array($result)) {
					cbwsExecuteWorkflow($row['workflow_id'], json_encode(array($crmids)), $current_user, $destinationRecordId, $destinationModule);
				}
			}
		} elseif ($eventName == 'corebos.entity.unlink.after') {
			$unrelate_excution_condition = 10;
			// for source module
			$wsid = vtws_getEntityId(getSalesEntityType($sourceRecordId)).'x';
			$crmids = $wsid.$sourceRecordId;
			$result = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE execution_condition=? AND module_name=? AND 
			(module_to_relate=? OR module_to_relate="Any")', array($unrelate_excution_condition, $sourceModule, $destinationModule));
			if ($result || $adb->num_rows($result)!= 0) {
				while ($row = $adb->fetch_array($result)) {
					cbwsExecuteWorkflow($row['workflow_id'],  json_encode(array($crmids)), $current_user, $destinationRecordId, $destinationModule);
				}
			}

			// for destination module
			$wsid = vtws_getEntityId(getSalesEntityType($destinationRecordId)).'x';
			$crmids = $wsid.$destinationRecordId;
			$result = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE execution_condition=? AND module_name=? AND 
			(module_to_relate=? OR module_to_relate="Any")', array($unrelate_excution_condition, $destinationModule, $sourceModule));
			if ($result || $adb->num_rows($result)!= 0) {
				while ($row = $adb->fetch_array($result)) {
					cbwsExecuteWorkflow($row['workflow_id'],  json_encode(array($crmids)), $current_user, $destinationRecordId, $destinationModule);
				}
			}
		}
	}
}
?>