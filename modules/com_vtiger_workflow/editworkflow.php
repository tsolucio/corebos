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
require_once 'include/events/VTWSEntityType.inc';
require_once 'VTWorkflowManager.inc';
require_once 'VTTaskManager.inc';
require_once 'VTWorkflowApplication.inc';
require_once 'VTWorkflowTemplateManager.inc';
require_once 'VTWorkflowUtils.php';
require_once 'include/Webservices/getRelatedModules.php';
require_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc';

function vtWorkflowEdit($adb, $request, $requestUrl, $current_language, $app_strings) {
	global $theme, $current_user;
	$util = new VTWorkflowUtils();

	$image_path = "themes/$theme/images/";

	$module = new VTWorkflowApplication('editworkflow');

	$mod = return_module_language($current_language, $module->name);

	if (!$util->checkAdminAccess()) {
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}

	$smarty = new vtigerCRM_Smarty();
	$wfs = new VTWorkflowManager($adb);
	if (isset($request['source']) && $request['source']=='from_template') {
		$tm = new VTWorkflowTemplateManager($adb);
		$template = $tm->retrieveTemplate($request['template_id']);
		$workflow = $tm->createWorkflow($template);
		$smarty->assign('MaxAllowedScheduledWorkflows', $wfs->getMaxAllowedScheduledWorkflows());
	} else {
		if (isset($request['workflow_id'])) {
			$workflow = $wfs->retrieve($request['workflow_id']);
			if (!$workflow->checkNonAdminAccess()) {
				$errorUrl = $module->errorPageUrl(getTranslatedString('LBL_PERMISSION'));
				$util->redirectTo($errorUrl, getTranslatedString('LBL_PERMISSION'));
				return;
			}
			if ($workflow->executionCondition!=VTWorkflowManager::$ON_SCHEDULE) {
				$smarty->assign('MaxAllowedScheduledWorkflows', $wfs->getMaxAllowedScheduledWorkflows());
			} else {
				$smarty->assign('MaxAllowedScheduledWorkflows', $wfs->getScheduledWorkflowsCount());
			}
		} else {
			$moduleName=$request['module_name'];
			$workflow = $wfs->newWorkflow($moduleName);
			$smarty->assign('MaxAllowedScheduledWorkflows', $wfs->getMaxAllowedScheduledWorkflows());
		}
	}
	$smarty->assign('ScheduledWorkflowsCount', $wfs->getScheduledWorkflowsCount());
	if (empty($workflow->schtime)) {
		$smarty->assign('schdtime_12h', date('h:ia'));
	} else {
		$smarty->assign('schdtime_12h', date('h:ia', strtotime(substr($workflow->schtime, 0, strrpos($workflow->schtime, ':')))));
	}
	if (!empty($workflow->schannualdates)) {
		$schannualdates = json_decode($workflow->schannualdates);
		$schannualdates = DateTimeField::convertToUserFormat($schannualdates[0]);
	} else {
		$schannualdates = '';
	}
	$smarty->assign('schdate', $schannualdates);
	if (empty($workflow->schdayofmonth)) {
		$smarty->assign('selected_days1_31', '');
	} else {
		$smarty->assign('selected_days1_31', json_decode($workflow->schdayofmonth));
	}
	if (empty($workflow->schminuteinterval)) {
		$smarty->assign('selected_minute_interval', '');
	} else {
		$smarty->assign('selected_minute_interval', json_decode($workflow->schminuteinterval));
	}
	if (empty($workflow->schdayofweek)) {
		$smarty->assign('dayOfWeek', '');
	} else {
		$smarty->assign('dayOfWeek', json_decode($workflow->schdayofweek));
	}

	if ($workflow->active == 'true') {
		$smarty->assign('selected_active', 'selected');
		$smarty->assign('selected_inactive', '');
	} else {
		$smarty->assign('selected_active', '');
		$smarty->assign('selected_inactive', 'selected');
	}
	if ($workflow==null) {
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NO_WORKFLOW']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NO_WORKFLOW']);
		return;
	}
	$workflow->test = !empty($workflow->test) ? addslashes($workflow->test) : '';
	$tm = new VTTaskManager($adb);
	$tasks = !empty($workflow->id) ? $tm->getTasksForWorkflow($workflow->id) : array();
	$smarty->assign('tasks', $tasks);
	$taskTypes = $tm->getTaskTypes($workflow->moduleName);
	$smarty->assign('taskTypes', $taskTypes);
	$smarty->assign('newTaskReturnUrl', vtlib_purify($requestUrl));
	$dayrange = array();
	$intervalrange=array();
	for ($d=1; $d<=31; $d++) {
		$dayrange[$d] = $d;
	}
	for ($interval=5; $interval<=50; $interval+=5) {
		$intervalrange[$interval-2]=$interval-2;
		$intervalrange[$interval]=$interval;
	}
	$smarty->assign('days1_31', $dayrange);
	$smarty->assign('interval_range', $intervalrange);
	if (empty($workflow->nexttrigger_time)) {
		$smarty->assign('wfnexttrigger_time', '');
	} else {
		$smarty->assign('wfnexttrigger_time', DateTimeField::convertToUserFormat($workflow->nexttrigger_time));
	}
	$smarty->assign('dateFormat', parse_calendardate($current_user->date_format));
	$smarty->assign('returnUrl', isset($request['return_url']) ? vtlib_purify($request['return_url']) : '');
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', array_merge(
		return_module_language($current_language, 'Settings'),
		return_module_language($current_language, $module->name)
	));
	// Related Module List for Relate Event Triggers
	$relatedMods = getRelatedModulesInfomation($workflow->moduleName, $current_user);
	$relatedmodules = array(
		'Any' => getTranslatedString('LBL_ANY', 'Settings'),
	);
	$relatedmodule = 'Any';
	foreach ($relatedMods as $modval) {
		if ($workflow->relatemodule == $modval['related_module']) {
			$relatedmodule = $modval['related_module'];
		}
		if ($modval['relationtype'] == 'N:N' && !empty($modval['related_module'])) {
			$relatedmodules[$modval['related_module']] = $modval['labeli18n'];
		}
	}
	$smarty->assign('relatedmodules', $relatedmodules);
	$smarty->assign('onrelatedmodule', $workflow->executionConditionAsLabel() == 'ON_RELATE' ? $relatedmodule : 'Any');
	$smarty->assign('onunrelatedmodule', $workflow->executionConditionAsLabel() == 'ON_UNRELATE' ? $relatedmodule : 'Any');
	$emgr = new VTExpressionsManager($adb);
	$smarty->assign('FNDEFS', json_encode($emgr->expressionFunctionDetails()));
	$smarty->assign('FNCATS', $emgr->expressionFunctionCategories());

	$smarty->assign('ISADMIN', is_admin($current_user));
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', $image_path);
	$smarty->assign('MODULE_NAME', $module->label);
	$smarty->assign('PAGE_NAME', $mod['LBL_EDIT_WORKFLOW']);
	$smarty->assign('PAGE_TITLE', $mod['LBL_EDIT_WORKFLOW_TITLE']);

	$smarty->assign('workflow', $workflow);
	$smarty->assign('saveType', !empty($workflow->id) ? 'edit' : 'new');
	$smarty->assign('module', $module);

	if (coreBOS_Session::has('malaunch_records')) {
		$malaunch_records = coreBOS_Session::get('malaunch_records');
		$smarty->assign('malaunch_records', $malaunch_records);
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		if ($workflow->options=='onerecord') {
			$msg = $mod['Records execution success'];
		} else {
			$msg = $mod['Records put in queue'];
		}
		$msg .= '<br />';
		$msg .= $mod['Records'];
		$msg .= '<br />';
		$msg .= '<ul>';
		foreach ($malaunch_records as $record) {
			$msg .= '<li>'.$record.'</li>';
		}
		$msg .= '</ul>';
		$smarty->assign('ERROR_MESSAGE', $msg);
		coreBOS_Session::delete('malaunch_records');
	}

	$smarty->display("{$module->name}/EditWorkflow.tpl");
}
$returl = 'index.php?'.$_SERVER['QUERY_STRING'];
vtWorkflowEdit($adb, $_REQUEST, $returl, $current_language, $app_strings);
?>
