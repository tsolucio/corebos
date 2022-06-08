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
require_once 'VTTaskManager.inc';
require_once 'VTWorkflowUtils.php';
require_once 'VTWorkflowApplication.inc';

function vtSaveTask($adb, $request) {
	global $current_language;
	$util = new VTWorkflowUtils();
	$module = new VTWorkflowApplication('savetask');
	$mod = return_module_language($current_language, $module->name);
	if (!$util->checkAdminAccess()) {
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}

	$request = vtlib_purify($request);  // this cleans all values of the array
	$tm = new VTTaskManager($adb);
	if (isset($request['task_id'])) {
		$task = $tm->retrieveTask($request['task_id']);
	} else {
		$taskType = $request['task_type'];
		$workflowId = $request['workflow_id'];
		$task = $tm->createTask($taskType, $workflowId);
	}
	$task->summary = decode_html($request['summary']);

	if ($request['active']=='true') {
		$task->active=true;
	} elseif ($request['active']=='false') {
		$task->active=false;
	}
	if (isset($request['check_select_date'])) {
		if ($request['select_date_days'] != '') {
			$trigger = array(
				'days'=>($request['select_date_direction']=='after'?1:-1)*(int)$request['select_date_days'],
				'field'=>$request['select_date_field']
			);
		}
		if ($request['select_date_hours'] != '') {
			$trigger = array(
				'field'=>$request['select_date_field']
			);
			$triggeridx = ($request['select_days_hours_option']=='hours' ? 'hours' : 'mins');
			$trigger[$triggeridx] = ($request['select_date_direction']=='after'?1:-1)*(int)$request['select_date_hours'];
		}
		$task->trigger=$trigger;
	} else {
		$task->trigger=null;
	}

	$fieldNames = $task->getFieldNames();
	foreach ($fieldNames as $fieldName) {
		if (isset($request[$fieldName])) {
			$result = json_decode($_REQUEST[$fieldName], true);
			if (json_last_error() === JSON_ERROR_NONE) { // JSON is valid
				if (is_array($result)) {
					$cleanarray = array();
					foreach ($result as $key => $value) {
						if (empty($value['valuetype'])) {
							$cleanarray[$key] = vtlib_purify($value);
						} else {
							$cleanarray[$key] = $value;
						}
					}
					$task->$fieldName = json_encode($cleanarray, JSON_UNESCAPED_UNICODE);
				} else {
					$task->$fieldName = $request[$fieldName];
				}
			} else {
				$task->$fieldName = $request[$fieldName];
			}
		} else {
			$task->$fieldName = '';
		}
		if ($fieldName == 'calendar_repeat_limit_date') {
			$task->$fieldName = DateTimeField::convertToDBFormat($request[$fieldName]);
		}
		if ($fieldName == 'content') {
			$task->$fieldName = (isset($_REQUEST[$fieldName]) ? $_REQUEST[$fieldName] : '');
		}
	}
	$task->test = $request['conditions'];
	$task->reevaluate = ((isset($request['reevaluate']) && $request['reevaluate']=='on') ? 1 : 0);
	$tm->saveTask($task);

	$module->setReturnUrl('');
	$returnUrl=$module->editWorkflowUrl($task->workflowId);
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo $returnUrl?>";
	</script>
	<?php
}
Vtiger_Request::validateRequest();
vtSaveTask($adb, $_REQUEST);
?>