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

/** This class is used to store and display the login history of all the Users.
  * An Admin User can view his login history details  and of all the other users as well.
  * StandardUser is allowed to view only his login history details.
**/
class LoginHistory {
	var $log;
	var $db;

	// Stored vtiger_fields
	var $login_id;
	var $user_name;
	var $user_ip;
	var $login_time;
	var $logout_time;
	var $status;
	var $module_name = "Users";

	var $table_name = "vtiger_loginhistory";

	var $column_fields = Array("id"
		,"login_id"
		,"user_name"
		,"user_ip"
		,"login_time"
		,"logout_time"
		,"status"
		);

	function __construct() {
		$this->log = LoggerManager::getLogger('loginhistory');
		$this->db = PearDatabase::getInstance();
	}

	var $sortby_fields = Array('user_name', 'user_ip', 'login_time', 'logout_time', 'status');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			'User Name'=>Array('vtiger_loginhistory'=>'user_name'),
			'User IP'=>Array('vtiger_loginhistory'=>'user_ip'),
			'Signin Time'=>Array('vtiger_loginhistory'=>'login_time'),
			'Signout Time'=>Array('vtiger_loginhistory'=>'logout_time'),
			'Status'=>Array('vtiger_loginhistory'=>'status'),
		);

	var $list_fields_name = Array(
		'User Name'=>'user_name',
		'User IP'=>'user_ip',
		'Signin Time'=>'login_time',
		'Signout Time'=>'logout_time',
		'Status'=>'status'
		);
	var $default_order_by = "login_time";
	var $default_sort_order = 'DESC';

	/**
	 * Function to get the Header values of Login History.
	 * Returns Header Values like UserName, IP, LoginTime etc in an array format.
	**/
	function getHistoryListViewHeader()
	{
		global $log,$app_strings;
		$log->debug("Entering getHistoryListViewHeader method ...");
		$header_array = array($app_strings['LBL_LIST_USER_NAME'], $app_strings['LBL_LIST_USERIP'], $app_strings['LBL_LIST_SIGNIN'], $app_strings['LBL_LIST_SIGNOUT'], $app_strings['LBL_LIST_STATUS']);
		$log->debug("Exiting getHistoryListViewHeader method ...");
		return $header_array;
	}

	/**
	  * Function to get the Login History values of the User.
	  * @param $navigation_array - Array values to navigate through the number of entries.
	  * @param $sortorder - DESC
	  * @param $orderby - login_time
	  * Returns the login history entries in an array format.
	**/
	function getHistoryListViewEntries($username, $navigation_array, $sorder='', $orderby='')
	{
		global $log, $adb, $current_user;
		$log->debug("Entering getHistoryListViewEntries() method ...");

		if($sorder != '' && $order_by != '')
			$list_query = "Select * from vtiger_loginhistory where user_name=? order by ".$order_by." ".$sorder;
		else
			$list_query = "Select * from vtiger_loginhistory where user_name=? order by ".$this->default_order_by." ".$this->default_sort_order;

		$result = $adb->pquery($list_query, array($username));
		$entries_list = array();

		if($navigation_array['end_val'] != 0) {
			$in = getTranslatedString('Signed in');
			$out = getTranslatedString('Signed off');
			for($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++)
			{
				$entries = array();
				$loginid = $adb->query_result($result, $i-1, 'login_id');
				$entries[] = $adb->query_result($result, $i-1, 'user_name');
				$entries[] = $adb->query_result($result, $i-1, 'user_ip');
				$entries[] = $adb->query_result($result, $i-1, 'login_time');
				$entries[] = $adb->query_result($result, $i-1, 'logout_time');
				$entries[] = ($adb->query_result($result, $i-1, 'status')=='Signed in' ? $in : $out);
				$entries_list[] = $entries;
			}
		}
		$log->debug("Exiting getHistoryListViewEntries() method ...");
		return $entries_list;
	}

	function getHistoryJSON($userid, $page, $order_by='login_time', $sorder='DESC')
	{
		global $log, $adb, $current_user;
		$log->debug("Entering getHistoryJSON() method ...");

		if (empty($userid)) {
			$where = '';
			$params = array();
		} else {
			$where = 'where user_name=?';
			$username = getUserName($userid);
			$params = array($username);
		}
		if($sorder != '' && $order_by != '')
			$list_query = "Select * from vtiger_loginhistory $where order by $order_by $sorder";
		else
			$list_query = "Select * from vtiger_loginhistory $where order by ".$this->default_order_by." ".$this->default_sort_order;
		$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize',40);
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";

		$result = $adb->pquery($list_query.$limit, $params);
		$rscnt = $adb->pquery("select count(*) from vtiger_loginhistory $where", array($params));
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
			$entries_list['next_page_url'] = 'index.php?module=cbLoginHistory&action=cbLoginHistoryAjax&file=getJSON&page='.($islastpage ? $page : $page+1);
		}
		$entries_list['prev_page_url'] = 'index.php?module=cbLoginHistory&action=cbLoginHistoryAjax&file=getJSON&page='.($page == 1 ? 1 : $page-1);
		$in = getTranslatedString('Signed in');
		$out = getTranslatedString('Signed off');
		while($lgn = $adb->fetch_array($result)) {
			$entry = array();
			$entry['User Name'] = $lgn['user_name'];
			$entry['User IP'] = $lgn['user_ip'];
			$entry['Signin Time'] = $lgn['login_time'];
			$entry['Signout Time'] = $lgn['logout_time'];
			$entry['Status'] = ($lgn['status']=='Signed in' ? $in : $out);
			$entries_list['data'][] = $entry;
		}
		$log->debug("Exiting getHistoryJSON() method ...");
		return json_encode($entries_list);
	}

	/** Function that Records the Login info of the User
	 *  @param ref variable $usname :: Type varchar
	 *  @param ref variable $usip :: Type varchar
	 *  @param ref variable $intime :: Type timestamp
	 *  Returns the query result which contains the details of User Login Info
	*/
	function user_login(&$usname,&$usip,&$intime) {
		global $adb;
		cbEventHandler::do_action('corebos.audit.login',array($usname, 'Users', 'Login', $usname, date("Y-m-d H:i:s")));
		$query = "Insert into vtiger_loginhistory (user_name, user_ip, logout_time, login_time, status) values (?,?,?,?,?)";
		$params = array($usname,$usip,null, $this->db->formatDate($intime, true),'Signed in');
		return $adb->pquery($query, $params);
	}

	/** Function that Records the Logout info of the User
	 *  @param ref variable $usname :: Type varchar
	 *  @param ref variable $usip :: Type varchar
	 *  @param ref variable $outime :: Type timestamp
	 *  Returns the query result which contains the details of User Logout Info
	*/
	function user_logout(&$usname,&$usip,&$outtime) {
		global $adb;
		cbEventHandler::do_action('corebos.audit.logout',array($usname, 'Users', 'Logout', $usname, date("Y-m-d H:i:s")));
		$logid_qry = "SELECT max(login_id) AS login_id from vtiger_loginhistory where user_name=? and user_ip=?";
		$result = $adb->pquery($logid_qry, array($usname, $usip));
		$loginid = $adb->query_result($result,0,"login_id");
		if ($loginid == '') {
			return;
		}
		// update the user login info.
		$query = "Update vtiger_loginhistory set logout_time =?, status=? where login_id = ?";
		$result = $adb->pquery($query, array($this->db->formatDate($outtime, true), 'Signed off', $loginid));
	}

	/**
	 * Determine if the user has logged-in first
	 * @param accept_delay_seconds Allow the delay (in seconds) between login_time recorded and current time as first time.
	 * This will be helpful if login is performed and client is redirected for home page where this function is invoked.
	 */
	static function firstTimeLoggedIn($user_name, $accept_delay_seconds=10) {
		global $adb;
		$firstTimeLoginStatus = false;

		// Search for at-least two records.
		$query = 'SELECT login_time, logout_time FROM vtiger_loginhistory WHERE user_name=? ORDER BY login_id DESC LIMIT 2';
		$result= $adb->pquery($query, array($user_name));
		$recordCount = $result? $adb->num_rows($result) : 0;

		if ($recordCount === 0) {
			$firstTimeLoginStatus = true;
			cbEventHandler::do_action('corebos.audit.firsttime.login',array($user_name, 'Users', 'FirstTimeLogin', $user_name, date("Y-m-d H:i:s")));
		} else {
			if ($recordCount == 1) { // Only first time?
				$row = $adb->fetch_array($result);
				$login_delay = time() - strtotime($row['login_time']);
				// User not logged out and is within expected delay?
				if (empty($row['logout_time']) && $login_delay < $accept_delay_seconds) {
					$firstTimeLoginStatus = true;
				}
			}
		}
		return $firstTimeLoginStatus;
	}
}

?>
