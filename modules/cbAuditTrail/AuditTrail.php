<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/database/PearDatabase.php');

/** This class is used to track all the operations done by the particular User while using vtiger crm.
 *  It is intended to be called when the check for audit trail is enabled.
 **/
class AuditTrail{

	var $log;
	var $db;

	var $auditid;
	var $userid;
	var $module;
	var $action;
	var $recordid;
	var $actiondate;

	var $module_name = "Settings";
	var $table_name = "vtiger_audit_trial";

	function __construct() {
		$this->log = LoggerManager::getLogger('audit_trial');
		$this->db = PearDatabase::getInstance();
	}

	var $sortby_fields = Array('module', 'action', 'actiondate', 'recordid');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			'User Name'=>Array('vtiger_audit_trial'=>'userid'),
			'Module'=>Array('vtiger_audit_trial'=>'module'),
			'Action'=>Array('vtiger_audit_trial'=>'action'),
			'Record'=>Array('vtiger_audit_trial'=>'recordid'),
			'Action Date'=>Array('vtiger_audit_trial'=>'actiondate'),
		);

	var $list_fields_name = Array(
			'User Name'=>'userid',
			'Module'=>'module',
			'Action'=>'action',
			'Record'=>'recordid',
			'Action Date'=>'actiondate',
		);

	var $default_order_by = "actiondate";
	var $default_sort_order = 'DESC';

	/**
	 * Function to get the Headers of Audit Trail Information like Module, Action, RecordID, ActionDate.
	 * Returns Header Values like Module, Action etc in an array format.
	**/
	function getAuditTrailHeader() {
		global $log, $app_strings;
		$log->debug("Entering getAuditTrailHeader() method ...");
		$header_array = array($app_strings['LBL_LIST_USER_NAME'], $app_strings['LBL_MODULE'], $app_strings['LBL_ACTION'], $app_strings['LBL_RECORD_ID'], $app_strings['LBL_ACTION_DATE']);
		$log->debug("Exiting getAuditTrailHeader() method ...");
		return $header_array;
	}

	/**
	  * Function to get the Audit Trail Information values of the actions performed by a particular User.
	  * @param integer $userid - User's ID
	  * @param $navigation_array - Array values to navigate through the number of entries.
	  * @param $sortorder - DESC
	  * @param $orderby - actiondate
	  * Returns the audit trail entries in an array format.
	**/
	function getAuditTrailEntries($userid, $navigation_array, $sorder='', $orderby='') {
		global $log, $adb, $current_user;
		$log->debug("Entering getAuditTrailEntries(".$userid.") method ...");

		if($sorder != '' && $order_by != '')
			$list_query = "Select * from vtiger_audit_trial where userid =? order by ".$order_by." ".$sorder;
		else
			$list_query = "Select * from vtiger_audit_trial where userid =? order by ".$this->default_order_by." ".$this->default_sort_order;

		$result = $adb->pquery($list_query, array($userid));
		$entries_list = array();

		if($navigation_array['end_val'] != 0) {
			for($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++) {
				$entries = array();
				$userid = $adb->query_result($result, $i-1, 'userid');
				$entries[] = getTranslatedString($adb->query_result($result, $i-1, 'module'));
				$entries[] = $adb->query_result($result, $i-1, 'action');
				$entries[] = $adb->query_result($result, $i-1, 'recordid');
				$date = new DateTimeField($adb->query_result($result, $i-1, 'actiondate'));
				$entries[] = $date->getDBInsertDateValue();
				$entries_list[] = $entries;
			}
			$log->debug("Exiting getAuditTrailEntries() method ...");
			return $entries_list;
		}
	}

	function getAuditJSON($userid, $page, $order_by='actiondate', $sorder='DESC')
	{
		global $log, $adb, $current_user;
		$log->debug("Entering getAuditJSON() method ...");

		if (empty($userid)) {
			$where = '';
			$params = array();
		} else {
			$where = 'where userid=?';
			$params = array($userid);
		}
		if($sorder != '' && $order_by != '')
			$list_query = "Select * from vtiger_audit_trial $where order by $order_by $sorder";
		else
			$list_query = "Select * from vtiger_audit_trial $where order by ".$this->default_order_by." ".$this->default_sort_order;
		$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize',40);
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";

		$result = $adb->pquery($list_query.$limit, $params);
		$rscnt = $adb->pquery("select count(*) from vtiger_audit_trial $where", array($params));
		$noofrows = $adb->query_result($rscnt, 0,0);
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
		if ($islastpage and $page!=1) {
			$entries_list['next_page_url'] = null;
		} else {
			$entries_list['next_page_url'] = 'index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=getJSON&page='.($islastpage ? $page : $page+1);
		}
		$entries_list['prev_page_url'] = 'index.php?module=cbAuditTrail&action=cbAuditTrailAjax&file=getJSON&page='.($page == 1 ? 1 : $page-1);
		$unames = array();
		while($lgn = $adb->fetch_array($result)) {
			$entry = array();
			if (!isset($unames[$lgn['userid']])) {
				$unames[$lgn['userid']] = getUserFullName($lgn['userid']);
			}
			$entry['User Name'] = $unames[$lgn['userid']];
			$entry['Module'] = $lgn['module'];
			$entry['Action'] = $lgn['action'];
			if (empty($lgn['recordid'])) {
				$rurl = '';
			} else {
				if ($lgn['module']=='Reports') {
					$rurl = 'index.php?module=Reports&action=SaveAndRun&record='.$lgn['recordid'];
				} else {
					$rurl = 'index.php?module='.$lgn['module'].'&action=DetailView&record='.$lgn['recordid'];
				}
			}
			$entry['Record'] = $rurl;
			$entry['RecordDetail'] = $lgn['recordid'];
			$entry['Action Date'] = $lgn['actiondate'];
			$entries_list['data'][] = $entry;
		}
		$log->debug("Exiting getAuditJSON() method ...");
		return json_encode($entries_list);
	}

}

?>
