<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	require_once("VTJsonCondition.inc");
	require_once 'include/utils/ConfigReader.php';
	require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
	require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
	require_once 'modules/com_vtiger_workflow/include.inc';
	require_once 'include/Webservices/Retrieve.php';

	class VTWorkflowManager{

		static $ON_FIRST_SAVE = 1;
		static $ONCE = 2;
		static $ON_EVERY_SAVE = 3;
		static $ON_MODIFY = 4;
		static $ON_DELETE = 5;
		static $ON_SCHEDULE=6;
		static $MANUAL = 7;
		static $RECORD_ACCESS_CONTROL = 8;

		function __construct($adb){
			$this->adb = $adb;
		}

		function save($workflow){
			$adb=$this->adb;
			if(isset($workflow->id)){
				$wf=$workflow;
				$adb->pquery("update com_vtiger_workflows set
								module_name=?, summary=?, test=?, execution_condition=?, defaultworkflow=?,
								schtypeid=?, schtime=?, schdayofmonth=?, schdayofweek=?, schannualdates=?,schminuteinterval=? where workflow_id=?",
					array($wf->moduleName, $wf->description, $wf->test, $wf->executionCondition, $wf->defaultworkflow,
						$wf->schtypeid, $wf->schtime, $wf->schdayofmonth, $wf->schdayofweek, $wf->schannualdates, $wf->schminuteinterval,$wf->id));
			}else{
				$workflowId = $adb->getUniqueID("com_vtiger_workflows");
				$workflow->id = $workflowId;
				$wf=$workflow;

				$result=$adb->getColumnNames("com_vtiger_workflows");
				if(in_array("type",$result)) {
					$adb->pquery("insert into com_vtiger_workflows
									(workflow_id, module_name, summary, test, execution_condition, type, defaultworkflow,
									schtypeid, schtime, schdayofmonth, schdayofweek, schannualdates,schminuteinterval) values (?,?,?,?,?,?,?,?,?,?,?,?,?)",
						array($workflowId, $wf->moduleName, $wf->description, $wf->test,  $wf->executionCondition, $wf->type, $wf->defaultworkflow,
							$wf->schtypeid, $wf->schtime, $wf->schdayofmonth, $wf->schdayofweek, $wf->schannualdates,$wf->schminuteinterval));
				} else {
					$adb->pquery("insert into com_vtiger_workflows
									(workflow_id, module_name, summary, test, execution_condition, defaultworkflow,
									schtypeid, schtime, schdayofmonth, schdayofweek, schannualdates,schminuteinterval) values (?,?,?,?,?,?,?,?,?,?,?,?)",
						array($workflowId, $wf->moduleName, $wf->description, $wf->test,  $wf->executionCondition, $wf->defaultworkflow,
							$wf->schtypeid, $wf->schtime, $wf->schdayofmonth, $wf->schdayofweek, $wf->schannualdates,$wf->schminuteinterval));
				}
			}
			$this->updateNexTriggerTime($workflow);
		}


		function getWorkflows(){
			$adb=$this->adb;

			$result=$adb->getColumnNames("com_vtiger_workflows");
			if(in_array("defaultworkflow",$result)){
				$result = $adb->query("select workflow_id, module_name, summary, test, execution_condition,defaultworkflow, type
									from com_vtiger_workflows ");
			}else{

				$result = $adb->query("select workflow_id, module_name, summary, test, execution_condition, type
									from com_vtiger_workflows");
			}
			return $this->getWorkflowsForResult($result);

		}

		/**
		 * Function returns scheduled workflows
		 * @param DateTime $referenceTime
		 * @return Workflow
		 */
		function getScheduledWorkflows($referenceTime='') {
			$adb=$this->adb;
			$query = 'SELECT * FROM com_vtiger_workflows WHERE execution_condition = ?';
			$params = array(VTWorkflowManager::$ON_SCHEDULE);
			if($referenceTime != '') {
				$query .= " AND (nexttrigger_time = '' OR nexttrigger_time IS NULL OR nexttrigger_time <= ?)";
				array_push($params, $referenceTime);
			}
			$result = $adb->pquery($query, $params);
			return $this->getWorkflowsForResult($result);
		}

		/**
		 * Function to get the number of scheduled workflows
		 * @return Integer
		 */
		function getScheduledWorkflowsCount() {
			$adb=$this->adb;
			$query = 'SELECT count(*) AS count FROM com_vtiger_workflows WHERE execution_condition = ?';
			$params = array(VTWorkflowManager::$ON_SCHEDULE);
			$result = $adb->pquery($query, $params);
			return $adb->query_result($result, 0, 'count');
		}

		/**
		 * Function returns the maximum allowed scheduled workflows
		 * @return int
		 */
		function getMaxAllowedScheduledWorkflows() {
			return GlobalVariable::getVariable('Maximum_Scheduled_Workflows', 10);
		}

		function getWorkflowsForModule($moduleName){
			$adb=$this->adb;
			$result=$adb->getColumnNames("com_vtiger_workflows");
			if(in_array('defaultworkflow',$result)){
				$result = $adb->pquery("select workflow_id, module_name, summary, test, execution_condition, defaultworkflow, type
									from com_vtiger_workflows where module_name=?",array($moduleName));
			}
			else{
				$result = $adb->pquery("select workflow_id, module_name, summary, test, execution_condition, type
									from com_vtiger_workflows where module_name=?",array($moduleName));
			}
			return $this->getWorkflowsForResult($result);
		}

		function getRACRuleForRecord($moduleName,$recordid) {
			$adb=$this->adb;
			$not_supported = array('RecycleBin','Users','Home');
			if (in_array($moduleName, $not_supported)) {
				return false;
			}
			if (strpos($recordid,'x')>0) {
				list($void,$recordid) = explode('x', $recordid);
			}
			$rrs = $adb->pquery('select setype,deleted from vtiger_crmentity where crmid=?', array($recordid));
			if (!$rrs) return false;
			$rec = $adb->fetch_array($rrs);
			$moduleName = $adb->query_result($rrs, 0, 'setype');
			$deleted = $adb->query_result($rrs, 0, 'deleted');
			if ($deleted!=0 or in_array($moduleName, $not_supported)) {
				return false;
			}
			if ($moduleName=='Calendar') {
				$actrs = $adb->pquery('select activitytype from vtiger_activity where activityid=?',array($recordid));
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
			$result = $adb->pquery('select id from vtiger_ws_entity where name=?',array($moduleName));
			$recordid = $adb->query_result($result, 0, 0).'x'.$recordid;
			$wf = $this->getoneRACWorkflowForRecord($moduleName,$recordid);
			if ($wf) {
				return $this->executeRACWorkflow($wf,$recordid);
			}
			return false;
		}

		function getRACWorkflowsForModule($moduleName) {
			$adb=$this->adb;
			$result = $adb->pquery('select * from com_vtiger_workflows where execution_condition = ? and module_name=?',
				array(VTWorkflowManager::$RECORD_ACCESS_CONTROL,$moduleName));
			return $this->getWorkflowsForResult($result);
		}

		function getoneRACWorkflowForRecord($moduleName,$recordid) {
			$util = new VTWorkflowUtils();
			$adminUser = $util->adminUser();
			$entityCache = new VTEntityCache($adminUser);
			$entityData = $entityCache->forId($recordid);
			$wfs = $this->getRACWorkflowsForModule($moduleName);
			$found = false;
			foreach ($wfs as $wf) {
				$found = $wf->evaluate($entityCache, $recordid);
				if ($found) break;
			}
			$util->revertUser();
			if ($found) {
				return $wf;
			} else {
				return false;
			}
		}

		function executeRACWorkflow($workflow,$recordid) {
			$adb=$this->adb;
			$tm = new VTTaskManager($adb);
			$taskQueue = new VTTaskQueue($adb);
			$tasks = $tm->getTasksForWorkflow($workflow->id);
			foreach($tasks as $task){
				if(is_object($task) and $task->active and get_class($task) == 'CBSelectcbMap') {
					$void = array();
					return $task->doTask($recordid);
				}
			}
			return false;
		}

		function getWorkflowsForResult($result){
			$adb=$this->adb;

			$it = new SqlResultIterator($adb, $result);
			$workflows=array();
			foreach($it as $row){
				$workflow = $this->getWorkflowInstance($row->type);
				$workflow->setup($row->data);

				if(!is_a($workflow, 'Workflow')) continue;

				$workflows[$workflow->id]=$workflow;
			}
			return $workflows;
		}

		protected function getWorkflowInstance($type='basic') {
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
		function retrieve($id){
			$adb=$this->adb;
			$result = $adb->pquery("select * from com_vtiger_workflows where workflow_id=?", array($id));

			if($adb->num_rows($result)){
				$data = $adb->raw_query_result_rowdata($result, 0);
				$workflow = $this->getWorkflowInstance($data['type']);
				$workflow->setup($data);
				return $workflow;
			}else{
				return null;
			}
		}

		function delete($id){
			$adb=$this->adb;
			$adb->pquery("DELETE FROM com_vtiger_workflowtasks WHERE workflow_id IN
							(SELECT workflow_id FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow != 1))",
						array($id));
			$adb->pquery("DELETE FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow != 1)", array($id));
		}

		function newWorkflow($moduleName){
			$workflow = $this->getWorkflowInstance();
			$workflow->moduleName = $moduleName;
			$workflow->executionCondition = self::$ON_EVERY_SAVE;
			$workflow->type = 'basic';
			return $workflow;

		}

		/**
		 * Export a workflow as a json encoded string
		 *
		 * @param $workflow The workflow instance to export.
		 */
		public function serializeWorkflow($workflow){
			$exp = array();
			$exp['moduleName'] = $workflow->moduleName;
			$exp['description'] = $workflow->description;
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
			foreach($tasks as $task){
				unset($task->id);
				unset($task->workflowId);
				$exp['tasks'][] = serialize($task);
			}
			return  json_encode($exp);
		}

		/**
		 * Import a json encoded string as a workflow object
		 *
		 * @return The Workflow instance representing the imported workflow.
		 */
		public function deserializeWorkflow($str){
			$data =  json_decode($str,true);
			$workflow = $this->newWorkflow($data['moduleName']);
			$workflow->description = $data['description'];
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
			foreach($tasks as $taskStr){
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

	class Workflow{
		static $SCHEDULED_HOURLY = 1;
		static $SCHEDULED_DAILY = 2;
		static $SCHEDULED_WEEKLY = 3;
		static $SCHEDULED_ON_SPECIFIC_DATE = 4;
		static $SCHEDULED_MONTHLY_BY_DATE = 5;
		static $SCHEDULED_MONTHLY_BY_WEEKDAY = 6;
		static $SCHEDULED_ANNUALLY = 7;
		static $SCHEDULED_BY_MINUTE=8;

		function __construct(){
			$this->conditionStrategy = new VTJsonCondition();
		}

		function setup($row) {
			$this->id = $row['workflow_id'];
			$this->moduleName = $row['module_name'];
			$this->description = to_html(getTranslatedString($row['summary'],'com_vtiger_workflow'));
			$this->test = $row['test'];
			$this->executionCondition = $row['execution_condition'];
			$this->schtypeid = isset($row['schtypeid']) ? $row['schtypeid'] : '';
			$this->schtime = isset($row['schtime']) ? $row['schtime'] : '';
			$this->schdayofmonth = isset($row['schdayofmonth']) ? $row['schdayofmonth'] : '';
			$this->schdayofweek = isset($row['schdayofweek']) ? $row['schdayofweek'] : '';
			$this->schannualdates = isset($row['schannualdates']) ? $row['schannualdates'] : '';
			$this->schminuteinterval = isset($row['schminuteinterval']) ? $row['schminuteinterval'] : '';
			if($row['defaultworkflow']){
				$this->defaultworkflow=$row['defaultworkflow'];
			}
			$this->nexttrigger_time = isset($row['nexttrigger_time']) ? $row['nexttrigger_time'] : '';
		}

		function evaluate($entityCache, $id){
			if($this->test==""){
				return true;
			}else{
				$cs = $this->conditionStrategy;
				return $cs->evaluate($this->test, $entityCache, $id);
			}
		}

		function isCompletedForRecord($recordId) {
			global $adb;

			$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_activatedonce
							WHERE entity_id=? and workflow_id=?", array($recordId, $this->id));

			$result2=$adb->pquery("SELECT * FROM com_vtiger_workflowtasks
							INNER JOIN com_vtiger_workflowtask_queue
							ON com_vtiger_workflowtasks.task_id= com_vtiger_workflowtask_queue.task_id
							WHERE workflow_id=? AND entity_id=?",
							array($this->id,$recordId));

			if($adb->num_rows($result)===0 && $adb->num_rows($result2)===0) { // Workflow not done for specified record
				return false;
			} else {
				return true;
			}
		}

		function markAsCompletedForRecord($recordId) {
			global $adb;

			$adb->pquery("INSERT INTO com_vtiger_workflow_activatedonce (entity_id, workflow_id)
				VALUES (?,?)", array($recordId, $this->id));
		}

		function performTasks($entityData) {
			global $adb,$logbg;
			$logbg->debug('entering PerformTasks for Workflow: '.$this->id);
			$data = $entityData->getData();
			$util = new VTWorkflowUtils();
			$user = $util->adminUser();
			$entityCache = new VTEntityCache($user);
			$util->revertUser();
			require_once('modules/com_vtiger_workflow/VTTaskManager.inc');
			require_once('modules/com_vtiger_workflow/VTTaskQueue.inc');

			$tm = new VTTaskManager($adb);
			$taskQueue = new VTTaskQueue($adb);
			$tasks = $tm->getTasksForWorkflow($this->id);

			foreach($tasks as $task){
				if(is_object($task) and $task->active) {
					$logbg->debug($task->summary);
					$trigger = (empty($task->trigger) ? null : $task->trigger);
					if($trigger != null){
						$delay = strtotime($data[$trigger['field']])+$trigger['days']*86400;
					}else{
						$delay = 0;
					}
					if($task->executeImmediately==true){
						// we permit update field delayed tasks even though some may not make sense
						// for example a mathematical operation or a decision on a value of a field that
						// may change during the delay. This is for some certain types of updates, generally
						// absolute updates. You MUST know what you are doing when creating workflows.
						if ($delay!=0 and get_class($task) == 'VTUpdateFieldsTask') {
							$taskQueue->queueTask($task->id,$entityData->getId(), $delay);
						} else {
							if (empty($task->test) or $task->evaluate($entityCache, $entityData->getId())) {
								$task->doTask($entityData);
							}
						}
					}else{
						$taskQueue->queueTask($task->id,$entityData->getId(), $delay);
					}
				}
			}
		}

		function executionConditionAsLabel($label=null){
			if($label==null){
				$arr = array('ON_FIRST_SAVE', 'ONCE', 'ON_EVERY_SAVE', 'ON_MODIFY', 'ON_DELETE', 'ON_SCHEDULE', 'MANUAL', 'RECORD_ACCESS_CONTROL');
				return $arr[$this->executionCondition-1];
			}else{
				$arr = array(
					'ON_FIRST_SAVE'=>VTWorkflowManager::$ON_FIRST_SAVE,
					'ONCE'=>VTWorkflowManager::$ONCE,
					'ON_EVERY_SAVE'=>VTWorkflowManager::$ON_EVERY_SAVE,
					'ON_MODIFY'=>VTWorkflowManager::$ON_MODIFY,
					'ON_DELETE'=>VTWorkflowManager::$ON_DELETE,
					'ON_SCHEDULE'=>VTWorkflowManager::$ON_SCHEDULE,
					'MANUAL'=>VTWorkflowManager::$MANUAL,
					'RECORD_ACCESS_CONTROL'=>VTWorkflowManager::$RECORD_ACCESS_CONTROL,
				);
				$this->executionCondition = $arr[$label];
			}
		}
		function setNextTriggerTime($time) {
			if($time) {
				$db = PearDatabase::getInstance();
				$db->pquery("UPDATE com_vtiger_workflows SET nexttrigger_time=? WHERE workflow_id=?", array($time, $this->id));
				$this->nexttrigger_time = $time;
			}
		}

		function getNextTriggerTimeValue() {
			return $this->nexttrigger_time;
		}

		function getWFScheduleType(){
			return ($this->executionCondition == VTWorkflowManager::$ON_SCHEDULE ? $this->schtypeid : 0);
		}

		function getScheduleMinute(){
			return $this->schminuteinterval;
		}

		function getWFScheduleTime(){
			return $this->schtime;
		}

		function getWFScheduleDay(){
			return $this->schdayofmonth;
		}

		function getWFScheduleWeek(){
			return $this->schdayofweek;
		}

		function getWFScheduleAnnualDates(){
			return $this->schannualdates;
		}

		/**
		 * Function gets the next trigger for the workflows
		 * @global <String> $default_timezone
		 * @return type
		 */
		function getNextTriggerTime() {
			global $default_timezone;
			$admin = Users::getActiveAdminUser();
			$adminTimeZone = $admin->time_zone;
			@date_default_timezone_set($adminTimeZone);

			$scheduleType = $this->getWFScheduleType();
			$scheduleMinute= $this->getScheduleMinute();
			
            if($scheduleType==Workflow::$SCHEDULED_BY_MINUTE){
            	$nextTime=date("Y-m-d H:i:s",strtotime("+ $scheduleMinute minutes"));
            }

			if($scheduleType == Workflow::$SCHEDULED_HOURLY) {
				$nextTime = date("Y-m-d H:i:s",strtotime("+1 hour"));
			}

			if($scheduleType == Workflow::$SCHEDULED_DAILY) {
				$nextTime = $this->getNextTriggerTimeForDaily($this->getWFScheduleTime());
			}

			if($scheduleType == Workflow::$SCHEDULED_WEEKLY) {
				$nextTime = $this->getNextTriggerTimeForWeekly($this->getWFScheduleWeek(), $this->getWFScheduleTime());
			}

			if($scheduleType == Workflow::$SCHEDULED_MONTHLY_BY_DATE) {
				$nextTime = $this->getNextTriggerTimeForMonthlyByDate($this->getWFScheduleDay(), $this->getWFScheduleTime());
			}

			if($scheduleType == Workflow::$SCHEDULED_MONTHLY_BY_WEEKDAY) {
				$nextTime = $this->getNextTriggerTimeForMonthlyByWeekDay($this->getWFScheduleDay(), $this->getWFScheduleTime());
			}

			if($scheduleType == Workflow::$SCHEDULED_ON_SPECIFIC_DATE or $scheduleType == Workflow::$SCHEDULED_ANNUALLY) {
				$nextTime = $this->getNextTriggerTimeForAnnualDates($this->getWFScheduleAnnualDates(), $this->getWFScheduleTime());
			}
			@date_default_timezone_set($default_timezone);
			return $nextTime;
		}
		/**
		 * get next trigger time for daily
		 * @param type $schTime
		 * @return time
		 */
		function getNextTriggerTimeForDaily($scheduledTime) {
			$now = strtotime(date("Y-m-d H:i:s"));
			$todayScheduledTime = strtotime(date("Y-m-d H:i:s", strtotime($scheduledTime)));
			if ($now > $todayScheduledTime) {
				$nextTime = date("Y-m-d H:i:s", strtotime('+1 day ' . $scheduledTime));
			} else {
				$nextTime = date("Y-m-d H:i:s", $todayScheduledTime);
			}
			return $nextTime;
		}

		/**
		 * get next trigger Time For weekly
		 * @param type $scheduledDaysOfWeek
		 * @param type $scheduledTime
		 * @return <time>
		 */
		function getNextTriggerTimeForWeekly($scheduledDaysOfWeek, $scheduledTime) {
			$weekDays = array('0' => 'Sunday', '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');
			$currentTime = time();
			$currentWeekDay = date('N', $currentTime);
			$nextTime = null;
			if ($scheduledDaysOfWeek) {
				$scheduledDaysOfWeek = json_decode($scheduledDaysOfWeek,true);
				if (is_array($scheduledDaysOfWeek)) {
					// algorithm :
					//1. First sort all the weekdays(stored as 0,1,2,3 etc in db) and find the closest weekday which is greater than currentWeekDay
					//2. If found, set the next trigger date to the next weekday value in the same week.
					//3. If not found, set the trigger date to the next first value.
					$nextTriggerWeekDay = null;
					sort($scheduledDaysOfWeek);
					foreach ($scheduledDaysOfWeek as $index => $weekDay) {
						if ($weekDays[$weekDay-1] == $weekDays[$currentWeekDay]) { //if today is the weekday selected
							$scheduleWeekDayInTime = strtotime(date('Y-m-d', strtotime($weekDays[$currentWeekDay])) . ' ' . $scheduledTime);
							if ($currentTime < $scheduleWeekDayInTime) { //if the scheduled time is greater than current time, select today
								$nextTriggerWeekDay = $currentWeekDay;
								break;
							} else {
								//current time greater than scheduled time, get the next weekday
								if (count($scheduledDaysOfWeek) == 1) { //if only one weekday selected, then get next week
									$nextTime = date('Y-m-d', strtotime('next ' . $weekDays[$weekDay-1])) . ' ' . $scheduledTime;
								} else {
									$nextWeekDay = $scheduledDaysOfWeek[$index + 1];
									if (empty($nextWeekDay)) { // its the last day of the week i.e. sunday
										$nextWeekDay = $scheduledDaysOfWeek[0];
									}
									$nextTime = date('Y-m-d', strtotime('next ' . $weekDays[$nextWeekDay-1])) . ' ' . $scheduledTime;
								}
							}
						} else if ($weekDay-1 > $currentWeekDay) {
							$nextTriggerWeekDay = $weekDay-1;
							break;
						}
					}
					if ($nextTime == null) {
						if (!empty($nextTriggerWeekDay)) {
							$nextTime = date("Y-m-d H:i:s", strtotime($weekDays[$nextTriggerWeekDay] . ' ' . $scheduledTime));
						} else {
							$nextTime = date("Y-m-d H:i:s", strtotime($weekDays[$scheduledDaysOfWeek[0]-1] . ' ' . $scheduledTime));
						}
					}
				}
			}
			return $nextTime;
		}

		/**
		 * get next triggertime for monthly
		 * @param type $scheduledDayOfMonth
		 * @param type $scheduledTime
		 * @return <time>
		 */
		function getNextTriggerTimeForMonthlyByDate($scheduledDayOfMonth, $scheduledTime){
			$currentDayOfMonth = date('j', time());
			if($scheduledDayOfMonth) {
				$scheduledDaysOfMonth = json_decode($scheduledDayOfMonth,true);
				if(is_array($scheduledDaysOfMonth)) {
					// algorithm :
					//1. First sort all the days in ascending order and find the closest day which is greater than currentDayOfMonth
					//2. If found, set the next trigger date to the found value which is in the same month.
					//3. If not found, set the trigger date to the next month's first selected value.
					$nextTriggerDay = null;
					sort($scheduledDaysOfMonth);
					foreach ($scheduledDaysOfMonth as $day) {
						if($day == $currentDayOfMonth) {
							$currentTime = time();
							$schTime = strtotime($date = date('Y').'-'.date('m').'-'.$day.' '.$scheduledTime);
							if($schTime > $currentTime) {
								$nextTriggerDay = $day;
								break;
							}
						} elseif ($day > $currentDayOfMonth) {
							$nextTriggerDay = $day;
							break;
						}
					}
					if(!empty($nextTriggerDay)) {
						$firstDayofNextMonth = date('Y:m:d H:i:s', strtotime('first day of this month'));
						$nextTime = date('Y:m:d', strtotime($firstDayofNextMonth.' + '.($nextTriggerDay-1).' days'));
						$nextTime = $nextTime.' '.$scheduledTime;
					} else {
						$firstDayofNextMonth = date('Y:m:d H:i:s', strtotime('first day of next month'));
						$nextTime = date('Y:m:d', strtotime($firstDayofNextMonth.' + '.($scheduledDaysOfMonth[0]-1).' days'));
						$nextTime = $nextTime.' '.$scheduledTime;
					}
				}
			}
			return $nextTime;
		}
		
		/**
		 * to get next trigger time for weekday of the month
		 * @param type $scheduledWeekDayOfMonth
		 * @param type $scheduledTime
		 * @return <time>
		 */
		function getNextTriggerTimeForMonthlyByWeekDay($scheduledWeekDayOfMonth, $scheduledTime){
			$currentTime = time();
			$currentDayOfMonth = date('j',$currentTime);
			$scheduledTime = $this->getWFScheduleTime();
			if($scheduledWeekDayOfMonth == $currentDayOfMonth) {
				$nextTime = date("Y-m-d H:i:s",strtotime('+1 month '.$scheduledTime));
			} else {
				$monthInFullText = date('F',$currentTime);
				$yearFullNumberic = date('Y',$currentTime);
				if($scheduledWeekDayOfMonth < $currentDayOfMonth) {
					$nextMonth = date("Y-m-d H:i:s",strtotime('next month'));
					$monthInFullText = date('F',strtotime($nextMonth));
				}
				$nextTime = date("Y-m-d H:i:s",strtotime($scheduledWeekDayOfMonth.' '.$monthInFullText.' '.$yearFullNumberic.' '.$scheduledTime));
			}
			return $nextTime;
		}
		
		/**
		 * to get next trigger time
		 * @param type $annualDates
		 * @param type $scheduledTime
		 * @return <time>
		 */
		function getNextTriggerTimeForAnnualDates($annualDates, $scheduledTime){
			if($annualDates) {
				$today = date('Y-m-d');
				$annualDates = json_decode($annualDates);
				$nextTriggerDay = null;
				// sort the dates
				sort($annualDates);
				$currentTime = time();
				$currentDayOfMonth = date('Y-m-d',$currentTime);
				foreach ($annualDates as $day) {
					if($day == $currentDayOfMonth) {
						$schTime = strtotime($day.' '.$scheduledTime);
						if($schTime > $currentTime) {
							$nextTriggerDay = $day;
							break;
						}
					}else if ($day > $today) {
						$nextTriggerDay = $day;
						break;
					}
				}
				if(!empty($nextTriggerDay)) {
					$nextTime = date('Y:m:d H:i:s', strtotime($nextTriggerDay.' '.$scheduledTime));
				} else {
					$nextTriggerDay = $annualDates[0];
					$nextTime = date('Y:m:d H:i:s', strtotime($nextTriggerDay.' '.$scheduledTime.'+1 year'));
				}
			}
			return $nextTime;
		}
	}
?>