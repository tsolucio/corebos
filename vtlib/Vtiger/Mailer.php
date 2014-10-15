<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Emails/PHPMailerAutoload.php';
include_once('include/utils/CommonUtils.php');
include_once('config.inc.php');
include_once('include/database/PearDatabase.php');
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/Event.php');

/**
 * Provides API to work with PHPMailer & Email Templates
 * @package vtlib
 */
class Vtiger_Mailer extends PHPMailer {

	var $_serverConfigured = false;

	/**
	 * Constructor
	 */	
	function __construct() {
		$this->initialize();
	}

	/**
	 * Get the unique id for insertion
	 * @access private
	 */
	function __getUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_mailer_queue');
	}

	/**
	 * Initialize this instance
	 * @access private
	 */
	function initialize() {
		$this->IsSMTP();

		global $adb;
		$result = $adb->pquery("SELECT * FROM vtiger_systems WHERE server_type=?", Array('email'));
		if($adb->num_rows($result)) {
			$this->Host = $adb->query_result($result, 0, 'server');
			$this->Username = $adb->query_result($result, 0, 'server_username');
			$this->Password = $adb->query_result($result, 0, 'server_password');
			$this->SMTPAuth = $adb->query_result($result, 0, 'smtp_auth');
			if(empty($this->SMTPAuth)) $this->SMTPAuth = false;

			$this->ConfigSenderInfo($adb->query_result($result, 0, 'from_email_field'));

			$this->_serverConfigured = true;
			$this->Sender= getReturnPath($this->Host, $this->From);
		}
	}

	/**
	 * Reinitialize this instance for use
	 * @access private
	 */
	function reinitialize() {
		$this->to = Array();
		$this->cc = Array();
		$this->bcc = Array();
		$this->ReplyTo = Array();
		$this->Body = '';
		$this->Subject ='';
		$this->attachment = Array();
	}

	/**
	 * Initialize this instance using mail template
	 * @access private
	 */
	function initFromTemplate($emailtemplate) {
		global $adb;
		$result = $adb->pquery("SELECT * from vtiger_emailtemplates WHERE templatename=? AND foldername=?", 
			Array($emailtemplate, 'Public'));
		if($adb->num_rows($result)) {
			$this->IsHTML(true);
			$usesubject = $adb->query_result($result, 0, 'subject');
			$usebody = decode_html($adb->query_result($result, 0, 'body'));

			$this->Subject = $usesubject;
			$this->Body    = $usebody;
			return true;			
		}
		return false;		
	}
	/**
	*Adding signature to mail
	*/
	function addSignature($userId) {
		global $adb;
		$sign = nl2br($adb->query_result($adb->pquery("select signature from vtiger_users where id=?", array($userId)),0,"signature"));
		$this->Signature = $sign;
	}


	/**
	 * Configure sender information
	 */
	function ConfigSenderInfo($fromemail, $fromname='', $replyto='') {
		if(empty($fromname)) $fromname = $fromemail;

		$this->From = $fromemail;
		$this->FromName = $fromname;
		$this->AddReplyTo($replyto);
	}

	/**
	 * Overriding default send
	 */
	function Send($sync=false, $linktoid=false) {
		if(!$this->_serverConfigured) return;

		if($sync) return parent::Send();

		$this->__AddToQueue($linktoid);
		return true;
	}

	/**
	 * Send mail using the email template
	 * @param String Recipient email
	 * @param String Recipient name
	 * @param String vtiger CRM Email template name to use
	 */
	function SendTo($toemail, $toname='', $emailtemplate=false, $linktoid=false, $sync=false) {
		if(empty($toname)) $toname = $toemail;
		$this->AddAddress($toemail, $toname);
		if($emailtemplate) $this->initFromTemplate($emailtemplate);
		return $this->Send($sync, $linktoid);
	}

	/** Mail Queue **/
	// Check if this instance is initialized.
	var $_queueinitialized = false;
	function __initializeQueue() {
		if(!$this->_queueinitialized) {
			if(!Vtiger_Utils::CheckTable('vtiger_mailer_queue')) {
				Vtiger_Utils::CreateTable('vtiger_mailer_queue',
					'(id INT NOT NULL PRIMARY KEY,
					fromname VARCHAR(100), fromemail VARCHAR(100),
					mailer VARCHAR(10), content_type VARCHAR(15), subject VARCHAR(999), body TEXT, relcrmid INT, 
					failed INT(1) NOT NULL DEFAULT 0, failreason VARCHAR(255))',
					true);
			}
			if(!Vtiger_Utils::CheckTable('vtiger_mailer_queueinfo')) {
				Vtiger_Utils::CreateTable('vtiger_mailer_queueinfo',
					'(id INTEGER, name VARCHAR(100), email VARCHAR(100), type VARCHAR(7))',
					true);
			}
			if(!Vtiger_Utils::CheckTable('vtiger_mailer_queueattachments')) {
				Vtiger_Utils::CreateTable('vtiger_mailer_queueattachments',
					'(id INTEGER, path TEXT, name VARCHAR(100), encoding VARCHAR(50), type VARCHAR(100))',
					true);
			}
			$this->_queueinitialized = true;
		}
		return true;
	}

	/**
	 * Add this mail to queue
	 */
	function __AddToQueue($linktoid) {
		if($this->__initializeQueue()) {
			global $adb;
			$uniqueid = self::__getUniqueId();
			$adb->pquery('INSERT INTO vtiger_mailer_queue(id,fromname,fromemail,content_type,subject,body,mailer,relcrmid) VALUES(?,?,?,?,?,?,?,?)',
				Array($uniqueid, $this->FromName, $this->From, $this->ContentType, $this->Subject, $this->Body, $this->Mailer, $linktoid));
			$queueid = $adb->database->Insert_ID();
			foreach($this->to as $toinfo) {
				if(empty($toinfo[0])) continue;
				$adb->pquery('INSERT INTO vtiger_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $toinfo[1], $toinfo[0], 'TO'));
			}
			foreach($this->cc as $ccinfo) {
				if(empty($ccinfo[0])) continue;
				$adb->pquery('INSERT INTO vtiger_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $ccinfo[1], $ccinfo[0], 'CC'));
			}
			foreach($this->bcc as $bccinfo) {
				if(empty($bccinfo[0])) continue;
				$adb->pquery('INSERT INTO vtiger_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $bccinfo[1], $bccinfo[0], 'BCC'));
			}
			foreach($this->ReplyTo as $rtoinfo) {
				if(empty($rtoinfo[0])) continue;
				$adb->pquery('INSERT INTO vtiger_mailer_queueinfo(id, name, email, type) VALUES(?,?,?,?)',
					Array($queueid, $rtoinfo[1], $rtoinfo[0], 'RPLYTO'));
			}
			foreach($this->attachment as $attachmentinfo) {
				if(empty($attachmentinfo[0])) continue;
				$adb->pquery('INSERT INTO vtiger_mailer_queueattachments(id, path, name, encoding, type) VALUES(?,?,?,?,?)',
					Array($queueid, $attachmentinfo[0], $attachmentinfo[2], $attachmentinfo[3], $attachmentinfo[4]));
			}
		}
	}

	/**
	 * Dispatch (send) email that was queued.
	 */
	static function dispatchQueue(Vtiger_Mailer_Listener $listener=null) {
		global $adb;
		if(!Vtiger_Utils::CheckTable('vtiger_mailer_queue')) return;

		$mailer = new self();
		$queue = $adb->pquery('SELECT * FROM vtiger_mailer_queue WHERE failed != ?', array(1));
		if($adb->num_rows($queue)) {
			for($index = 0; $index < $adb->num_rows($queue); ++$index) {
				$mailer->reinitialize();

				$queue_record = $adb->fetch_array($queue, $index);
				$queueid = $queue_record['id'];
				$relcrmid= $queue_record['relcrmid'];

				$mailer->From = $queue_record['fromemail'];
				$mailer->From = $queue_record['fromname'];
				$mailer->Subject=$queue_record['subject'];
				$mailer->Body = decode_html($queue_record['body']);
				$mailer->Mailer=$queue_record['mailer'];
				$mailer->ContentType = $queue_record['content_type'];

				$emails = $adb->pquery('SELECT * FROM vtiger_mailer_queueinfo WHERE id=?', Array($queueid));
				for($eidx = 0; $eidx < $adb->num_rows($emails); ++$eidx) {
					$email_record = $adb->fetch_array($emails, $eidx);
					if($email_record[type] == 'TO')     $mailer->AddAddress($email_record[email], $email_record[name]);
					else if($email_record[type] == 'CC')$mailer->AddCC($email_record[email], $email_record[name]);
					else if($email_record[type] == 'BCC')$mailer->AddBCC($email_record[email], $email_record[name]);
					else if($email_record[type] == 'RPLYTO')$mailer->AddReplyTo($email_record[email], $email_record[name]);
				}
				
				$attachments = $adb->pquery('SELECT * FROM vtiger_mailer_queueattachments WHERE id=?', Array($queueid));
				for($aidx = 0; $aidx < $adb->num_rows($attachments); ++$aidx) {
					$attachment_record = $adb->fetch_array($attachments, $aidx);
					if($attachment_record['path'] != '') {
						$mailer->AddAttachment($attachment_record['path'], $attachment_record['name'],
												$attachment_record['encoding'], $attachment_record['type']);
					}
				}
				$sent = $mailer->Send(true);
				if($sent) {
					Vtiger_Event::trigger('vtiger.mailer.mailsent', $relcrmid);
					if($listener) {
						$listener->mailsent($queueid);
					}
					$adb->pquery('DELETE FROM vtiger_mailer_queue WHERE id=?', Array($queueid));
					$adb->pquery('DELETE FROM vtiger_mailer_queueinfo WHERE id=?', Array($queueid));
					$adb->pquery('DELETE FROM vtiger_mailer_queueattachments WHERE id=?', Array($queueid));
				} else {
					if($listener) {
						$listener->mailerror($queueid);
					}
					$adb->pquery('UPDATE vtiger_mailer_queue SET failed=?, failreason=? WHERE id=?', Array(1, $mailer->ErrorInfo, $queueid));
				}
			}
		}
	}
}

/**
 * Provides API to act on the different events triggered by send email action.
 * @package vtlib
 */
abstract class Vtiger_Mailer_Listener {
	function mailsent($queueid) { }
	function mailerror($queueid) { }
}

?>
