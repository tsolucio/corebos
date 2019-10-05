<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailScannerRule.php';

/**
 * Mail Scanner information manager.
 */
class Vtiger_MailScannerInfo {
	// id of this scanner record
	public $scannerid = false;
	// name of this scanner
	public $scannername=false;
	// mail server to connect to
	public $server    = false;
	// mail protocol to use
	public $protocol  = false;
	// username to use
	public $username  = false;
	// password to use
	public $password  = false;
	// notls/tls/ssl
	public $ssltype   = false;
	// validate-certificate or novalidate-certificate
	public $sslmethod = false;
	// last successful connection url to use
	public $connecturl= false;
	// search for type
	public $searchfor = false;
	// post scan mark record as
	public $markas = false;

	// is the scannered enabled?
	public $isvalid   = false;

	// Last scan on the folders.
	public $lastscan  = false;

	// Need rescan on the folders?
	public $rescan    = false;

	// Rules associated with this mail scanner
	public $rules = false;

	/**
	 * Constructor
	 */
	public function __construct($scannername, $initialize = true) {
		if ($initialize && $scannername) {
			$this->initialize($scannername);
		}
	}

	/**
	 * Encrypt/Decrypt input.
	 * @access private
	 */
	public function __crypt($password, $encrypt = true) {
		require_once 'include/utils/encryption.php';
		$cryptobj = new Encryption();
		if ($encrypt) {
			return $cryptobj->encrypt(trim($password));
		} else {
			return $cryptobj->decrypt(trim($password));
		}
	}

	/**
	 * Initialize this instance.
	 */
	public function initialize($scannername) {
		global $adb;
		$result = $adb->pquery('SELECT * FROM vtiger_mailscanner WHERE scannername=?', array($scannername));

		if ($adb->num_rows($result)) {
			$this->scannerid  = $adb->query_result($result, 0, 'scannerid');
			$this->scannername= $adb->query_result($result, 0, 'scannername');
			$this->server     = $adb->query_result($result, 0, 'server');
			$this->protocol   = $adb->query_result($result, 0, 'protocol');
			$this->username   = $adb->query_result($result, 0, 'username');
			$this->password   = $adb->query_result($result, 0, 'password');
			$this->password   = $this->__crypt($this->password, false);
			$this->ssltype    = $adb->query_result($result, 0, 'ssltype');
			$this->sslmethod  = $adb->query_result($result, 0, 'sslmethod');
			$this->connecturl = $adb->query_result($result, 0, 'connecturl');
			$this->searchfor  = $adb->query_result($result, 0, 'searchfor');
			$this->markas     = $adb->query_result($result, 0, 'markas');
			$this->isvalid    = $adb->query_result($result, 0, 'isvalid');

			$this->initializeFolderInfo();
			$this->initializeRules();
		}
	}

	/**
	 * Initialize the folder details
	 */
	public function initializeFolderInfo() {
		global $adb;
		if ($this->scannerid) {
			$this->lastscan = array();
			$this->rescan   = array();
			$lastscanres = $adb->pquery('SELECT * FROM vtiger_mailscanner_folders WHERE scannerid=?', array($this->scannerid));
			$lastscancount = $adb->num_rows($lastscanres);
			if ($lastscancount) {
				for ($lsindex = 0; $lsindex < $lastscancount; ++$lsindex) {
					$folder = $adb->query_result($lastscanres, $lsindex, 'foldername');
					$scannedon =$adb->query_result($lastscanres, $lsindex, 'lastscan');
					$nextrescan =$adb->query_result($lastscanres, $lsindex, 'rescan');
					$this->lastscan[$folder] = $scannedon;
					$this->rescan[$folder]   = $nextrescan != 0;
				}
			}
		}
	}

	/**
	 * Delete lastscan details with this scanner
	 */
	public function clearLastscan() {
		global $adb;
		$adb->pquery('DELETE FROM vtiger_mailscanner_folders WHERE scannerid=?', array($this->scannerid));
		$this->lastscan = false;
	}

	/**
	 * Update rescan flag on all folders
	 */
	public function updateAllFolderRescan($rescanFlag = false) {
		global $adb;
		$adb->pquery(
			'UPDATE vtiger_mailscanner_folders set rescan=? WHERE scannerid=?',
			array($rescanFlag, $this->scannerid)
		);
		if ($this->rescan) {
			foreach ($this->rescan as $folderName => $oldRescanFlag) {
				$this->rescan[$folderName] = $rescanFlag;
			}
		}
	}

	/**
	 * Update lastscan information on folder (or set for rescan next)
	 */
	public function updateLastscan($folderName, $rescanFolder = false) {
		global $adb;

		$scannedOn = date('d-M-Y');

		$needRescan = $rescanFolder? 1 : 0;

		$folderInfo = $adb->pquery(
			'SELECT folderid FROM vtiger_mailscanner_folders WHERE scannerid=? AND foldername=?',
			array($this->scannerid, $folderName)
		);
		if ($adb->num_rows($folderInfo)) {
			$folderid = $adb->query_result($folderInfo, 0, 'folderid');
			$adb->pquery(
				'UPDATE vtiger_mailscanner_folders SET lastscan=?, rescan=? WHERE folderid=?',
				array($scannedOn, $needRescan, $folderid)
			);
		} else {
			$enabledForScan = 1; // Enable folder for scan by default
			$adb->pquery(
				'INSERT INTO vtiger_mailscanner_folders(scannerid, foldername, lastscan, rescan, enabled) VALUES(?,?,?,?,?)',
				array($this->scannerid, $folderName, $scannedOn, $needRescan, $enabledForScan)
			);
		}
		if (!$this->lastscan) {
			$this->lastscan = array();
		}
		$this->lastscan[$folderName] = $scannedOn;

		if (!$this->rescan) {
			$this->rescan = array();
		}
		$this->rescan[$folderName] = $needRescan;
	}

	/**
	 * Get lastscan of the folder.
	 */
	public function getLastscan($folderName) {
		if ($this->lastscan) {
			return $this->lastscan[$folderName];
		} else {
			return false;
		}
	}

	/**
	 * Does the folder need message rescan?
	 */
	public function needRescan($folderName) {
		if ($this->rescan && isset($this->rescan[$folderName])) {
			return $this->rescan[$folderName];
		}
		// TODO Pick details of rescan flag of folder from database?
		return false;
	}

	/**
	 * Check if rescan is required atleast on a folder?
	 */
	public function checkRescan() {
		$rescanRequired = false;
		if ($this->rescan) {
			foreach ($this->rescan as $folderName => $rescan) {
				if ($rescan) {
					$rescanRequired = $folderName;
					break;
				}
			}
		}
		return $rescanRequired;
	}

	/**
	 * Get the folder information that has been scanned
	 */
	public function getFolderInfo() {
		$folderinfo = false;
		if ($this->scannerid) {
			global $adb;
			$fldres = $adb->pquery('SELECT * FROM vtiger_mailscanner_folders WHERE scannerid=?', array($this->scannerid));
			$fldcount = $adb->num_rows($fldres);
			if ($fldcount) {
				$folderinfo = array();
				for ($index = 0; $index < $fldcount; ++$index) {
					$foldername = $adb->query_result($fldres, $index, 'foldername');
					$folderid   = $adb->query_result($fldres, $index, 'folderid');
					$lastscan   = $adb->query_result($fldres, $index, 'lastscan');
					$rescan     = $adb->query_result($fldres, $index, 'rescan');
					$enabled    = $adb->query_result($fldres, $index, 'enabled');
					$folderinfo[$foldername] = array ('folderid'=>$folderid, 'lastscan'=>$lastscan, 'rescan'=> $rescan, 'enabled'=>$enabled);
				}
			}
		}
		return $folderinfo;
	}

	/**
	 * Update the folder information with given folder names
	 */
	public function updateFolderInfo($foldernames, $rescanFolder = false) {
		if ($this->scannerid && !empty($foldernames)) {
			global $adb;
			$qmarks = array();
			foreach ($foldernames as $foldername) {
				$qmarks[] = '?';
				$this->updateLastscan($foldername, $rescanFolder);
			}
			// Delete the folder that is no longer present
			$adb->pquery(
				'DELETE FROM vtiger_mailscanner_folders WHERE scannerid=? AND foldername NOT IN ('. implode(',', $qmarks) . ')',
				array($this->scannerid, $foldernames)
			);
		}
	}

	/**
	 * Enable only given folders for scanning
	 */
	public function enableFoldersForScan($folderinfo) {
		if ($this->scannerid) {
			global $adb;
			$adb->pquery('UPDATE vtiger_mailscanner_folders set enabled=0 WHERE scannerid=?', array($this->scannerid));
			foreach ($folderinfo as $foldervalue) {
				$folderid = $foldervalue['folderid'];
				$enabled = $foldervalue['enabled'];
				$adb->pquery(
					'UPDATE vtiger_mailscanner_folders set enabled=? WHERE folderid=? AND scannerid=?',
					array($enabled,$folderid,$this->scannerid)
				);
			}
		}
	}

	/**
	 * Initialize scanner rule information
	 */
	public function initializeRules() {
		global $adb;
		if ($this->scannerid) {
			$this->rules = array();
			$rulesres = $adb->pquery('SELECT * FROM vtiger_mailscanner_rules WHERE scannerid=? ORDER BY sequence', array($this->scannerid));
			$rulescount = $adb->num_rows($rulesres);
			if ($rulescount) {
				for ($index = 0; $index < $rulescount; ++$index) {
					$ruleid = $adb->query_result($rulesres, $index, 'ruleid');
					$scannerrule = new Vtiger_MailScannerRule($ruleid);
					//$scannerrule->debug = $this->debug;
					$this->rules[] = $scannerrule;
				}
			}
		}
	}

	/**
	 * Get scanner information as map
	 */
	public function getAsMap() {
		$infomap = array();
		$keys = array('scannerid','scannername','server','protocol','username','password','ssltype','sslmethod','connecturl','searchfor','markas','isvalid','rules');
		foreach ($keys as $key) {
			$infomap[$key] = $this->$key;
		}
		$infomap['requireRescan'] = $this->checkRescan();
		return $infomap;
	}

	/**
	 * Compare this instance with give instance
	 */
	public function compare($otherInstance) {
		$checkkeys = array('server', 'scannername', 'protocol', 'username', 'password', 'ssltype', 'sslmethod', 'searchfor', 'markas');
		foreach ($checkkeys as $key) {
			if ($this->$key != $otherInstance->$key) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Create/Update the scanner information in database
	 */
	public function update($otherInstance) {
		$mailServerChanged = false;

		// Is there is change in server setup?
		if ($this->server != $otherInstance->server || $this->username != $otherInstance->username) {
			$mailServerChanged = true;
			$this->clearLastscan();
			// TODO How to handle lastscan info if server settings switches back in future?
		}

		$this->server    = $otherInstance->server;
		$this->scannername= $otherInstance->scannername;
		$this->protocol  = $otherInstance->protocol;
		$this->username  = $otherInstance->username;
		$this->password  = $otherInstance->password;
		$this->ssltype   = $otherInstance->ssltype;
		$this->sslmethod = $otherInstance->sslmethod;
		$this->connecturl= $otherInstance->connecturl;
		$this->searchfor = $otherInstance->searchfor;
		$this->markas    = $otherInstance->markas;
		$this->isvalid   = $otherInstance->isvalid;

		$useisvalid = ($this->isvalid)? 1 : 0;

		$usepassword = $this->__crypt($this->password);

		global $adb;
		if ($this->scannerid == false) {
			$adb->pquery(
				'INSERT INTO vtiger_mailscanner(scannername,server,protocol,username,password,ssltype,
				sslmethod,connecturl,searchfor,markas,isvalid) VALUES(?,?,?,?,?,?,?,?,?,?,?)',
				array($this->scannername,$this->server, $this->protocol, $this->username, $usepassword,
				$this->ssltype,
				$this->sslmethod,
				$this->connecturl,
				$this->searchfor,
				$this->markas,
				$useisvalid)
			);
			$this->scannerid = $adb->database->Insert_ID();
		} else { //this record is exist in the data
			$adb->pquery(
				'UPDATE vtiger_mailscanner SET scannername=?,server=?,protocol=?,username=?,password=?,ssltype=?,
				sslmethod=?,connecturl=?,searchfor=?,markas=?,isvalid=? WHERE scannerid=?',
				array($this->scannername,$this->server,$this->protocol, $this->username, $usepassword, $this->ssltype,
				$this->sslmethod,
				$this->connecturl,
				$this->searchfor,
				$this->markas,
				$useisvalid,
				$this->scannerid)
			);
		}
		return $mailServerChanged;
	}

	/**
	 * Delete the scanner information from database
	 */
	public function delete() {
		global $adb;

		// Delete dependencies
		if (!empty($this->rules)) {
			foreach ($this->rules as $rule) {
				$rule->delete();
			}
		}

		if ($this->scannerid) {
			$tables = array(
				'vtiger_mailscanner',
				'vtiger_mailscanner_ids',
				'vtiger_mailscanner_folders'
			);
			foreach ($tables as $table) {
				$adb->pquery("DELETE FROM $table WHERE scannerid=?", array($this->scannerid));
			}
			$adb->pquery(
				'DELETE FROM vtiger_mailscanner_ruleactions WHERE actionid in (SELECT actionid FROM vtiger_mailscanner_actions WHERE scannerid=?)',
				array($this->scannerid)
			);
			$adb->pquery('DELETE FROM vtiger_mailscanner_actions WHERE scannerid=?', array($this->scannerid));
		}
	}

	/**
	 * List all the mail-scanners configured.
	 */
	public static function listAll() {
		global $adb;
		$scanners = array();
		$result = $adb->pquery('SELECT scannername FROM vtiger_mailscanner', array());
		if ($result && $adb->num_rows($result)) {
			while ($resultrow = $adb->fetch_array($result)) {
				$scanners[] = new self(decode_html($resultrow['scannername']));
			}
		}
		return $scanners;
	}
}
?>
