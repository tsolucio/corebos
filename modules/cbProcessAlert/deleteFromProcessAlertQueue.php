<?php
/*************************************************************************************************
 * Copyright 2019 Spike Associates -- This file is a part of coreBOS customizations.
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
 *************************************************************************************************
 *  Module       : Process Flow Alert WF Method
 *  Version      : 5.4.0
 *  Author       : AT CONSULTING
 *************************************************************************************************/

function deleteFromProcessAlertQueueCurrent($entity) {
	global $adb;
	if (!empty($entity->WorkflowContext['ProcessAlertValueToDelete'])) {
		$value = $entity->WorkflowContext['ProcessAlertValueToDelete'];
	} else {
		list($wsid, $crmid) = explode('x', $entity->id);
		$rs = $adb->pquery(
			'select pffield
			from vtiger_cbprocessflow
			inner join vtiger_cbprocessalert on processflow=cbprocessflowid
			inner join vtiger_cbprocessalertqueue on cbprocessalertid=alertid
			where crmid=? limit 1',
			array($crmid)
		);
		$value = $entity->data[$rs->fields['pffield']];
	}
	$adb->pquery(
		'delete vtiger_cbprocessalertqueue from vtiger_cbprocessalertqueue inner join vtiger_cbprocessalert on cbprocessalertid=alertid where crmid=? and whilein=?',
		array($crmid, $value)
	);
}

function deleteFromProcessAlertQueueAll($entity) {
	global $adb;
	$adb->pquery('delete from vtiger_cbprocessalertqueue where crmid=?', array($entity->data['id']));
}
?>