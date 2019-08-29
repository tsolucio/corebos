<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

class qactions_Action extends CoreBOS_ActionController {

	private function checkQIDParam() {
		$record = isset($_REQUEST['qid']) ? vtlib_purify($_REQUEST['qid']) : 0;
		if (empty($record)) {
			$rdo = array();
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('LBL_RECORD_NOT_FOUND');
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
			$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
			echo json_encode($rdo);
			die();
		}
		return $record;
	}

	private function updateMViewField($qid, $field, $value) {
		global $current_user;
		include_once 'include/Webservices/Revise.php';
		$upd = array(
			'id' => vtws_getEntityId('cbQuestion').'x'.$qid,
			$field => $value,
		);
		vtws_revise($upd, $current_user);
	}

	public function testSQL() {
		global $adb;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		include_once 'modules/cbQuestion/cbQuestion.php';
		$sql = cbQuestion::getSQL($record);
		$rs = $adb->query($sql);
		$rdo = array();
		if ($rs) {
			$rdo['status'] = 'OK';
			$rdo['msg'] = getTranslatedString('SQLTESTOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('SQLTESTNOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		}
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function createMap() {
		global $adb, $current_user;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		include_once 'modules/cbQuestion/cbQuestion.php';
		$rs = $adb->pquery('select qname,qmodule from vtiger_cbquestion where cbquestionid=?', array($record));
		$qname = str_replace(' ', '_', $rs->fields['qname']);
		$sql = cbQuestion::getSQL($record);
		$focus = CRMEntity::getInstance('cbMap');
		$focus->column_fields['assigned_user_id'] = $current_user->id;
		$focus->column_fields['mapname'] = $qname;
		$focus->column_fields['targetname'] = $rs->fields['qmodule'];
		$focus->column_fields['content']='<map><sql>'.$sql.'</sql><return>recordset</return></map>';
		$focus->column_fields['description'] = 'Business Question Materialized View Creator';
		$focus->column_fields['maptype'] = 'Condition Query';
		$focus->save('cbMap');
		$rdo = array();
		if (!empty($focus->id)) {
			$rdo['status'] = 'OK';
			$rdo['msg'] = getTranslatedString('MAPOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
			$smarty->assign('ERROR_MESSAGE', $rdo['msg'].' <a href="index.php?module=cbMap&action=DetailView&record='.$focus->id.'" target=_blank>'.$qname.'</a>');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('MAPNOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
			$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		}
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function createView() {
		global $adb;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		include_once 'modules/cbQuestion/cbQuestion.php';
		$rs = $adb->pquery('select qname from vtiger_cbquestion where cbquestionid=?', array($record));
		$sql = cbQuestion::getSQL($record);
		$rs = $adb->query('CREATE OR REPLACE VIEW '.str_replace(' ', '_', $rs->fields['qname']).' AS '.$sql);
		$rdo = array();
		if ($rs) {
			$rdo['status'] = 'OK';
			$rdo['msg'] = getTranslatedString('VIEWOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('VIEWNOK', 'cbQuestion').' '.$adb->getErrorMsg();
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		}
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function createMView() {
		global $adb;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		include_once 'modules/cbQuestion/cbQuestion.php';
		$rs = $adb->pquery('select qname from vtiger_cbquestion where cbquestionid=?', array($record));
		$sql = cbQuestion::getSQL($record);
		$vname = str_replace(' ', '_', $rs->fields['qname']);
		$adb->query('DROP TABLE '.$vname);
		$rs = $adb->query('CREATE TABLE '.$vname.' AS '.$sql);
		$rdo = array();
		if ($rs) {
			$rdo['status'] = 'OK';
			$rdo['msg'] = getTranslatedString('MVIEWOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
			$this->updateMViewField($record, 'mviewcron', '1');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('VIEWNOK', 'cbQuestion').' '.$adb->getErrorMsg();
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
			$this->updateMViewField($record, 'mviewcron', '0');
		}
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function removeMView() {
		global $adb;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		include_once 'modules/cbQuestion/cbQuestion.php';
		$rs = $adb->pquery('select qname from vtiger_cbquestion where cbquestionid=?', array($record));
		$vname = str_replace(' ', '_', $rs->fields['qname']);
		$rs = $adb->query('drop table '.$vname);
		$rdo = array();
		if ($rs) {
			$rdo['status'] = 'OK';
			$rdo['msg'] = getTranslatedString('DELMVIEWOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
			$this->updateMViewField($record, 'mviewcron', '0');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('DELMVIEWNOK', 'cbQuestion').' '.$adb->getErrorMsg();
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
			$this->updateMViewField($record, 'mviewcron', '0');
		}
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function addMViewCron() {
		Vtiger_Cron::register(
			'MaterializedViewSync',
			'modules/cbQuestion/cron/mview.php',
			43200,
			'cbQuestion',
			Vtiger_Cron::$STATUS_DISABLED,
			0,
			'Sync all active materialized views.'
		);
		$record = $this->checkQIDParam();
		$this->updateMViewField($record, 'mviewcron', '1');
		$smarty = new vtigerCRM_Smarty();
		$rdo = array();
		$rdo['status'] = 'OK';
		$rdo['msg'] = getTranslatedString('MVIEWACTIVATED', 'cbQuestion');
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function delMViewCron() {
		$record = $this->checkQIDParam();
		$this->updateMViewField($record, 'mviewcron', '0');
		$smarty = new vtigerCRM_Smarty();
		$rdo = array();
		$rdo['status'] = 'OK';
		$rdo['msg'] = getTranslatedString('MVIEWDEACTIVATED', 'cbQuestion');
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function addMViewWF() {
		global $adb;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		$rdo = array();
		require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
		$emm = new VTEntityMethodManager($adb);
		$rs = $adb->pquery('select qname,qmodule,uniqueid,qmodule from vtiger_cbquestion where cbquestionid=?', array($record));
		if (empty($rs->fields['uniqueid']) || empty($rs->fields['qmodule']) || !vtlib_isModuleActive($rs->fields['qmodule'])) {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('MVIEWWFMissingFields', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
			$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
			$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
			echo json_encode($rdo);
			die();
		}
		$emm->addEntityMethod($rs->fields['qmodule'], 'CBQuestionMViewFunction', 'modules/cbQuestion/workflow/mview.php', 'CBQuestionMViewFunction');
		$this->updateMViewField($record, 'mviewwf', '1');
		// create workflow tasks for sync
		$WorkFlowMgr = new VTWorkflowManager($adb);
		$WorkFlow = $WorkFlowMgr->newWorkFlow($rs->fields['qmodule']);
		$WorkFlow->description = 'Sync materialized view for Update on '.$rs->fields['qname'];
		$WorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
		$WorkFlow->defaultworkflow = 0;
		$WorkFlow->schtypeid = 0;
		$WorkFlow->test = '';
		$WorkFlow->schtime = '00:00:00';
		$WorkFlow->schdayofmonth = '';
		$WorkFlow->schdayofweek = '';
		$WorkFlow->schannualdates = '';
		$WorkFlow->schminuteinterval = '';
		$WorkFlowMgr->save($WorkFlow);

		$tm = new VTTaskManager($adb);
		$task = $tm->createTask('VTEntityMethodTask', $WorkFlow->id);
		$task->active = true;
		$task->summary = 'Sync materialized view for Update on '.$rs->fields['qname'];
		$task->methodName = 'CBQuestionMViewFunction';
		$task->executeImmediately = '1';
		$task->test = '';
		$task->reevaluate = 0;
		$tm->saveTask($task);
		$WorkFlowMgr = new VTWorkflowManager($adb);
		$WorkFlow = $WorkFlowMgr->newWorkFlow($rs->fields['qmodule']);
		$WorkFlow->description = 'Sync materialized view for Delete on '.$rs->fields['qname'];
		$WorkFlow->executionCondition = VTWorkflowManager::$ON_DELETE;
		$WorkFlow->defaultworkflow = 0;
		$WorkFlow->schtypeid = 0;
		$WorkFlow->test = '';
		$WorkFlow->schtime = '00:00:00';
		$WorkFlow->schdayofmonth = '';
		$WorkFlow->schdayofweek = '';
		$WorkFlow->schannualdates = '';
		$WorkFlow->schminuteinterval = '';
		$WorkFlowMgr->save($WorkFlow);

		$tm = new VTTaskManager($adb);
		$task = $tm->createTask('VTEntityMethodTask', $WorkFlow->id);
		$task->active = true;
		$task->summary = 'Sync materialized view for Delete on '.$rs->fields['qname'];
		$task->methodName = 'CBQuestionMViewFunction';
		$task->executeImmediately = '1';
		$task->test = '';
		$task->reevaluate = 0;
		$tm->saveTask($task);
		$rdo['status'] = 'OK';
		$rdo['msg'] = getTranslatedString('MVIEWWFAdded', 'cbQuestion');
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	public function delMViewWF() {
		global $adb;
		$record = $this->checkQIDParam();
		$rs = $adb->pquery('select qname,qmodule from vtiger_cbquestion where cbquestionid=?', array($record));
		$adb->pquery(
			"DELETE FROM com_vtiger_workflows where summary LIKE 'Sync materialized view for % on ".$rs->fields['qname']."' and module_name=?",
			array($rs->fields['qmodule'])
		);
		$adb->pquery(
			"DELETE FROM com_vtiger_workflowtasks WHERE summary LIKE 'Sync materialized view for % on ".$rs->fields['qname']."' and task LIKE ?",
			array('%CBQuestionMViewFunction%')
		);
		$this->updateMViewField($record, 'mviewwf', '0');
		$smarty = new vtigerCRM_Smarty();
		$rdo = array();
		$rdo['status'] = 'OK';
		$rdo['msg'] = getTranslatedString('MVIEWWFDeleted', 'cbQuestion');
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}
}
?>