<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'config.inc.php';
require_once 'include/logging.php';
require_once 'include/ListView/ListView.php';
require_once 'include/database/PearDatabase.php';

/** This class is used to track all the operations done by the particular User while using vtiger crm.
 *  It is intended to be called when the check for audit trail is enabled.
 **/
class AuditTrail {
	public $auditid;
	public $userid;
	public $module;
	public $action;
	public $recordid;
	public $actiondate;

	public $module_name = 'Settings';
	public $table_name = 'vtiger_audit_trial';

	public function __construct() {
	}

	public $sortby_fields = array('module', 'action', 'actiondate', 'recordid');

	// This is the list of vtiger_fields that are in the lists.
	public $list_fields = array(
			'User Name' => array('vtiger_audit_trial'=>'userid'),
			'Module' => array('vtiger_audit_trial'=>'module'),
			'Action' => array('vtiger_audit_trial'=>'action'),
			'Record' => array('vtiger_audit_trial'=>'recordid'),
			'Action Date' => array('vtiger_audit_trial'=>'actiondate'),
		);

	public $list_fields_name = array(
			'User Name'=>'userid',
			'Module'=>'module',
			'Action'=>'action',
			'Record'=>'recordid',
			'Action Date'=>'actiondate',
		);

	public $default_order_by = 'actiondate';
	public $default_sort_order = 'DESC';

	/**
	 * Function to get the Headers of Audit Trail Information like Module, Action, RecordID, ActionDate.
	 * Returns Header Values like Module, Action etc in an array format.
	**/
	public function getAuditTrailHeader() {
		global $log, $app_strings;
		$log->debug('> getAuditTrailHeader');
		$header_array = array(
			$app_strings['LBL_LIST_USER_NAME'],
			$app_strings['LBL_MODULE'],
			$app_strings['LBL_ACTION'],
			$app_strings['LBL_RECORD_ID'],
			$app_strings['LBL_ACTION_DATE'],
		);
		$log->debug('< getAuditTrailHeader');
		return $header_array;
	}

	/**
	  * Function to get the Audit Trail Information values of the actions performed by a particular User.
	  * @param integer $userid - User's ID
	  * @param $navigation_array - Array values to navigate through the number of entries.
	  * @param $sortorder - DESC
	  * @param $order_by - actiondate
	  * Returns the audit trail entries in an array format.
	**/
	public function getAuditTrailEntries($userid, $navigation_array, $sorder = '', $order_by = '') {
		global $log, $adb;
		$log->debug('> getAuditTrailEntries '.$userid);

		if ($sorder != '' && $order_by != '') {
			$list_query = 'Select * from vtiger_audit_trial where userid=? order by '.$adb->sql_escape_string($order_by).' '.$adb->sql_escape_string($sorder);
		} else {
			$list_query = 'Select * from vtiger_audit_trial where userid=? order by '.$this->default_order_by.' '.$this->default_sort_order;
		}
		$result = $adb->pquery($list_query, array($userid));
		$entries_list = array();

		if ($navigation_array['end_val'] != 0) {
			for ($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++) {
				$entries = array();
				$userid = $adb->query_result($result, $i-1, 'userid');
				$entries[] = getTranslatedString($adb->query_result($result, $i-1, 'module'));
				$entries[] = $adb->query_result($result, $i-1, 'action');
				$entries[] = $adb->query_result($result, $i-1, 'recordid');
				$date = new DateTimeField($adb->query_result($result, $i-1, 'actiondate'));
				$entries[] = $date->getDBInsertDateValue();
				$entries_list[] = $entries;
			}
			$log->debug('< getAuditTrailEntries');
			return $entries_list;
		}
	}
	public function getAuditJSON($userid, $page, $order_by = 'actiondate', $sorder = 'DESC', $action_search = '') {
		global $log, $adb;
		require_once 'include/ListView/ListViewJSON.php';
		$log->debug('> getAuditJSON');
		$where = '';
		if (!empty($userid)) {
			$where .=  $adb->convert2Sql(' where userid=?', array($userid));
		}
		if (!empty($action_search)) {
			if (empty($where)) {
				$where .= ' where 1';
			}
			$where .=  $adb->convert2Sql(' and action like ?', array('%' . $action_search . '%'));
		}
		if ($sorder != '' && $order_by != '') {
			$list_query = "select * from vtiger_audit_trial $where order by $order_by $sorder";
		} else {
			$list_query = "select * from vtiger_audit_trial $where order by ".$this->default_order_by.' '.$this->default_sort_order;
		}
		if (!empty($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage'])) {
			$rowsperpage = (int) vtlib_purify($_REQUEST['perPage']);
		} else {
			$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 40);
		}
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";
		$q = $list_query.$limit;
		$grid = new GridListView('cbAuditTrail');
		$grid->currentPage = $page;
		$entries_list = $grid->gridTableBasedEntries($q, $this->list_fields, $this->table_name, ['userid'=>'getUserFullName','recordid'=>'formatRecordIdAuditTrail']);
		$log->debug('< getAuditJSON');
		return json_encode($entries_list);
	}
}
function formatRecordIdAuditTrail($recordid, $row) {
		global $adb;
	if (empty($recordid)) {
			$rurl = '';
	} else {
		if ($row['Module']=='Reports') {
				$rname = $adb->pquery('select vtiger_report.reportname from vtiger_report where vtiger_report.reportid=?', array($recordid));
				$rurl = '<a href="index.php?module=Reports&action=SaveAndRun&record='.$recordid.'">'.$rname->fields['reportname'].'</a>';
		} else {
				$rinfo = getEntityName($row['Module'], $recordid);
			if (empty($rinfo)) {
					$rurl = $recordid;
			} else {
					$rurl = '<a href="index.php?module='.$row['Module'].'&action=DetailView&record='.$recordid.'">'.$rinfo[$recordid].'</a>';
			}
		}
	}
			return $rurl;
}
?>
