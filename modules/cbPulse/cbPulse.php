<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Module.php';

class cbPulse extends CRMEntity {
	public $table_name = 'vtiger_cbpulse';
	public $table_index= 'cbpulseid';
	public $column_fields = array();
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $customFieldTable = array('vtiger_cbpulsecf', 'cbpulseid');
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbpulse', 'vtiger_cbpulsecf');
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbpulse'   => 'cbpulseid',
		'vtiger_cbpulsecf' => 'cbpulseid',
	);

	public $list_fields = array(
		'questionid'=> array('cbquestion' => 'questionid'),
		'cbpulse_no'=> array('cbpulse' => 'cbpulse_no'),
		'Assigned To' => array('crmentity' => 'smownerid'),
		'active' => array('cbpulse' => 'active'),
		'schtypeid' => array('cbpulse' => 'schtypeid'),
	);
	public $list_fields_name = array(
		'questionid'=> 'questionid',
		'Assigned To' => 'assigned_user_id',
		'cbpulse_no'=> 'cbpulse_no',
		'active'=> 'active',
		'schtypeid'=> 'schtypeid',
	);

	public $list_link_field = 'cbpulse_no';

	public $search_fields = array(
		'cbpulse_no'=> array('cbpulse' => 'cbpulse_no'),
		'questionid'=> array('cbquestion' => 'questionid'),
		'Assigned To'=> array('crmentity' => 'smownerid'),
		'active'=> array('cbpulse' => 'active'),
		'schtypeid'=> array('cbpulse' => 'schtypeid')
	);
	public $search_fields_name = array(
		'sendmethod'=> 'sendmethod'
	);

	public $popup_fields = array();
	public $sortby_fields = array();
	public $def_basicsearch_col;

	public $def_detailview_recname;
	public $required_fields = array();
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbpulse_no';
	public $default_sort_order='ASC';
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cbpulse_no');

	public function save_module($module) {
		global $adb, $current_user;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if ($this->mode == 'edit') {
			$result = $adb->pquery('select * from vtiger_cbpulse WHERE cbpulseid=?', array(vtlib_purify($_REQUEST['record'])));
			$workflowId = (int)$result->fields['workflowid'];
			$delIns = new VTWorkflowManager($adb);
			$delres = $delIns->delete($workflowId);
		}
		$flowManager = new VTWorkflowManager($adb);
		$taskManager = new VTTaskManager($adb);
		$pulseworkflow = $flowManager->newWorkFlow('cbPulse');
		$pulseworkflow->test = '[{"fieldname":"questionid","operation":"is","value":'.$this->column_fields['questionid'].'}]';
		$pulseworkflow->description = 'Send MM Question';
		$pulseworkflow->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
		$pulseworkflow->defaultworkflow = 0;
		$pulseworkflow->schtypeid = 8;
		$intervalMin = (int)($this->column_fields['schminuteinterval']);
		$pulseworkflow->schminuteinterval = $this->column_fields['schminuteinterval'];
		$pulseworkflow->schtime = $this->column_fields['schtime'];
		$pulseworkflow->schdayofmonth = $this->column_fields['schdayofmonth'];
		$pulseworkflow->schdayofweek = $this->column_fields['schdayofweek'];
		$pulseworkflow->schannualdates = $this->column_fields['schannualdates'];
		$pulseworkflow->purpose = 'Send Question message to mattermost';
		$flowManager->save($pulseworkflow);
		$qres = $adb->pquery("select * from vtiger_cbquestion WHERE cbquestionid=?", array($this->column_fields['questionid']));
		$adb->pquery("UPDATE vtiger_cbpulse SET workflowid = ? WHERE cbpulse_no=?", array((int)$pulseworkflow->id, $this->column_fields['cbpulse_no']));
		$task = $taskManager->createTask('CBSendMMMSGTask', $pulseworkflow->id);
		$task->messageTitle = $qres->fields['qtype'];
		$task->messageBody = $qres->fields['qname'];
		$task->active = $this->column_fields['active'] == 'on' ? true : false;
		$task->summary = 'Send Question to MM';
		$task->executeImmediately = true;
		$taskManager->saveTask($task);
	}
	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			require_once 'include/utils/utils.php';
			global $adb;
			$this->setModuleSeqNumber('configure', $modulename, 'PL-', '00000001');
			include_once 'vtlib/Vtiger/Module.php';
			$PulseModule  = Vtiger_Module::getInstance('cbPulse');
			$cbQuestionModule  = Vtiger_Module::getInstance('cbQuestion');
			if ($cbQuestionModule) {
				$cbQuestionModule->setRelatedList($PulseModule, 'cbPulse', array('ADD'), 'get_dependents_list');
			}
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	public function trash($modulename, $id) {
		global $adb;
		$result = $adb->pquery('select workflowid from vtiger_cbpulse WHERE cbpulseid=?', array($id));
		$workflowId = (int)$result->fields['workflowid'];
		$delIns = new VTWorkflowManager($adb);
		$delIns->delete($workflowId);
		parent::trash($modulename, $id);
	}
}
?>