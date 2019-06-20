<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
	# Incoming Mail Server Config Properties
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
	
	# Outgoing Mail Server Config Properties
	public $og_mail_server_active;
	public $og_mail_server_username;
    public $og_mail_server_password;
    public $og_mail_server_smtp_auth;
    public $og_mail_server_from_email;
    public $og_mail_server;
    public $og_mail_server_port;
    public $og_mail_server_type;
	public $og_mail_server_path;

	# Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	public function __construct() {
		$this->initGlobalScope();
	}
	
	public function initGlobalScope() {
		global $adb, $current_user;

		$result = $adb->pquery("SELECT * FROM vtiger_mail_accounts WHERE user_id=? AND status=1 AND set_default=0", array($current_user->id));
		if ($adb->num_rows($result)) {
			# for Incoming Mail Server
			$this->ic_mail_server_name = trim($adb->query_result($result, 0, 'mail_servername'));
			$this->ic_mail_server_username = trim($adb->query_result($result, 0, 'mail_username'));
			$this->ic_mail_server_password = trim($adb->query_result($result, 0, 'mail_password'));
			$this->ic_mail_server_protocol = trim($adb->query_result($result, 0, 'mail_protocol'));
			$this->ic_mail_server_ssltype = trim($adb->query_result($result, 0, 'ssltype'));
			$this->ic_mail_server_sslmeth = trim($adb->query_result($result, 0, 'sslmeth'));
			$this->mId = trim($adb->query_result($result, 0, 'account_id'));
			$this->ic_mail_server_box_refresh = trim($adb->query_result($result, 0, 'box_refresh'));
			$this->mServerName = self::setServerName($instance->ic_mail_server_name);

			# for Outgoing Mail Server
			$this->$og_mail_server_active = trim($adb->query_result($result, 0, ''));
			$this->$og_mail_server_username = trim($adb->query_result($result, 0, 'server_username'));
			$this->$og_mail_server_password = trim($adb->query_result($result, 0, 'server_password'));
			$this->$og_mail_server_smtp_auth = trim($adb->query_result($result, 0, 'smtp_auth'));
			$this->$og_mail_server_from_email = trim($adb->query_result($result, 0, 'from_email_field'));
			$this->$og_mail_server = trim($adb->query_result($result, 0, 'server'));
			$this->$og_mail_server_port = trim($adb->query_result($result, 0, 'server_port'));
			$this->$og_mail_server_type = trim($adb->query_result($result, 0, 'server_type'));
			$this->$og_mail_server_path = trim($adb->query_result($result, 0, 'server_path'));
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
			# Added code to send a test mail to the currently logged in user
			require_once 'modules/Emails/mail.php';
			$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
			
			$to_email = getUserEmailId('id', $current_user->id);
			$from_email = $to_email;
			$subject = 'Test mail about the mail server configuration.';
			$description = 'Dear '.$current_user->user_name.', <br><br><b> This is a test mail sent to confirm if a mail is actually being sent through the smtp server that you'
				.' have configured. </b><br>Feel free to delete this mail.<br><br>Thanks and Regards,<br> '.$HELPDESK_SUPPORT_NAME.' <br>';

			if ($to_email != '') {
				$mail_status = send_mail('Users', $to_email, $current_user->user_name, $from_email, $subject, $description);
				$mail_status_str = $to_email.'='.$mail_status.'&&&';
			} else {
				$mail_status_str = "'".$to_email."'=0&&&";
			}
			$error_str = getMailErrorString($mail_status_str);
			$action = 'EmailConfig';
			if ($mail_status != 1) {
				$action = 'EmailConfig&emailconfig_mode=edit&server_name='.
					urlencode(vtlib_purify($_REQUEST['server'])).'&server_user='.
					urlencode(vtlib_purify($_REQUEST['server_username'])).'&auth_check='.
					urlencode(vtlib_purify($_REQUEST['smtp_auth']));
			} else {
				global $current_user;
				$userid = $current_user->id;
				$idrs = $adb->pquery('select * from vtiger_mail_account where user_id = ?', array($userid));
				if ($idrs && $adb->num_rows($idrs)>0) {
					$id = $adb->query_result($idrs, 0, 'id');
					$sql ='update vtiger_mail_account set server=?, server_username=?, server_password=?, smtp_auth=?, server_type=?, server_port=?,from_email_field=? where id=?';
					$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port,$from_email_field,$id);
				} else {
					$sql = 'insert into vtiger_mail_account (server, server_username, server_password, smtp_auth, server_type, server_port,from_email_field) values(?,?,?,?,?,?,?,?)';
					$params = array($server, $port, $server_username, $server_password, $server_type, $smtp_auth, '',$from_email_field);
				}
				var_dump($sql);
				$adb->pquery($sql, $params);
			}
	}
	
	/**
	 * Function to Save Incoming Mail Server Configuration
	 */
	public function saveIncomingMailServerConfiguration(
		$isIncomingMailServerActive, 
		$displayname, $account_name, 
		$mailprotocol, $server_username, 
		$server_password, 
		$mail_servername, 
		$box_refresh, 
		$mails_per_page, 
		$ssltype, 
		$sslmeth) {
			global $adb, $log;
			if ($mails_per_page == '') {
				$mails_per_page='0';
			}
			
			if (isset($_REQUEST['record']) && $_REQUEST['record']!='') {
				$id = $_REQUEST['record'];
			}

			require_once 'include/database/PearDatabase.php';
			require_once 'modules/Users/Users.php';
			
			$focus = new Users();
			$encrypted_password=$focus->changepassword($_REQUEST['server_password']);
			if (isset($_REQUEST['edit']) && $_REQUEST['edit'] && $_REQUEST['record']!='') {
				$sql='update vtiger_mail_accounts set display_name = ?, mail_id = ?, account_name = ?, mail_protocol = ?, mail_username = ?';
				$params = array($displayname, $email, $account_name, $mailprotocol, $server_username);
				if ($server_password != '*****') {
					$sql.=', mail_password=?';
					$params[] = $encrypted_password;
				}
				$sql.=', mail_servername=?,  box_refresh=?,  mails_per_page=?, ssltype=? , sslmeth=?, int_mailer=? where user_id = ?';
				array_push($params, $mail_servername, $box_refresh, $mails_per_page, $ssltype, $sslmeth, $_REQUEST['int_mailer'], $id);
			} else {
				$account_id = $adb->getUniqueID('vtiger_mail_accounts');
				$sql='insert into vtiger_mail_accounts
					(account_id, user_id, display_name, mail_id, account_name, mail_protocol, mail_username, mail_password, mail_servername,
					box_refresh, mails_per_page, ssltype, sslmeth, int_mailer, status, set_default)
					values
					(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
				$params = array(
					$account_id, $current_user->id, $displayname, $email, $account_name, $mailprotocol, $server_username, $encrypted_password, $mail_servername,
					$box_refresh, $mails_per_page, $ssltype, $sslmeth, $_REQUEST['int_mailer'],'1','0'
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
	public function getIncomingMailServerActiveStatus()
	{
		return $this->ic_mail_server_active;
	}

    /**
     * Get the value of ic_mail_server_displayname
     */ 
    public function getIncomingMailServerDisplayName()
    {
        return $this->ic_mail_server_displayname;
    }

    /**
     * Get the value of ic_mail_server_email
     */ 
    public function getIncomingMailServerEmail()
    {
        return $this->ic_mail_server_email;
    }

    /**
     * Get the value of ic_mail_server_account_name
     */ 
    public function getIncomingMailServerAccountName()
    {
        return $this->ic_mail_server_account_name;
    }

    /**
     * Get the value of ic_mail_server_protocol
     */ 
    public function getIncomingMailServerProtocol()
    {
        return $this->ic_mail_server_protocol;
    }

    /**
     * Get the value of ic_mail_server_username
     */ 
    public function getIncomingMailServerUsername()
    {
        return $this->ic_mail_server_username;
    }

    /**
     * Get the value of ic_mail_server_password
     */ 
    public function getIncomingMailServerPassword()
    {
        return $this->ic_mail_server_password;
    }

    /**
     * Get the value of ic_mail_server_name
     */ 
    public function getIncomingMailServerName()
    {
        return $this->ic_mail_server_name;
    }

    /**
     * Get the value of ic_mail_server_box_refresh
     */ 
    public function getIncomingMailServerRefreshTime()
    {
        return $this->ic_mail_server_box_refresh;
    }

    /**
     * Get the value of ic_mail_server_mails_per_page
     */ 
    public function getIncomingMailServerMailsPerPage()
    {
        return $this->ic_mail_server_mails_per_page;
    }

    /**
     * Get the value of ic_mail_server_ssltype
     */ 
    public function getIncomingMailServerSSLTYPE()
    {
        return $this->ic_mail_server_ssltype;
    }

	/**
	 * Get the value of ic_mail_server_sslmeth
	 */ 
	public function getIncomingMailServerSSLMETH()
	{
		return $this->ic_mail_server_sslmeth;
	}

	/**
	 * Get the value of og_mail_server_active
	 */ 
	public function getOutgoingMailServerActiveStatus()
	{
		return $this->og_mail_server_active;
	}

	/**
	 * Get the value of og_mail_server_username
	 */ 
	public function getOutgoingMailServerUsername()
	{
		return $this->og_mail_server_username;
	}

    /**
     * Get the value of og_mail_server_password
     */ 
    public function getOutgoingMailServerPassword()
    {
        return $this->og_mail_server_password;
    }

    /**
     * Get the value of og_mail_server_smtp_auth
     */ 
    public function getOutgoingMailServerSMTPAuthetication()
    {
        return $this->og_mail_server_smtp_auth;
    }

    /**
     * Get the value of og_mail_server_from_email
     */ 
    public function getOutgoingMailsServerFromEmail()
    {
        return $this->og_mail_server_from_email;
    }

    /**
     * Get the value of og_mail_server
     */ 
    public function getOutgoingMailServer()
    {
        return $this->og_mail_server;
    }

    /**
     * Get the value of og_mail_server_port
     */ 
    public function getOutgoingMailServerPort()
    {
        return $this->og_mail_server_port;
    }

    /**
     * Get the value of og_mail_server_type
     */ 
    public function getOutgoingMailServerType()
    {
        return $this->og_mail_server_type;
    }

	/**
	 * Get the value of og_mail_server_path
	 */ 
	public function getOutgoingMailServerPath()
	{
		return $this->og_mail_server_path;
	}
}
?>