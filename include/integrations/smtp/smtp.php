<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module    : SMTP Custom Configuration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

class corebos_smtp {
	// Incoming Mail Server Config Properties
	public $ic_mail_server_active;
	public $ic_mail_server_displayname;
	public $ic_mail_server_email;
	public $ic_mail_server_account_name;
	public $ic_mail_server_protocol;
	public $ic_mail_server_username;
	public $ic_mail_server_password;
	public $ic_mail_server_name;
	public $ic_mail_server_box_refresh;
	public $ic_mail_server_mails_per_page;
	public $ic_mail_server_ssltype;
	public $ic_mail_server_sslmeth;
	public $ic_mail_server_validation_status;

	// Outgoing Mail Server Config Properties
	public $og_mail_server_active;
	public $og_mail_server_username;
	public $og_mail_server_password;
	public $og_mail_server_smtp_auth;
	public $og_mail_server_from_email;
	public $og_mail_server_name;
	public $og_mail_server_port;
	public $og_mail_server_type;
	public $og_mail_server_path;
	public $og_mail_server_validation_status;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		global $adb, $current_user;

		$result = $adb->pquery("SELECT * FROM vtiger_mail_accounts WHERE user_id=?", array($current_user->id));
		if ($adb->num_rows($result)) {
			// for Incoming Mail Server
			$this->ic_mail_server_name = trim($adb->query_result($result, 0, 'mail_servername'));
			$this->ic_mail_server_displayname = trim($adb->query_result($result, 0, 'display_name'));
			$this->ic_mail_server_username = trim($adb->query_result($result, 0, 'mail_username'));
			$this->ic_mail_server_password = trim($adb->query_result($result, 0, 'mail_password'));
			$this->ic_mail_server_protocol = trim($adb->query_result($result, 0, 'mail_protocol'));
			$this->ic_mail_server_ssltype = trim($adb->query_result($result, 0, 'ssltype'));
			$this->ic_mail_server_sslmeth = trim($adb->query_result($result, 0, 'sslmeth'));
			$this->ic_mail_server_active = trim($adb->query_result($result, 0, 'status'));
			$this->ic_mail_server_box_refresh = trim($adb->query_result($result, 0, 'box_refresh'));
			$this->ic_mail_server_validation_status = trim($adb->query_result($result, 0, 'ic_server_validation_status'));

			// for Outgoing Mail Server
			$this->og_mail_server_active = trim($adb->query_result($result, 0, 'og_server_status'));
			$this->og_mail_server_username = trim($adb->query_result($result, 0, 'og_server_username'));
			$this->og_mail_server_password = trim($adb->query_result($result, 0, 'og_server_password'));
			$this->og_mail_server_smtp_auth = trim($adb->query_result($result, 0, 'og_smtp_auth'));
			$this->og_mail_server_from_email = trim($adb->query_result($result, 0, 'og_from_email_field'));
			$this->og_mail_server_name = trim($adb->query_result($result, 0, 'og_server_name'));
			$this->og_mail_server_port = trim($adb->query_result($result, 0, 'og_server_port'));
			$this->og_mail_server_type = trim($adb->query_result($result, 0, 'og_server_type'));
			$this->og_mail_server_path = trim($adb->query_result($result, 0, 'og_server_path'));
			$this->og_mail_server_validation_status = trim($adb->query_result($result, 0, 'og_server_validation_status'));
		}
	}

	/**
	 *
	 * Function to Save OutGoing Mail Server Configuration
	 */
	public function saveOutGoingMailServerConfiguration(
		$isOutgoingMailServerActive,
		$server_username,
		$server_password,
		$smtp_auth,
		$from_email_field,
		$server,
		$port,
		$server_type,
		$og_mail_server_path
	) {
			global $adb, $log, $current_user;
			require_once 'modules/Emails/mail.php';
		if (!empty($from_email_field)) {
			$from_email = $from_email_field;
		} else {
			$from_email = getUserEmailId('id', $current_user->id);
		}

			$userid = $current_user->id;
			$idrs = $adb->pquery('select * from vtiger_mail_accounts where user_id = ?', array($userid));
		if ($idrs && $adb->num_rows($idrs)>0) {
			$id = $adb->query_result($idrs, 0, 'id');
			$sql ='update vtiger_mail_accounts set og_server_name=?, og_server_username=?, og_server_password=?, og_smtp_auth=?, og_server_type=?, 
						og_server_port=?, og_from_email_field=?, og_server_status=? where id=?';
			$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, $from_email, $isOutgoingMailServerActive, $id);
		} else {
			$sql = 'insert into vtiger_mail_accounts (og_server_name, og_server_username, og_server_password, og_smtp_auth, og_server_type, og_server_port, 
			og_from_email_field, og_server_status) values(?,?,?,?,?,?,?,?)';
			$params = array($server, $port, $server_username, $server_password, $server_type, $smtp_auth, '',$from_email, $isOutgoingMailServerActive);
		}
			$adb->pquery($sql, $params);
	}

	/**
	 * Function to Save Incoming Mail Server Configuration
	 */
	public function saveSMTPServerConfiguration(
		$isIncomingMailServerActive,
		$displayname,
		$mailprotocol,
		$server_username,
		$server_password,
		$mail_servername,
		$box_refresh,
		$mails_per_page,
		$ssltype,
		$sslmeth,
		$isOutgoingMailServerActive,
		$og_server_username,
		$og_server_password,
		$og_smtp_auth,
		$og_from_email_field,
		$og_server_name,
		$og_server_port,
		$og_server_type,
		$og_mail_server_path,
		$og_validation_status,
		$ic_validation_status
	) {
			global $adb, $log, $current_user, $site_URL;
		if ($mails_per_page == '') {
			$mails_per_page = '0';
		}
			require_once 'include/database/PearDatabase.php';
			require_once 'modules/Users/Users.php';
			$focus = new Users();
			$ic_server_encrypted_password=$focus->changepassword($server_password);
			$og_server_encrypted_password=$focus->changepassword($og_server_password);

			$result = $adb->pquery('select * from vtiger_mail_accounts where user_id = ?', array($current_user->id));
		if ($adb->num_rows($result) > 0) {
			$sql='update vtiger_mail_accounts set display_name =?, mail_id =?, mail_protocol =?, mail_username =?, mail_password =?, 
				mail_servername = ?, box_refresh=?, mails_per_page=?, ssltype=? , sslmeth=?, status =?, og_server_name=?, og_server_username=?,
				og_server_password=?, og_smtp_auth=?, og_server_type=?, og_server_port=?, og_from_email_field=?, og_server_status=?, 
				og_server_validation_status=?, ic_server_validation_status=? where user_id ='.$current_user->id;
			$params = array(
				$displayname,
				$server_username,
				$mailprotocol,
				$server_username,
				$ic_server_encrypted_password,
				$mail_servername,
				$box_refresh,
				$mails_per_page,
				$ssltype,
				$sslmeth,
				$isIncomingMailServerActive,
				$og_server_name,
				$og_server_username,
				$og_server_encrypted_password,
				$og_smtp_auth,
				$og_server_type,
				$og_server_port,
				$og_from_email_field,
				$isOutgoingMailServerActive,
				$og_validation_status,
				$ic_validation_status
			);
		} else {
			$account_id = $adb->getUniqueID('vtiger_mail_accounts');
			$sql='insert into vtiger_mail_accounts(account_id, user_id, display_name, mail_id, mail_protocol, mail_username, mail_password, mail_servername,
					box_refresh, mails_per_page, ssltype, sslmeth, int_mailer, status, set_default, og_server_name, og_server_username, og_server_password, og_smtp_auth,
					og_server_type, og_server_port, og_from_email_field, og_server_status,og_server_validation_status, 
					ic_server_validation_status)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
			$params = array(
				$account_id,
				$current_user->id,
				$displayname,
				$server_username,
				$mailprotocol,
				$server_username,
				$ic_server_encrypted_password,
				$mail_servername,
				$box_refresh,
				$mails_per_page,
				$ssltype,
				$sslmeth,
				$_REQUEST['int_mailer'],
				$isIncomingMailServerActive,
				'0',
				$og_server_name,
				$og_server_username,
				$og_server_encrypted_password,
				$og_smtp_auth,
				$og_server_type,
				$og_server_port,
				$og_from_email_field,
				$isOutgoingMailServerActive,
				$og_validation_status,
				$ic_validation_status
			);
		}
		$adb->pquery($sql, $params);
	}

	public static function setServerName($mServer) {
		if ($mServer == 'imap.gmail.com') {
			$mServerName = 'gmail';
		} elseif ($mServer == 'imap.mail.yahoo.com') {
			$mServerName = 'yahoo';
		} elseif ($mServer == 'mail.messagingengine.com') {
			$mServerName = 'fastmail';
		} else {
			$mServerName = 'other';
		}
		return $mServerName;
	}

	/**
	 * Get the value of ic_mail_server_active
	 */
	public function getIncomingMailServerActiveStatus() {
		return $this->ic_mail_server_active;
	}

	/**
	 * Get the value of ic_mail_server_displayname
	 */
	public function getIncomingMailServerDisplayName() {
		return $this->ic_mail_server_displayname;
	}

	/**
	 * Get the value of ic_mail_server_email
	 */
	public function getIncomingMailServerEmail() {
		return $this->ic_mail_server_email;
	}

	/**
	 * Get the value of ic_mail_server_account_name
	 */
	public function getIncomingMailServerAccountName() {
		return $this->ic_mail_server_account_name;
	}

	/**
	 * Get the value of ic_mail_server_protocol
	 */
	public function getIncomingMailServerProtocol() {
		return $this->ic_mail_server_protocol;
	}

	/**
	 * Get the value of ic_mail_server_username
	 */
	public function getIncomingMailServerUsername() {
		return $this->ic_mail_server_username;
	}

	/**
	 * Get the value of ic_mail_server_password
	 */
	public function getIncomingMailServerPassword() {
		return $this->ic_mail_server_password;
	}

	/**
	 * Get the value of ic_mail_server_name
	 */
	public function getIncomingMailServerName() {
		return $this->ic_mail_server_name;
	}

	/**
	 * Get the value of ic_mail_server_box_refresh
	 */
	public function getIncomingMailServerRefreshTime() {
		return $this->ic_mail_server_box_refresh;
	}

	/**
	 * Get the value of ic_mail_server_mails_per_page
	 */
	public function getIncomingMailServerMailsPerPage() {
		return $this->ic_mail_server_mails_per_page;
	}

	/**
	 * Get the value of ic_mail_server_ssltype
	 */
	public function getIncomingMailServerSSLTYPE() {
		return $this->ic_mail_server_ssltype;
	}

	/**
	 * Get the value of ic_mail_server_sslmeth
	 */
	public function getIncomingMailServerSSLMETH() {
		return $this->ic_mail_server_sslmeth;
	}

	/**
	 * Get the value of og_mail_server_active
	 */
	public function getOutgoingMailServerActiveStatus() {
		return $this->og_mail_server_active;
	}

	/**
	 * Get the value of og_mail_server_username
	 */
	public function getOutgoingMailServerUsername() {
		return $this->og_mail_server_username;
	}

	/**
	 * Get the value of og_mail_server_password
	 */
	public function getOutgoingMailServerPassword() {
		return $this->og_mail_server_password;
	}

	/**
	 * Get the value of og_mail_server_smtp_auth
	 */
	public function getOutgoingMailServerSMTPAuthetication() {
		return $this->og_mail_server_smtp_auth;
	}

	/**
	 * Get the value of og_mail_server_from_email
	 */
	public function getOutgoingMailsServerFromEmail() {
		return $this->og_mail_server_from_email;
	}

	/**
	 * Get the value of og_mail_server
	 */
	public function getOutgoingMailServerName() {
		return $this->og_mail_server_name;
	}

	/**
	 * Get the value of og_mail_server_port
	 */
	public function getOutgoingMailServerPort() {
		return $this->og_mail_server_port;
	}

	/**
	 * Get the value of og_mail_server_type
	 */
	public function getOutgoingMailServerType() {
		return $this->og_mail_server_type;
	}

	/**
	 * Get the value of og_mail_server_path
	 */
	public function getOutgoingMailServerPath() {
		return $this->og_mail_server_path;
	}

	/**
	 * Get the value of og_mail_server_validation_status
	 */
	public function getOutgoingMailServerValidationStatus() {
		return $this->og_mail_server_validation_status;
	}

	/**
	 * Get the value of ic_mail_server_validation_status
	 */
	public function getIncomingMailServerValidationStatus() {
		return $this->ic_mail_server_validation_status;
	}
}
?>