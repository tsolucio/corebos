<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
require 'build/cbHeader.inc';
require_once 'include/Webservices/Utils.php';
require_once "modules/Users/Users.php";
require_once "include/Webservices/State.php";
require_once "include/Webservices/OperationManager.php";
require_once "include/Webservices/SessionManager.php";
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once "include/Webservices/VtigerCRMObject.php";
require_once "include/Webservices/VtigerCRMObjectMeta.php";
require_once "include/Webservices/DataTransform.php";
require_once "include/Webservices/WebServiceError.php";
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'include/logging.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once "include/language/$default_language.lang.php";
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Emails/mail.php';
require_once 'include/events/SqlResultIterator.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/WorkFlowScheduler.php';

if (empty($_REQUEST['workflowid']) || empty($_REQUEST['crmid'])) {
	echo "<h2>Parameters required:</h2>";
	echo "<b>workflowid</b>: ID of the workflow to evaluate. For example: 19<br>";
	echo "<b>crmid</b>: webservice enhanced ID of the record to evaluate the workflow against. For example: 12x57<br>";
	echo "?workflowid=19&crmid=12x57";
	die();
}
/////////////////////////////////////////////////////
// PARAMETERS TO SET
 $workflowid_to_evaluate = $_REQUEST['workflowid'];
 $crm_record_to_evaluate = $_REQUEST['crmid'];
/////////////////////////////////////////////////////

global $currentModule, $adb;

function evalwfEmailTask($entityid, $task) {
	global $entityCache,$HELPDESK_SUPPORT_EMAIL_ID;
	$util = new VTWorkflowUtils();
	$util->adminUser();

	$from_name = $from_email = '';
	if (!empty($task->fromname)) {
		$fnt = new VTEmailRecipientsTemplate($task->fromname);
		$from_name  = $fnt->render($entityCache, $entityid);
	}
	$fromname = $from_name;
	if (!empty($task->fromemail)) {
		$fet = new VTEmailRecipientsTemplate($task->fromemail);
		$from_email = $fet->render($entityCache, $entityid);
	}
	$fromemail = $from_email;
	if (empty($from_name) && !empty($from_email)) {
		$fromname = 'first and last name of user with email: '.$from_email;
	}
	if (!empty($from_name) && empty($from_email)) {
		$fromname = 'first and last name of user with user_name: '.$from_name;
		$fromemail = 'email of user with user_name: '.$from_name;
	}
	if (empty($from_name) && empty($from_email)) {
		$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
		$fromemail = $HELPDESK_SUPPORT_EMAIL_ID;
		$fromname = 'first and last name of user with user_name: '.$HELPDESK_SUPPORT_EMAIL_ID;
	}

	$et = new VTEmailRecipientsTemplate($task->recepient);
	$to_email = $et->render($entityCache, $entityid);
	$ecct = new VTEmailRecipientsTemplate($task->emailcc);
	$cc = $ecct->render($entityCache, $entityid);
	$ebcct = new VTEmailRecipientsTemplate($task->emailbcc);
	$bcc = $ebcct->render($entityCache, $entityid);
	$to_email = preg_replace('/,,+/', ',', $to_email);
	$cc = preg_replace('/,,+/', ',', $cc);
	$bcc = preg_replace('/,,+/', ',', $bcc);
	$st = new VTSimpleTemplate($task->subject);
	$subject = $st->render($entityCache, $entityid);
	$ct = new VTSimpleTemplate($task->content);
	$content = $ct->render($entityCache, $entityid);
	$util->revertUser();
	return array(
		'from_name' => $fromname,
		'from_email' => $fromemail,
		'to_email' => $to_email,
		'cc' => $cc,
		'bcc' => $bcc,
		'subject' => $subject,
		'content' => $content,
	);
}

if (strpos($crm_record_to_evaluate, 'x')) {
	list($wsmod,$crmid) = explode('x', $crm_record_to_evaluate);
	$semod = getSalesEntityType($crmid);
} else {
	$semod = getSalesEntityType($crm_record_to_evaluate);
	$crmid = $crm_record_to_evaluate;
	$wsmod = vtws_getEntityId($semod);
	$crm_record_to_evaluate = $wsmod.'x'.$crmid;
}
$wsrs = $adb->pquery('select name FROM vtiger_ws_entity where id=?', array($wsmod));
if (!$wsrs || $adb->num_rows($wsrs)==0) {
	echo "<h2>Incorrect crmid:</h2>";
	echo "<b>crmid</b> could not be evaluated as a valid webservice enhanced ID<br>";
	die();
}
$currentModule = $adb->query_result($wsrs, 0, 0);
if ($semod != $currentModule && ($semod!='Calendar' && $currentModule!='Events')) {
	echo "<h2>Incorrect crmid:</h2>";
	echo "<b>crmid</b> could not be evaluated as a valid record ID<br>";
	die();
}
$util = new VTWorkflowUtils();
$adminUser = $util->adminUser();
$entityCache = new VTEntityCache($adminUser);
$wfs = new VTWorkflowManager($adb);
$result = $adb->pquery('select * from com_vtiger_workflows where workflow_id=?', array($workflowid_to_evaluate));
if (!$result || $adb->num_rows($result)==0) {
	echo "<h2>Incorrect workflowid:</h2>";
	echo "<b>workflowid</b> could not be found as a valid workflow<br>";
	die();
}
$workflows = $wfs->getWorkflowsForResult($result);
$workflow = $workflows[$workflowid_to_evaluate];
$entityData = $entityCache->forId($crm_record_to_evaluate);
$data = $entityData->getData();
if ($workflows[$workflowid_to_evaluate]->executionCondition==VTWorkflowManager::$ON_SCHEDULE) {
	echo "<h2>Scheduled: SQL for affected records:</h2>";
	$workflowScheduler = new WorkFlowScheduler($adb);
	$query = $workflowScheduler->getWorkflowQuery($workflow);
	echo "<span style='font-size: large;'>$query</span>";
	$wfcandidatesrs = $adb->pquery('SELECT * FROM com_vtiger_workflows WHERE workflow_id = ?', array($workflowid_to_evaluate));
	echo '<br><br><table border=1><tr><th>workflow</th><th>module</th><th>next trigger</th></tr>';
	while ($cwf=$adb->fetch_array($wfcandidatesrs)) {
		echo '<tr><td><a href="'.$site_URL.'index.php?module=com_vtiger_workflow&action=editworkflow&return_url=index.php&workflow_id='.$cwf['workflow_id']
			.'">'.$cwf['summary'].'</a></td><td>'.$cwf['module_name'].'</td><td>'.$cwf['nexttrigger_time'].'</td></tr>';
	}
	$ntt = $workflow->getNextTriggerTime();
	echo '</table><br><br>&nbsp;Next trigger time if launched now: '.$ntt;
} else {
	echo "<h2>Launch Conditions:</h2>";
	echo "<span style='font-size: large;'>";
	$test = json_decode($workflow->test, true);
	$haschanged = false;
	$newtest = array();
	if (is_array($test)) {
		foreach ($test as $tst) {
			if (substr($tst['operation'], 0, 11)=='has changed') {
				$haschanged = true;
			} else {
				$newtest[] = $tst;
			}
			echo $tst['fieldname'].'('.$data[$tst['fieldname']].') '.$tst['operation'].' '.$tst['value'].' ('.(isset($tst['valuetype']) ? $tst['valuetype'] : '').')<br>';
			echo (isset($tst['joincondition']) ? $tst['joincondition'] : '').'<br>';
		}
	}
	if ($haschanged) {
		echo "<br><b>** has changed condition being ignored **</b><br>";
		$workflow->test = json_encode($newtest);
	}
	echo '<b>** RESULT:</b><br>';
	$eval = $workflow->evaluate($entityCache, $crm_record_to_evaluate);
	var_export($eval);
	echo '</span>';
}
$tm = new VTTaskManager($adb);
$taskQueue = new VTTaskQueue($adb);
$tasks = $tm->getTasksForWorkflow($workflow->id);
foreach ($tasks as $task) {
	if (is_object($task) && $task->active && get_class($task) == 'VTEmailTask') {
		echo "<br><br><b>** EMail TASK **</b><br><br>";
		$email = evalwfEmailTask($crm_record_to_evaluate, $task);
		foreach ($email as $key => $value) {
			echo "<h2>$key</h2>$value <br><hr>";
		}
	}
}
?>
</table>
</body>
</html>
