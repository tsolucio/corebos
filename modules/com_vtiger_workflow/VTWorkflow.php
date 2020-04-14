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

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'summary';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Description'=> array('com_vtiger_workflows' => 'summary'),
		'Module' => array('com_vtiger_workflows' => 'module_name'),
		'Purpose' => array('com_vtiger_workflows'=>'purpose'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Description'=> 'summary',
		'Module' => 'module_name',
		'Purpose' =>'purpose',
	);

	// For Popup window record selection
	public $popup_fields = array('summary');

	// For Alphabetical search
	public $def_basicsearch_col = 'summary';

	public $default_order_by = 'summary';
	public $default_sort_order = 'DESC';

	/**
	 * Function to get the Headers of Workflow List Information like Module, Description, Purpose, Trigger.
	 * Returns Header Values like Module, Description etc in an array format.
	**/
	public function getWorkListHeader() {
		global $log, $app_strings;
		$log->debug('> getWorkListHeader');
		$header_array = array(
			$app_strings['LBL_MODULE'],
			$app_strings['LBL_DESCRIPTION'],
			$app_strings['LBL_PURPOSE'],
			$app_strings['LBL_TRIGGER'],
			$app_strings['LBL_TOOLS'],
		);
		$log->debug('< getWorkListHeader');
		return $header_array;
	}

	public function filterInactiveFields($module) {
		return;
	}

	/**
	 * Function to get sort order
	 * return string $sorder    - sortorder string either 'ASC' or 'DESC'
	 */
	public function getSortOrder() {
		global $log;
		$cmodule = get_class($this);
		$log->debug('> getSortOrder');
		$sorder = strtoupper(GlobalVariable::getVariable('Application_ListView_Default_OrderDirection', $this->default_sort_order, $cmodule));
		if (isset($_REQUEST['sorder'])) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} elseif (!empty($_SESSION[$cmodule.'_Sort_Order'])) {
			$sorder = $this->db->sql_escape_string($_SESSION[$cmodule.'_Sort_Order']);
		}
		$log->debug('< getSortOrder');
		return $sorder;
	}

	/**
	 * Function to get order by
	 * return string $order_by    - fieldname(eg: 'accountname')
	 */
	public function getOrderBy() {
		global $log;
		$log->debug('> getOrderBy');
		$cmodule = get_class($this);
		$order_by = '';
		if (GlobalVariable::getVariable('Application_ListView_Default_Sorting', 0, $cmodule)) {
			$order_by = GlobalVariable::getVariable('Application_ListView_Default_OrderField', $this->default_order_by, $cmodule);
		}

		if (isset($_REQUEST['order_by'])) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} elseif (!empty($_SESSION[$cmodule.'_Order_By'])) {
			$order_by = $this->db->sql_escape_string($_SESSION[$cmodule.'_Order_By']);
		}
		$log->debug('< getOrderBy');
		return $order_by;
	}

	/**
	 * Function to initialize the sortby fields array
	 */
	public function initSortByField($module) {
		global $log;
		$log->debug('> initSortByField '.$module);
		$this->sortby_fields = array('summary','purpose','description','trigger');
		$log->debug('< initSortByField');
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
		$this->defaultworkflow=$row['defaultworkflow'];
		$this->purpose = isset($row['purpose']) ? $row['purpose'] : '';
		$this->wfstarton = isset($row['wfstarton']) ? $row['wfstarton'] : '';
		$this->wfendon = isset($row['wfendon']) ? $row['wfendon'] : '';
		$this->active = isset($row['active']) ? $row['active'] : '';
		$this->nexttrigger_time = isset($row['nexttrigger_time']) ? $row['nexttrigger_time'] : '';
		if ($row['execution_condition']==VTWorkflowManager::$ON_RELATE || $row['execution_condition']==VTWorkflowManager::$ON_UNRELATE) {
			$this->relatemodule = isset($row['relatemodule']) ? $row['relatemodule'] : '';
		} else {
			$this->relatemodule = '';
		}
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

	public function performTasks(&$entityData, $context = array(), $webservice = false) {
		global $adb,$logbg;
		$logbg->debug('> PerformTasks for Workflow: '.$this->id);
		$wflaunch = 0;
		$wf = $adb->pquery('select execution_condition from com_vtiger_workflows where workflow_id=?', array($this->id));
		if ($wf && $adb->num_rows($wf)>0) {
			$wflaunch = $wf->fields['execution_condition'];
		}
		$entityData->WorkflowEvent = $wflaunch;
		$entityData->WorkflowContext = $context;
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
		$errortasks = array();
		foreach ($tasks as $task) {
			if (is_object($task) && $task->active) {
				$logbg->debug($task->summary);
				$trigger = (empty($task->trigger) ? null : $task->trigger);
				if ($trigger != null) {
					$delay = strtotime($data[$trigger['field']])+$trigger['days']*86400;
				} else {
					$delay = 0;
				}
				if ($task->executeImmediately==true || $this->executionCondition==VTWorkflowManager::$MANUAL) {
					// we permit update field delayed tasks even though some may not make sense
					// for example a mathematical operation or a decision on a value of a field that
					// may change during the delay. This is for some certain types of updates, generally
					// absolute updates. You MUST know what you are doing when creating workflows.
					if ($delay!=0 && get_class($task) == 'VTUpdateFieldsTask') {
						$taskQueue->queueTask($task->id, $entityData->getId(), $delay);
					} else {
						if (empty($task->test) || $task->evaluate($entityCache, $entityData->getId())) {
							try {
								$task->startTask($entityData);
								$task->doTask($entityData);
								$task->endTask($entityData);
							} catch (Exception $e) {
								$errortasks[] = array(
									'entitydata' => $entityData->data,
									'entityid' => $entityData->getId(),
									'taskid' => $task->id,
									'error' => $e->getMessage(),
								);
							}
						}
					}
				} else {
					$taskQueue->queueTask($task->id, $entityData->getId(), $delay);
				}
			}
		}
		if (count($errortasks)>0) {
			$logbg->fatal('> *** Workflow Tasks Errors:');
			$logbg->fatal($errortasks);
			$logbg->fatal('> **************************');
			if ($webservice) {
				require_once 'include/Webservices/WebServiceError.php';
				throw new WebServiceException(WebServiceErrorCode::$WORKFLOW_TASK_FAILED, print_r($errortasks, true));
			}
		}
	}

	public function activeWorkflow() {
		$active = true;
		$today = strtotime(DateTimeField::convertToUserFormat(date('Y-m-d H:i:s')));
		$wfstarton = $this->wfstarton == '0000-00-00 00:00:00' ? '' : strtotime($this->wfstarton);
		$wfendon = $this->wfendon == '0000-00-00 00:00:00' ? '' : strtotime($this->wfendon);
		if ($this->active == 'true') {
			//check Active status between these days
			if ($today >= $wfstarton && $today <= $wfendon && $wfendon != "" && $wfstarton != "") {
				$active = true;
			} else if($today >= $wfstarton && $wfendon == "") {
				$active = true;
			} else if($today <= $wfendon && $wfstarton == "") {
				$active = true;
			} else if($wfendon == "" && $wfstarton == "") {
				$active = true;
			} else {
				//status is active but is out of date range
				$active = false;
			}		
		} else {
			//check Inactive status between these days
			if ($today >= $wfstarton && $today <= $wfendon && $wfendon != "" && $wfstarton != "") {
				$active = false;
			} else if($today >= $wfstarton && $wfendon == "") {
				$active = false;
			} else if($today <= $wfendon && $wfstarton == "") {
				echo 1;$active = false;
			} else if($wfendon == "" && $wfstarton == "") {
				$active = false;
			} else {
				//status is inactive but is out of date range
				$active = true;
			}		
		}
		return $active;
	}

	public function executionConditionAsLabel($label = null) {
		if ($label==null) {
			$arr=array('ON_FIRST_SAVE', 'ONCE', 'ON_EVERY_SAVE', 'ON_MODIFY', 'ON_DELETE', 'ON_SCHEDULE', 'MANUAL', 'RECORD_ACCESS_CONTROL', 'ON_RELATE', 'ON_UNRELATE');
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
				'ON_RELATE'=>VTWorkflowManager::$ON_RELATE,
				'ON_UNRELATE'=>VTWorkflowManager::$ON_UNRELATE
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

	public static function geti18nTriggerLabels() {
		return array(
			VTWorkflowManager::$ON_FIRST_SAVE => 'LBL_ONLY_ON_FIRST_SAVE',
			VTWorkflowManager::$ONCE => 'LBL_UNTIL_FIRST_TIME_CONDITION_TRUE',
			VTWorkflowManager::$ON_EVERY_SAVE => 'LBL_EVERYTIME_RECORD_SAVED',
			VTWorkflowManager::$ON_MODIFY => 'LBL_ON_MODIFY',
			VTWorkflowManager::$ON_DELETE => 'LBL_ON_DELETE',
			VTWorkflowManager::$ON_SCHEDULE => 'LBL_ON_SCHEDULE',
			VTWorkflowManager::$MANUAL => 'LBL_MANUAL',
			VTWorkflowManager::$RECORD_ACCESS_CONTROL => 'LBL_RECORD_ACCESS_CONTROL',
			VTWorkflowManager::$ON_RELATE => 'LBL_ON_RELATE',
			VTWorkflowManager::$ON_UNRELATE => 'LBL_ON_UNRELATE',
		);
	}

	public function getWorkFlowJSON($conds, $params, $page, $order_by) {
		global $log, $adb, $current_user;
		$log->debug('> getWorkFlowJSON');

		$workflow_execution_condtion_list = self::geti18nTriggerLabels();
		$where = is_admin($current_user) ? '' : (empty($conds) ? 'where defaultworkflow=2' : 'and defaultworkflow=2');
		if ($order_by != '') {
			$list_query = "Select * from com_vtiger_workflows $conds $where order by $order_by";
		} else {
			$list_query = "Select * from com_vtiger_workflows $conds $where order by ".$this->default_order_by.' '.$this->default_sort_order;
		}
		$rowsperpage = GlobalVariable::getVariable('Workflow_ListView_PageSize', 20);
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";

		$result = $adb->pquery($list_query.$limit, $params);
		$rscnt = $adb->pquery("select count(*) from com_vtiger_workflows $conds", array($params));
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
			'query' => $list_query,
			'data' => array(),
		);
		if ($islastpage && $page!=1) {
			$entries_list['next_page_url'] = null;
		} else {
			$entries_list['next_page_url'] = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page='.($islastpage ? $page : $page+1);
		}
		$entries_list['prev_page_url'] = 'index.php?module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getJSON&page='.($page == 1 ? 1 : $page-1);
		$edit_return_url = 'index.php?module=com_vtiger_workflow&action=workflowlist';
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
					$delurl = $vtwfappObject->deleteWorkflowUrl($lgn['workflow_id'], false);
				}
			}
			$entry['Record'] = $rurl;
			$entry['RecordDel'] = $delurl;
			$entry['RecordDetail'] = $lgn['workflow_id'];
			$entry['workflow_id'] = $lgn['workflow_id'];
			$entry['Purpose'] = $lgn['purpose'];
			$i18n = getTranslatedString($workflow_execution_condtion_list[$lgn['execution_condition']], 'Settings');
			if ($i18n==$workflow_execution_condtion_list[$lgn['execution_condition']]) {
				$i18n = getTranslatedString($workflow_execution_condtion_list[$lgn['execution_condition']], 'com_vtiger_workflow');
			}
			$entry['Trigger'] = $i18n;
			$entries_list['data'][] = $entry;
		}
		$log->debug('< getWorkFlowJSON');
		return json_encode($entries_list);
	}
}
?>