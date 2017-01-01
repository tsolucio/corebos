<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

	/**
	 * Functionality to save and retrieve Tasks from the database.
	 */
	class VTTaskManager{
		function __construct($adb){
			$this->adb = $adb;
		}

		/**
		 * Save the task into the database.
		 *
		 * When a new task is saved for the first time a field is added to it called
		 * id that stores the task id used in the database.
		 *
		 * @param $summary A summary of the task instance.
		 * @param $task The task instance to save.
		 * @return The id of the task
		 */
		public function saveTask($task){
			$adb = $this->adb;
			if(is_numeric($task->id)){//How do I check whether a member exists in php?
				$taskId = $task->id;
				$adb->pquery("update com_vtiger_workflowtasks set summary=?, task=? where task_id=?",
							array($task->summary, serialize($task), $taskId));
				return $taskId;
			}else{
				$taskId = $adb->getUniqueID("com_vtiger_workflowtasks");
				$task->id = $taskId;
				$orderrs = $adb->pquery('select max(executionorder) from com_vtiger_workflowtasks where workflow_id=?',array($task->workflowId));
				$order = $adb->query_result($orderrs, 0, 0)+1;
				$adb->pquery("insert into com_vtiger_workflowtasks
						(task_id, workflow_id, summary, task, executionorder)
						values (?, ?, ?, ?, ?)",
					array($taskId, $task->workflowId, $task->summary, serialize($task), $order));
				return $taskId;
			}
		}

		public function deleteTask($taskId){
			$adb = $this->adb;
			$orderrs = $adb->pquery('select executionorder,workflow_id from com_vtiger_workflowtasks where task_id=?',array($taskId));
			$order = $adb->query_result($orderrs, 0, 0);
			$wfid = $adb->query_result($orderrs, 0, 1);
			$orderrs = $adb->pquery('update com_vtiger_workflowtasks set executionorder=executionorder-1
				where workflow_id=? and executionorder>?',array($wfid,$order));
			$adb->pquery("delete from com_vtiger_workflowtasks where task_id=?", array($taskId));
		}

		/**
		 * Create a new class instance
		 */
		public function createTask($taskType, $workflowId) {
			$taskTypeInstance = VTTaskType::getInstanceFromTaskType($taskType);
			$taskClass = $taskTypeInstance->get('classname');
			$this->requireTask($taskClass, $taskTypeInstance);
			$task = new $taskClass();
			$task->workflowId=$workflowId;
			$task->summary = "";
			$task->active=true;
			return $task;
		}

		/**
		 * Retrieve a task from the database
		 *
		 * @param $taskId The id of the task to retrieve.
		 * @return VTTask The retrieved task.
		 */
		public function retrieveTask($taskId){
			$adb = $this->adb;
			$result = $adb->pquery("select task from com_vtiger_workflowtasks where task_id=?", array($taskId));
			$data = $adb->raw_query_result_rowdata($result, 0);
			$task = $data["task"];
			$task = $this->unserializeTask($task);
			$timeFieldList = $task->getTimeFieldList();
			foreach ($timeFieldList as $field) {
				$task->$field = $task->formatTimeForTimePicker($task->$field);
			}
			return $task;
		}

		/**
		 *
		 */
		public function getTasksForWorkflow($workflowId){
			$adb = $this->adb;
			$result = $adb->pquery('select task,executionorder from com_vtiger_workflowtasks where workflow_id=? order by executionorder',
				array($workflowId));
			return $this->getTasksForResult($result);
		}

		/**
		 *
		 */
		public function unserializeTask($str){
			$tasktype = VTTaskType::getInstanceFromTaskType(self::taskName($str));
			$this->requireTask(self::taskName($str),$tasktype);
			return unserialize($str);
		}

		function getTasks(){
			$adb = $this->adb;
			$result = $adb->query("select task from com_vtiger_workflowtasks");
			return $this->getTasksForResult($result);
		}

		function getTaskTypes($moduleName=''){
			return VTTaskType::getAll($moduleName);
		}

		private function getTasksForResult($result){
			$adb = $this->adb;
			$it = new SqlResultIterator($adb, $result);
			$tasks = array();
			foreach($it as $row){
				$text = $row->task;
				$tasktype = VTTaskType::getInstanceFromTaskType(self::taskName($text));
				$this->requireTask(self::taskName($text),$tasktype);
				$wftask = unserialize($text);
				$wftask->executionorder = $row->executionorder;
				$tasks[] = $wftask;
			}
			return $tasks;
		}

		private function taskName($serializedTask){
			$matches = array();
			preg_match ('/"([^"]+)"/', $serializedTask, $matches);
			return $matches[1];
		}

		private function requireTask($taskType, $taskTypeInstance='') {
			if(!empty($taskTypeInstance)) {
				$taskClassPath = $taskTypeInstance->get('classpath');
				require_once($taskClassPath);
			} else {
				if(!empty($taskType)) {
					require_once("tasks/".$taskType.".inc");
				}
			}
		}

		public function retrieveTemplatePath($moduleName, $taskTypeInstance) {
			$taskTemplatePath = $taskTypeInstance->get('templatepath');
			if(!empty($taskTemplatePath)) {
				return $taskTemplatePath;
			} else {
				$taskType = $taskTypeInstance->get('classname');
				return $moduleName."/taskforms/".$taskType.".tpl";
			}
		}

	}

	abstract class VTTask{
		public abstract function doTask(&$data);
		public abstract function getFieldNames();

		public function getTimeFieldList() {
			return array();
		}

		public function formatTimeForTimePicker($time) {
			$AM_PM = array('am', 'pm');
			if (in_array(strtolower(substr($time, -2)),$AM_PM))
				return $time; // ya tiene el formato correcto, devolverlo sin más
			if (substr_count($time, ':')==1) {
				list($h, $m) = explode(':', $time);
			} else {
				list($h, $m, $s) = explode(':', $time);
			}
			$mn = str_pad($m - $m % 15, 2, 0, STR_PAD_LEFT);
			return str_pad(($h%12), 2, 0, STR_PAD_LEFT).':'.$mn.$AM_PM[($h/12)%2];
		}

		function evaluate($entityCache, $id){
			if($this->test==""){
				return true;
			}else{
				$cs = new VTJsonCondition();
				return $cs->evaluate($this->test, $entityCache, $id);
			}
		}

		public function getMetaVariables() {
			return array(
				'Current Date' => '(general : (__VtigerMeta__) date)',
				'Current Time' => '(general : (__VtigerMeta__) time)',
				'User Timezone' => '(general : (__VtigerMeta__) dbtimezone)',
				'CRM Detail View URL' => '(general : (__VtigerMeta__) crmdetailviewurl)',
				'Portal Detail View URL' => '(general : (__VtigerMeta__) portaldetailviewurl)',
				'Site Url' => '(general : (__VtigerMeta__) siteurl)',
				'Portal Url' => '(general : (__VtigerMeta__) portalurl)',
				'Record Id' => '(general : (__VtigerMeta__) recordId)',
				'LBL_ORGANIZATION_LOGO' => '(general : (__VtigerMeta__) logo)',
				'LBL_HELPDESK_SUPPORT_NAME' => '(general : (__VtigerMeta__) supportName)',
				'LBL_HELPDESK_SUPPORT_EMAILID' => '(general : (__VtigerMeta__) supportEmailId)',
				'Comments' => '(general : (__VtigerMeta__) comments)',
				'groupEmailList' => '(general : (__VtigerMeta__) groupEmailList_Put_Group_Name_Here)',
				'invitedUsers' => '(general : (__VtigerMeta__) Events_Users_Invited)',
			);
		}

	}

class VTTaskType {
	var $data;

	public function get($key) {
		return $this->data[$key];
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}

	public function setData($valueMap) {
		$this->data = $valueMap;
		return $this;
	}

	public static function getInstance($values) {
		$instance = new self();
		return $instance->setData($values);
	}

	public static function registerTaskType($taskType) {
		$adb = PearDatabase::getInstance();
		$modules = json_encode($taskType['modules']);

		$exists = $adb->pquery('select count(*) from com_vtiger_workflow_tasktypes
				where tasktypename=? and label=? and classname=? and classpath=? and
				 templatepath=? and modules=? and sourcemodule=?',
				array($taskType['name'], $taskType['label'], $taskType['classname'], $taskType['classpath'], $taskType['templatepath'], $modules, $taskType['sourcemodule']));
		if ($exists and $adb->query_result($exists,0,0)==0) {
		$taskTypeId = $adb->getUniqueID("com_vtiger_workflow_tasktypes");
		$taskType['id'] = $taskTypeId;

		$adb->pquery("INSERT INTO com_vtiger_workflow_tasktypes
						(id, tasktypename, label, classname, classpath, templatepath, modules, sourcemodule)
						values (?,?,?,?,?,?,?,?)",
				array($taskTypeId, $taskType['name'], $taskType['label'], $taskType['classname'], $taskType['classpath'], $taskType['templatepath'], $modules, $taskType['sourcemodule']));
		}
	}

	public static function getAll($moduleName='') {
		$adb = PearDatabase::getInstance();

		$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_tasktypes",array());
		$numrows = $adb->num_rows($result);
		for ($i = 0; $i < $numrows; $i++) {
			$rawData = $adb->raw_query_result_rowdata($result,$i);
			$taskName = $rawData['tasktypename'];
			$moduleslist = $rawData['modules'];
			$sourceModule = $rawData['sourcemodule'];
			$modules = json_decode($moduleslist,true);
			$includeModules = $modules['include'];
			$excludeModules = $modules['exclude'];

			if(!empty ($sourceModule)) {
				if(getTabid($sourceModule) == null || !vtlib_isModuleActive($sourceModule)) {
					continue;
				}
			}

			if(empty($includeModules) && empty($excludeModules)) {
				$taskTypeInstances[] = $taskName;
				continue;
			} elseif(!empty($includeModules)) {
				if(in_array($moduleName, $includeModules)) {
					$taskTypeInstances[] = $taskName;
				}
				continue;
			} elseif(!empty($excludeModules)) {
				if(!(in_array($moduleName, $excludeModules))) {
					$taskTypeInstances[] = $taskName;
				}
				continue;
			}
		}
		return $taskTypeInstances;
	}

	public static function getInstanceFromTaskType($taskType) {
		$adb = PearDatabase::getInstance();

		$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_tasktypes where tasktypename=?",array($taskType));
		$taskTypes['name'] = $adb->query_result($result, 0, 'tasktypename');
		$taskTypes['label'] = $adb->query_result($result, 0, 'label');
		$taskTypes['classname'] = $adb->query_result($result, 0, 'classname');
		$taskTypes['classpath'] = $adb->query_result($result, 0, 'classpath');
		$taskTypes['templatepath'] = $adb->query_result($result, 0, 'templatepath');
		$taskTypes['sourcemodule'] = $adb->query_result($result, 0, 'sourcemodule');

		$taskDetails = self::getInstance($taskTypes);
		return $taskDetails;
	}
}
?>