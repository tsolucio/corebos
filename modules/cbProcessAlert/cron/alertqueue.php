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
 *  Module       : Process Flow Alert Queue
 *  Version      : 5.4.0
 *  Author       : AT CONSULTING
 *************************************************************************************************/
$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';
include_once 'include/Webservices/ExecuteWorkflow.php';
include_once 'include/Webservices/Revise.php';
global $adb, $default_timezone, $current_user;
$specialWFIDForPostUserAssign = -100;

$admin = Users::getActiveAdminUser();
$adminTimeZone = $admin->time_zone;
@date_default_timezone_set($adminTimeZone);
$currentTimestamp = date('Y-m-d H:i:s');
@date_default_timezone_set($default_timezone);
if (is_null($current_user)) {
	$current_user = $admin;
}
// we have to cleanup the relations because workflow doesn't do it, so when a workflow is deleted, that ID is not deleted from the relation
$adb->query('delete from vtiger_cbprocesssteprel where wfid not in (select workflow_id from com_vtiger_workflows)');
// Alerting
$rsa = $adb->pquery(
	"select cbprocessalertqueueid, processflow, whilein, context, schtypeid, schtime, schdayofmonth, schdayofweek, schannualdates, schminuteinterval, crmid, alertid, executeuser
	from vtiger_cbprocessalertqueue
	inner join vtiger_cbprocessalert on cbprocessalertid=alertid
	where nexttrigger_time is null OR nexttrigger_time IS NULL OR nexttrigger_time<=?",
	array($currentTimestamp)
);
$wf = new Workflow();
$pflwwsid = vtws_getEntityId('cbProcessFlow').'x';
$paltwsid = vtws_getEntityId('cbProcessAlert').'x';
$pstpwsid = vtws_getEntityId('cbProcessStep').'x';
while ($alert=$adb->fetch_array($rsa)) {
	if (!isRecordExists($alert['crmid'])) {
		// record has been deleted, we delete the task from the queue and continue
		$adb->pquery('delete from vtiger_cbprocessalertqueue where cbprocessalertqueueid=?', array($alert['cbprocessalertqueueid']));
		continue;
	}
	// do batch control here
	// process context map
	$context = array(
		'ProcessRelatedFlow' => $alert['processflow'],
		'ProcessRelatedStepOrAlert' => $alert['alertid'],
		'ProcessPreviousStatus' => '',
		'ProcessNextStatus' => $alert['whilein'],
	);
	$moduleName = getSalesEntityType($alert['crmid']);
	$cbfrom = CRMEntity::getInstance($moduleName);
	$cbfrom->retrieve_entity_info($alert['crmid'], $moduleName);
	if (empty($alert['context'])) {
		$context = array_merge($cbfrom->column_fields, $context);
	} else {
		$cbMap = cbMap::getMapByID($alert['context']);
		$context = $cbMap->Mapping($cbfrom->column_fields, $context);
	}
	// WFs
	$wfrs = $adb->pquery(
		'SELECT workflow_id
		FROM com_vtiger_workflows
		INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = workflow_id OR vtiger_crmentityrel.crmid = workflow_id)
		WHERE (vtiger_crmentityrel.crmid = ? OR vtiger_crmentityrel.relcrmid = ?)',
		array($alert['alertid'], $alert['alertid'])
	);
	while ($workflow=$adb->fetch_array($wfrs)) {
		if (strpos($alert['crmid'], 'x')===false) {
			$wsid = vtws_getEntityId($moduleName).'x'.$alert['crmid'];
		} else {
			$wsid = $alert['crmid'];
		}
		$wfuser = new Users();
		if (Users::is_ActiveUserID($alert['executeuser'])) {
			$wfuser->retrieveCurrentUserInfoFromFile($alert['executeuser']);
		} else {
			$wfuser = $current_user;
		}
		cbwsExecuteWorkflowWithContext($workflow['workflow_id'], json_encode(array($wsid)), json_encode($context), $wfuser);
	}
	// next trigger
	$alert['workflow_id'] = 0;
	$alert['module_name'] = $moduleName;
	$alert['summary'] = '';
	$alert['test'] = '';
	$alert['execution_condition'] = '';
	$alert['defaultworkflow'] = false;
	$wf->setup($alert);
	$next = $wf->getNextTriggerTime();
	if (empty($next)) {
		$adb->pquery('delete from vtiger_cbprocessalertqueue where cbprocessalertqueueid=?', array($alert['cbprocessalertqueueid']));
	} else {
		$adb->pquery('update vtiger_cbprocessalertqueue set nexttrigger_time=? where crmid=? and alertid=?', array($next, $alert['crmid'], $alert['alertid']));
	}
}
unset($wf, $rsa);

// Steps
$usrwsid = vtws_getEntityId('Users').'x';
$grpwsid = vtws_getEntityId('Groups').'x';
$rss = $adb->query('select cbprocessalertqueueid, processflow, fromstep, tostep, context, crmid, wfid, alertid, usermap, executeuser
	from vtiger_cbprocessalertqueue
	inner join vtiger_cbprocessstep on cbprocessstepid=alertid
	where nexttrigger_time IS NULL and (wfid>0 or wfid='.$specialWFIDForPostUserAssign.')');
while ($step=$adb->fetch_array($rss)) {
	if (!isRecordExists($step['crmid'])) {
		// record has been deleted, we delete the task from the queue and continue
		$adb->pquery('delete from vtiger_cbprocessalertqueue where cbprocessalertqueueid=?', array($step['cbprocessalertqueueid']));
		continue;
	}
	// do batch control here
	// process context map
	$context = array(
		'ProcessRelatedFlow' => $step['processflow'],
		'ProcessRelatedStepOrAlert' => $step['alertid'],
		'ProcessPreviousStatus' => $step['fromstep'],
		'ProcessNextStatus' => $step['tostep'],
	);
	$moduleName = getSalesEntityType($step['crmid']);
	$cbfrom = CRMEntity::getInstance($moduleName);
	$cbfrom->retrieve_entity_info($step['crmid'], $moduleName);
	if (empty($step['context'])) {
		$context = array_merge($cbfrom->column_fields, $context);
	} else {
		$cbMap = cbMap::getMapByID($step['context']);
		$context = $cbMap->Mapping($cbfrom->column_fields, $context);
	}
	// WFs
	if (strpos($step['crmid'], 'x')===false) {
		$wsid = vtws_getEntityId($moduleName).'x'.$step['crmid'];
	} else {
		$wsid = $step['crmid'];
	}
	$wfuser = new Users();
	if (Users::is_ActiveUserID($alert['executeuser'])) {
		$wfuser->retrieveCurrentUserInfoFromFile($alert['executeuser']);
	} else {
		$wfuser = $current_user;
	}
	if ($step['wfid']==$specialWFIDForPostUserAssign) {
		if (!empty($step['usermap'])) {
			$newuserid = coreBOS_Rule::evaluate($step['usermap'], $step['crmid']);
			if (!empty($newuserid)) {
				if (strpos($newuserid, 'x')===false) {
					if (empty(getUserName($newuserid))) {
						$newuserid = $grpwsid.$newuserid;
					} else {
						$newuserid = $usrwsid.$newuserid;
					}
				}
				vtws_revise(array('id'=>$wsid,'assigned_user_id'=>$newuserid), $wfuser);
			}
		}
	} else {
		cbwsExecuteWorkflowWithContext($step['wfid'], json_encode(array($wsid)), json_encode($context), $wfuser);
	}
	$adb->pquery('delete from vtiger_cbprocessalertqueue where cbprocessalertqueueid=?', array($step['cbprocessalertqueueid']));
}
?>
