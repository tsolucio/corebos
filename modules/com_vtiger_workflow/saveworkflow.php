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
require_once 'include/Webservices/ExecuteWorkflow.php';
require_once 'modules/com_vtiger_workflow/WorkFlowScheduler.php';

function vtWorkflowSave($adb, $request) {
	global $current_language, $current_user;
	$util = new VTWorkflowUtils();
	$edit_return_url = 'index.php?module=com_vtiger_workflow&action=workflowlist';
	$module = new VTWorkflowApplication('saveworkflow', $edit_return_url);
	$mod = return_module_language($current_language, $module->name);
	$request = vtlib_purify($request);  // this cleans all values of the array
	if (!$util->checkAdminAccess()) {
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}

	$description = decode_html($request['description']);
	$purpose = $request['purpose'];
	$wfstarton = $request['wfstarton'];
	$wfendon = $request['wfendon'];
	$active = $request['active'];
	$moduleName = $request['module_name'];
	$conditions = $request['conditions'];
	$saveType = $request['save_type'];
	$executionCondition = $request['execution_condition'];
	$options = isset($request['options']) ? $request['options'] : null;
	$cbquestion = $request['cbquestion'];
	$recordset = $request['recordset'];
	$onerecord = $request['onerecord'];
	if ($options == 'conditions') {
		$cbquestion = $recordset = $onerecord = null;
	} elseif ($options == 'cbquestion') {
		$recordset = $onerecord = null;
	} elseif ($options == 'recordset') {
		$cbquestion = $onerecord = null;
	} elseif ($options == 'onerecord') {
		$cbquestion = $recordset = null;
	}
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

	// for relate and unrelate execution conditions
	if ($executionCondition == 'ON_RELATE' && isset($request['onrelatemodule'])) {
		$relatemodule = $request['onrelatemodule'];
	} elseif ($executionCondition == 'ON_UNRELATE' && isset($request['onunrelatemodule'])) {
		$relatemodule = $request['onunrelatemodule'];
	} else {
		$relatemodule = '';
	}

	$wm = new VTWorkflowManager($adb);
	if ($saveType == 'new') {
		$wf = $wm->newWorkflow($moduleName);
		$wf->description = $description;
		$wf->purpose = $purpose;
		$wf->wfstarton = DateTimeField::convertToDBFormat($wfstarton);
		$wf->wfendon = DateTimeField::convertToDBFormat($wfendon);
		$wf->active = $active;
		$wf->test = $conditions;
		$wf->executionConditionAsLabel($executionCondition);
		$wf->schtypeid = $request['schtypeid'];
		$fmt = (date('a', strtotime($request['schtime'])));
		$wf->schtime = DateTimeField::formatDatebaseTimeString($request['schtime'], $fmt);
		$wf->schdayofmonth = isset($request['schdayofmonth']) ? json_encode($request['schdayofmonth']) : '';
		$wf->schdayofweek = isset($schdayofweek) ? json_encode($schdayofweek) : '';
		$wf->schannualdates = $schannualdates;
		$wf->schminuteinterval = $schminuteinterval;
		$wf->relatemodule = $relatemodule;
		$wf->options = $options;
		$wf->cbquestion = ($cbquestion !== '') ? $cbquestion : null;
		$wf->recordset = ($recordset !== '') ? $recordset : null;
		$wf->onerecord = ($onerecord !== '') ? $onerecord : null;
		$wm->save($wf);
	} elseif ($saveType == 'edit') {
		$wf = $wm->retrieve($request['workflow_id']);
		$wf->description = $description;
		$wf->purpose = $purpose;
		$wf->wfstarton = DateTimeField::convertToDBFormat($wfstarton);
		$wf->wfendon = DateTimeField::convertToDBFormat($wfendon);
		$wf->active = $active;
		$wf->test = $conditions;
		$wf->executionConditionAsLabel($executionCondition);
		$wf->schtypeid = $request['schtypeid'];
		$fmt = (date('a', strtotime($request['schtime'])));
		$wf->schtime = DateTimeField::formatDatebaseTimeString($request['schtime'], $fmt);
		$wf->schdayofmonth = isset($request['schdayofmonth']) ? json_encode($request['schdayofmonth']) : '';
		$wf->schdayofweek = isset($schdayofweek) ? json_encode($schdayofweek) : '';
		$wf->schannualdates = $schannualdates;
		$wf->schminuteinterval = $schminuteinterval;
		$wf->relatemodule = $relatemodule;
		$wf->options = $options;
		$wf->cbquestion = ($cbquestion !== '') ? $cbquestion : null;
		$wf->recordset = ($recordset !== '') ? $recordset : null;
		$wf->onerecord = ($onerecord !== '') ? $onerecord : null;
		$wm->save($wf);
	} else {
		throw new InvalidArgumentException();
	}
	if (isset($request['return_url'])) {
		$returnUrl=$request['return_url'];
	} else {
		$module->setReturnUrl('');
		$returnUrl=$module->editWorkflowUrl($wf->id);
	}

	if (isset($request['btnmalaunch']) && $options) {
		$wsid = vtws_getEntityId($moduleName).'x';
		$context = '[]';
		$crmids = $crmnames = array();
		if ($options == 'onerecord') {
			$se = getSalesEntityType($onerecord);
			$dp = getEntityName($se, $onerecord);
			$crmnames[] = '<a href="index.php?module='.$se.'&action=DetailView&record='.$onerecord.'">'.$dp[$onerecord].'</a>';
			cbwsExecuteWorkflowWithContext($wf->id, json_encode([$onerecord]), $context, $current_user);
		} else {
			$ids = null;
			if ($options == 'cbquestion') {
				$ids = cbQuestion::getAnswer($cbquestion, '');
				if (getSingleFieldValue('vtiger_cbquestion', 'qtype', 'cbquestionid', $cbquestion)=='Global Search') {
					array_walk($ids['answer']['records'], function (&$val) {
						$val = $val['id'];
					});
					$ids = $ids['answer']['records'];
				} else {
					array_walk($ids['answer'], function (&$val) {
						$val = $val['id'];
					});
					$ids = $ids['answer'];
				}
			} elseif ($options == 'conditions') {
				$workflowScheduler = new WorkFlowScheduler($adb);
				$query = $workflowScheduler->getWorkflowQuery($wf);
				try {
					$rs = $adb->query($query);
					if ($rs && $adb->num_rows($rs)>0) {
						$ids = array();
						while ($row = $adb->fetch_array($rs)) {
							$ids[] = $row[0]; // will be the ID field
						}
					}
				} catch (\Throwable $th) {
					global $log;
					$log->fatal('Mass Action condition query failed: '.$query);
				}
			} elseif ($options == 'recordset') {
				$cbmap = cbMap::getMapByID($recordset);
				$rsm = $cbmap->RecordSetMapping();
				$ids = $rsm->getRecordSet('include');
			}
			if ($ids) {
				foreach ($ids as $crmid) {
					if (strpos($crmid, 'x')) {
						list($void, $crmid) = explode('x', $crmid);
					}
					$crmids[] = $wsid.$crmid;
					$se = getSalesEntityType($crmid);
					$dp = getEntityName($se, $crmid);
					if (count($crmnames)<10) {
						$crmnames[] = '<a href="index.php?module='.$se.'&action=DetailView&record='.$crmid.'">'.$dp[$crmid].'</a>';
					}
				}
				if (count($crmnames)==10) {
					$crmnames[] = (count($crmids)-10).' '.getTranslatedString('LBL_MORE');
				}
				$cbmq = coreBOS_MQTM::getInstance();
				$msg = array(
					'wfid' => $wf->id,
					'crmids' => $crmids,
				);
				$cbmq->sendMessage('wfLaunchNowChannel', 'malaunchnow', 'malaunchnow', 'Data', '1:M', 0, 8640000, 0, 0, json_encode($msg));
			}
		}
		if (!empty($crmnames)) {
			coreBOS_Session::set('malaunch_records', $crmnames);
		}
	}
	?>
	<script type="text/javascript" charset="utf-8">
		window.location="<?php echo urldecode($returnUrl)?>";
	</script>
	<a href="<?php echo urldecode($returnUrl)?>">Return</a>
	<?php
}
Vtiger_Request::validateRequest();
vtWorkflowSave($adb, $_REQUEST);
?>