<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once dirname(__FILE__) . '/../models/Folder.php';
include_once dirname(__FILE__) . '/../models/Message.php';

class MailManager_Connector {

	/*
	 * Cache interval time
	 */
	static $DB_CACHE_CLEAR_INTERVAL = "-1 day"; // strtotime

	/*
	 * Mail Box URL
	 */
	protected $mBoxUrl;

	/*
	 * Mail Box connection instance
	 */
	protected $mBox;

	/*
	 * Last imap error
	 */
	protected $mError;

	/*
	 * Mail Box folders
	 */
	protected $mFolders = false;

	/**
	 * Modified Time of the mail
	 */
	protected $mModified = false;

	/*
	 * Base URL of the Mail Box excluding folder name
	 */
	protected $mBoxBaseUrl;

	
	/**
	 * Connects to the Imap server with the given parameters
	 * @param $model MailManager_Model_Mailbox Instance
	 * $param $folder String optional - mail box folder name
	 * @returns MailManager_Connector Object
	 */
	static function connectorWithModel($model, $folder='') {
		
		$port = 143; // IMAP
		if (strcasecmp($model->protocol(), 'pop') === 0) $port = 110; // NOT IMPLEMENTED
		else if (strcasecmp($model->ssltype(), 'ssl') === 0) $port = 993; // IMAP SSL

		$url = sprintf('{%s:%s/%s/%s/%s}%s', $model->server(), $port, $model->protocol(), 
			$model->ssltype(), $model->certvalidate(), $folder);
		$baseUrl = sprintf('{%s:%s/%s/%s/%s}', $model->server(), $port, $model->protocol(),
			$model->ssltype(), $model->certvalidate());
		return new self($url, $model->username(), $model->password(), $baseUrl);
	}

	
	/**
	 * Opens up imap connection to the specified url
	 * @param $url String - mail server url
	 * @param $username String  - user name of the mail box
	 * @param $password String  - pass word of the mail box
	 * @param $baseUrl Optional - url of the mailserver excluding folder name.
	 *	This is used to fetch the folders of the mail box
	 */
	function __construct($url, $username, $password, $baseUrl=false) {
		$boxUrl = $this->convertCharacterEncoding(html_entity_decode($url),'UTF7-IMAP','UTF-8'); //handle both utf8 characters and html entities
		$this->mBoxUrl = $boxUrl;
		$this->mBoxBaseUrl = $baseUrl; // Used for folder List
		$this->mBox = @imap_open($url, $username, $password);
		$this->isError();
	}


	/**
	 * Closes the connection
	 */
	function __destruct() {
		$this->close();
	}


	/**
	 * Closes the imap connection
	 */
	function close() {
		if (!empty($this->mBox)) {
			
			if ($this->mModified) imap_close($this->mBox, CL_EXPUNGE);
			else imap_close($this->mBox);
			
			$this->mBox = null;
		}
	}


	/**
	 * Checks for the connection
	 */
	function isConnected() {
		return !empty($this->mBox);
	}


	/**
	 * Returns the last imap error 
	 */
	function isError() {
		$this->mError = imap_last_error();
		return $this->hasError();
	}


	/**
	 * Checks if the error exists
	 */
	function hasError() {
		return !empty($this->mError);
	}


	/**
	 * Returns the error
	 */
	function lastError() {
		return $this->mError;
	}


	/**
	 * Reads mail box folders
	 * @param string $ref Optional - 
	 */
	function folders($ref="{folder}") {
		if ($this->mFolders) return $this->mFolders;
		
		$result = imap_getmailboxes($this->mBox, $ref, "*");
		if ($this->isError()) return false;
		
		$folders = array();
		foreach($result as $row) {
			$folderName = str_replace($ref, "", $row->name);
			$folder = $this->convertCharacterEncoding( $folderName, "ISO_8859-1", "UTF7-IMAP" ); //Decode folder name
			$folders[] = $this->folderInstance($folder);
		}
		$this->mFolders = $folders;
		return $folders;
	}


	/**
	 * Used to update the folders optionus
	 * @param imap_stats flag $options
	 */
	function updateFolders($options=SA_UNSEEN) {
		$this->folders(); // Initializes the folder Instance 
		foreach($this->mFolders as $folder) {
			$this->updateFolder($folder, $options);
		}
	}


	/**
	 * Updates the mail box's folder
	 * @param MailManager_Model_Folder $folder - folder instance
	 * @param $options imap_status flags like SA_UNSEEN, SA_MESSAGES etc
	 */
	function updateFolder($folder, $options) {
		$mailbox = $this->convertCharacterEncoding($folder->name($this->mBoxUrl), "UTF7-IMAP","ISO_8859-1"); //Encode folder name
		$result = @imap_status($this->mBox, $mailbox, $options);
		if ($result) {
			if (isset($result->unseen)) $folder->setUnreadCount($result->unseen);
			if (isset($result->messages)) $folder->setCount($result->messages);
		}
	}


	/**
	 * Returns MailManager_Model_Folder Instance
	 * @param String $name - folder name
	 */
	function folderInstance($name) {
		return new MailManager_Model_Folder($name);
	}


	/**
	 * Sets a list of mails with paging
	 * @param String $folder - MailManager_Model_Folder Instance
	 * @param Integer $start  - Page number
	 * @param Integer $maxLimit - Number of mails
	 */
	function folderMails($folder, $start, $maxLimit) {
		$folderCheck = @imap_check($this->mBox);
		if ($folderCheck->Nmsgs) {
			
			$reverse_start = $folderCheck->Nmsgs - ($start*$maxLimit);
			$reverse_end = $reverse_start - $maxLimit + 1;
			
			if ($reverse_start < 1) $reverse_start = 1;
			if ($reverse_end < 1) $reverse_end = 1;
		
			$sequence = sprintf("%s:%s", $reverse_start, $reverse_end);
			
			$records = imap_fetch_overview($this->mBox, $sequence);
			$mails = array();
			foreach($records as $result) {
				array_unshift($mails, MailManager_Model_Message::parseOverview($result));
			}		
			$folder->setMails($mails);
			$folder->setPaging($reverse_end, $reverse_start, $maxLimit, $folderCheck->Nmsgs, $start);
		}
	}


	/**
	 * Return the cache interval
	 */
	function clearDBCacheInterval() {
		// TODO Provide configuration option.
		if (self::$DB_CACHE_CLEAR_INTERVAL) {
			return strtotime(self::$DB_CACHE_CLEAR_INTERVAL);
		}
		return false;
	}


	/**
	 * Clears the cache data
	 */
	function clearDBCache() {
		// Trigger purne any older mail saved in DB first
		$interval = $this->clearDBCacheInterval();
		
		$timenow = strtotime("now");
		
		// Optimization to avoid trigger for ever mail open (with interval specified)
		$lastClearTimeFromSession = false;
		if ($interval && isset($_SESSION) && isset($_SESSION['mailmanager_clearDBCacheIntervalLast'])) {
			$lastClearTimeFromSession = intval($_SESSION['mailmanager_clearDBCacheIntervalLast']);
			if (($timenow - $lastClearTimeFromSession) < ($timenow - $interval)) {
				$interval = false;
			}
		}
		if ($interval) {
			MailManager_Model_Message::pruneOlderInDB($interval);
			$_SESSION['mailmanager_clearDBCacheIntervalLast'] = $timenow;
		}
	}


	/**
	 * Function which deletes the mails
	 * @param String $msgno - List of message number seperated by commas.
	 */
	function deleteMail($msgno){
		$msgno = trim($msgno,',');
		$msgno = explode(',',$msgno);
		for($i = 0;$i<count($msgno);$i++){
			@imap_delete($this->mBox, $msgno[$i]);
		}	
	}


	/**
	 * Function which moves mail to another folder
	 * @param String $msgno - List of message number separated by commas
	 * @param String $folderName - folder name
	 */
	function moveMail($msgno, $folderName){
		$msgno = trim($msgno,',');
		$msgno = explode(',',$msgno);
		$folder = $this->convertCharacterEncoding(html_entity_decode($folderName),'UTF7-IMAP','UTF-8'); //handle both utf8 characters and html entities
		for($i = 0;$i<count($msgno);$i++){
			@imap_mail_move($this->mBox, $msgno[$i], $folder);
		}
		@imap_expunge($this->mBox);
	}


	/**
	 * Creates an instance of Message
	 * @param String $msgno - Message number
	 * @return MailManager_Model_Message
	 */
	function openMail($msgno) {
		$this->clearDBCache();
		return new MailManager_Model_Message($this->mBox, $msgno, true);
	}


	/**
	 * Marks the mail as Unread
	 * @param <String> $msgno - Message Number
	 */
	function markMailUnread($msgno) {
		imap_clearflag_full( $this->mBox, $msgno, '\\Seen');
		$this->mModified = true;
	}


	/**
	 * Marks the mail as Read
	 * @param String $msgno - Message Number
	 */
	function markMailRead($msgno) {
		imap_setflag_full($this->mBox, $msgno, '\\Seen');
		$this->mModified = true;
	}


	/**
	 * Searches the Mail Box with the query
	 * @param String $query - imap search format
	 * @param MailManager_Model_Folder $folder - folder instance
	 * @param Integer $start - Page number
	 * @param Integer $maxLimit - Number of mails
	 */
	function searchMails($query, $folder, $start, $maxLimit) {
		$nos = imap_search($this->mBox, $query);

		if (!empty($nos)) {
			$nmsgs = count($nos);
			
			$reverse_start = $nmsgs - ($start*$maxLimit);
			$reverse_end   = $reverse_start - $maxLimit;
			
			if ($reverse_start < 1) $reverse_start = 1;
			if ($reverse_end < 1) $reverse_end = 0;

			if($nmsgs > 1)
				$nos = array_slice($nos, $reverse_end, ($reverse_start-$reverse_end));
			
			// Reverse order the messages
			rsort($nos, SORT_NUMERIC);
			
			$mails = array();
			$records = imap_fetch_overview($this->mBox, implode(',', $nos));
			foreach($records as $result) {
				array_unshift($mails, MailManager_Model_Message::parseOverview($result));
			}
			$folder->setMails($mails);
			$folder->setPaging($reverse_end, $reverse_start, $maxLimit, $nmsgs, $start);  //-1 as it starts from 0
		}
	}

	
	/**
	 * Returns list of Folder for the Mail Box
	 * @return Array folder list
	 */
	function getFolderList() {
		if(!empty($this->mBoxBaseUrl)) {
			$list = @imap_list($this->mBox, $this->mBoxBaseUrl, '*');
			if (is_array($list)) {
				foreach ($list as $val) {
					$folder = $this->convertCharacterEncoding( $val, 'ISO_8859-1', 'UTF7-IMAP' ); //Decode folder name
					$folderList[] =  preg_replace("/{(.*?)}/", "", $folder);
				}
			}
		}
		return $folderList;
	}

	 function convertCharacterEncoding($value, $toCharset, $fromCharset) {
		if (function_exists('mb_convert_encoding')) {
			$value = mb_convert_encoding($value, $toCharset, $fromCharset);
		} else {
			$value = iconv($toCharset, $fromCharset, $value);
		}
		return $value;
	}
}
?>