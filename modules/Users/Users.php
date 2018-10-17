<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'data/CRMEntity.php';
require_once 'modules/Calendar/Activity.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'data/Tracker.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/Webservices/Utils.php';
require_once 'modules/Users/UserTimeZonesArray.php';
include_once 'modules/Users/authTypes/TwoFactorAuth/autoload.php';
use \RobThree\Auth\TwoFactorAuth;

class Users extends CRMEntity {
	public $db;
	public $log;

	// Stored fields
	public $id;
	public $authenticated = false;
	public $twoFAauthenticated = false;
	public $error_string;
	public $is_admin;
	public $deleted;

	public $tab_name = array('vtiger_users', 'vtiger_attachments', 'vtiger_user2role', 'vtiger_asteriskextensions');
	public $tab_name_index = array('vtiger_users'=>'id', 'vtiger_attachments'=>'attachmentsid', 'vtiger_user2role'=>'userid', 'vtiger_asteriskextensions'=>'userid');

	public $table_name = 'vtiger_users';
	public $table_index = 'id';

	// This is the list of fields that are in the lists.
	public $list_link_field = 'last_name';

	public $list_mode;
	public $popup_type;

	public $search_fields = array(
		'Name' => array('vtiger_users' => 'last_name'),
		'Email' => array('vtiger_users' => 'email1'),
		'Email2' => array('vtiger_users' => 'email2'),
	);
	public $search_fields_name = array(
		'Name' => 'last_name',
		'Email' => 'email1',
		'Email2' => 'email2',
	);

	public $module_name = 'Users';

	public $user_preferences;
	public $homeorder_array = array(
		'HDB'=>'',
		'ALVT'=>'',
		'PLVT'=>'',
		'QLTQ'=>'',
		'CVLVT'=>'',
		'HLT'=>'',
		'GRT'=>'',
		'OLTSO'=>'',
		'ILTI'=>'',
		'MNL'=>'',
		'OLTPO'=>'',
		'LTFAQ'=>'',
		'UA'=>'',
		'PA'=>'',
	);

	public $encodeFields = array('first_name', 'last_name', 'description');

	public $sortby_fields = array('status', 'email1', 'email2', 'phone_work', 'is_admin', 'user_name', 'last_name');

	// This is the list of fields that are in the lists.
	public $list_fields = array(
		'First Name' => array('vtiger_users' => 'first_name'),
		'Last Name' => array('vtiger_users' => 'last_name'),
		'Role Name' => array('vtiger_user2role' => 'roleid'),
		'User Name' => array('vtiger_users' => 'user_name'),
		'Status' => array('vtiger_users' => 'status'),
		'Email' => array('vtiger_users' => 'email1'),
		'Email2' => array('vtiger_users' => 'email2'),
		'Admin' => array('vtiger_users' => 'is_admin'),
		'Phone' => array('vtiger_users' => 'phone_work'),
	);
	public $list_fields_name = array(
		'Last Name' => 'last_name',
		'First Name' => 'first_name',
		'Role Name' => 'roleid',
		'User Name' => 'user_name',
		'Status' => 'status',
		'Email' => 'email1',
		'Email2' => 'email2',
		'Admin' => 'is_admin',
		'Phone' => 'phone_work',
	);

	public $popup_fields = array('last_name');

	// This is the list of fields that are in the lists.
	public $default_order_by = "user_name";
	public $default_sort_order = 'ASC';

	public $record_id;

	public $DEFAULT_PASSWORD_CRYPT_TYPE;
	//'BLOWFISH', /* before PHP5.3*/ MD5;

	/** constructor function for the main user class
	 instantiates the Logger class and PearDatabase Class
	 */
	public function __construct() {
		$this->log = LoggerManager::getLogger('user');
		$this->log->debug("Entering Users() method ...");
		$this->db = PearDatabase::getInstance();
		$this->DEFAULT_PASSWORD_CRYPT_TYPE = (version_compare(PHP_VERSION, '5.3.0') >= 0) ? 'PHP5.3MD5' : 'MD5';
		$this->column_fields = getColumnFields('Users');
		$this->column_fields['currency_name'] = '';
		$this->column_fields['currency_code'] = '';
		$this->column_fields['currency_symbol'] = '';
		$this->column_fields['conv_rate'] = '';
		$this->log->debug("Exiting Users() method ...");
	}

	/**
	 * Function to get sort order
	 * return string  $sorder    - sortorder string either 'ASC' or 'DESC'
	 */
	public function getSortOrder() {
		global $log;
		$log->debug("Entering getSortOrder() method ...");
		if (isset($_REQUEST['sorder'])) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} else {
			$sorder = (!empty($_SESSION['USERS_SORT_ORDER']) ? ($_SESSION['USERS_SORT_ORDER']) : ($this->default_sort_order));
		}
		$log->debug("Exiting getSortOrder method ...");
		return $sorder;
	}

	/**
	 * Function to get order by
	 * return string  $order_by    - fieldname(eg: 'subject')
	 */
	public function getOrderBy() {
		global $log;
		$log->debug("Entering getOrderBy() method ...");

		$use_default_order_by = '';
		if (GlobalVariable::getVariable('Application_ListView_Default_Sorting', 0)) {
			$use_default_order_by = $this->default_order_by;
		}

		if (isset($_REQUEST['order_by'])) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} else {
			$order_by = (!empty($_SESSION['USERS_ORDER_BY']) ? ($_SESSION['USERS_ORDER_BY']) : ($use_default_order_by));
		}
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}

	/** Function to set the user preferences in the session
	 * @param $name -- name:: Type varchar
	 * @param $value -- value:: Type varchar
	 */
	public function setPreference($name, $value) {
		if (!isset($this->user_preferences)) {
			if (isset($_SESSION["USER_PREFERENCES"])) {
				$this->user_preferences = $_SESSION["USER_PREFERENCES"];
			} else {
				$this->user_preferences = array();
			}
		}
		if (!array_key_exists($name, $this->user_preferences) || $this->user_preferences[$name] != $value) {
			$this->log->debug("Saving To Preferences:" . $name . "=" . print_r($value, true));
			$this->user_preferences[$name] = $value;
			$this->savePreferecesToDB();
		}
		coreBOS_Session::set($name, $value);
	}

	/** Function to save the user preferences to db
	 *
	 */
	public function savePreferecesToDB() {
		$data = base64_encode(serialize($this->user_preferences));
		$query = "UPDATE $this->table_name SET user_preferences=? where id=?";
		$result = $this->db->pquery($query, array($data, $this->id));
		$this->log->debug("SAVING: PREFERENCES SIZE " . strlen($data) . "ROWS AFFECTED WHILE UPDATING USER PREFERENCES:" . $this->db->getAffectedRowCount($result));
		coreBOS_Session::set('USER_PREFERENCES', $this->user_preferences);
	}

	/** Function to load the user preferences from db
	 *
	 */
	public function loadPreferencesFromDB($value) {
		if (isset($value) && !empty($value)) {
			$this->log->debug("LOADING :PREFERENCES SIZE " . strlen($value));
			$this->user_preferences = unserialize(base64_decode($value));
			coreBOS_Session::merge($this->user_preferences);
			$this->log->debug("Finished Loading");
			coreBOS_Session::set('USER_PREFERENCES', $this->user_preferences);
		}
	}

	/**
	 * @return string encrypted password for storage in DB and comparison against DB password.
	 * @param string $user_name - Must be non null and at least 2 characters
	 * @param string $user_password - Must be non null and at least 1 character.
	 * @desc Take an unencrypted username and password and return the encrypted password
	 */
	public function encrypt_password($user_password, $crypt_type = '') {
		// encrypt the password.
		$salt = substr($this->column_fields["user_name"], 0, 2);

		// Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4923
		if ($crypt_type == '') {
			// Try to get the crypt_type which is in database for the user
			$crypt_type = $this->get_user_crypt_type();
		}

		// For more details on salt format look at: http://in.php.net/crypt
		if ($crypt_type == 'MD5') {
			$salt = '$1$' . $salt . '$';
		} elseif ($crypt_type == 'BLOWFISH') {
			$salt = '$2$' . $salt . '$';
		} elseif ($crypt_type == 'PHP5.3MD5') {
			//only change salt for php 5.3 or higher version for backward
			//compactibility.
			//crypt API is lot stricter in taking the value for salt.
			$salt = '$1$' . str_pad($salt, 9, '0');
		}
		return crypt($user_password, $salt);
	}

	/** Function for authorization check */
	public function authorization_check($validate, $authkey, $i) {
		$validate = base64_decode($validate);
		$authkey = base64_decode($authkey);
		if (file_exists($validate) && $handle = fopen($validate, 'rb', true)) {
			$buffer = fread($handle, filesize($validate));
			if (substr_count($buffer, $authkey) < $i) {
				return -1;
			}
		} else {
			return -1;
		}
	}

	/**
	 * Checks the User_AuthenticationType global variavle value for login type and forks off to the proper module
	 *
	 * @param string $user_password - The password of the user to authenticate
	 * @return true if the user is authenticated, false otherwise
	 */
	public function doLogin($user_password) {
		$authType = GlobalVariable::getVariable('User_AuthenticationType', 'SQL');
		if ($this->is_admin) {
			$authType = 'SQL'; // admin users always login locally
		}
		$usr_name = $this->column_fields["user_name"];

		switch (strtoupper($authType)) {
			case 'LDAP':
				$this->log->debug("Using LDAP authentication");
				require_once 'modules/Users/authTypes/LDAP.php';
				$result = ldapAuthenticate($this->column_fields["user_name"], $user_password);
				if ($result == null) {
					return false;
				} else {
					return true;
				}
				break;

			case 'AD':
				$this->log->debug("Using Active Directory authentication");
				require_once 'modules/Users/authTypes/adLDAP.php';
				$adldap = new adLDAP();
				if ($adldap->authenticate($this->column_fields["user_name"], $user_password)) {
					return true;
				} else {
					return false;
				}
				break;

			default:
				$this->log->debug("Using integrated/SQL authentication");
				$query = "SELECT crypt_type FROM $this->table_name WHERE BINARY user_name=?";
				$result = $this->db->requirePsSingleResult($query, array($usr_name), false);
				if (empty($result)) {
					return false;
				}
				$crypt_type = $this->db->query_result($result, 0, 'crypt_type');
				$encrypted_password = $this->encrypt_password($user_password, $crypt_type);
				$maxFailedLoginAttempts = GlobalVariable::getVariable('Application_MaxFailedLoginAttempts', 5);
				$query = "SELECT * from $this->table_name where user_name=? AND user_password=?";
				$params = array($usr_name, $encrypted_password);
				$cnuser=$this->db->getColumnNames($this->table_name);
				if (in_array('failed_login_attempts', $cnuser)) {
					$query.= ' AND COALESCE(failed_login_attempts,0)<?';
					$params[] = $maxFailedLoginAttempts;
				}
				$result = $this->db->requirePsSingleResult($query, $params, false);
				if (empty($result)) {
					return false;
				} else {
					return true;
				}
				break;
		}
		return false;
	}

	public static function send2FACode($code, $userid) {
		global $adb;
		$msg = sprintf(getTranslatedString('2FA_ACCESSCODE', 'Users'), $code);
		$SendCodeMethod = strtoupper(GlobalVariable::getVariable('User_2FAAuthentication_SendMethod', 'EMAIL', 'Users', $userid));
		if (!vtlib_isModuleActive('SMSNotifier') && $SendCodeMethod=='SMS') {
			$SendCodeMethod = 'EMAIL';
		}
		switch ($SendCodeMethod) {
			case 'SMS':
				require_once 'modules/SMSNotifier/SMSNotifier.php';
				$rs = $adb->pquery('select coalesce(phone_work,phone_mobile) as phone from vtiger_users where userid=?', array($userid));
				if ($rs && $adb->num_rows($rs)>0) {
					$phone = $adb->query_result($rs, 0, 0);
					if (!empty($phone)) {
						SMSNotifier::sendsms($msg, $phone, $userid, $userid, 'Users');
					}
				}
				break;
			case 'MOBILE':
				break;
			case 'EMAIL':
			default:
				require_once 'modules/Emails/mail.php';
				require_once 'modules/Emails/Emails.php';
				$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk', $userid);
				$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk', $userid);
				$mailto = getUserEmail($userid);
				if ($mailto!='') {
					send_mail('Emails', $mailto, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $msg, $msg);
				}
				break;
		}
	}

	/**
	 * Load a user based on the user_name in $this
	 * @return -- this if load was successul and null if load failed.
	 */
	public function load_user($user_password) {
		$usr_name = $this->column_fields["user_name"];
		if (!empty($_POST['twofauserauth'])) {
			$this->authenticated = false;
			$this->twoFAauthenticated = false;
			// csrf check
			// Get the fields for the user
			$query = "SELECT * from $this->table_name where user_name='$usr_name'";
			$result = $this->db->requireSingleResult($query, false);
			$row = $this->db->fetchByAssoc($result);
			if ($row['id']!=$_POST['twofauserauth']) {
				return null;
			}
			// validate 2fa
			$tfa = new TwoFactorAuth('coreBOSWebApp');
			$twofasecret = coreBOS_Settings::getSetting('coreBOS_2FA_Secret_'.$row['id'], false);
			if ($twofasecret === false || $tfa->verifyCode($twofasecret, $_POST['user_2facode']) === false) {
				return null;
			}
			$this->column_fields = $row;
			$this->id = $row['id'];
			$this->authenticated = true;
			$this->twoFAauthenticated = true;
			return $this;
		}
		$maxFailedLoginAttempts = GlobalVariable::getVariable('Application_MaxFailedLoginAttempts', 5, 'Users');
		if (isset($_SESSION['loginattempts'])) {
			coreBOS_Session::set('loginattempts', $_SESSION['loginattempts'] + 1);
		} else {
			coreBOS_Session::set('loginattempts', 1);
		}
		if ($_SESSION['loginattempts'] > $maxFailedLoginAttempts) {
			$this->log->warn("SECURITY: " . $usr_name . " has attempted to login " . $_SESSION['loginattempts'] . " times.");
		}
		$this->log->debug("Starting user load for $usr_name");

		if (!isset($this->column_fields["user_name"]) || $this->column_fields["user_name"] == "" || !isset($user_password) || $user_password == "") {
			return null;
		}

		$authCheck = false;
		$authCheck = $this->doLogin($user_password);

		if (!$authCheck) {
			$this->log->warn("User authentication for $usr_name failed");
			return null;
		}

		// Get the fields for the user
		$query = "SELECT * from $this->table_name where user_name='$usr_name'";
		$result = $this->db->requireSingleResult($query, false);

		$row = $this->db->fetchByAssoc($result);
		$this->column_fields = $row;
		$this->id = $row['id'];

		$this->loadPreferencesFromDB($row['user_preferences']);

		// Make sure user is logging in from authorized IPs
		$UserLoginIPs = GlobalVariable::getVariable('Application_UserLoginIPs', '', 'Users', $this->id);
		if ($UserLoginIPs != '') {
			$user_ip_addresses = explode(',', $UserLoginIPs);
			$the_ip = Vtiger_Request::get_ip();
			if (!in_array($the_ip, $user_ip_addresses)) {
				$row['status'] = 'Inactive';
				$this->authenticated = false;
				coreBOS_Session::set('login_error', getTranslatedString('ERR_INVALID_USERIPLOGIN', 'Users'));
				$mailsubject = "[Security Alert]: User login attempt rejected for login: $usr_name from external IP: $the_ip";
				$this->log->warn($mailsubject);
				// Send email with authentification error.
				$mailto = GlobalVariable::getVariable('Debug_Send_UserLoginIPAuth_Error', '', 'Users');
				if ($mailto != '') {
					require_once 'modules/Emails/mail.php';
					require_once 'modules/Emails/Emails.php';
					$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
					$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
					$mailcontent = $mailsubject. "\n";
					send_mail('Emails', $mailto, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $mailsubject, $mailcontent);
				}
			}
		}
		// Make sure admin is logging in from authorized IPs
		if ($row['is_admin'] == 'on' || $row['is_admin'] == '1') {
			$AdminLoginIPs = GlobalVariable::getVariable('Application_AdminLoginIPs', '', 'Users', $this->id);
			if ($AdminLoginIPs != '') {
				$admin_ip_addresses = explode(',', $AdminLoginIPs);
				$the_ip = Vtiger_Request::get_ip();
				if (!in_array($the_ip, $admin_ip_addresses)) {
					$row['status'] = 'Inactive';
					$this->authenticated = false;
					coreBOS_Session::set('login_error', getTranslatedString('ERR_INVALID_ADMINIPLOGIN', 'Users'));
					$mailsubject = "[Security Alert]: Admin login attempt rejected for login: $usr_name from external IP: $the_ip";
					$this->log->warn($mailsubject);
					// Send email with authentification error.
					$mailto = GlobalVariable::getVariable('Debug_Send_AdminLoginIPAuth_Error', '', 'Users');
					if ($mailto != '') {
						require_once 'modules/Emails/mail.php';
						require_once 'modules/Emails/Emails.php';
						$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
						$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
						$mailcontent = $mailsubject. "\n";
						send_mail('Emails', $mailto, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $mailsubject, $mailcontent);
					}
				}
			}
		}
		if ($row['status'] != 'Inactive') {
			$this->authenticated = true;
		}

		coreBOS_Session::delete('loginattempts');
		return $this;
	}

	/**
	 * Get crypt type to use for password for the user.
	 * Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4923
	 */
	public function get_user_crypt_type() {
		$crypt_res = null;
		$crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;

		// For backward compatability, we need to make sure to handle this case.
		global $adb;
		$table_cols = $adb->getColumnNames("vtiger_users");
		if (!in_array("crypt_type", $table_cols)) {
			return $crypt_type;
		}

		if (isset($this->id)) {
			// Get the type of crypt used on password before actual comparision
			$qcrypt_sql = "SELECT crypt_type from $this->table_name where id=?";
			$crypt_res = $this->db->pquery($qcrypt_sql, array($this->id), true);
		} elseif (isset($this->column_fields["user_name"])) {
			$qcrypt_sql = "SELECT crypt_type from $this->table_name where user_name=?";
			$crypt_res = $this->db->pquery($qcrypt_sql, array($this->column_fields["user_name"]));
		} else {
			$crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;
		}

		if ($crypt_res && $this->db->num_rows($crypt_res)) {
			$crypt_row = $this->db->fetchByAssoc($crypt_res);
			$crypt_type = $crypt_row['crypt_type'];
		}
		return $crypt_type;
	}

	/**
	 * @param string $user name - Must be non null and at least 1 character.
	 * @param string $user_password - Must be non null and at least 1 character.
	 * @param string $new_password - Must be non null and at least 1 character.
	 * @return boolean - If passwords pass verification and query succeeds, return true, else return false.
	 * @desc Verify that the current password is correct and write the new password to the DB.
	 */
	public function change_password($user_password, $new_password, $dieOnError = true) {
		global $mod_strings, $current_user;
		$usr_name = $this->column_fields["user_name"];
		$this->log->debug("Starting password change for $usr_name");

		if (!isset($new_password) || $new_password == "") {
			$this->error_string = $mod_strings['ERR_PASSWORD_CHANGE_FAILED_1'] . $user_name . $mod_strings['ERR_PASSWORD_CHANGE_FAILED_2'];
			return false;
		}

		if (!$this->verifyPassword($user_password) && !is_admin($current_user)) {
			$this->log->warn("Incorrect old password for $usr_name");
			$this->error_string = $mod_strings['ERR_PASSWORD_INCORRECT_OLD'];
			return false;
		}

		//set new password
		$crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;
		$encrypted_new_password = $this->encrypt_password($new_password, $crypt_type);

		// Set change password at next login to 0 if resetting your own password
		if (!empty($current_user) && $current_user->id == $this->id) {
			$change_password_next_login = 0;
		} else {
			$change_password_next_login = 1;
		}
		$cnuser=$this->db->getColumnNames($this->table_name);
		if (!in_array('change_password', $cnuser)) {
			$this->db->query("ALTER TABLE `vtiger_users` ADD `change_password` boolean NOT NULL DEFAULT 0");
		}
		if (!in_array('last_password_reset_date', $cnuser)) {
			$this->db->query("ALTER TABLE `vtiger_users` ADD `last_password_reset_date` date DEFAULT NULL");
		}
		$query = "UPDATE $this->table_name
			SET user_password=?, confirm_password=?, crypt_type=?, change_password=?, last_password_reset_date=now(), failed_login_attempts=0
			where id=?";
		$this->db->pquery($query, array($encrypted_new_password, $encrypted_new_password, $crypt_type, $change_password_next_login, $this->id));
		$this->createAccessKey();
		require_once 'modules/Users/CreateUserPrivilegeFile.php';
		createUserPrivilegesfile($this->id);
		return true;
	}

	public function mustChangePassword() {
		$cnuser=$this->db->getColumnNames('vtiger_users');
		if (!in_array('change_password', $cnuser)) {
			$this->db->query("ALTER TABLE `vtiger_users` ADD `change_password` boolean NOT NULL DEFAULT 0");
		}
		$cprs = $this->db->pquery('select change_password from vtiger_users where id=?', array($this->id));
		return $this->db->query_result($cprs, 0, 0);
	}

	public function de_cryption($data) {
		require_once 'include/utils/encryption.php';
		$de_crypt = new Encryption();
		if (isset($data)) {
			$decrypted_password = $de_crypt->decrypt($data);
		}
		return $decrypted_password;
	}

	public function changepassword($newpassword) {
		require_once 'include/utils/encryption.php';
		$en_crypt = new Encryption();
		if (isset($newpassword)) {
			$encrypted_password = $en_crypt->encrypt($newpassword);
		}
		return $encrypted_password;
	}

	public function verifyPassword($password) {
		$query = "SELECT user_name,user_password,crypt_type FROM {$this->table_name} WHERE id=?";
		$result = $this->db->pquery($query, array($this->id));
		$row = $this->db->fetchByAssoc($result);
		$encryptedPassword = $this->encrypt_password($password, $row['crypt_type']);
		return !($encryptedPassword != $row['user_password']);
	}

	public function is_authenticated() {
		return $this->authenticated;
	}

	public function is_twofaauthenticated() {
		$do2FA = GlobalVariable::getVariable('User_2FAAuthentication', 0, 'Users', (empty($this->id) ? Users::getActiveAdminId() : $this->id));
		if ($do2FA) {
			return $this->twoFAauthenticated;
		} else {
			return true;
		}
	}

	/** gives the user id for the specified user name
	 * @param $user_name -- user name:: Type varchar
	 * @returns user id
	 */
	public function retrieve_user_id($user_name) {
		global $adb;
		$query = 'SELECT id from vtiger_users where user_name=? AND deleted=0';
		$result = $adb->pquery($query, array($user_name));
		if ($result && $adb->num_rows($result) > 0) {
			$userid = $adb->query_result($result, 0, 'id');
		} else {
			$userid = 0;
		}
		return $userid;
	}

	/** check if given number is a valid and active user ID
	 * @param integer $userid
	 * @returns boolean
	 */
	public static function is_ActiveUserID($userid) {
		global $adb;
		if (empty($userid) || !is_numeric($userid)) {
			return false;
		}
		$query = "SELECT 1 from vtiger_users where status='Active' AND id=? AND deleted=0";
		$result = $adb->pquery($query, array($userid));
		return ($result && $adb->num_rows($result) > 0);
	}

	/**
	 * @return -- returns a list of all users in the system.
	 */
	public function verify_data() {
		$usr_name = $this->column_fields["user_name"];
		global $mod_strings;

		$query = 'SELECT user_name from vtiger_users where user_name=? AND id<>? AND deleted=0';
		$result = $this->db->pquery($query, array($usr_name, $this->id), true, 'Error selecting possible duplicate users: ');
		$dup_users = $this->db->fetchByAssoc($result);

		$query = "SELECT user_name from vtiger_users where is_admin = 'on' AND deleted=0";
		$result = $this->db->pquery($query, array(), true, "Error selecting possible duplicate vtiger_users: ");
		$last_admin = $this->db->fetchByAssoc($result);

		$this->log->debug("last admin length: " . count($last_admin));
		$this->log->debug($last_admin['user_name'] . " == " . $usr_name);

		$verified = true;
		if ($dup_users != null) {
			$this->error_string .= $mod_strings['ERR_USER_NAME_EXISTS_1'] . $usr_name . '' . $mod_strings['ERR_USER_NAME_EXISTS_2'];
			$verified = false;
		}
		if (!isset($_REQUEST['is_admin']) && count($last_admin) == 1 && $last_admin['user_name'] == $usr_name) {
			$this->log->debug("last admin length: " . count($last_admin));

			$this->error_string .= $mod_strings['ERR_LAST_ADMIN_1'] . $usr_name . $mod_strings['ERR_LAST_ADMIN_2'];
			$verified = false;
		}

		return $verified;
	}

	/** Function to return the column name array */
	public function getColumnNames_User() {
		$mergeflds = array('FIRSTNAME', 'LASTNAME', 'USERNAME', 'SECONDARYEMAIL', 'TITLE', 'OFFICEPHONE', 'DEPARTMENT', 'MOBILE', 'OTHERPHONE', 'FAX', 'EMAIL',
			'HOMEPHONE', 'OTHEREMAIL', 'PRIMARYADDRESS', 'CITY', 'STATE', 'POSTALCODE', 'COUNTRY');
		return $mergeflds;
	}

	public function fill_in_additional_list_fields() {
		$this->fill_in_additional_detail_fields();
	}

	public function fill_in_additional_detail_fields() {
		$query = "SELECT u1.first_name, u1.last_name from vtiger_users u1, vtiger_users u2 where u1.id = u2.reports_to_id AND u2.id = ? and u1.deleted=0";
		$result = $this->db->pquery($query, array($this->id), true, "Error filling in additional detail vtiger_fields");

		$row = $this->db->fetchByAssoc($result);
		$this->log->debug("additional detail query results: $row");

		if ($row != null) {
			$this->reports_to_name = stripslashes(getFullNameFromArray('Users', $row));
		} else {
			$this->reports_to_name = '';
		}
	}

	/** Function to get the current user information from the user_privileges file
	 * @param $userid -- user id:: Type integer
	 * @returns user info in $this->column_fields array:: Type array
	 *
	 */
	public function retrieveCurrentUserInfoFromFile($userid) {
		checkFileAccessForInclusion('user_privileges/user_privileges_' . $userid . '.php');
		require 'user_privileges/user_privileges_' . $userid . '.php';
		foreach ($this->column_fields as $field => $value_iter) {
			if (isset($user_info[$field])) {
				$this->$field = $user_info[$field];
				$this->column_fields[$field] = $user_info[$field];
			}
		}
		$this->id = $userid;
		return $this;
	}

	/** Function to save the user information into the database
	 * @param $module -- module name:: Type varchar
	 */
	public function saveentity($module, $fileid = '') {
		global $current_user;
		//$adb added by raju for mass mailing
		$insertion_mode = $this->mode;
		if (empty($this->column_fields['time_zone'])) {
			$dbDefaultTimeZone = DateTimeField::getDBTimeZone();
			$this->column_fields['time_zone'] = $dbDefaultTimeZone;
			$this->time_zone = $dbDefaultTimeZone;
		}
		if (empty($this->column_fields['currency_id'])) {
			$this->column_fields['currency_id'] = CurrencyField::getDBCurrencyId();
		}
		if (empty($this->column_fields['date_format'])) {
			$this->column_fields['date_format'] = 'yyyy-mm-dd';
		}

		$this->db->println("TRANS saveentity starts $module");
		$this->db->startTransaction();
		foreach ($this->tab_name as $table_name) {
			if ($table_name == 'vtiger_attachments') {
				$this->insertIntoAttachment($this->id, $module, $fileid);
			} else {
				$this->insertIntoEntityTable($table_name, $module, $fileid);
			}
		}
		require_once 'modules/Users/CreateUserPrivilegeFile.php';
		createUserPrivilegesfile($this->id);
		coreBOS_Session::delete('next_reminder_interval');
		coreBOS_Session::delete('next_reminder_time');
		if ($insertion_mode != 'edit') {
			$this->createAccessKey();
		}
		$this->db->completeTransaction();
		$this->db->println("TRANS saveentity ends");
	}

	public function createAccessKey() {
		global $log;
		$log->info("Entering Into function createAccessKey()");
		$updateQuery = "update vtiger_users set accesskey=? where id=?";
		$insertResult = $this->db->pquery($updateQuery, array(vtws_generateRandomAccessKey(16), $this->id));
		$log->info("Exiting function createAccessKey()");
	}

	/** Function to insert values in the specifed table for the specified module
	 * @param $table_name -- table name:: Type varchar
	 * @param $module -- module:: Type varchar
	 */
	public function insertIntoEntityTable($table_name, $module, $fileid = '') {
		global $log;
		$log->info("function insertIntoEntityTable " . $module . ' vtiger_table name ' . $table_name);
		global $adb, $current_user;
		$insertion_mode = $this->mode;
		//Checkin whether an entry is already is present in the vtiger_table to update
		if ($insertion_mode == 'edit') {
			$check_query = "select * from " . $table_name . " where " . $this->tab_name_index[$table_name] . "=?";
			$check_result = $this->db->pquery($check_query, array($this->id));

			$num_rows = $this->db->num_rows($check_result);

			if ($num_rows <= 0) {
				$insertion_mode = '';
			}
		}

		// We will set the crypt_type based on the insertion_mode
		$crypt_type = '';

		if ($insertion_mode == 'edit') {
			$update = '';
			$update_params = array();
			$tabid = getTabid($module);
			$sql = "select * from vtiger_field where tabid=? and tablename=? and displaytype in (1,3) and vtiger_field.presence in (0,2)";
			$params = array($tabid, $table_name);
		} else {
			$column = $this->tab_name_index[$table_name];
			if ($column == 'id' && $table_name == 'vtiger_users') {
				$currentuser_id = $this->db->getUniqueID("vtiger_users");
				$this->id = $currentuser_id;
			}
			$qparams = array($this->id);
			$tabid = getTabid($module);
			$sql = "select * from vtiger_field where tabid=? and tablename=? and displaytype in (1,3,4) and vtiger_field.presence in (0,2)";
			$params = array($tabid, $table_name);

			$crypt_type = $this->DEFAULT_PASSWORD_CRYPT_TYPE;
		}

		$result = $this->db->pquery($sql, $params);
		$noofrows = $this->db->num_rows($result);
		for ($i = 0; $i < $noofrows; $i++) {
			$fieldname = $this->db->query_result($result, $i, "fieldname");
			$columname = $this->db->query_result($result, $i, "columnname");
			$uitype = $this->db->query_result($result, $i, "uitype");
			$typeofdata = $adb->query_result($result, $i, "typeofdata");

			$typeofdata_array = explode("~", $typeofdata);
			$datatype = $typeofdata_array[0];

			if (isset($this->column_fields[$fieldname])) {
				if ($uitype == 56) {
					if ($this->column_fields[$fieldname] === 'on' || $this->column_fields[$fieldname] == 1) {
						$fldvalue = 1;
					} else {
						$fldvalue = 0;
					}
				} elseif ($uitype == 15) {
					if ($this->column_fields[$fieldname] == $app_strings['LBL_NOT_ACCESSIBLE']) {
						//If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
						$sql = "select $columname from $table_name where " . $this->tab_name_index[$table_name] . "=?";
						$res = $adb->pquery($sql, array($this->id));
						$pick_val = $adb->query_result($res, 0, $columname);
						$fldvalue = $pick_val;
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
				} elseif ($uitype == 5 || $uitype == 6 || $uitype == 23) {
					if (isset($current_user->date_format)) {
						$fldvalue = getValidDBInsertDateValue($this->column_fields[$fieldname]);
					} else {
						$fldvalue = $this->column_fields[$fieldname];
					}
				} elseif ($uitype == 33) {
					if (is_array($this->column_fields[$fieldname])) {
						$field_list = implode(' |##| ', $this->column_fields[$fieldname]);
					} else {
						$field_list = $this->column_fields[$fieldname];
					}
					$fldvalue = $field_list;
				} elseif ($uitype == 99) {
					$fldvalue = $this->encrypt_password($this->column_fields[$fieldname], $crypt_type);
				} else {
					$fldvalue = $this->column_fields[$fieldname];
					$fldvalue = stripslashes($fldvalue);
				}
			} else {
				$fldvalue = '';
			}
			if ($uitype == 31) {
				$themeList = get_themes();
				if (!in_array($fldvalue, $themeList) || $fldvalue == '') {
					global $default_theme;
					if (!empty($default_theme) && in_array($default_theme, $themeList)) {
						$fldvalue = $default_theme;
					} else {
						$fldvalue = $themeList[0];
					}
				}
				if ($current_user->id == $this->id) {
					coreBOS_Session::set('vtiger_authenticated_user_theme', $fldvalue);
				}
			} elseif ($uitype == 32) {
				$languageList = Vtiger_Language::getAll();
				$languageList = array_keys($languageList);
				if (!in_array($fldvalue, $languageList) || $fldvalue == '') {
					global $default_language;
					if (!empty($default_language) && in_array($default_language, $languageList)) {
						$fldvalue = $default_language;
					} else {
						$fldvalue = $languageList[0];
					}
				}
				if ($current_user->id == $this->id) {
					coreBOS_Session::set('authenticated_user_language', $fldvalue);
				}
			}
			if ($fldvalue == '') {
				$fldvalue = $this->get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
				//$fldvalue =null;
			}
			if ($columname == 'is_admin' && !is_admin($current_user)) {// only admin users can change admin field
				if ($insertion_mode == 'edit') {// we force the same value that is currently set in database
					$rs = $adb->pquery('select is_admin from vtiger_users where id=?', array($this->id));
					$fldvalue = $adb->query_result($rs, 0, 0);
				}
			}
			if ($insertion_mode == 'edit') {
				if ($i == 0) {
					$update = $columname . "=?";
				} else {
					$update .= ', ' . $columname . "=?";
				}
				$update_params[] = $fldvalue;
			} else {
				$column .= ", " . $columname;
				$qparams[] = $fldvalue;
			}
		}

		if ($insertion_mode == 'edit') {
			//Check done by Don. If update is empty the the query fails
			if (trim($update) != '') {
				$sql1 = "update $table_name set $update where " . $this->tab_name_index[$table_name] . "=?";
				$update_params[] = $this->id;
				$this->db->pquery($sql1, $update_params);
			}
		} else {
			// Set the crypt_type being used, to override the DB default constraint as it is not in vtiger_field
			if ($table_name == 'vtiger_users' && strpos('crypt_type', $column) === false) {
				$column .= ', crypt_type';
				$qparams[] = $crypt_type;
			}
			if ($table_name == 'vtiger_users') {
				$sql1 = "insert into $table_name ($column, date_entered) values(" . generateQuestionMarks($qparams) . ',NOW())';
			} else {
				$sql1 = "insert into $table_name ($column) values(" . generateQuestionMarks($qparams) . ')';
			}
			$this->db->pquery($sql1, $qparams);
		}
	}

	/** Function to insert values into the attachment table
	 * @param $id -- entity id:: Type integer
	 * @param $module -- module:: Type varchar
	 */
	public function insertIntoAttachment($id, $module, $direct_import = false) {
		global $log;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		foreach ($_FILES as $fileindex => $files) {
			if ($files['name'] != '' && $files['size'] > 0) {
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex . '_hidden']);
				$this->uploadAndSaveFile($id, $module, $files);
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/** Function to retreive the user info of the specifed user id The user info will be available in $this->column_fields array
	 * @param $record -- record id:: Type integer
	 * @param $module -- module:: Type varchar
	 */
	public function retrieve_entity_info($record, $module, $deleted = false) {
		global $adb, $log;
		$log->debug("Entering into retrieve_entity_info($record, $module) method.");

		if ($record == '') {
			$log->debug("record is empty. returning null");
			return null;
		}

		$result = array();
		foreach ($this->tab_name_index as $table_name => $index) {
			$result[$table_name] = $adb->pquery("select * from " . $table_name . " where " . $index . "=?", array($record));
		}
		$tabid = getTabid($module);
		$sql1 = "select columnname, tablename, fieldname from vtiger_field where tabid=? and vtiger_field.presence in (0,2)";
		$result1 = $adb->pquery($sql1, array($tabid));
		$noofrows = $adb->num_rows($result1);
		for ($i = 0; $i < $noofrows; $i++) {
			$fieldcolname = $adb->query_result($result1, $i, "columnname");
			$tablename = $adb->query_result($result1, $i, "tablename");
			$fieldname = $adb->query_result($result1, $i, "fieldname");

			$fld_value = $adb->query_result($result[$tablename], 0, $fieldcolname);
			$this->column_fields[$fieldname] = $fld_value;
			$this->$fieldname = $fld_value;
		}
		$this->column_fields["record_id"] = $record;
		$this->column_fields["record_module"] = $module;

		$currency_query = "select * from vtiger_currency_info where id=? and currency_status='Active' and deleted=0";
		$currency_result = $adb->pquery($currency_query, array($this->column_fields["currency_id"]));
		if ($adb->num_rows($currency_result) == 0) {
			$currency_query = "select * from vtiger_currency_info where id =1";
			$currency_result = $adb->pquery($currency_query, array());
		}
		$currency_array = array("$" => "&#36;", "&euro;" => "&#8364;", "&pound;" => "&#163;", "&yen;" => "&#165;");
		if (isset($currency_array[$adb->query_result($currency_result, 0, "currency_symbol")])) {
			$ui_curr = $currency_array[$adb->query_result($currency_result, 0, "currency_symbol")];
		} else {
			$ui_curr = $adb->query_result($currency_result, 0, "currency_symbol");
		}
		$this->column_fields["currency_name"] = $this->currency_name = $adb->query_result($currency_result, 0, "currency_name");
		$this->column_fields["currency_code"] = $this->currency_code = $adb->query_result($currency_result, 0, "currency_code");
		$this->column_fields["currency_symbol"] = $this->currency_symbol = $ui_curr;
		$this->column_fields["conv_rate"] = $this->conv_rate = $adb->query_result($currency_result, 0, "conversion_rate");

		// TODO - This needs to be cleaned up once default values for fields are picked up in a cleaner way.
		// This is just a quick fix to ensure things doesn't start breaking when the user currency configuration is missing
		if ($this->column_fields['currency_grouping_pattern'] == '' && $this->column_fields['currency_symbol_placement'] == '') {
			$this->column_fields['currency_grouping_pattern'] = $this->currency_grouping_pattern = '123,456,789';
			$this->column_fields['currency_decimal_separator'] = $this->currency_decimal_separator = '.';
			$this->column_fields['currency_grouping_separator'] = $this->currency_grouping_separator = ',';
			$this->column_fields['currency_symbol_placement'] = $this->currency_symbol_placement = '$1.0';
		}

		$this->id = $record;
		$log->debug("Exit from retrieve_entity_info($record, $module) method.");

		return $this;
	}

	/** Function to upload the file to the server and add the file details in the attachments table
	 * @param $id -- user id:: Type varchar
	 * @param $module -- module name:: Type varchar
	 * @param $file_details -- file details array:: Type array
	 */
	public function uploadAndSaveFile($id, $module, $file_details, $attachmentname = '', $direct_import = false, $forfield = '') {
		global $log, $current_user, $upload_badext;

		$date_var = date('Y-m-d H:i:s');

		//to get the owner id
		$ownerid = (isset($this->column_fields['assigned_user_id']) ? $this->column_fields['assigned_user_id'] : $current_user->id);
		if (!isset($ownerid) || $ownerid == '') {
			$ownerid = $current_user->id;
		}

		$file = $file_details['name'];
		$binFile = sanitizeUploadFileName($file, $upload_badext);

		$filename = ltrim(basename(" " . $binFile));
		//allowed filename like UTF-8 characters
		$filetype = $file_details['type'];
		$filesize = $file_details['size'];
		$filetmp_name = $file_details['tmp_name'];

		if (validateImageFile($file_details) == 'true' && validateImageContents($filetmp_name) == false) {
			$log->debug('Skip the save attachment process.');
			return;
		}

		$current_id = $this->db->getUniqueID('vtiger_crmentity');

		//get the file path inwhich folder we want to upload the file
		$upload_file_path = decideFilePath();
		//upload the file in server
		$upload_status = move_uploaded_file($filetmp_name, $upload_file_path . $current_id . "_" . $binFile);

		if ($upload_status) {
			$sql1 = 'insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)';
			$params1 = array($current_id, $current_user->id, $ownerid, $module . ' Attachment', $this->column_fields['description'],
				$this->db->formatString('vtiger_crmentity', 'createdtime', $date_var), $this->db->formatDate($date_var, true));
			$this->db->pquery($sql1, $params1);

			$sql2 = 'insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?,?,?,?,?)';
			$params2 = array($current_id, $filename, $this->column_fields['description'], $filetype, $upload_file_path);
			$result = $this->db->pquery($sql2, $params2);

			if ($id != '') {
				$delquery = 'delete from vtiger_salesmanattachmentsrel where smid = ?';
				$this->db->pquery($delquery, array($id));
			}

			$sql3 = 'insert into vtiger_salesmanattachmentsrel values(?,?)';
			$this->db->pquery($sql3, array($id, $current_id));

			//we should update the imagename in the users table
			$this->db->pquery("update vtiger_users set imagename=? where id=?", array($filename, $id));
		}
		return;
	}

	/** Function to save the user information into the database
	 * @param $module -- module name:: Type varchar
	 */
	public function save($module_name, $fileid = '') {
		global $log, $adb, $current_user, $cbodUserLog;
		if (!is_admin($current_user) && $current_user->id != $this->id) {// only admin users can change other users profile
			return false;
		}
		//Check if status change to notify ODController
		if ($this->mode == 'edit') {
			$res_user = $adb->pquery('SELECT status FROM vtiger_users WHERE id=?', array($this->id));
			$status_prev = $adb->query_result($res_user, 0, 0);
		}
		$userrs = $adb->pquery('select roleid from vtiger_user2role where userid = ?', array($this->id));
		$oldrole = $adb->query_result($userrs, 0, 0);
		//Save entity being called with the modulename as parameter
		$this->saveentity($module_name);

		// Added for Reminder Popup support
		$query_prev_interval = $adb->pquery("SELECT reminder_interval from vtiger_users where id=?", array($this->id));
		$prev_reminder_interval = $adb->query_result($query_prev_interval, 0, 'reminder_interval');

		//$focus->imagename = $image_upload_array['imagename'];
		$this->saveHomeStuffOrder($this->id);
		SaveTagCloudView($this->id);

		// Added for Reminder Popup support
		$this->resetReminderInterval($prev_reminder_interval);
		//Creating the Privileges Flat File
		if (isset($this->column_fields['roleid'])) {
			updateUser2RoleMapping($this->column_fields['roleid'], $this->id);
		}
		require_once 'modules/Users/CreateUserPrivilegeFile.php';
		//createUserPrivilegesfile($this->id); // done in saveentity above
		if ($this->mode!='edit' || $oldrole != $this->column_fields['roleid']) {
			createUserSharingPrivilegesfile($this->id);
		}
		// ODController
		if ($cbodUserLog) {
			if ($this->mode == 'create') { // creating user, we send to ODController
				$cbmq = coreBOS_MQTM::getInstance();
				$msg = array(
					'date' => date('Y-m-d H:i:s'),
					'currentuser' => $current_user->id,
					'action' => 'create',
					'userstatus' => 'Active',
					'oduser' => $this->id,
				);
				$cbmq->sendMessage('coreBOSOnDemandChannel', 'Users', 'CentralSync', 'Data', '1:M', 0, 8640000, 0, 0, serialize($msg));
			} else {
				if ($status_prev != $this->column_fields['status']) {
					$cbmq = coreBOS_MQTM::getInstance();
					$msg = array(
						'date' => date('Y-m-d H:i:s'),
						'currentuser' => $current_user->id,
						'action' => 'edit',
						'userstatus' => $this->column_fields['status'],
						'oduser' => $this->id,
					);
					$cbmq->sendMessage('coreBOSOnDemandChannel', 'Users', 'CentralSync', 'Data', '1:M', 0, 8640000, 0, 0, serialize($msg));
				}
			}
		}
	}

	/**
	 * gives the order in which the modules have to be displayed in the home page for the specified user id
	 * @param $id -- user id:: Type integer
	 * @returns the customized home page order in $return_array
	 */
	public function getHomeStuffOrder($id) {
		global $adb;
		if (!is_array($this->homeorder_array)) {
			$this->homeorder_array = array(
				'HDB'=>'',
				'ALVT'=>'',
				'PLVT'=>'',
				'QLTQ'=>'',
				'CVLVT'=>'',
				'HLT'=>'',
				'GRT'=>'',
				'OLTSO'=>'',
				'ILTI'=>'',
				'MNL'=>'',
				'OLTPO'=>'',
				'LTFAQ'=>'',
				'UA'=>'',
				'PA'=>'',
			);
		}
		$return_array = array();
		$homeorder = array();
		if ($id != '') {
			$qry = 'select distinct(vtiger_homedefault.hometype)
				from vtiger_homedefault
				inner join vtiger_homestuff on vtiger_homestuff.stuffid=vtiger_homedefault.stuffid
				where vtiger_homestuff.visible=0 and vtiger_homestuff.userid=?';
			$res = $adb->pquery($qry, array($id));
			for ($q = 0; $q < $adb->num_rows($res); $q++) {
				$homeorder[] = $adb->query_result($res, $q, 'hometype');
			}
			foreach ($this->homeorder_array as $key => $value) {
				if (in_array($key, $homeorder)) {
					$return_array[$key] = $key;
				} else {
					$return_array[$key] = '';
				}
			}
		} else {
			foreach ($this->homeorder_array as $fieldname => $val) {
				if (isset($this->column_fields[$fieldname])) {
					$value = trim($this->column_fields[$fieldname]);
					$this->homeorder_array[$fieldname] = $value;
				}
			}
			foreach ($this->homeorder_array as $key => $value) {
				$return_array[$key] = $value;
			}
		}
		if ($id == '' && isset($this->column_fields['tagcloudview'])) {
			$return_array['Tag Cloud'] = $this->column_fields['tagcloudview'];
		} else {
			$return_array['Tag Cloud'] = getTagCloudView($id);
		}
		if ($id == '' && isset($this->column_fields['showtagas'])) {
			$return_array['showtagas'] = $this->column_fields['showtagas'];
		} else {
			$return_array['showtagas'] = getTagCloudShowAs($id);
		}
		return $return_array;
	}

	public function getDefaultHomeModuleVisibility($home_string, $inVal) {
		$homeModComptVisibility = 0;
		if ($inVal == 'postinstall') {
			if ($_REQUEST[$home_string] != '') {
				$homeModComptVisibility = 0;
			}
		}
		return $homeModComptVisibility;
	}

	public function insertUserdetails($inVal) {
		global $adb;
		$uid = $this->id;
		$s1 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('ALVT', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s1, 1, 'Default', $uid, $visibility, 'Top Accounts'));

		$s2 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('HDB', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s2, 2, 'Default', $uid, $visibility, 'Home Page Dashboard'));

		$s3 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('PLVT', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s3, 3, 'Default', $uid, $visibility, 'Top Potentials'));

		$s4 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('QLTQ', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s4, 4, 'Default', $uid, $visibility, 'Top Quotes'));

		$s5 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('CVLVT', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s5, 5, 'Default', $uid, $visibility, 'Key Metrics'));

		$s6 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('HLT', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s6, 6, 'Default', $uid, $visibility, 'Top Trouble Tickets'));

		$s7 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('UA', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s7, 7, 'Default', $uid, $visibility, 'Upcoming Activities'));

		$s8 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('GRT', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s8, 8, 'Default', $uid, $visibility, 'My Group Allocation'));

		$s9 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('OLTSO', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s9, 9, 'Default', $uid, $visibility, 'Top Sales Orders'));

		$s10 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('ILTI', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s10, 10, 'Default', $uid, $visibility, 'Top Invoices'));

		$s11 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('MNL', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s11, 11, 'Default', $uid, $visibility, 'My New Leads'));

		$s12 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('OLTPO', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s12, 12, 'Default', $uid, $visibility, 'Top Purchase Orders'));

		$s13 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('PA', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s13, 13, 'Default', $uid, $visibility, 'Pending Activities'));
		;

		$s14 = $adb->getUniqueID("vtiger_homestuff");
		$visibility = $this->getDefaultHomeModuleVisibility('LTFAQ', $inVal);
		$sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
		$res = $adb->pquery($sql, array($s14, 14, 'Default', $uid, $visibility, 'My Recent FAQs'));

		// Non-Default Home Page widget (no entry is requried in vtiger_homedefault below)
		$tc = $adb->getUniqueID("vtiger_homestuff");
		$visibility = 0;
		$sql = "insert into vtiger_homestuff values($tc, 15, 'Tag Cloud', $uid, $visibility, 'Tag Cloud')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s1 . ",'ALVT',5,'Accounts')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s2 . ",'HDB',5,'Dashboard')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s3 . ",'PLVT',5,'Potentials')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s4 . ",'QLTQ',5,'Quotes')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s5 . ",'CVLVT',5,'NULL')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s6 . ",'HLT',5,'HelpDesk')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s7 . ",'UA',5,'Calendar')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s8 . ",'GRT',5,'NULL')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s9 . ",'OLTSO',5,'SalesOrder')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s10 . ",'ILTI',5,'Invoice')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s11 . ",'MNL',5,'Leads')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s12 . ",'OLTPO',5,'PurchaseOrder')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s13 . ",'PA',5,'Calendar')";
		$adb->pquery($sql, array());

		$sql = "insert into vtiger_homedefault values(" . $s14 . ",'LTFAQ',5,'Faq')";
		$adb->pquery($sql, array());
	}

	/** function to save the order in which the modules have to be displayed in the home page for the specified user id
	 * @param $id -- user id:: Type integer
	 */
	public function saveHomeStuffOrder($id) {
		global $log, $adb;
		$log->debug("Entering in function saveHomeOrder($id)");

		if ($this->mode == 'edit') {
			$qry = 'update vtiger_homestuff,vtiger_homedefault
				set vtiger_homestuff.visible=?
				where vtiger_homestuff.stuffid=vtiger_homedefault.stuffid and vtiger_homestuff.userid=? and vtiger_homedefault.hometype=?';
			foreach ($this->homeorder_array as $key => $value) {
				if ($_REQUEST[$key] != '') {
					$save_array[] = $key;
					$visible = 0; //To show the default Homestuff on the the Home Page
				} else {
					$visible = 1; //To hide the default Homestuff on the the Home Page
				}
				$result = $adb->pquery($qry, array($visible, $id, $key));
			}
			if ($save_array != "") {
				$homeorder = implode(',', $save_array);
			}
		} else {
			$this->insertUserdetails('postinstall');
		}
		$log->debug("Exiting from function saveHomeOrder($id)");
	}

	/**
	 * Function to get the column value of a field
	 * @param $column_name -- Column name
	 * @param $input_value -- Input value for the column taken from the User
	 * @return Column value of the field.
	 */
	public function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype = '') {
		if (is_uitype($uitype, "_date_") && $fldvalue == '') {
			return null;
		}
		if ($datatype == 'I' || $datatype == 'N' || $datatype == 'NN') {
			return 0;
		}
		return $fldvalue;
	}

	/**
	 * Function to reset the Reminder Interval setup and update the time for next reminder interval
	 * @param $prev_reminder_interval -- Last Reminder Interval on which the reminder popup's were triggered.
	 */
	public function resetReminderInterval($prev_reminder_interval) {
		global $adb;
		if ($prev_reminder_interval != $this->column_fields['reminder_interval']) {
			coreBOS_Session::delete('next_reminder_interval');
			coreBOS_Session::delete('next_reminder_time');
			$set_reminder_next = date('Y-m-d H:i');
			$adb->pquery("UPDATE vtiger_users SET reminder_next_time=? WHERE id=?", array($set_reminder_next, $this->id));
		}
	}

	public function initSortByField($module) {
		// Right now, we do not have any fields to be handled for Sorting in Users module. This is just a place holder as it is called from Popup.php
	}

	public function filterInactiveFields($module) {
		// TODO Nothing do right now
	}

	public function deleteImage() {
		$sql1 = 'SELECT attachmentsid FROM vtiger_salesmanattachmentsrel WHERE smid = ?';
		$res1 = $this->db->pquery($sql1, array($this->id));
		if ($this->db->num_rows($res1) > 0) {
			$attachmentId = $this->db->query_result($res1, 0, 'attachmentsid');

			$sql2 = "DELETE FROM vtiger_crmentity WHERE crmid=? AND setype='Users Attachments'";
			$this->db->pquery($sql2, array($attachmentId));

			$sql3 = 'DELETE FROM vtiger_salesmanattachmentsrel WHERE smid=? AND attachmentsid=?';
			$this->db->pquery($sql3, array($this->id, $attachmentId));

			$sql2 = "UPDATE vtiger_users SET imagename='' WHERE id=?";
			$this->db->pquery($sql2, array($this->id));

			$sql4 = 'DELETE FROM vtiger_attachments WHERE attachmentsid=?';
			$this->db->pquery($sql4, array($attachmentId));
		}
	}

	/** Function to delete an entity with given Id */
	public function trash($module, $id) {
		global $log, $current_user, $cbodUserLog;
		$this->mark_deleted($id);
		// ODController delete user
		if ($cbodUserLog) {
			global $adb;
			$uinf = $adb->pquery('select user_name, last_name from vtiger_users where id=?', array($id));
			$cbmq = coreBOS_MQTM::getInstance();
			$msg = array(
				'date' => date('Y-m-d H:i:s'),
				'currentuser' => $current_user->id,
				'action' => 'trash',
				'userstatus' => 'Inactive',
				'username' => $adb->query_result($uinf, 0, 'user_name'),
				'lastname' => $adb->query_result($uinf, 0, 'last_name'),
				'oduser' => $id,
			);
			$cbmq->sendMessage('coreBOSOnDemandChannel', 'Users', 'CentralSync', 'Data', '1:M', 0, 8640000, 0, 0, serialize($msg));
		}
	}

	public function transformOwnerShipAndDelete($userId, $transformToUserId) {
		global $current_user, $cbodUserLog;
		$adb = PearDatabase::getInstance();

		$em = new VTEventsManager($adb);

		// Initialize Event trigger cache
		$em->initTriggerCache();

		$entityData = VTEntityData::fromUserId($adb, $userId);

		//set transform user id
		$entityData->set('transformtouserid', $transformToUserId);

		$em->triggerEvent("vtiger.entity.beforedelete", $entityData);

		vtws_transferOwnership($userId, $transformToUserId);
		// ODController delete user
		if ($cbodUserLog) {
			$uinf = $adb->pquery('select user_name, last_name from vtiger_users where id=?', array($userId));
			$cbmq = coreBOS_MQTM::getInstance();
			$msg = array(
				'date' => date('Y-m-d H:i:s'),
				'currentuser' => $current_user->id,
				'action' => 'transfer',
				'userstatus' => 'Inactive',
				'username' => $adb->query_result($uinf, 0, 'user_name'),
				'lastname' => $adb->query_result($uinf, 0, 'last_name'),
				'oduser' => $userId,
			);
			$cbmq->sendMessage('coreBOSOnDemandChannel', 'Users', 'CentralSync', 'Data', '1:M', 0, 8640000, 0, 0, serialize($msg));
		}

		//delete from user vtiger_table;
		$sql = "delete from vtiger_users where id=?";
		$adb->pquery($sql, array($userId));
		//Delete user extension in asterisk.
		$sql = "delete from vtiger_asteriskextensions where userid=?";
		$adb->pquery($sql, array($userId));
	}

	/**
	 * This function should be overridden in each module. It marks an item as deleted.
	 * @param <type> $id
	 */
	public function mark_deleted($id) {
		global $log, $current_user, $adb;
		$date_var = date('Y-m-d H:i:s');
		$query = "UPDATE vtiger_users set status=?,date_modified=?,modified_user_id=? where id=?";
		$adb->pquery($query, array('Inactive', $adb->formatDate($date_var, true), $current_user->id, $id), true, "Error marking record deleted: ");
	}

	/**
	 * Function to get the user id of the active admin user.
	 * @return Integer - Active Admin User ID
	 */
	public static function getActiveAdminId() {
		global $adb;
		$sql = "SELECT id FROM vtiger_users WHERE is_admin='On' and status='Active' limit 1";
		$result = $adb->pquery($sql, array());
		if ($result && $adb->num_rows($result) == 1) {
			$adminId = $adb->query_result($result, 0, 'id');
		} else {
			$adminId = 1;
		}
		return $adminId;
	}

	/**
	 * Function to get the active admin user object
	 * @return Users - Active Admin User Instance
	 */
	public static function getActiveAdminUser() {
		$adminId = self::getActiveAdminId();
		$user = new Users();
		$user->retrieveCurrentUserInfoFromFile($adminId);
		return $user;
	}

	public function loggedIn() {
		return (coreBOS_Settings::getSetting('cbodUserConnection'.$this->id, -1)!=-1);
	}

	public function canUnblock() {
		global $cbodTimeToSessionLogout;
		$now = time();
		$ltime = coreBOS_Settings::getSetting('cbodLastLoginTime'.$this->id, $now);
		$time_noact = ($now-$ltime)/60;
		return $cbodTimeToSessionLogout < $time_noact;
	}

	/** Function to export the Users records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Users Query.
	*/
	public function create_export_query($where = '') {
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		$query = '';

		if (is_admin($current_user)) {
			include "include/utils/ExportUtils.php";

			//To get the Permitted fields query and the permitted fields list
			$sql = getPermittedFieldsQuery('Users', 'detail_view');
			$fields_list = getFieldsListFromQuery($sql);

			$query = "SELECT $fields_list
				FROM vtiger_crmentity
				INNER JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.crmid
				INNER JOIN vtiger_user2role ON vtiger_user2role.userid = vtiger_users.id
				INNER JOIN vtiger_asteriskextensions ON vtiger_asteriskextensions.userid = vtiger_users.id";

			$query .= $this->getNonAdminAccessControlQuery('Users', $current_user);
			$where_auto = ' vtiger_crmentity.deleted = 0 ';

			if ($where != '') {
				$query .= " WHERE ($where) AND ".$where_auto;
			} else {
				$query .= ' WHERE '.$where_auto;
			}

			$log->debug('Exiting create_export_query method ...');
		}
		return $query;
	}
}
?>