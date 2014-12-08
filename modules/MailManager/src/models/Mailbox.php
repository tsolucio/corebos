<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class MailManager_Model_Mailbox {
	
	protected $mServer;
	protected $mUsername;
	protected $mPassword;
	protected $mProtocol = 'IMAP2';
	protected $mSSLType  = 'tls';
	protected $mCertValidate = 'novalidate-cert';
	protected $mRefreshTimeOut;
	protected $mId;
    protected $mServerName;
	
	function exists() {
		return !empty($this->mId);
	}
	
	function decrypt($value) {
		require_once('include/utils/encryption.php');
		$e = new Encryption();
		return $e->decrypt($value);
	}
	
	function encrypt($value) {
		require_once('include/utils/encryption.php');
		$e = new Encryption();
		return $e->encrypt($value);
	}
	
	function server() {
		return $this->mServer;
	}

	function setServer($server) {
		$this->mServer = trim($server);
	}

    function serverName() {
        return $this->mServerName;
    }

	function username() {
		return $this->mUsername;
	}
	
	function setUsername($username) {
		$this->mUsername = trim($username);
	}
	
	function password($decrypt=true) {
		if ($decrypt) return $this->decrypt($this->mPassword);
		return $this->mPassword;
	}
	
	function setPassword($password) {
		$this->mPassword = $this->encrypt(trim($password));
	}
	
	function protocol() {
		return $this->mProtocol;
	}
	
	function setProtocol($protocol) {
		$this->mProtocol = trim($protocol);
	}
	
	function ssltype() {
		if (strcasecmp($this->mSSLType, 'ssl') === 0) {
			return $this->mSSLType;
		}
		return $this->mSSLType;
	}
	
	function setSSLType($ssltype) {
		$this->mSSLType = trim($ssltype);
	}
	
	function certvalidate() {
		return $this->mCertValidate;
	}
	
	function setCertValidate($certvalidate) {
		$this->mCertValidate = trim($certvalidate);
	}

	function setRefreshTimeOut($value) {
		$this->mRefreshTimeOut = $value;
	}
	
	function refreshTimeOut() {
		return $this->mRefreshTimeOut;
	}

	function delete() {
		global $adb, $current_user;
		$adb->pquery("DELETE FROM vtiger_mail_accounts WHERE user_id = ? AND account_id = ?", array($current_user->id, $this->mId));
	}
	
	function save() {	
		global $adb, $current_user, $list_max_entries_per_page;
		
		$account_id = 1;
		$maxresult = $adb->pquery("SELECT max(account_id) as max_account_id FROM vtiger_mail_accounts", array());
		if ($adb->num_rows($maxresult)) $account_id += intval($adb->query_result($maxresult, 0, 'max_account_id'));
		
		$isUpdate = !empty($this->mId);
		
		$sql = "";
		$parameters = array($this->username(), $this->server(), $this->username(), $this->password(false), $this->protocol(), $this->ssltype(), $this->certvalidate(), $this->refreshTimeOut(), $current_user->id);
		
		if ($isUpdate) {
			$sql = "UPDATE vtiger_mail_accounts SET display_name=?, mail_servername=?, mail_username=?, mail_password=?, mail_protocol=?, ssltype=?, sslmeth=?, box_refresh=? WHERE user_id=? AND account_id=?";
			$parameters[] = $this->mId;
		} else {
			$sql = "INSERT INTO vtiger_mail_accounts(display_name, mail_servername, mail_username, mail_password, mail_protocol, ssltype, sslmeth, box_refresh, user_id, mails_per_page, account_name, status, set_default, account_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$parameters[] = $list_max_entries_per_page; // Number of emails per page
			$parameters[] = $this->username();
			$parameters[] = 1; // Status
			$parameters[] = '0'; // Set Default
			$parameters[] = $account_id;
		}
		$adb->pquery($sql, $parameters);
		if (!$isUpdate) {
			$this->mId = $account_id;
		}
	}
	
	
	static function activeInstance() {
		global $adb, $current_user;
		$instance = new MailManager_Model_Mailbox();
		
		$result = $adb->pquery("SELECT * FROM vtiger_mail_accounts WHERE user_id=? AND status=1 AND set_default=0", array($current_user->id));
		if ($adb->num_rows($result)) {
			$instance->mServer = trim($adb->query_result($result, 0, 'mail_servername'));
			$instance->mUsername = trim($adb->query_result($result, 0, 'mail_username'));
			$instance->mPassword = trim($adb->query_result($result, 0, 'mail_password'));
			$instance->mProtocol = trim($adb->query_result($result, 0, 'mail_protocol'));
			$instance->mSSLType = trim($adb->query_result($result, 0, 'ssltype'));
			$instance->mCertValidate = trim($adb->query_result($result, 0, 'sslmeth'));
			$instance->mId = trim($adb->query_result($result, 0, 'account_id'));
			$instance->mRefreshTimeOut = trim($adb->query_result($result, 0, 'box_refresh'));
            $instance->mServerName = self::setServerName($instance->mServer);
		}
		return $instance;
	}

    static function setServerName($mServer) {
        if($mServer == 'imap.gmail.com') {
            $mServerName = 'gmail';
        } else if($mServer == 'imap.mail.yahoo.com') {
            $mServerName = 'yahoo';
        } else if($mServer == 'mail.messagingengine.com') {
            $mServerName = 'fastmail';
        } else {
            $mServerName = 'other';
        }
        return $mServerName;
    }
	
}

?>