<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/events/SqlResultIterator.inc';
require_once 'VTWorkflowApplication.inc';
require_once 'VTWorkflowManager.inc';
require_once 'VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTCreateEventTask.inc';

function vtWorkflowSave($adb, $request) {
	global $current_language;
	$util = new VTWorkflowUtils();
	$module = new VTWorkflowApplication('saveworkflow');
	$mod = return_module_language($current_language, $module->name);
	$request = vtlib_purify($request);  // this cleans all values of the array
	if (!$util->checkAdminAccess()) {
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}

	$description = $request['description'];
	$purpose = $request['purpose'];
	$moduleName = $request['module_name'];
	$conditions = $request['conditions'];
	$saveType = $request['save_type'];
	$executionCondition = $request['execution_condition'];
	$schdayofweek = array();
	if (isset($request['sun_flag']) && $_REQUEST['sun_flag'] != null) {
		$schdayofweek[] = 1;
	}
	if (isset($request['mon_flag']) && $_REQUEST['mon_flag'] != null) {
		$schdayofweek[] = 2;
	}
	if (isset($request['tue_flag']) && $_REQUEST['tue_flag'] != null) {
		$schdayofweek[] = 3;
	}
	if (isset($request['wed_flag']) && $_REQUEST['wed_flag'] != null) {
		$schdayofweek[] = 4;
	}
	if (isset($request['thu_flag']) && $_REQUEST['thu_flag'] != null) {
		$schdayofweek[] = 5;
	}
	if (isset($request['fri_flag']) && $_REQUEST['fri_flag'] != null) {
		$schdayofweek[] = 6;
	}
	if (isset($request['sat_flag']) && $_REQUEST['sat_flag'] != null) {
		$schdayofweek[] = 7;
	}
	// internally the code is prepared to launch the same workflow on many dates but the interface only sends one in
	// TODO: change interface to send in many dates for annual scheduling
	$schannualdates = DateTimeField::convertToDBFormat($request['schdate']);
	$schannualdates = json_encode(array($schannualdates));
	$schminuteinterval = $request['schminuteinterval'];

	$wm = new VTWorkflowManager($adb);
	if ($saveType == 'new') {
		$wf = $wm->newWorkflow($moduleName);
		$wf->description = $description;
		$wf->purpose = $purpose;
		$wf->test = $conditions;
		$wf->executionConditionAsLabel($executionCondition);
		$wf->schtypeid = $request['schtypeid'];
		$wf->schtime = VTCreateEventTask::conv12to24hour($request['schtime']);
		$wf->schdayofmonth = isset($request['schdayofmonth']) ? json_encode($request['schdayofmonth']) : '';
		$wf->schdayofweek = isset($schdayofweek) ? json_encode($schdayofweek) : '';
		$wf->schannualdates = $schannualdates;
		$wf->schminuteinterval = $schminuteinterval;
		$wm->save($wf);
	} elseif ($saveType == 'edit') {
		$wf = $wm->retrieve($request["workflow_id"]);
		$wf->description = $description;
		$wf->purpose = $purpose;
		$wf->test = $conditions;
		$wf->executionConditionAsLabel($executionCondition);
		$wf->schtypeid = $request['schtypeid'];
		$wf->schtime = VTCreateEventTask::conv12to24hour($request['schtime']);
		$wf->schdayofmonth = isset($request['schdayofmonth']) ? json_encode($request['schdayofmonth']) : '';
		$wf->schdayofweek = isset($schdayofweek) ? json_encode($schdayofweek) : '';
		$wf->schannualdates = $schannualdates;
		$wf->schminuteinterval = $schminuteinterval;
		$wm->save($wf);
	} else {
		throw new Exception();
	}
	if (isset($request['return_url'])) {
		$returnUrl=$request['return_url'];
	} else {
		$returnUrl=$module->editWorkflowUrl($wf->id);
	}
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo $returnUrl?>";
	</script>
	<a href="<?php echo $returnUrl?>">Return</a>
	<?php
}

vtWorkflowSave($adb, $_REQUEST);
?>