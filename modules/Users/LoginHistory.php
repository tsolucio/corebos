<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
require_once 'include/logging.php';
require_once 'include/ListView/ListView.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/ListView/ListViewJSON.php';

/** This class is used to store and display the login history of all the Users.
 * An Admin User can view his login history details  and of all the other users as well.
* StandardUser is allowed to view only his login history details.
**/
class LoginHistory {
	private $db;

	// Stored fields
	public $login_id;
	public $user_name;
	public $user_ip;
	public $login_time;
	public $logout_time;
	public $status;
	public $module_name = 'Users';

	public $table_name = 'vtiger_loginhistory';

	public $column_fields = array(
		'id',
		'login_id',
		'user_name',
		'user_ip',
		'login_time',
		'logout_time',
		'status',
	);

	public function __construct() {
		$this->db = PearDatabase::getInstance();
	}

	public $sortby_fields = array('user_name', 'user_ip', 'login_time', 'logout_time', 'status');

	// This is the list of vtiger_fields that are in the lists.
	public $list_fields = array(
			'User Name'=>array('vtiger_loginhistory'=>'user_name'),
			'User IP'=>array('vtiger_loginhistory'=>'user_ip'),
			'Signin Time'=>array('vtiger_loginhistory'=>'login_time'),
			'Signout Time'=>array('vtiger_loginhistory'=>'logout_time'),
			'Status'=>array('vtiger_loginhistory'=>'status'),
		);

	public $list_fields_name = array(
		'User Name'=>'user_name',
		'User IP'=>'user_ip',
		'Signin Time'=>'login_time',
		'Signout Time'=>'logout_time',
		'Status'=>'status'
		);
	public $default_order_by = 'login_time';
	public $default_sort_order = 'DESC';

	/**
	 * Function to get the Header values of Login History.
	 * Returns Header Values like UserName, IP, LoginTime etc in an array format.
	**/
	public function getHistoryListViewHeader() {
		global $log, $app_strings;
		$log->debug('> getHistoryListViewHeader');
		$header_array = array(
			$app_strings['LBL_LIST_USER_NAME'],
			$app_strings['LBL_LIST_USERIP'],
			$app_strings['LBL_LIST_SIGNIN'],
			$app_strings['LBL_LIST_SIGNOUT'],
			$app_strings['LBL_LIST_STATUS'],
		);
		$log->debug('< getHistoryListViewHeader');
		return $header_array;
	}

	/**
	 * Function to get the Login History values of the User.
	* @param $navigation_array - Array values to navigate through the number of entries.
	* @param $sortorder - DESC
	* @param $order_by - login_time
	* Returns the login history entries in an array format.
	**/
	public function getHistoryListViewEntries($username, $navigation_array, $sorder = '', $order_by = '') {
		global $log, $adb;
		$log->debug('> getHistoryListViewEntries');

		if ($sorder != '' && $order_by != '') {
			$list_query = 'Select * from vtiger_loginhistory where user_name=? order by '.$order_by.' '.$sorder;
		} else {
			$list_query = 'Select * from vtiger_loginhistory where user_name=? order by '.$this->default_order_by.' '.$this->default_sort_order;
		}

		$result = $adb->pquery($list_query, array($username));
		$entries_list = array();

		if ($navigation_array['end_val'] != 0) {
			$in = getTranslatedString('Signed in');
			$out = getTranslatedString('Signed off');
			for ($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++) {
				$entries = array();
				$entries[] = $adb->query_result($result, $i-1, 'user_name');
				$entries[] = $adb->query_result($result, $i-1, 'user_ip');
				$entries[] = $adb->query_result($result, $i-1, 'login_time');
				$entries[] = $adb->query_result($result, $i-1, 'logout_time');
				$entries[] = ($adb->query_result($result, $i-1, 'status')=='Signed in' ? $in : $out);
				$entries_list[] = $entries;
			}
		}
		$log->debug('< getHistoryListViewEntries');
		return $entries_list;
	}

	public function getHistoryJSON($userid, $page, $order_by = 'login_time', $sorder = 'DESC') {
		global $log, $adb;
		$log->debug('> getHistoryJSON');
		$where = '';
		if (!empty($userid)) {
			$username = getUserName($userid);
			$where .=  $adb->convert2Sql(' where user_name=?', array($username));
		}
		if ($sorder != '' && $order_by != '') {
			$list_query = "Select * from vtiger_loginhistory $where order by $order_by $sorder";
		} else {
			$list_query = "Select * from vtiger_loginhistory $where order by ".$this->default_order_by.' '.$this->default_sort_order;
		}
		if (!empty($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage'])) {
			$rowsperpage = (int) vtlib_purify($_REQUEST['perPage']);
		} else {
			$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 40);
		}
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";
		$q = $list_query.$limit;
		$grid = new GridListView('cbLoginHistory');
		$grid->currentPage = $page;
		$entries_list = $grid->gridTableBasedEntries($q, $this->list_fields, $this->table_name);
		$log->debug('< getHistoryJSON');
		return json_encode($entries_list);
	}

	/** Function that Records the Login info of the User
	 *  @param string $usname user name logging in
	 *  @param string $usip IP from which user is logging in
	 *  @param datetime $intime login time
	 *  Returns the query result which contains the details of User Login Info
	*/
	public function user_login(&$usname, $usip, $intime) {
		global $adb;
		cbEventHandler::do_action('corebos.audit.login', array($usname, 'Users', 'Login', $usname, date('Y-m-d H:i:s')));
		$query = 'Insert into vtiger_loginhistory (user_name, user_ip, logout_time, login_time, status) values (?,?,?,?,?)';
		$params = array($usname, $usip, null, $this->db->formatDate($intime, true), 'Signed in');
		return $adb->pquery($query, $params);
	}

	/** Function that Records the Logout info of the User
	 *  @param ref variable $usname :: Type varchar
	 *  @param ref variable $usip :: Type varchar
	 *  @param ref variable $outime :: Type timestamp
	 *  Returns the query result which contains the details of User Logout Info
	*/
	public function user_logout($usname, $usip, $outtime) {
		global $adb;
		cbEventHandler::do_action('corebos.audit.logout', array($usname, 'Users', 'Logout', $usname, date('Y-m-d H:i:s')));
		$logid_qry = 'SELECT max(login_id) AS login_id from vtiger_loginhistory where user_name=? and user_ip=?';
		$result = $adb->pquery($logid_qry, array($usname, $usip));
		$loginid = $adb->query_result($result, 0, 'login_id');
		if ($loginid == '') {
			return;
		}
		// update the user login info.
		$query = 'Update vtiger_loginhistory set logout_time =?, status=? where login_id = ?';
		$result = $adb->pquery($query, array($this->db->formatDate($outtime, true), 'Signed off', $loginid));
	}

	/**
	 * Determine if the user has logged-in first
	 * @param accept_delay_seconds Allow the delay (in seconds) between login_time recorded and current time as first time.
	 * This will be helpful if login is performed and client is redirected for home page where this function is invoked.
	 */
	public static function firstTimeLoggedIn($user_name, $accept_delay_seconds = 10) {
		global $adb;
		$firstTimeLoginStatus = false;

		// Search for at-least two records.
		$query = 'SELECT login_time, logout_time FROM vtiger_loginhistory WHERE user_name=? ORDER BY login_id DESC LIMIT 2';
		$result= $adb->pquery($query, array($user_name));
		$recordCount = $result? $adb->num_rows($result) : 0;

		if ($recordCount === 0) {
			$firstTimeLoginStatus = true;
			cbEventHandler::do_action('corebos.audit.firsttime.login', array($user_name, 'Users', 'FirstTimeLogin', $user_name, date('Y-m-d H:i:s')));
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
