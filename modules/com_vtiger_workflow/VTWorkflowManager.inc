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

	public function __construct($adb) {
		$this->adb = $adb;
	}

	public function save($workflow) {
		$adb=$this->adb;
		if (!isset($workflow->schtypeid)) {
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
		$wf=$workflow;
		if (!empty($workflow->id)) {
			$adb->pquery(
				'update com_vtiger_workflows set
					module_name=?, summary=?, test=?, execution_condition=?, defaultworkflow=?,
					schtypeid=?, schtime=?, schdayofmonth=?, schdayofweek=?, schannualdates=?,schminuteinterval=?, purpose=? where workflow_id=?',
				array($wf->moduleName, $wf->description, $wf->test, $wf->executionCondition, (isset($wf->defaultworkflow) ? $wf->defaultworkflow : 0),
					$wf->schtypeid,
					$wf->schtime,
					$wf->schdayofmonth,
					$wf->schdayofweek,
					$wf->schannualdates,
					$wf->schminuteinterval,
					$wf->purpose,
					$wf->id)
			);
		} else {
			$workflowId = $adb->getUniqueID("com_vtiger_workflows");
			$workflow->id = $workflowId;

			$result=$adb->getColumnNames("com_vtiger_workflows");
			if (in_array("type", $result)) {
				$adb->pquery(
					'insert into com_vtiger_workflows
						(workflow_id, module_name, summary, test, execution_condition, type, defaultworkflow,
						schtypeid, schtime, schdayofmonth, schdayofweek, schannualdates, schminuteinterval, purpose) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
					array($workflowId, $wf->moduleName, $wf->description, $wf->test,  $wf->executionCondition, $wf->type,
						(isset($wf->defaultworkflow) ? $wf->defaultworkflow : 0),
						$wf->schtypeid,
						$wf->schtime,
						$wf->schdayofmonth,
						$wf->schdayofweek,
						$wf->schannualdates,
						$wf->schminuteinterval,
						$wf->purpose)
				);
			} else {
				$adb->pquery(
					'insert into com_vtiger_workflows
						(workflow_id, module_name, summary, test, execution_condition, defaultworkflow,
						schtypeid, schtime, schdayofmonth, schdayofweek, schannualdates,schminuteinterval, purpose) values (?,?,?,?,?,?,?,?,?,?,?,?)',
					array($workflowId, $wf->moduleName, $wf->description, $wf->test,  $wf->executionCondition, (isset($wf->defaultworkflow) ? $wf->defaultworkflow : 0),
						$wf->schtypeid,
						$wf->schtime,
						$wf->schdayofmonth,
						$wf->schdayofweek,
						$wf->schannualdates,
						$wf->schminuteinterval,
						$wf->purpose)
				);
			}
		}
		$this->updateNexTriggerTime($workflow);
	}

	public function getWorkflows() {
		$result=$this->adb->getColumnNames('com_vtiger_workflows');
		if (in_array('defaultworkflow', $result)) {
			$result = $this->adb->query('select workflow_id, module_name, summary, test, execution_condition,defaultworkflow, type, purpose from com_vtiger_workflows');
		} else {
			$result = $this->adb->query('select workflow_id, module_name, summary, test, execution_condition, type, purpose from com_vtiger_workflows');
		}
		return $this->getWorkflowsForResult($result);
	}

	/**
	 * Function returns scheduled workflows
	 * @param DateTime $referenceTime
	 * @return Workflow
	 */
	public function getScheduledWorkflows($referenceTime = '') {
		$query = 'SELECT * FROM com_vtiger_workflows WHERE execution_condition = ?';
		$params = array(VTWorkflowManager::$ON_SCHEDULE);
		if ($referenceTime != '') {
			$query .= " AND (nexttrigger_time = '' OR nexttrigger_time IS NULL OR nexttrigger_time <= ?)";
			$params[] = $referenceTime;
		}
		$result = $this->adb->pquery($query, $params);
		return $this->getWorkflowsForResult($result);
	}

	/**
	 * Function to get the number of scheduled workflows
	 * @return Integer
	 */
	public function getScheduledWorkflowsCount() {
		$result = $this->adb->pquery('SELECT count(*) AS count FROM com_vtiger_workflows WHERE execution_condition = ?', array(VTWorkflowManager::$ON_SCHEDULE));
		return $this->adb->query_result($result, 0, 'count');
	}

	/**
	 * Function returns the maximum allowed scheduled workflows
	 * @return int
	 */
	public function getMaxAllowedScheduledWorkflows() {
		return GlobalVariable::getVariable('Workflow_Maximum_Scheduled', 10);
	}

	public function getWorkflowsForModule($moduleName) {
		$result=$this->adb->getColumnNames('com_vtiger_workflows');
		if (in_array('defaultworkflow', $result)) {
			$result = $this->adb->pquery(
				'select workflow_id, module_name, summary, test, execution_condition, defaultworkflow, type, purpose from com_vtiger_workflows where module_name=?',
				array($moduleName)
			);
		} else {
			$result = $this->adb->pquery(
				'select workflow_id, module_name, summary, test, execution_condition, type, purpose from com_vtiger_workflows where module_name=?',
				array($moduleName)
			);
		}
		return $this->getWorkflowsForResult($result);
	}

	public function getRACRuleForRecord($moduleName, $recordid) {
		$adb=$this->adb;
		$not_supported = array('RecycleBin','Users','Home');
		if (in_array($moduleName, $not_supported)) {
			return false;
		}
		if (strpos($recordid, 'x')>0) {
			list($void,$recordid) = explode('x', $recordid);
		}
		$rrs = $adb->pquery('select setype,deleted from vtiger_crmentity where crmid=?', array($recordid));
		if (!$rrs) {
			return false;
		}
		$moduleName = $adb->query_result($rrs, 0, 'setype');
		$deleted = $adb->query_result($rrs, 0, 'deleted');
		if ($deleted!=0 || in_array($moduleName, $not_supported)) {
			return false;
		}
		if ($moduleName=='Calendar') {
			$actrs = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($recordid));
			$actvity_type = $adb->query_result($actrs, 0, 'activitytype');
			switch ($actvity_type) {
				case 'Emails':
					$moduleName = 'Emails';
					break;
				case 'Task':
					$moduleName = 'Calendar';
					break;
				default:
					$moduleName = 'Events';
					break;
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
			'select * from com_vtiger_workflows where execution_condition = ? and module_name=?',
			array(VTWorkflowManager::$RECORD_ACCESS_CONTROL,$moduleName)
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
		$workflow = new $workflowClass();
		return $workflow;
	}

	/**
	 * Retrieve a workflow from the database
	 *
	 * Returns null if the workflow doesn't exist.
	 *
	 * @param The id of the workflow
	 * @return A workflow object.
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
		$this->adb->pquery(
			'DELETE FROM com_vtiger_workflowtasks WHERE workflow_id IN
				(SELECT workflow_id FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow != 1))',
			array($id)
		);
		$this->adb->pquery('DELETE FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow != 1)', array($id));
	}

	public function newWorkflow($moduleName) {
		$workflow = $this->getWorkflowInstance();
		$workflow->moduleName = $moduleName;
		$workflow->executionCondition = self::$ON_EVERY_SAVE;
		$workflow->type = 'basic';
		$workflow->id = 0;
		$workflow->description = '';
		$workflow->purpose = '';
		$workflow->schtypeid = 0;
		$workflow->nexttrigger_time = '';
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