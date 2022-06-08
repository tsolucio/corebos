<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailBox.php';
require_once 'modules/Settings/MailScanner/core/MailAttachmentMIME.php';
require_once 'modules/Users/Users.php';

/**
 * Mail Scanner provides the ability to scan through the given mailbox
 * applying the rules configured.
 */
class Vtiger_MailScanner {
	// MailScanner information instance
	private $_scannerinfo = false;
	// Reference mailbox to use
	private $_mailbox = false;
	// other CRMIDs we have to relate the email with
	public $otherEmailRelations = array();

	// Ignore scanning the folders always
	private $_generalIgnoreFolders = array(
		'INBOX.Trash', 'INBOX.Drafts', '[Gmail]/Spam', '[Gmail]/Trash', '[Gmail]/Drafts', '[Gmail]/Important', '[Gmail]/Starred', '[Gmail]/Sent Mail', '[Gmail]/All Mail'
	);

	/** DEBUG functionality. */
	public $debug = false;
	public function log($message) {
		global $log;
		if ($log && $this->debug) {
			$log->debug($message);
		} elseif ($this->debug) {
			echo "$message\n";
		}
	}

	/**
	 * Constructor.
	 */
	public function __construct($scannerinfo) {
		$this->_scannerinfo = $scannerinfo;
	}

	/**
	 * Get mailbox instance configured for the scan
	 */
	public function getMailBox() {
		if (!$this->_mailbox) {
			$this->_mailbox = new Vtiger_MailBox($this->_scannerinfo);
			$this->_mailbox->debug = $this->debug;
		}
		return $this->_mailbox;
	}

	/**
	 * Start Scanning.
	 */
	public function performScanNow() {
		global $current_user;
		if (is_null($current_user)) {
			$current_user = Users::getActiveAdminUser();
		}

		// Check if rules exists to proceed
		$rules = $this->_scannerinfo->rules;

		if (empty($rules)) {
			$this->log('No rules setup for scanner ['. $this->_scannerinfo->scannername . "] SKIPPING\n");
			return;
		}

		// Build ignore folder list
		$ignoreFolders = array() + $this->_generalIgnoreFolders;
		$folderinfoList = $this->_scannerinfo->getFolderInfo();
		foreach ($folderinfoList as $foldername => $folderinfo) {
			if (!$folderinfo['enabled']) {
				$ignoreFolders[] = $foldername;
			}
		}

		// Get mailbox instance to work with
		$mailbox = $this->getMailBox();
		$mailbox->connect();

		/** Loop through all the folders. */
		$folders = $mailbox->getFolders();

		if ($folders) {
			$this->log('Folders found: ' . implode(',', $folders) . "\n");
		}

		foreach ($folders as $lookAtFolder) {
			// Skip folder scanning?
			if (in_array($lookAtFolder, $ignoreFolders)) {
				$this->log("\nIgnoring Folder: $lookAtFolder\n");
				continue;
			}
			// If a new folder has been added we should avoid scanning it
			if (!isset($folderinfoList[$lookAtFolder])) {
				$this->log("\nSkipping New Folder: $lookAtFolder\n");
				continue;
			}

			// Search for mail in the folder
			$mailsearch = $mailbox->search($lookAtFolder);
			$this->log($mailsearch? "Total Mails Found in [$lookAtFolder]: " . count($mailsearch) : "No Mails Found in [$lookAtFolder]");

			// No emails? Continue with next folder
			if (empty($mailsearch)) {
				continue;
			}

			// Loop through each of the email searched
			foreach ($mailsearch as $messageid) {
				// Fetch only header part first, based on account lookup fetch the body.
				$mailrecord = $mailbox->getMessage($messageid, false);
				$mailrecord->debug = $mailbox->debug;
				$mailrecord->log();

				// If the email is already scanned & rescanning is not set, skip it
				if ($this->isMessageScanned($mailrecord, $lookAtFolder)) {
					$this->log("\nMessage already scanned [$mailrecord->_subject], IGNORING...\n");
					unset($mailrecord);
					continue;
				}

				// Apply rules configured for the mailbox
				$crmid = false;
				foreach ($rules as $mailscannerrule) {
					$mailrecord->_assign_to = $mailscannerrule->assign_to;
					$crmid = $this->applyRule($mailscannerrule, $mailrecord, $mailbox, $messageid);
					if ($crmid) {
						break; // Rule was successfully applied and action taken
					}
				}
				// Mark the email message as scanned
				$this->markMessageScanned($mailrecord, $crmid);
				$mailbox->markMessage($messageid, $mailrecord->_flags);

				/** Free the resources consumed. */
				unset($mailrecord);
			}
			/* Update lastscan for this folder and reset rescan flag */
			$rescanFolderFlag = false;
			$this->updateLastScan($lookAtFolder, $rescanFolderFlag);
		}
		// Close the mailbox at end
		$mailbox->close();
	}

	/**
	 * Apply all the rules configured for a mailbox on the mailrecord.
	 */
	public function applyRule($mailscannerrule, $mailrecord, $mailbox, $messageid) {
		// If no actions are set, don't proceed
		if (empty($mailscannerrule->actions)) {
			return false;
		}

		// Check if rule is defined for the body
		$bodyrule = $mailscannerrule->hasBodyRule();

		if ($bodyrule) {
			// We need the body part for rule evaluation
			$mailrecord->fetchBody($mailbox->_imap, $messageid);
		}

		// Apply rule to check if record matches the criteria
		$matchresult = $mailscannerrule->applyAll($mailrecord, $bodyrule);

		// If record matches the conditions fetch body to take action.
		$crmid = false;
		if ($matchresult) {
			$mailrecord->fetchBody($mailbox->_imap, $messageid);
			$this->otherEmailRelations = array();
			$crmid = $mailscannerrule->takeAction($this, $mailrecord, $matchresult);
		}
		// Return the CRMID
		return $crmid;
	}

	/**
	 * Mark the email as scanned.
	 */
	public function markMessageScanned($mailrecord, $crmid = false) {
		global $adb;
		if ($crmid === false) {
			$crmid = null;
		}
		$adb->pquery(
			'INSERT INTO vtiger_mailscanner_ids(scannerid, messageid, crmid) VALUES(?,?,?)',
			array($this->_scannerinfo->scannerid, $mailrecord->_uniqueid, $crmid)
		);
	}

	/**
	 * Check if email was scanned.
	 */
	public function isMessageScanned($mailrecord, $lookAtFolder) {
		global $adb;
		$messages = $adb->pquery(
			'SELECT * FROM vtiger_mailscanner_ids WHERE scannerid=? AND messageid=?',
			array($this->_scannerinfo->scannerid, $mailrecord->_uniqueid)
		);

		$folderRescan = $this->_scannerinfo->needRescan($lookAtFolder);
		$isScanned = false;

		if ($adb->num_rows($messages)) {
			$isScanned = true;

			// If folder is scheduled for rescan and earlier message was not acted upon?
			$relatedCRMId = $adb->query_result($messages, 0, 'crmid');

			if ($folderRescan && empty($relatedCRMId)) {
				$adb->pquery(
					'DELETE FROM vtiger_mailscanner_ids WHERE scannerid=? AND messageid=?',
					array($this->_scannerinfo->scannerid, $mailrecord->_uniqueid)
				);
				$isScanned = false;
			}
		}
		return $isScanned;
	}

	/**
	 * Update last scan on the folder.
	 */
	public function updateLastscan($folder) {
		$this->_scannerinfo->updateLastscan($folder);
	}

	/**
	 * Convert string to integer value.
	 * @param $strvalue
	 * @returns false if given contain non-digits, else integer value
	 */
	public function __toInteger($strvalue) {
		$ival = (int)$strvalue;
		$intvalstr = "$ival";
		if (strlen($strvalue) == strlen($intvalstr)) {
			return $ival;
		}
		return false;
	}

	/** Lookup functionality. */
	private $_cachedContactIds = array();
	private $_cachedAccountIds = array();
	private $_cachedUserIds = array();
	private $_cachedEmployeeIds = array();
	private $_cachedTicketIds = array();
	private $_cachedProjectIds = array();

	private $_cachedAccounts = array();
	private $_cachedContacts = array();
	private $_cachedTickets  = array();
	private $_cachedProjects = array();

	public $linkedid;
	public $linkedtype;

	public function getUserList($crmobj) {
		global $adb;
		$module = get_class($crmobj);
		$tickettab = getTabid($module);
		$usrfldssel = 'SELECT * FROM vtiger_field WHERE tabid=? AND uitype IN (53,101,52)';
		$fldres = $adb->pquery($usrfldssel, array($tickettab));
		$retusr = array();
		while ($row = $adb->fetch_array($fldres)) {
			if (!empty($crmobj->column_fields[$row['fieldname']])) {
				$retusr[] = $crmobj->column_fields[$row['fieldname']];
			}
		}
		return $retusr;
	}

	public function getEmployeeList($crmobj) {
		global $adb,$currentModule,$current_user;

		if (is_null($current_user)) {
			$current_user = Users::getActiveAdminUser();
		}
		$retemp = array();
		if (vtlib_isModuleActive('cbEmployee')) {
			$currentModule = 'cbEmployee';
			$module = get_class($crmobj);
			$modtab = getTabid($module);
			$emptab = getTabid('cbEmployee');
			$fldres = $adb->pquery(
				'SELECT fld.fieldname FROM vtiger_field fld LEFT JOIN vtiger_fieldmodulerel fr ON fld.fieldid=fr.fieldid WHERE fld.tabid=? AND fld.uitype=10 AND fr.relmodule=?',
				array($modtab, 'cbEmployee')
			);
			while ($row = $adb->fetch_array($fldres)) {
				if (!empty($crmobj->column_fields[$row['fieldname']]) && getSalesEntityType($crmobj->column_fields[$row['fieldname']]) == 'cbEmployee') {
					$retemp[] = $crmobj->column_fields[$row['fieldname']];
				}
			}
			$rel = $crmobj->get_related_list($crmobj->id, $modtab, $emptab);
			$dep = $crmobj->get_dependents_list($crmobj->id, $modtab, $emptab);
			$relids = (!is_null(array_keys($rel['entries'])) ? array_keys($rel['entries']) : array());
			$depids = (!is_null(array_keys($dep['entries'])) ? array_keys($dep['entries']) : array());
			$retemp = array_merge($retemp, $relids, $depids);
		}
		return $retemp;
	}

	/**
	 * Lookup User record based on the email given.
	 */
	public function LookupUser($email, $checkWithId = false) {
		global $adb;
		if (isset($this->_cachedUserIds[$email])) {
			$this->log("Reusing Cached User Id for email: $email");
			return $this->_cachedUserIds[$email];
		}
		$userid = false;
		$userres = $adb->pquery('SELECT id FROM vtiger_users WHERE deleted=0 and (email1=? or email2=? or secondaryemail=?)', array($email,$email,$email));
		if ($adb->num_rows($userres)) {
			$userid = $adb->query_result($userres, 0, 'id');
		}
		if ($userid) {
			if ($checkWithId && !in_array($userid, (array)$checkWithId)) {
				$userid = false;
				$this->log("Matching User found for email: $email, but not implied.");
			} else {
				$this->log("Caching User Id found for email: $email");
				$this->_cachedUserIds[$email] = $userid;
			}
		} else {
			$this->log("No matching User found for email: $email");
		}
		if ($userid) {
			$this->linkedid = $userid;
			$this->linkedtype = 'user';
		}
		return $userid;
	}

	/**
	 * Lookup Employee record based on the email given.
	 */
	public function LookupEmployee($email, $checkWithId = false) {
		global $adb;
		$empid = false;
		if (vtlib_isModuleActive('cbEmployee')) {
			if (isset($this->_cachedEmployeeIds[$email])) {
				$this->log("Reusing Cached Employee Id for email: $email");
				return $this->_cachedEmployeeIds[$email];
			}
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbEmployee');
			$dnjoin = 'INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=vtiger_cbemployee.cbemployeeid';
			$empres = $adb->pquery(
				'SELECT cbemployeeid,userid FROM vtiger_cbemployee '.$dnjoin.' WHERE vtiger_crmentity.deleted=0 and (personal_email=? or work_email=?)',
				array($email,$email)
			);
			if ($adb->num_rows($empres)) {
				$empid = $adb->query_result($empres, 0, 'empid');
				$userid = $adb->query_result($empres, 0, 'userid');
			}
			if ($empid) {
				if ($checkWithId && !in_array($empid, (array)$checkWithId)) {
					$empid = false;
					$this->log("Matching Employee found for email: $email, but not implied");
				} else {
					$this->log("Caching Employee Id found for email: $email");
					$this->_cachedEmployeeIds[$email] = $empid;
				}
			} else {
				$this->log("No matching Employee found for email: $email");
			}
		}
		if (!empty($userid)) {
			$this->linkedid = $userid;
			$this->linkedtype = 'user';
		}
		return $empid;
	}

	/**
	 * Lookup Contact record based on the email given.
	 */
	public function LookupContact($email) {
		global $adb;
		if (isset($this->_cachedContactIds[$email])) {
			$this->log("Reusing Cached Contact Id for email: $email");
			return $this->_cachedContactIds[$email];
		}
		$contactid = false;
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Contacts');
		$dnjoin = 'INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid';
		$contactres = $adb->pquery(
			'SELECT vtiger_contactdetails.contactid FROM vtiger_contactdetails '.$dnjoin.' WHERE vtiger_crmentity.deleted=0 and (email=? or secondaryemail=?)',
			array($email,$email)
		);
		while ($cto = $adb->fetch_array($contactres)) {
			$contactid = $cto['contactid'];
			$this->otherEmailRelations[] = $contactid;
		}
		if ($contactid) {
			$this->log("Caching Contact Id found for email: $email");
			$this->_cachedContactIds[$email] = $contactid;
		} else {
			$this->log("No matching Contact found for email: $email");
		}
		if ($contactid) {
			$this->linkedid = $contactid;
			$this->linkedtype = 'customer';
		}
		return $contactid;
	}

	/**
	 * Lookup Account record based on the email given.
	 */
	public function LookupAccount($email) {
		global $adb;
		if (isset($this->_cachedAccountIds[$email])) {
			$this->log("Reusing Cached Account Id for email: $email");
			return $this->_cachedAccountIds[$email];
		}

		$accountid = false;
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Accounts');
		$dnjoin = 'INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=vtiger_account.accountid';
		$accountres = $adb->pquery(
			'SELECT vtiger_account.accountid FROM vtiger_account '.$dnjoin.' WHERE vtiger_crmentity.deleted=0 and (email1=? OR email2=?)',
			array($email, $email)
		);
		while ($acc = $adb->fetch_array($accountres)) {
			$accountid = $acc['accountid'];
			$this->otherEmailRelations[] = $accountid;
		}
		if ($accountid) {
			$this->log("Caching Account Id found for email: $email");
			$this->_cachedAccountIds[$email] = $accountid;
		} else {
			$this->log("No matching Account found for email: $email");
		}
		if ($accountid) {
			$this->linkedid = $accountid;
			$this->linkedtype = 'customer';
		}
		return $accountid;
	}
	/**
	 * Lookup Ticket record based on the subject or id given.
	 */
	public function LookupTicket($subjectOrId) {
		global $adb;

		$checkTicketId = $this->__toInteger($subjectOrId);
		if (!$checkTicketId) {
			$ticketres = $adb->pquery('SELECT ticketid FROM vtiger_troubletickets WHERE title = ? OR ticket_no = ?', array($subjectOrId, $subjectOrId));
			if ($adb->num_rows($ticketres)) {
				$checkTicketId = $adb->query_result($ticketres, 0, 'ticketid');
			}
		}
		// Try with ticket_no before CRMID (case where ticket_no is also just number)
		if (!$checkTicketId) {
			$ticketres = $adb->pquery('SELECT ticketid FROM vtiger_troubletickets WHERE ticketid=?', array($subjectOrId));
			if ($adb->num_rows($ticketres)) {
				$checkTicketId = $adb->query_result($ticketres, 0, 'ticketid');
			}
		}
		// Nothing found?
		if (!$checkTicketId) {
			return false;
		}

		if (isset($this->_cachedTicketIds[$checkTicketId])) {
			$this->log("Reusing Cached Ticket Id for: $subjectOrId");
			return $this->_cachedTicketIds[$checkTicketId];
		}

		// Verify ticket is not deleted
		$ticketid = false;
		if ($checkTicketId) {
			$crmres = $adb->pquery('SELECT setype, deleted FROM vtiger_crmobject WHERE crmid=?', array($checkTicketId));
			if ($adb->num_rows($crmres) && $adb->query_result($crmres, 0, 'setype') == 'HelpDesk' && $adb->query_result($crmres, 0, 'deleted') == '0') {
				$ticketid = $checkTicketId;
			}
		}
		if ($ticketid) {
			$this->log("Caching Ticket Id found for: $subjectOrId");
			$this->_cachedTicketIds[$checkTicketId] = $ticketid;
		} else {
			$this->log("No matching Ticket found for: $subjectOrId");
		}
		return $ticketid;
	}

	/**
	 * Lookup Project record based on the subject or id given.
	 */
	public function LookupProject($subjectOrId) {
		global $adb;
		$checkProjectId = $this->__toInteger($subjectOrId);
		if (!$checkProjectId) {
			$projectres = $adb->pquery('SELECT projectid FROM vtiger_project WHERE projectname=? OR project_no=?', array($subjectOrId, $subjectOrId));
			if ($adb->num_rows($projectres)) {
				$checkProjectId = $adb->query_result($projectres, 0, 'projectid');
			}
		}
		// Try with ticket_no before CRMID (case where ticket_no is also just number)
		if (!$checkProjectId) {
			$projectres = $adb->pquery('SELECT projectid FROM vtiger_project WHERE projectid=?', array($subjectOrId));
			if ($adb->num_rows($projectres)) {
				$checkProjectId = $adb->query_result($projectres, 0, 'projectid');
			}
		}
		// Nothing found?
		if (!$checkProjectId) {
			return false;
		}

		if (isset($this->_cachedProjectIds[$checkProjectId])) {
			$this->log("Reusing Cached Project Id for: $subjectOrId");
			return $this->_cachedProjectIds[$checkProjectId];
		}

		// Verify ticket is not deleted
		$projectid = false;
		if ($checkProjectId) {
			$crmres = $adb->pquery('SELECT setype, deleted FROM vtiger_crmobject WHERE crmid=?', array($checkProjectId));
			if ($adb->num_rows($crmres) && $adb->query_result($crmres, 0, 'setype') == 'Project' && $adb->query_result($crmres, 0, 'deleted') == '0') {
				$projectid = $checkProjectId;
			}
		}
		if ($projectid) {
			$this->log("Caching Project Id found for: $subjectOrId");
			$this->_cachedProjectIds[$checkProjectId] = $projectid;
		} else {
			$this->log("No matching Project found for: $subjectOrId");
		}
		return $projectid;
	}

	/**
	 * Get Account record information based on email.
	 */
	public function GetAccountRecord($email) {
		require_once 'modules/Accounts/Accounts.php';
		$accountid = $this->LookupAccount($email);
		$account_focus = false;
		if ($accountid) {
			if (isset($this->_cachedAccounts[$accountid])) {
				$account_focus = $this->_cachedAccounts[$accountid];
				$this->log('Reusing Cached Account [' . $account_focus->column_fields['accountname'] . ']');
			} else {
				$account_focus = new Accounts();
				$account_focus->retrieve_entity_info($accountid, 'Accounts');
				$account_focus->id = $accountid;

				$this->log('Caching Account [' . $account_focus->column_fields['accountname'] . ']');
				$this->_cachedAccounts[$accountid] = $account_focus;
			}
		}
		return $account_focus;
	}
	/**
	 * Get Contact record information based on email.
	 */
	public function GetContactRecord($email) {
		require_once 'modules/Contacts/Contacts.php';
		$contactid = $this->LookupContact($email);
		$contact_focus = false;
		if ($contactid) {
			if (isset($this->_cachedContacts[$contactid])) {
				$contact_focus = $this->_cachedContacts[$contactid];
				$this->log('Reusing Cached Contact [' . $contact_focus->column_fields['lastname'] . '-' . $contact_focus->column_fields['firstname'] . ']');
			} else {
				$contact_focus = new Contacts();
				$contact_focus->retrieve_entity_info($contactid, 'Contacts');
				$contact_focus->id = $contactid;
				$this->log('Caching Contact [' . $contact_focus->column_fields['lastname'] . '-' . $contact_focus->column_fields['firstname'] . ']');
				$this->_cachedContacts[$contactid] = $contact_focus;
			}
		}
		return $contact_focus;
	}

	/**
	 * Lookup Contact or Account based on from email and with respect to given CRMID
	 */
	public function LookupContactOrAccount($fromemail, $checkWithId = false) {
		$recordid = $this->LookupContact($fromemail);
		if ($checkWithId && $recordid != $checkWithId) {
			$recordid = $this->LookupAccount($fromemail);
			if ($checkWithId && $recordid != $checkWithId) {
				$recordid = false;
			}
		}
		return $recordid;
	}

	/**
	 * Get Ticket record information based on subject or id.
	 */
	public function GetTicketRecord($subjectOrId, $fromemail = false, $must_be_related = true) {
		require_once 'modules/HelpDesk/HelpDesk.php';
		$ticketid = $this->LookupTicket($subjectOrId);
		$ticket_focus = false;
		if ($ticketid) {
			if (isset($this->_cachedTickets[$ticketid])) {
				$ticket_focus = $this->_cachedTickets[$ticketid];
				$usrlist = $this->getUserlist($ticket_focus);
				$employeelist = $this->getEmployeeList($ticket_focus);
				// Check the parentid association if specified.
				if ($fromemail && !$this->LookupContactOrAccount($fromemail, ($must_be_related ? $ticket_focus->column_fields['parent_id'] : false)) &&
					!$this->LookupUser($fromemail, ($must_be_related ? $usrlist : false)) &&
					!$this->LookupEmployee($fromemail, ($must_be_related ? $employeelist : false))) {
					$ticket_focus = false;
				}
				if ($ticket_focus) {
					$this->log('Reusing Cached Ticket [' . $ticket_focus->column_fields['ticket_title'] .']');
				}
			} else {
				$ticket_focus = new HelpDesk();
				$ticket_focus->retrieve_entity_info($ticketid, 'HelpDesk');
				$ticket_focus->id = $ticketid;
				$usrlist = $this->getUserlist($ticket_focus);
				$employeelist = $this->getEmployeeList($ticket_focus);
				// Check the parentid association if specified.
				if ($fromemail && !$this->LookupContactOrAccount($fromemail, ($must_be_related ? $ticket_focus->column_fields['parent_id'] : false)) &&
					!$this->LookupUser($fromemail, ($must_be_related ? $usrlist : false)) &&
					!$this->LookupEmployee($fromemail, ($must_be_related ? $employeelist : false))) {
					$ticket_focus = false;
				}
				if ($ticket_focus) {
					$this->log('Caching Ticket [' . $ticket_focus->column_fields['ticket_title'] . ']');
					$this->_cachedTickets[$ticketid] = $ticket_focus;
				}
			}
		}
		return $ticket_focus;
	}

	/**
	 * Get Project record information based on subject or id.
	 */
	public function GetProjectRecord($subjectOrId, $fromemail = false, $must_be_related = true) {
		$projectid = $this->LookupProject($subjectOrId);
		$project_focus = false;
		if ($projectid) {
			if (isset($this->_cachedProjects[$projectid])) {
				$project_focus = $this->_cachedProjects[$projectid];
				$usrlist = $this->getUserlist($project_focus);
				$employeelist = $this->getEmployeeList($project_focus);
				// Check the parentid association if specified.
				if ($fromemail && !$this->LookupContactOrAccount($fromemail, ($must_be_related ? $project_focus->column_fields['linktoaccountscontacts'] : false)) &&
					!$this->LookupUser($fromemail, ($must_be_related ? $usrlist : false)) &&
					!$this->LookupEmployee($fromemail, ($must_be_related ? $employeelist : false))) {
					$project_focus = false;
				}
				if ($project_focus) {
					$this->log('Reusing Cached Project [' . $project_focus->column_fields['projectname'] .']');
				}
			} else {
				$project_focus = CRMEntity::getInstance('Project');
				$project_focus->retrieve_entity_info($projectid, 'Project');
				$project_focus->id = $projectid;
				$usrlist = $this->getUserlist($project_focus);
				$employeelist = $this->getEmployeeList($project_focus);
				// Check the parentid association if specified.
				if ($fromemail && !$this->LookupContactOrAccount($fromemail, ($must_be_related ? $project_focus->column_fields['linktoaccountscontacts'] : false)) &&
					!$this->LookupUser($fromemail, ($must_be_related ? $usrlist : false)) &&
					!$this->LookupEmployee($fromemail, ($must_be_related ? $employeelist : false))) {
					$project_focus = false;
				}
				if ($project_focus) {
					$this->log('Caching Project [' . $project_focus->column_fields['projectname'] . ']');
					$this->_cachedProject[$projectid] = $project_focus;
				}
			}
		}
		return $project_focus;
	}
}
?>
