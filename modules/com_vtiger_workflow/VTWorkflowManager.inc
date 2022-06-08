<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'VTJsonCondition.inc';
require_once 'include/utils/ConfigReader.php';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'include/Webservices/Retrieve.php';
require_once 'modules/com_vtiger_workflow/VTWorkflow.php';

class VTWorkflowManager {

	public static $ON_FIRST_SAVE = 1;
	public static $ONCE = 2;
	public static $ON_EVERY_SAVE = 3;
	public static $ON_MODIFY = 4;
	public static $ON_DELETE = 5;
	public static $ON_SCHEDULE=6;
	public static $MANUAL = 7;
	public static $RECORD_ACCESS_CONTROL = 8;
	public static $ON_RELATE = 9;
	public static $ON_UNRELATE = 10;

	public function __construct($adb) {
		$this->adb = $adb;
	}

	public function save($workflow) {
		global $current_user;
		$adb=$this->adb;
		if (empty($workflow->schtypeid)) {
			$workflow->schtypeid = 0;
		}
		if (!isset($workflow->schtime)) {
			$workflow->schtime = '00:00:00';
		}
		if (!isset($workflow->schdayofmonth)) {
			$workflow->schdayofmonth = '';
		}
		if (!isset($workflow->schdayofweek)) {
			$workflow->schdayofweek = '';
		}
		if (!isset($workflow->schannualdates)) {
			$workflow->schannualdates = '';
		}
		if (!isset($workflow->schminuteinterval)) {
			$workflow->schminuteinterval = '';
		}
		if (!isset($workflow->purpose)) {
			$workflow->purpose = '';
		}
		if (!isset($workflow->relatemodule)) {
			$workflow->relatemodule = '';
		}
		if (!isset($workflow->wfstarton)) {
			$workflow->wfstarton = '';
		}
		if (!isset($workflow->wfendon)) {
			$workflow->wfendon = '';
		}
		if (!isset($workflow->options)) {
			$workflow->options = '';
		}
		if (!isset($workflow->cbquestion)) {
			$workflow->cbquestion = null;
		}
		if (!isset($workflow->recordset)) {
			$workflow->recordset = null;
		}
		if (!isset($workflow->onerecord)) {
			$workflow->onerecord = null;
		}
		$wf=$workflow;
		if (!empty($workflow->id)) {
			$adb->pquery(
				'update com_vtiger_workflows set
					module_name=?, summary=?, test=?, execution_condition=?, defaultworkflow=?,
					schtypeid=?, schtime=?, schdayofmonth=?, schdayofweek=?, schannualdates=?,schminuteinterval=?,
					purpose=?, relatemodule=?, wfstarton=?, wfendon=?, active=?, options=?, cbquestion=?,
					recordset=?, onerecord=? where workflow_id=?',
				array(
					$wf->moduleName,
					$wf->description,
					$wf->test,
					$wf->executionCondition,
					(isset($wf->defaultworkflow) ? $wf->defaultworkflow : (is_admin($current_user) ? 0 : 2)),
					$wf->schtypeid,
					$wf->schtime,
					$wf->schdayofmonth,
					$wf->schdayofweek,
					$wf->schannualdates,
					$wf->schminuteinterval,
					$wf->purpose,
					$wf->relatemodule,
					empty($wf->wfstarton) ? null : $wf->wfstarton,
					empty($wf->wfendon) ? null : $wf->wfendon,
					(empty($wf->active) || $wf->active=='true' ? 'true' : 'false'),
					$wf->options,
					$wf->cbquestion,
					$wf->recordset,
					$wf->onerecord,
					$wf->id,
				)
			);
		} else {
			$workflowId = $adb->getUniqueID('com_vtiger_workflows');
			$workflow->id = $workflowId;

			$result=$adb->getColumnNames('com_vtiger_workflows');
			if (in_array('type', $result)) {
				$vals = array(
					$workflowId,
					$wf->moduleName,
					$wf->description,
					$wf->test,
					$wf->executionCondition,
					$wf->type,
					(isset($wf->defaultworkflow) ? $wf->defaultworkflow : (is_admin($current_user) ? 0 : 2)),
					$wf->schtypeid,
					$wf->schtime,
					$wf->schdayofmonth,
					$wf->schdayofweek,
					$wf->schannualdates,
					$wf->schminuteinterval,
					$wf->purpose,
					$wf->relatemodule,
					empty($wf->wfstarton) ? null : $wf->wfstarton,
					empty($wf->wfendon) ? null : $wf->wfendon,
					(empty($wf->active) || $wf->active=='true' ? 'true' : 'false'),
					$wf->options,
					$wf->cbquestion,
					$wf->recordset,
					$wf->onerecord,
				);
				$adb->pquery(
					'insert into com_vtiger_workflows
						(workflow_id, module_name, summary, test, execution_condition, type, defaultworkflow, schtypeid, schtime, schdayofmonth,
						schdayofweek, schannualdates, schminuteinterval, purpose, relatemodule, wfstarton, wfendon, active, options, cbquestion, recordset, onerecord)
						values ('.generateQuestionMarks($vals).')',
					$vals
				);
			} else {
				$vals = array(
					$workflowId,
					$wf->moduleName,
					$wf->description,
					$wf->test,
					$wf->executionCondition,
					(isset($wf->defaultworkflow) ? $wf->defaultworkflow : (is_admin($current_user) ? 0 : 2)),
					$wf->schtypeid,
					$wf->schtime,
					$wf->schdayofmonth,
					$wf->schdayofweek,
					$wf->schannualdates,
					$wf->schminuteinterval,
					$wf->purpose,
					$wf->relatemodule,
					empty($wf->wfstarton) ? null : $wf->wfstarton,
					empty($wf->wfendon) ? null : $wf->wfendon,
					(empty($wf->active) || $wf->active=='true' ? 'true' : 'false'),
					$wf->options,
					$wf->cbquestion,
					$wf->recordset,
					$wf->onerecord,
				);
				$adb->pquery(
					'insert into com_vtiger_workflows
						(workflow_id, module_name, summary, test, execution_condition, defaultworkflow, schtypeid, schtime, schdayofmonth, schdayofweek,
						schdayofweek, schannualdates,schminuteinterval, purpose, relatemodule, wfstarton, wfendon, active, options, cbquestion, recordset, onerecord)
						values ('.generateQuestionMarks($vals).')',
					$vals
				);
			}
		}
		$this->updateNexTriggerTime($workflow);
	}

	public function getWorkflows() {
		global $current_user;
		$where = is_admin($current_user) ? '' : ' where defaultworkflow=2';
		$result = $this->adb->query(
			'select workflow_id, module_name, summary, test, execution_condition, defaultworkflow, type, purpose, relatemodule, wfstarton, wfendon, active, options, cbquestion, recordset, onerecord from com_vtiger_workflows'.$where
		);
		return $this->getWorkflowsForResult($result);
	}

	/**
	 * Function returns scheduled workflows
	 * @param DateTime $referenceTime
	 * @return Workflow
	 */
	public function getScheduledWorkflows($referenceTime = '') {
		$query = 'SELECT * FROM com_vtiger_workflows WHERE execution_condition=? and active=?';
		$params = array(VTWorkflowManager::$ON_SCHEDULE, 'true');
		if ($referenceTime != '') {
			$query .= ' AND (nexttrigger_time IS NULL OR nexttrigger_time <= ?)';
			$params[] = $referenceTime;
		}
		$result = $this->adb->pquery($query, $params);
		return $this->getWorkflowsForResult($result);
	}

	/**
	 * Function to get the number of scheduled workflows
	 * @return integer
	 */
	public function getScheduledWorkflowsCount() {
		$result = $this->adb->pquery(
			'SELECT count(*) AS count FROM com_vtiger_workflows WHERE execution_condition=? and active=?',
			array(VTWorkflowManager::$ON_SCHEDULE, 'true')
		);
		return $this->adb->query_result($result, 0, 'count');
	}

	/**
	 * Function returns the maximum allowed scheduled workflows
	 * @return integer
	 */
	public function getMaxAllowedScheduledWorkflows() {
		return GlobalVariable::getVariable('Workflow_Maximum_Scheduled', 10);
	}

	public function getWorkflowsForModule($moduleName, $onlyActive = false) {
		global $current_user;
		$where = is_admin($current_user) ? '' : ' and defaultworkflow=2';
		if ($onlyActive) {
			$where .= " and active='true'";
		}
		$result = $this->adb->pquery(
			'select workflow_id, module_name, summary, test, execution_condition, defaultworkflow, type, purpose, wfstarton, wfendon, active, options, cbquestion, recordset, onerecord from com_vtiger_workflows where module_name=?'.$where,
			array($moduleName)
		);
		return $this->getWorkflowsForResult($result);
	}

	public function getRACRuleForRecord($moduleName, $recordid) {
		$adb=$this->adb;
		$not_supported = array('RecycleBin','Users','Home','com_vtiger_workflow');
		if (in_array($moduleName, $not_supported)) {
			return false;
		}
		if (strpos($recordid, 'x')>0) {
			list($void,$recordid) = explode('x', $recordid);
		}
		$rrs = $adb->pquery('select setype,deleted from vtiger_crmobject where crmid=?', array($recordid));
		if (!$rrs) {
			return false;
		}
		$moduleName = $adb->query_result($rrs, 0, 'setype');
		$deleted = $adb->query_result($rrs, 0, 'deleted');
		if ($deleted!=0 || in_array($moduleName, $not_supported)) {
			return false;
		}
		if ($moduleName=='cbCalendar') {
			$actrs = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($recordid));
			if ($adb->query_result($actrs, 0, 'activitytype') == 'Emails') {
				$moduleName = 'Emails';
			}
		}
		$result = $adb->pquery('select id from vtiger_ws_entity where name=?', array($moduleName));
		$recordid = $adb->query_result($result, 0, 0).'x'.$recordid;
		$wf = $this->getoneRACWorkflowForRecord($moduleName, $recordid);
		if ($wf) {
			return $this->executeRACWorkflow($wf, $recordid);
		}
		return false;
	}

	public function getRACWorkflowsForModule($moduleName) {
		$adb=$this->adb;
		$result = $adb->pquery(
			'select * from com_vtiger_workflows where execution_condition=? and module_name=? and active=?',
			array(VTWorkflowManager::$RECORD_ACCESS_CONTROL, $moduleName, 'true')
		);
		return $this->getWorkflowsForResult($result);
	}

	public function getoneRACWorkflowForRecord($moduleName, $recordid) {
		$wfs = $this->getRACWorkflowsForModule($moduleName);
		if (count($wfs)==0) {
			return false;
		}
		$util = new VTWorkflowUtils();
		$adminUser = $util->adminUser();
		$entityCache = new VTEntityCache($adminUser);
		$entityCache->forId($recordid);
		$found = false;
		foreach ($wfs as $wf) {
			if (!$wf->activeWorkflow()) {
				continue;
			}
			$found = $wf->evaluate($entityCache, $recordid);
			if ($found) {
				break;
			}
		}
		$util->revertUser();
		if ($found) {
			return $wf;
		} else {
			return false;
		}
	}

	public function executeRACWorkflow($workflow, $recordid) {
		$tm = new VTTaskManager($this->adb);
		$tasks = $tm->getTasksForWorkflow($workflow->id);
		foreach ($tasks as $task) {
			if (is_object($task) && $task->active && get_class($task) == 'CBSelectcbMap') {
				return $task->doTask($recordid);
			}
		}
		return false;
	}

	public function getWorkflowsForResult($result) {
		$it = new SqlResultIterator($this->adb, $result);
		$workflows=array();
		foreach ($it as $row) {
			$workflow = $this->getWorkflowInstance($row->type);
			$workflow->setup($row->data);

			if (!is_a($workflow, 'Workflow')) {
				continue;
			}

			$workflows[$workflow->id]=$workflow;
		}
		return $workflows;
	}

	protected function getWorkflowInstance($type = 'basic') {
		$configReader = new ConfigReader('modules/com_vtiger_workflow/config.inc', 'workflowConfig');
		$workflowTypeConfig = $configReader->getConfig($type);
		$workflowClassPath = $workflowTypeConfig['classpath'];
		$workflowClass = $workflowTypeConfig['class'];
		require_once $workflowClassPath;
		return new $workflowClass();
	}

	/**
	 * Retrieve a workflow from the database
	 *
	 * Returns null if the workflow doesn't exist.
	 *
	 * @param integer id of the workflow
	 * @return Workflow object.
	 */
	public function retrieve($id) {
		$result = $this->adb->pquery('select * from com_vtiger_workflows where workflow_id=?', array($id));
		if ($this->adb->num_rows($result)) {
			$data = $this->adb->raw_query_result_rowdata($result, 0);
			$workflow = $this->getWorkflowInstance($data['type']);
			$workflow->setup($data);
			return $workflow;
		} else {
			return null;
		}
	}

	public function delete($id) {
		global $current_user;
		$dwf = is_admin($current_user) ? '!=1' : '=2';
		$count = $this->adb->pquery('SELECT count(*) FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow'.$dwf.')', array($id));
		$count = (int)$this->adb->query_result($count, 0, 0);
		if ($count) {
			$this->adb->pquery(
				'DELETE FROM com_vtiger_workflowtasks WHERE workflow_id IN
					(SELECT workflow_id FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow'.$dwf.'))',
				array($id)
			);
			$this->adb->pquery('DELETE FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow'.$dwf.')', array($id));
		}
		return $count;
	}

	public function newWorkflow($moduleName) {
		global $current_user;
		$workflow = $this->getWorkflowInstance();
		$workflow->moduleName = $moduleName;
		$workflow->executionCondition = (is_admin($current_user) ? self::$ON_EVERY_SAVE : self::$ON_SCHEDULE);
		$workflow->type = 'basic';
		$workflow->id = 0;
		$workflow->description = '';
		$workflow->purpose = '';
		$workflow->schtypeid = 0;
		$workflow->nexttrigger_time = '';
		$workflow->relatemodule = '';
		$workflow->wfstarton = '';
		$workflow->wfendon = '';
		$workflow->active = 'true';
		$workflow->options = '';
		$workflow->cbquestion = null;
		$workflow->cbquestiondisplay = '';
		$workflow->recordset = null;
		$workflow->recordsetdisplay = '';
		$workflow->onerecord = null;
		$workflow->onerecorddisplay = '';
		return $workflow;
	}

	/**
	 * Export a workflow as a json encoded string
	 *
	 * @param object workflow instance to export.
	 */
	public function serializeWorkflow($workflow) {
		$exp = array();
		$exp['moduleName'] = $workflow->moduleName;
		$exp['description'] = $workflow->description;
		$exp['purpose'] = $workflow->purpose;
		$exp['test'] = $workflow->test;
		$exp['executionCondition'] = $workflow->executionCondition;
		$exp['schtypeid'] = $workflow->schtypeid;
		$exp['schtime'] = $workflow->schtime;
		$exp['schdayofmonth'] = $workflow->schdayofmonth;
		$exp['schdayofweek'] = $workflow->schdayofweek;
		$exp['schannualdates'] = $workflow->schannualdates;
		$exp['schminuteinterval']=$workflow->schminuteinterval;
		$exp['relatemodule']=$workflow->relatemodule;
		$exp['wfstarton']=$workflow->wfstarton;
		$exp['wfendon']=$workflow->wfendon;
		$exp['active']=$workflow->active;
		$exp['options']=$workflow->options;
		$exp['cbquestion']=$workflow->cbquestion;
		if (empty($workflow->cbquestion)) {
			$exp['cbquestiondisplay'] = '';
		} else {
			$dp = getEntityName('cbQuestion', $workflow->cbquestion);
			$exp['cbquestiondisplay'] = $dp[$workflow->cbquestion];
		}
		$exp['recordset']=$workflow->recordset;
		if (empty($workflow->recordset)) {
			$exp['recordsetdisplay'] = '';
		} else {
			$dp = getEntityName('cbMap', $workflow->recordset);
			$exp['recordsetdisplay'] = $dp[$workflow->recordset];
		}
		$exp['onerecord']=$workflow->onerecord;
		if (empty($workflow->onerecord)) {
			$exp['onerecorddisplay'] = '';
		} else {
			$dp = getEntityName(getSalesEntityType($workflow->onerecord), $workflow->onerecord);
			$exp['onerecorddisplay'] = $dp[$workflow->onerecord];
		}
		$exp['tasks'] = array();
		$tm = new VTTaskManager($this->adb);
		$tasks = $tm->getTasksForWorkflow($workflow->id);
		foreach ($tasks as $task) {
			unset($task->id, $task->workflowId);
			$exp['tasks'][] = serialize($task);
		}
		return json_encode($exp);
	}

	/**
	 * Import a json encoded string as a workflow object
	 *
	 * @return object Workflow instance representing the imported workflow.
	 */
	public function deserializeWorkflow($str) {
		$data =  json_decode($str, true);
		$workflow = $this->newWorkflow($data['moduleName']);
		$workflow->description = $data['description'];
		$workflow->purpose = $data['purpose'];
		$workflow->test = $data['test'];
		$workflow->executionCondition = $data['executionCondition'];
		$workflow->schtypeid = $data['schtypeid'];
		$workflow->schtime = $data['schtime'];
		$workflow->schdayofmonth = $data['schdayofmonth'];
		$workflow->schdayofweek = $data['schdayofweek'];
		$workflow->schannualdates = $data['schannualdates'];
		$workflow->schminuteinterval=$data['schminuteinterval'];
		$workflow->relatemodule = $data['relatemodule'];
		$workflow->wfstarton = $data['wfstarton'];
		$workflow->wfendon = $data['wfendon'];
		$workflow->active = $data['active'];
		$workflow->options = $data['options'];
		$workflow->cbquestion = $data['cbquestion'];
		$workflow->cbquestiondisplay = $data['cbquestiondisplay'];
		$workflow->recordset = $data['recordset'];
		$workflow->recordsetdisplay = $data['recordsetdisplay'];
		$workflow->onerecord = $data['onerecord'];
		$workflow->onerecorddisplay = $data['onerecorddisplay'];
		$this->save($workflow);
		$tm = new VTTaskManager($this->adb);
		$tasks = $data['tasks'];
		foreach ($tasks as $taskStr) {
			$task = $tm->unserializeTask($taskStr);
			$task->workflowId = $workflow->id;
			$tm->saveTask($task);
		}
		return $workflow;
	}
	/**
	 * Update the Next trigger timestamp for a workflow
	 */
	public function updateNexTriggerTime($workflow) {
		$nextTriggerTime = $workflow->getNextTriggerTime();
		$workflow->setNextTriggerTime($nextTriggerTime);
	}
}
?>
