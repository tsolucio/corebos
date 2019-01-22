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
require_once 'VTWorkflowApplication.inc';

class Workflow {
	public static $SCHEDULED_HOURLY = 1;
	public static $SCHEDULED_DAILY = 2;
	public static $SCHEDULED_WEEKLY = 3;
	public static $SCHEDULED_ON_SPECIFIC_DATE = 4;
	public static $SCHEDULED_MONTHLY_BY_DATE = 5;
	public static $SCHEDULED_MONTHLY_BY_WEEKDAY = 6;
	public static $SCHEDULED_ANNUALLY = 7;
	public static $SCHEDULED_BY_MINUTE=8;

	public function __construct() {
		$this->conditionStrategy = new VTJsonCondition();
	}

	public $sortby_fields = array('module_name','workflow_id');

	// This is the list of vtiger_fields that are in the lists.
	public $list_fields = array(
			'Module' => array('com_vtiger_workflows'=>'module_name'),
			'Description' => array('com_vtiger_workflows'=>'summary'),
			'Purpose' => array('com_vtiger_workflows'=>'purpose'),
			'Trigger' => array('com_vtiger_workflows'=> 'execution_condition'),
			'Tools' => array('com_vtiger_workflows'=>'workflow_id'),
		);

	public $list_fields_name = array(
			'Module'=> 'module_name',
			'Description' => 'summary',
			'Purpose' =>'purpose',
			'Trigger' => 'execution_condition',
			'Tools' => 'workflow_id',
		);

	public $default_order_by = "module_name";
	public $default_sort_order = 'DESC';

	/**
	 * Function to get the Headers of Workflow List Information like Module, Description, Purpose, Trigger.
	 * Returns Header Values like Module, Description etc in an array format.
	**/
	public function getWorkListHeader() {
		global $log, $app_strings;
		$log->debug('Entering getAuditTrailHeader() method ...');
		$header_array = array(
			$app_strings['LBL_MODULE'],
			$app_strings['LBL_DESCRIPTION'],
			$app_strings['LBL_PURPOSE'],
			$app_strings['LBL_TRIGGER'],
			$app_strings['LBL_TOOLS'],
		);
		$log->debug('Exiting getAuditTrailHeader() method ...');
		return $header_array;
	}

	public function setup($row) {
		$this->id = $row['workflow_id'];
		$this->moduleName = $row['module_name'];
		$this->description = to_html(getTranslatedString($row['summary'], 'com_vtiger_workflow'));
		$this->test = $row['test'];
		$this->select_expressions = isset($row['select_expressions']) ? $row['select_expressions'] : '';
		$this->executionCondition = $row['execution_condition'];
		$this->schtypeid = isset($row['schtypeid']) ? $row['schtypeid'] : '';
		$this->schtime = isset($row['schtime']) ? $row['schtime'] : '';
		$this->schdayofmonth = isset($row['schdayofmonth']) ? $row['schdayofmonth'] : '';
		$this->schdayofweek = isset($row['schdayofweek']) ? $row['schdayofweek'] : '';
		$this->schannualdates = isset($row['schannualdates']) ? $row['schannualdates'] : '';
		$this->schminuteinterval = isset($row['schminuteinterval']) ? $row['schminuteinterval'] : '';
		if ($row['defaultworkflow']) {
			$this->defaultworkflow=$row['defaultworkflow'];
		}
		$this->purpose = isset($row['purpose']) ? $row['purpose'] : '';
		$this->nexttrigger_time = isset($row['nexttrigger_time']) ? $row['nexttrigger_time'] : '';
	}

	public function evaluate($entityCache, $id) {
		if ($this->test=='') {
			return true;
		} else {
			$cs = $this->conditionStrategy;
			return $cs->evaluate($this->test, $entityCache, $id);
		}
	}

	public function isCompletedForRecord($recordId) {
		global $adb;
		$result = $adb->pquery('SELECT * FROM com_vtiger_workflow_activatedonce WHERE entity_id=? and workflow_id=?', array($recordId, $this->id));
		$result2=$adb->pquery(
			'SELECT *
			FROM com_vtiger_workflowtasks
			INNER JOIN com_vtiger_workflowtask_queue ON com_vtiger_workflowtasks.task_id= com_vtiger_workflowtask_queue.task_id
			WHERE workflow_id=? AND entity_id=?',
			array($this->id, $recordId)
		);

		if ($adb->num_rows($result)===0 && $adb->num_rows($result2)===0) { // Workflow not done for specified record
			return false;
		} else {
			return true;
		}
	}

	public function markAsCompletedForRecord($recordId) {
		global $adb;
		$adb->pquery('INSERT INTO com_vtiger_workflow_activatedonce (entity_id, workflow_id) VALUES (?,?)', array($recordId, $this->id));
	}

	public function performTasks($entityData) {
		global $adb,$logbg;
		$logbg->debug('entering PerformTasks for Workflow: '.$this->id);
		$data = $entityData->getData();
		$util = new VTWorkflowUtils();
		$user = $util->adminUser();
		$entityCache = new VTEntityCache($user);
		$util->revertUser();
		require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
		require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';

		$tm = new VTTaskManager($adb);
		$taskQueue = new VTTaskQueue($adb);
		$tasks = $tm->getTasksForWorkflow($this->id);

		foreach ($tasks as $task) {
			if (is_object($task) && $task->active) {
				$logbg->debug($task->summary);
				$trigger = (empty($task->trigger) ? null : $task->trigger);
				if ($trigger != null) {
					$delay = strtotime($data[$trigger['field']])+$trigger['days']*86400;
				} else {
					$delay = 0;
				}
				if ($task->executeImmediately==true) {
					// we permit update field delayed tasks even though some may not make sense
					// for example a mathematical operation or a decision on a value of a field that
					// may change during the delay. This is for some certain types of updates, generally
					// absolute updates. You MUST know what you are doing when creating workflows.
					if ($delay!=0 && get_class($task) == 'VTUpdateFieldsTask') {
						$taskQueue->queueTask($task->id, $entityData->getId(), $delay);
					} else {
						if (empty($task->test) || $task->evaluate($entityCache, $entityData->getId())) {
							$task->doTask($entityData);
						}
					}
				} else {
					$taskQueue->queueTask($task->id, $entityData->getId(), $delay);
				}
			}
		}
	}

	public function executionConditionAsLabel($label = null) {
		if ($label==null) {
			$arr = array('ON_FIRST_SAVE', 'ONCE', 'ON_EVERY_SAVE', 'ON_MODIFY', 'ON_DELETE', 'ON_SCHEDULE', 'MANUAL', 'RECORD_ACCESS_CONTROL');
			return $arr[$this->executionCondition-1];
		} else {
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

	public function setNextTriggerTime($time) {
		if ($time) {
			$db = PearDatabase::getInstance();
			$db->pquery('UPDATE com_vtiger_workflows SET nexttrigger_time=? WHERE workflow_id=?', array($time, $this->id));
			$this->nexttrigger_time = $time;
		}
	}

	public function getNextTriggerTimeValue() {
		return $this->nexttrigger_time;
	}

	public function getWFScheduleType() {
		return ($this->executionCondition == VTWorkflowManager::$ON_SCHEDULE ? $this->schtypeid : 0);
	}

	public function getScheduleMinute() {
		return $this->schminuteinterval;
	}

	public function getWFScheduleTime() {
		return $this->schtime;
	}

	public function getWFScheduleDay() {
		return $this->schdayofmonth;
	}

	public function getWFScheduleWeek() {
		return $this->schdayofweek;
	}

	public function getWFScheduleAnnualDates() {
		return $this->schannualdates;
	}

	/**
	 * Function gets the next trigger for the workflows
	 * @global <String> $default_timezone
	 * @return string time
	 */
	public function getNextTriggerTime() {
		global $default_timezone;
		$admin = Users::getActiveAdminUser();
		$adminTimeZone = $admin->time_zone;
		@date_default_timezone_set($adminTimeZone);

		$scheduleType = $this->getWFScheduleType();
		$scheduleMinute= $this->getScheduleMinute();
		$nextTime = date('Y-m-d H:i:s');
		if ($scheduleType==Workflow::$SCHEDULED_BY_MINUTE) {
			$nextTime=date("Y-m-d H:i:s", strtotime("+ $scheduleMinute minutes"));
		}

		if ($scheduleType == Workflow::$SCHEDULED_HOURLY) {
			$nextTime = date("Y-m-d H:i:s", strtotime("+1 hour"));
		}

		if ($scheduleType == Workflow::$SCHEDULED_DAILY) {
			$nextTime = $this->getNextTriggerTimeForDaily($this->getWFScheduleTime());
		}

		if ($scheduleType == Workflow::$SCHEDULED_WEEKLY) {
			$nextTime = $this->getNextTriggerTimeForWeekly($this->getWFScheduleWeek(), $this->getWFScheduleTime());
		}

		if ($scheduleType == Workflow::$SCHEDULED_MONTHLY_BY_DATE) {
			$nextTime = $this->getNextTriggerTimeForMonthlyByDate($this->getWFScheduleDay(), $this->getWFScheduleTime());
		}

		if ($scheduleType == Workflow::$SCHEDULED_MONTHLY_BY_WEEKDAY) {
			$nextTime = $this->getNextTriggerTimeForMonthlyByWeekDay($this->getWFScheduleDay(), $this->getWFScheduleTime());
		}

		if ($scheduleType == Workflow::$SCHEDULED_ON_SPECIFIC_DATE || $scheduleType == Workflow::$SCHEDULED_ANNUALLY) {
			$nextTime = $this->getNextTriggerTimeForAnnualDates($this->getWFScheduleAnnualDates(), $this->getWFScheduleTime());
		}
		@date_default_timezone_set($default_timezone);
		return $nextTime;
	}

	/**
	 * get next trigger time for daily
	 * @param string $schTime
	 * @return DateTime
	 */
	public function getNextTriggerTimeForDaily($scheduledTime) {
		$now = strtotime(date('Y-m-d H:i:s'));
		$todayScheduledTime = strtotime(date('Y-m-d H:i:s', strtotime($scheduledTime)));
		if ($now > $todayScheduledTime) {
			$nextTime = date('Y-m-d H:i:s', strtotime('+1 day ' . $scheduledTime));
		} else {
			$nextTime = date('Y-m-d H:i:s', $todayScheduledTime);
		}
		return $nextTime;
	}

	/**
	 * get next trigger Time For weekly
	 * @param integer $scheduledDaysOfWeek
	 * @param string $scheduledTime
	 * @return <time>
	 */
	public function getNextTriggerTimeForWeekly($scheduledDaysOfWeek, $scheduledTime) {
		$weekDays = array('0' => 'Sunday', '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');
		$currentTime = time();
		$currentWeekDay = date('N', $currentTime);
		$nextTime = null;
		if ($scheduledDaysOfWeek) {
			$scheduledDaysOfWeek = json_decode($scheduledDaysOfWeek, true);
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
					} elseif ($weekDay-1 > $currentWeekDay) {
						$nextTriggerWeekDay = $weekDay-1;
						break;
					}
				}
				if ($nextTime == null) {
					if (!empty($nextTriggerWeekDay)) {
						$nextTime = date('Y-m-d H:i:s', strtotime($weekDays[$nextTriggerWeekDay] . ' ' . $scheduledTime));
					} else {
						$nextTime = date('Y-m-d H:i:s', strtotime($weekDays[$scheduledDaysOfWeek[0]-1] . ' ' . $scheduledTime));
					}
				}
			}
		}
		return $nextTime;
	}

	/**
	 * get next triggertime for monthly
	 * @param integer $scheduledDayOfMonth
	 * @param string $scheduledTime
	 * @return <time>
	 */
	public function getNextTriggerTimeForMonthlyByDate($scheduledDayOfMonth, $scheduledTime) {
		$currentDayOfMonth = date('j', time());
		if ($scheduledDayOfMonth) {
			$scheduledDaysOfMonth = json_decode($scheduledDayOfMonth, true);
			if (is_array($scheduledDaysOfMonth)) {
				// algorithm :
				//1. First sort all the days in ascending order and find the closest day which is greater than currentDayOfMonth
				//2. If found, set the next trigger date to the found value which is in the same month.
				//3. If not found, set the trigger date to the next month's first selected value.
				$nextTriggerDay = null;
				sort($scheduledDaysOfMonth);
				foreach ($scheduledDaysOfMonth as $day) {
					if ($day == $currentDayOfMonth) {
						$currentTime = time();
						$schTime = strtotime(date('Y-m-').$day.' '.$scheduledTime);
						if ($schTime > $currentTime) {
							$nextTriggerDay = $day;
							break;
						}
					} elseif ($day > $currentDayOfMonth) {
						$nextTriggerDay = $day;
						break;
					}
				}
				if (!empty($nextTriggerDay)) {
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
	 * @param integer $scheduledWeekDayOfMonth
	 * @param string $scheduledTime
	 * @return <time>
	 */
	public function getNextTriggerTimeForMonthlyByWeekDay($scheduledWeekDayOfMonth, $scheduledTime) {
		$currentTime = time();
		$currentDayOfMonth = date('j', $currentTime);
		$scheduledTime = $this->getWFScheduleTime();
		if ($scheduledWeekDayOfMonth == $currentDayOfMonth) {
			$nextTime = date("Y-m-d H:i:s", strtotime('+1 month '.$scheduledTime));
		} else {
			$monthInFullText = date('F', $currentTime);
			$yearFullNumberic = date('Y', $currentTime);
			if ($scheduledWeekDayOfMonth < $currentDayOfMonth) {
				$nextMonth = date("Y-m-d H:i:s", strtotime('next month'));
				$monthInFullText = date('F', strtotime($nextMonth));
			}
			$nextTime = date("Y-m-d H:i:s", strtotime($scheduledWeekDayOfMonth.' '.$monthInFullText.' '.$yearFullNumberic.' '.$scheduledTime));
		}
		return $nextTime;
	}

	/**
	 * to get next trigger time
	 * @param string $annualDates
	 * @param string $scheduledTime
	 * @return <time>
	 */
	public function getNextTriggerTimeForAnnualDates($annualDates, $scheduledTime) {
		if ($annualDates) {
			$today = date('Y-m-d');
			$annualDates = json_decode($annualDates);
			$nextTriggerDay = null;
			// sort the dates
			sort($annualDates);
			$currentTime = time();
			$currentDayOfMonth = date('Y-m-d', $currentTime);
			foreach ($annualDates as $day) {
				if ($day == $currentDayOfMonth) {
					$schTime = strtotime($day.' '.$scheduledTime);
					if ($schTime > $currentTime) {
						$nextTriggerDay = $day;
						break;
					}
				} elseif ($day > $today) {
					$nextTriggerDay = $day;
					break;
				}
			}
			if (!empty($nextTriggerDay)) {
				$nextTime = date('Y:m:d H:i:s', strtotime($nextTriggerDay.' '.$scheduledTime));
			} else {
				$nextTriggerDay = $annualDates[0];
				$nextTime = date('Y:m:d H:i:s', strtotime($nextTriggerDay.' '.$scheduledTime.'+1 year'));
			}
		}
		return $nextTime;
	}

	/**
	 * public function getWorkFlowJSON($userid, $page, $order_by = 'module_name', $sorder = 'DESC', $action_search = '')
	 */
	public function getWorkFlowJSON($modulename, $executioncondtionid, $page, $order_by = 'module_name', $sorder = 'DESC', $desc_search = '', $purpose_search = '') {
		global $log, $adb;
		$log->debug('Entering getWorkFlowJSON() method ...');

		$workflow_execution_condtion_list = array(
			VTWorkflowManager::$ON_FIRST_SAVE => 'LBL_ONLY_ON_FIRST_SAVE',
			VTWorkflowManager::$ONCE => 'LBL_UNTIL_FIRST_TIME_CONDITION_TRUE',
			VTWorkflowManager::$ON_EVERY_SAVE => 'LBL_EVERYTIME_RECORD_SAVED',
			VTWorkflowManager::$ON_MODIFY => 'LBL_ON_MODIFY',
			VTWorkflowManager::$ON_DELETE => 'LBL_ON_DELETE',
			VTWorkflowManager::$ON_SCHEDULE => 'LBL_ON_SCHEDULE',
			VTWorkflowManager::$MANUAL => 'LBL_MANUAL',
			VTWorkflowManager::$RECORD_ACCESS_CONTROL => 'LBL_RECORD_ACCESS_CONTROL',
		);

		$where = ' where 1 ';
		$params = array();
		if (!empty($modulename) && $modulename != 'all') {
			$where .= ' and module_name = ? ';
			array_push($params, $modulename);
		}
		if (!empty($executioncondtionid)) {
			$where .= ' and execution_condition = ? ';
			array_push($params, $executioncondtionid);
		}
		if (!empty($desc_search)) {
			$where .= " and summary like ? ";
			array_push($params, "%" . $desc_search . "%");
		}
		if (!empty($purpose_search)) {
			$where .= " and purpose like ? ";
			array_push($params, "%" . $purpose_search . "%");
		}
		if ($sorder != '' && $order_by != '') {
			$list_query = "Select * from com_vtiger_workflows $where order by $order_by $sorder";
		} else {
			$list_query = "Select * from com_vtiger_workflows $where order by ".$this->default_order_by." ".$this->default_sort_order;
		}
		$rowsperpage = GlobalVariable::getVariable('Workflow_ListView_PageSize', 20);
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";

		$result = $adb->pquery($list_query.$limit, $params);
		$rscnt = $adb->pquery("select count(*) from com_vtiger_workflows $where", array($params));
		$noofrows = $adb->query_result($rscnt, 0, 0);
		$last_page = ceil($noofrows/$rowsperpage);
		if ($page*$rowsperpage>$noofrows-($noofrows % $rowsperpage)) {
			$islastpage = true;
			$to = $noofrows;
		} else {
			$islastpage = false;
			$to = $page*$rowsperpage;
		}
		$entries_list = array(
			'total' => $noofrows,
			'per_page' => $rowsperpage,
			'current_page' => $page,
			'last_page' => $last_page,
			'next_page_url' => '',
			'prev_page_url' => '',
			'from' => $from+1,
			'to' => $to,
			'data' => array(),
		);
		if ($islastpage && $page!=1) {
			$entries_list['next_page_url'] = null;
		} else {
			$entries_list['next_page_url'] = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page='.($islastpage ? $page : $page+1);
		}
		$entries_list['prev_page_url'] = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page='.($page == 1 ? 1 : $page-1);
		$unames = array();
		$edit_return_url = 'index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings';
		$vtwfappObject= new VTWorkflowApplication('workflowlist', $edit_return_url);
		while ($lgn = $adb->fetch_array($result)) {
			$entry = array();
			$entry['isDefaultWorkflow'] = true;
			if (empty($lgn['defaultworkflow']) && getTranslatedString($workflow_execution_condtion_list[$lgn['execution_condition']], 'Settings') != 'MANUAL') {
				$entry['isDefaultWorkflow'] = false;
			}
			$entry['Module'] = getTranslatedString($lgn['module_name'], $lgn['module_name']);
			$entry['Description'] = getTranslatedString($lgn['summary'], 'com_vtiger_workflow');
			if (empty($lgn['workflow_id'])) {
				$rurl = '';
				$delurl = '';
			} else {
				if ($lgn['module_name']=='Reports') {
					$rurl = 'index.php?module=Reports&action=SaveAndRun&record='.$lgn['workflow_id'];
				} else {
					$rurl = $vtwfappObject->editWorkflowUrl($lgn['workflow_id']);
					$delurl = $vtwfappObject->deleteWorkflowUrl($lgn['workflow_id']);
				}
			}
			$entry['Record'] = $rurl;
			$entry['RecordDel'] = $delurl;
			$entry['RecordDetail'] = $lgn['workflow_id'];
			$entry['workflow_id'] = $lgn['workflow_id'];
			$entry['Purpose'] = $lgn['purpose'];
			$entry['Trigger'] = getTranslatedString($workflow_execution_condtion_list[$lgn['execution_condition']], 'Settings');
			$entries_list['data'][] = $entry;
		}
		$log->debug('Exiting getWorkFlowJSON() method ...');
		return json_encode($entries_list);
	}
}
?>