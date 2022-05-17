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
require_once 'modules/cbQuestion/cbQuestion.php';

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

	public function getSQL() {
		$record = $this->checkQIDParam();
		echo json_encode(cbQuestion::getSQL($record));
	}

	public function testSQL() {
		global $adb;
		$record = $this->checkQIDParam();
		$smarty = new vtigerCRM_Smarty();
		$sql = cbQuestion::getSQL($record);
		$rs = $adb->query($sql);
		$rdo = array();
		if ($rs) {
			$rdo['status'] = 'OK';
			$rdo['msg'] = getTranslatedString('SQLTESTOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('SQLTESTNOK', 'cbQuestion').' '.$adb->getErrorMsg();
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
		$wfname = 'Sync materialized view for Update on '.$rs->fields['qname'];
		$wfrs = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE summary=? and module_name=?', array($wfname, $rs->fields['qmodule']));
		if ($wfrs && $adb->num_rows($wfrs)==1) {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('MVIEWWFWFAlredyExists', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
			$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
			$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
			echo json_encode($rdo);
			die();
		}
		$emm = new VTEntityMethodManager($adb);
		$emm->addEntityMethod($rs->fields['qmodule'], 'CBQuestionMViewFunction', 'modules/cbQuestion/workflow/mview.php', 'CBQuestionMViewFunction');
		$this->updateMViewField($record, 'mviewwf', '1');
		// create workflow tasks for sync
		$WorkFlowMgr = new VTWorkflowManager($adb);
		$WorkFlow = $WorkFlowMgr->newWorkFlow($rs->fields['qmodule']);
		$WorkFlow->description = $wfname;
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
		$task->summary = $wfname;
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

	public function executeScript() {
		global $adb, $dbconfig;
		$record = $this->checkQIDParam();
		$db_username = $dbconfig['db_username'];
		$db_password = $dbconfig['db_password'];
		$db_name = $dbconfig['db_name'];
		$tablename = isset($_REQUEST['tablename']) ? vtlib_purify($_REQUEST['tablename']) : '';
		$script_path = isset($_REQUEST['script_path']) ? vtlib_purify($_REQUEST['script_path']) : '';
		$smarty = new vtigerCRM_Smarty();
		$rs = $adb->pquery('select * from authorized_scripts where script_path=?', array($script_path));
		$rdo = array();
		if ($rs && $adb->num_rows($rs) > 0) {
			$command = sprintf($script_path.' %s %s %s %s %s', $db_username, $db_password, $db_name, $tablename, $record);
			$output = shell_exec($command);
			$rdo['status'] = 'OK';
			$rdo['msg'] = $output;
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		} else {
			$rdo['status'] = 'NOK';
			$rdo['msg'] = getTranslatedString('EXECUTESCRIPTNOK', 'cbQuestion');
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		}
		$smarty->assign('ERROR_MESSAGE', $rdo['msg']);
		$rdo['notify'] = $smarty->fetch('applicationmessage.tpl');
		echo json_encode($rdo);
	}

	private function getQuestionContext() {
		global $current_user;
		$params = array();
		if (!empty($_REQUEST['cbQuestionContext'])) {
			$ctx = vtlib_purify(json_decode(urldecode($_REQUEST['cbQuestionContext']), true));
			$recordid = $ctx['RECORDID'];
			$module = $ctx['MODULE'];
			if (empty($module) && !empty($recordid)) {
				$module = getSalesEntityType($recordid);
			}
			$params = array(
				'$RECORD$' => $recordid,
				'$MODULE$' => $module,
				'$USERID$' => $current_user->id,
			);
			if (!empty($recordid)) {
				$ctxtmodule = getSalesEntityType($recordid);
				$params['$MODULE$'] = $ctxtmodule;
				$ent = CRMEntity::getInstance($ctxtmodule);
				$ent->id = $recordid;
				$ent->retrieve_entity_info($recordid, $ctxtmodule, false, true);
				foreach ($ent->column_fields as $fname => $fvalue) {
					$params['$'.$fname.'$'] = $fvalue;
				}
			}
		}
		return $params;
	}
	public function getBuilderAnswer() {
		$params = array('cbQuestionRecord' => json_decode($_REQUEST['cbQuestionRecord'], true));
		$ctx = $this->getQuestionContext();
		if (count($ctx)) {
			$params['cbQuestionContext'] = $ctx;
		}
		if (empty($params['cbQuestionRecord']['record_id'])) {
			echo cbQuestion::getFormattedAnswer(0, $params);
		} else {
			echo cbQuestion::getFormattedAnswer($params['cbQuestionRecord']['record_id'], $params);
		}
	}

	public function exportBuilderData() {
		global $log, $adb;
		$log->debug('> exportBuilderData');
		$bqname = vtlib_purify($_REQUEST['bqname']);
		$columns = json_decode(urldecode($_REQUEST['columns']), true);
		$columns = $columns['headers'];
		$translatedHeaders = array_map(function ($key) {
			return getTranslatedString($key['field'], $key['module']);
		}, $columns);

		set_time_limit(0);

		$qinfo = $this->getBuilderDataQuery(true);
		$result = $adb->query($qinfo['query']);
		$date = date_create(date('Y-m-d h:i:s'));
		$filename = $bqname.'_'.date_format($date, date('Ymdhis'));
		$path = 'cache/'.$filename.'.csv';
		$file = fopen($path, 'w');
		fputcsv($file, $translatedHeaders);
		while ($row = $adb->fetchByAssoc($result)) {
			fputcsv($file, $row);
		}
		fclose($file);
		$log->debug('< exportBuilderData');
		echo json_encode($filename);
	}

	public function getBuilderData($ret = false) {
		global $log, $adb, $current_user;
		$log->debug('> getBuilderData');

		if (empty($_REQUEST['cbQuestionRecord'])) {
			$entries_list = array(
				'data' => array(
					'contents' => array(),
					'pagination' => array(
						'page' => 1,
						'totalCount' => 0,
					),
				),
				'result' => false,
				'message' => getTranslatedString('ERR_SQL', 'cbAuditTrail'),
				'debug_query' => '',
				'debug_params' => json_encode($_REQUEST),
			);
			echo json_encode($entries_list);
			return;
		}
		$params = json_decode(urldecode($_REQUEST['cbQuestionRecord']), true);
		if ($params['qtype']=='Global Search') {
			$rdo = cbQuestion::getAnswer($params['record_id'], array());
			array_walk($rdo['answer']['records'], function (&$val) {
				$val = array('id' => $val['id'], 'module' => $val['search_module_name'], 'row' => json_encode($val));
			});
			$entries_list = array(
				'data' => array(
					'contents' => $rdo['answer']['records'],
					'pagination' => array(
						'page' => 1,
						'totalCount' => array_sum($rdo['answer']['totals']),
					),
				),
				'result' => true,
				'message' => '',
				'debug_query' => '',
				'debug_params' => json_encode($_REQUEST),
			);
		} else {
			$builderData = $this->getBuilderDataQuery($ret);
			$result = $adb->query(trim($builderData['query'], ';').$builderData['limit']);
			$count_result = $adb->query(mkXQuery(stripTailCommandsFromQuery($builderData['query'], false), 'count(*) AS count'));
			$noofrows = $adb->query_result($count_result, 0, 0);
			if ($result) {
				if ($noofrows>0) {
					$entries_list = array(
						'data' => array(
							'contents' => array(),
							'pagination' => array(
								'page' => (int)$builderData['page'],
								'totalCount' => (int)$noofrows,
							),
						),
						'result' => true,
					);
					while ($lgn = $adb->fetch_array($result)) {
						for ($col=0; $col < count($lgn); $col++) {
							unset($lgn[$col]);
						}
						$entries_list['data']['contents'][] = $lgn;
					}
				} else {
					$entries_list = array(
						'data' => array(
							'contents' => array(),
							'pagination' => array(
								'page' => 1,
								'totalCount' => 0,
							),
						),
						'result' => false,
						'message' => getTranslatedString('NoData', 'cbAuditTrail'),
					);
				}
			} else {
				$entries_list = array(
					'data' => array(
						'contents' => array(),
						'pagination' => array(
							'page' => 1,
							'totalCount' => 0,
						),
					),
					'result' => false,
					'message' => getTranslatedString('ERR_SQL', 'cbAuditTrail'),
					'debug_query' => $builderData['query'].$builderData['limit'],
					'debug_params' => json_encode($_REQUEST),
				);
			}
		}
		$log->debug('< getBuilderData');
		if ($ret) {
			return $entries_list;
		}
		echo json_encode($entries_list);
	}

	public function getBuilderDataQuery($ret) {
		global $current_user;
		$params = array('cbQuestionRecord' => json_decode(urldecode($_REQUEST['cbQuestionRecord']), true));
		$ctx = $this->getQuestionContext();
		$sql_question_context_variable = json_decode($params['cbQuestionRecord']['typeprops']);

		if (count($ctx)) {
			$params['cbQuestionContext'] = $ctx;
		}

		if ($params['cbQuestionRecord']['sqlquery']=='1' && !$params['cbQuestionRecord']['issqlwsq_disabled']) {
			include_once 'include/Webservices/showqueryfromwsdoquery.php';
			$sqlinfo = showqueryfromwsdoquery($params['cbQuestionRecord']['qcolumns'], $current_user);
			$list_query = $sqlinfo['sql'];
		} else {
			$list_query = cbQuestion::getSQL(0, $params);
		}

		if (stripos($list_query, ' LIMIT ') > 0) {
			$list_query = substr($list_query, 0, stripos($list_query, ' LIMIT '));
		}
		unset($_REQUEST['cbQuestionRecord']);
		if (!empty($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage'])) {
			$rowsperpage = (int) vtlib_purify($_REQUEST['perPage']);
		} else {
			$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 40);
		}
		if (isset($_REQUEST['page'])) {
			$page = vtlib_purify($_REQUEST['page']);
		} else {
			$page = 1;
		}
		$from = ($page-1)*$rowsperpage;
		$limit = '';

		if (!$ret) {
			$limit = " limit $from,$rowsperpage";
		}
		if (!empty($sql_question_context_variable->context_variables)) {
			foreach ((array) $sql_question_context_variable->context_variables as $key => $value) {
				$list_query = str_replace($key, $value, $list_query);
			}
		}

		return array(
		'query' => $list_query,
		'page' => $page,
		'rowsperpage' => $rowsperpage,
		'limit' => $limit,
		);
	}
}
?>
