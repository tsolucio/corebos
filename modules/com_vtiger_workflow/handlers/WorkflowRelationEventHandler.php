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
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';

class WorkflowRelationEventHandler extends VTEventHandler {

	public function handleEvent($eventName, $entityData) {
		global $adb, $current_user;
		if ($eventName == 'corebos.entity.link.after' || $eventName == 'corebos.entity.link.delete.final') {
			$execcond = VTWorkflowManager::$ON_UNRELATE;
			if ($eventName == 'corebos.entity.link.after') {
				$execcond = VTWorkflowManager::$ON_RELATE;
			}
			$sourceModule = $entityData['sourceModule'];
			$destinationModule = $entityData['destinationModule'];
			// source module
			$result = $adb->pquery(
				'SELECT workflow_id FROM com_vtiger_workflows WHERE execution_condition=? AND module_name=? AND active=? AND (relatemodule=? OR relatemodule="Any")',
				array($execcond, $sourceModule, 'true', $destinationModule)
			);
			if ($result && $adb->num_rows($result)>0) {
				$sourceRecordId = $entityData['sourceRecordId'];
				$crmids = json_encode(array(vtws_getEntityId($sourceModule).'x'.$sourceRecordId));
				$context = json_encode($entityData);
				while ($row = $adb->fetch_array($result)) {
					cbwsExecuteWorkflowWithContext($row['workflow_id'], $crmids, $context, $current_user);
				}
			}
			// destination module
			$result = $adb->pquery(
				'SELECT workflow_id FROM com_vtiger_workflows WHERE execution_condition=? AND module_name=? AND active=? AND (relatemodule=? OR relatemodule="Any")',
				array($execcond, $destinationModule, 'true', $sourceModule)
			);
			if ($result && $adb->num_rows($result)>0) {
				$destinationRecordId = $entityData['destinationRecordId'];
				$crmids = json_encode(array(vtws_getEntityId($destinationModule).'x'.$destinationRecordId));
				$context = json_encode($entityData);
				while ($row = $adb->fetch_array($result)) {
					cbwsExecuteWorkflowWithContext($row['workflow_id'], $crmids, $context, $current_user);
				}
			}
		}
	}
}
?>