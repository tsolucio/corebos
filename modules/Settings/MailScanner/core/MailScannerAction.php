<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Emails/Emails.php';
require_once 'modules/HelpDesk/HelpDesk.php';
require_once 'modules/Users/Users.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/Messages/Messages.php';
require_once 'include/Webservices/ExecuteWorkflow.php';

/**
 * Mail Scanner Action
 */
class Vtiger_MailScannerAction {
	// actionid for this instance
	public $actionid  = false;
	// scanner to which this action is associated
	public $scannerid = false;
	// type of mailscanner action
	public $actiontype= false;
	// text representation of action
	public $actiontext= false;
	// target module for action
	public $module    = false;
	// lookup information while taking action
	public $lookup    = false;
	// other CRMIDs we have to relate the email with
	private $otherEmailRelations = array();

	// Storage folder to use
	private $STORAGE_FOLDER = 'storage/mailscanner/';

	/** DEBUG functionality */
	public $debug     = false;
	private function log($message) {
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
	public function __construct($foractionid) {
		$this->initialize($foractionid);
	}

	/**
	 * Initialize this instance.
	 */
	public function initialize($foractionid) {
		global $adb;
		$result = $adb->pquery('SELECT * FROM vtiger_mailscanner_actions WHERE actionid=? ORDER BY sequence', array($foractionid));

		if ($adb->num_rows($result)) {
			$this->actionid   = $adb->query_result($result, 0, 'actionid');
			$this->scannerid  = $adb->query_result($result, 0, 'scannerid');
			$this->actiontype = $adb->query_result($result, 0, 'actiontype');
			$this->module     = $adb->query_result($result, 0, 'module');
			$this->lookup     = $adb->query_result($result, 0, 'lookup');
			$this->actiontext = "$this->actiontype,$this->module,$this->lookup";
		}
	}

	/**
	 * Create/Update the information of Action into database.
	 */
	public function update($ruleid, $actiontext) {
		global $adb;

		$inputparts = explode(',', $actiontext);
		$this->actiontype = $inputparts[0]; // LINK, CREATE
		$this->module     = $inputparts[1]; // Module name
		$this->lookup     = $inputparts[2]; // FROM, TO

		$this->actiontext = $actiontext;

		if ($this->actionid) {
			$adb->pquery(
				'UPDATE vtiger_mailscanner_actions SET scannerid=?, actiontype=?, module=?, lookup=? WHERE actionid=?',
				array($this->scannerid, $this->actiontype, $this->module, $this->lookup, $this->actionid)
			);
		} else {
			$this->sequence = $this->__nextsequence();
			$adb->pquery(
				'INSERT INTO vtiger_mailscanner_actions(scannerid, actiontype, module, lookup, sequence) VALUES(?,?,?,?,?)',
				array($this->scannerid, $this->actiontype, $this->module, $this->lookup, $this->sequence)
			);
			$this->actionid = $adb->database->Insert_ID();
		}
		$checkmapping = $adb->pquery(
			'SELECT COUNT(*) AS ruleaction_count FROM vtiger_mailscanner_ruleactions WHERE ruleid=? AND actionid=?',
			array($ruleid, $this->actionid)
		);
		if ($adb->num_rows($checkmapping) && !$adb->query_result($checkmapping, 0, 'ruleaction_count')) {
			$adb->pquery('INSERT INTO vtiger_mailscanner_ruleactions(ruleid, actionid) VALUES(?,?)', array($ruleid, $this->actionid));
		}
	}

	/**
	 * Delete the actions from tables.
	 */
	public function delete() {
		global $adb;
		if ($this->actionid) {
			$adb->pquery('DELETE FROM vtiger_mailscanner_actions WHERE actionid=?', array($this->actionid));
			$adb->pquery('DELETE FROM vtiger_mailscanner_ruleactions WHERE actionid=?', array($this->actionid));
		}
	}

	/**
	 * Get next sequence of Action to use.
	 */
	private function __nextsequence() {
		global $adb;
		$seqres = $adb->pquery('SELECT max(sequence) AS max_sequence FROM vtiger_mailscanner_actions', array());
		$maxsequence = 0;
		if ($adb->num_rows($seqres)) {
			$maxsequence = $adb->query_result($seqres, 0, 'max_sequence');
		}
		++$maxsequence;
		return $maxsequence;
	}

	/**
	 * Apply the action on the mail record.
	 */
	public function apply($mailscanner, $mailrecord, $mailscannerrule, $matchresult) {
		$returnid = false;
		$this->otherEmailRelations = array();
		if ($this->actiontype == 'CREATE') {
			switch ($this->module) {
				case 'HelpDesk':
					$returnid = $this->__CreateTicket($mailscanner, $mailrecord);
					break;
				case 'Messages':
					$returnid = $this->__CreateMessages($mailscanner, $mailrecord);
					break;
				case 'com_vtiger_workflow':
					$returnid = $this->__CreateMessages($mailscanner, $mailrecord);
					$this-> __triggerWorkflow($mailscannerrule, $mailrecord, $returnid);
					break;
				default:
					$returnid = false;
			}
		} elseif ($this->actiontype == 'LINK') {
			$returnid = $this->__LinkToRecord($mailscanner, $mailrecord);
		} elseif ($this->actiontype == 'UPDATE') {
			switch ($this->module) {
				case 'HelpDesk':
					$returnid = $this->__UpdateTicket($mailscanner, $mailrecord, $mailscannerrule->hasRegexMatch($matchresult), $mailscannerrule);
					break;
				case 'Project':
					$returnid = $this->__UpdateProject($mailscanner, $mailrecord, $mailscannerrule->hasRegexMatch($matchresult), $mailscannerrule);
					break;
				default:
					$returnid = false;
			}
		}
		return $returnid;
	}

	/**
	 * Update ticket action.
	 */
	public function __UpdateTicket($mailscanner, $mailrecord, $regexMatchInfo, $mailscannerrule) {
		global $adb;
		$returnid = false;
		$usesubject = false;
		if ($this->lookup == 'SUBJECT') {
			// If regex match was performed on subject use the matched group to lookup the ticket record
			if ($regexMatchInfo) {
				$usesubject = $regexMatchInfo['matches'];
			} else {
				$usesubject = $mailrecord->_subject;
			}

			// Get the ticket record that was created by SENDER earlier
			$fromemail = $mailrecord->_from[0];

			$linkfocus = $mailscanner->GetTicketRecord($usesubject, $fromemail, $mailscannerrule->must_be_related);
			$relatedid = $mailscanner->linkedid;
			$this->otherEmailRelations = $mailscanner->otherEmailRelations;

			// If matching ticket is found, update comment, attach email
			if ($linkfocus) {
				$returnid = $this->__CreateNewEmail($mailrecord, $this->module, $linkfocus);
				$timestamp = $adb->formatDate(date('YmdHis'), true);
				if ($mailscannerrule->add_email_as == 'CommentAndEmail') {
					$adb->pquery(
						'INSERT INTO vtiger_ticketcomments(ticketid, comments, ownerid, ownertype, createdtime) VALUES(?,?,?,?,?)',
						array($linkfocus->id, $mailrecord->getBodyText(), $relatedid, $mailscanner->linkedtype, $timestamp)
					);
				} elseif ($mailscannerrule->add_email_as == 'LinkAndEmail') {
					$comment = '<a href="index.php?action=DetailView&module=Emails&record='.$returnid.'" target=_blank>'.getTranslatedString('SINGLE_Emails', 'Emails').'</a>';
					$adb->pquery(
						'INSERT INTO vtiger_ticketcomments(ticketid, comments, ownerid, ownertype, createdtime) VALUES(?,?,?,?,?)',
						array($linkfocus->id, $comment, $relatedid, $mailscanner->linkedtype, $timestamp)
					);
				}
				// Set the ticket status to Open if its Closed
				$adb->pquery("UPDATE vtiger_troubletickets set status=? WHERE ticketid=? AND status='Closed'", array('Open', $linkfocus->id));
			} else {
				// If matching ticket was not found, create ticket?
				// $returnid = $this->__CreateTicket($mailscanner, $mailrecord);
			}
		}
		return $returnid;
	}

	/**
	 * Update Project action.
	 */
	public function __UpdateProject($mailscanner, $mailrecord, $regexMatchInfo, $mailscannerrule) {
		global $current_user;
		$returnid = false;
		$usesubject = false;
		if ($this->lookup == 'SUBJECT') {
			// If regex match was performed on subject use the matched group
			// to lookup the ticket record
			if ($regexMatchInfo) {
				$usesubject = $regexMatchInfo['matches'];
			} else {
				$usesubject = $mailrecord->_subject;
			}

			// Get the ticket record that was created by SENDER earlier
			$fromemail = $mailrecord->_from[0];
			$linkfocus = $mailscanner->GetProjectRecord($usesubject, $fromemail, $mailscannerrule->must_be_related);
			$this->otherEmailRelations = $mailscanner->otherEmailRelations;

			// If matching ticket is found, update comment, attach email
			if ($linkfocus) {
				$returnid = $this->__CreateNewEmail($mailrecord, $this->module, $linkfocus);
				if ($mailscannerrule->add_email_as == 'CommentAndEmail') {
					$comment = CRMEntity::getInstance('ModComments');
					$comment->column_fields['assigned_user_id'] = $current_user->id;
					$comment->column_fields['commentcontent'] = $mailrecord->getBodyText();
					$comment->column_fields['related_to'] = $linkfocus->id;
					$comment->save('ModComments');
				} elseif ($mailscannerrule->add_email_as == 'LinkAndEmail') {
					$comment = CRMEntity::getInstance('ModComments');
					$comment->column_fields['assigned_user_id'] = $current_user->id;
					$comment->column_fields['commentcontent'] = '<a href="index.php?action=DetailView&module=Emails&record='.$returnid.'" target=_blank>'.getTranslatedString('SINGLE_Emails', 'Emails').'</a>';
					$comment->column_fields['related_to'] = $linkfocus->id;
					$comment->save('ModComments');
				}
			} else {
				// If matching project was not found, create project?
			}
		}
		return $returnid;
	}

	/**
	 * Create Messages action.
	 */
	public function __CreateMessages($mailscanner, $mailrecord) {
		// Prepare data to create trouble ticket
		$usetitle = $mailrecord->_subject;
		$description = $mailrecord->getBodyText();

		// There will be only on FROM address to email, so pick the first one
		$fromemail = $mailrecord->_from[0];
		$linktoid = $mailscanner->LookupContact($fromemail);
		if (!$linktoid) {
			$linktoid = $mailscanner->LookupAccount($fromemail);
		}
		/** Now Create Ticket **/
		global $current_user;
		if (!$current_user) {
			$current_user = Users::getActiveAdminUser();
		}
		if (!empty($mailrecord->_assign_to)) {
			$usr = $mailrecord->_assign_to;
		} else {
			$usr = $current_user->id;
		}

		// Create message record
		$messages = new Messages();
		$messages->column_fields['messagename'] = $usetitle;
		$messages->column_fields['description'] = $description;
		$messages->column_fields['messagetype'] = 'Email';
		$messages->column_fields['assigned_user_id'] = $usr;
		if ($linktoid) {
			$messages->column_fields['contact_message'] = $linktoid;
		}
		$messages->save('Messages');

		// Associate any attachement of the email to messages
		$this->__SaveAttachements($mailrecord, 'Messages', $messages);

		return $messages->id;
	}

	public function __CreateTicket($mailscanner, $mailrecord) {
		global $adb;
		// Prepare data to create trouble ticket
		$usetitle = $mailrecord->_subject;
		$description = $mailrecord->getBodyText();

		// There will be only on FROM address to email, so pick the first one
		$fromemail = $mailrecord->_from[0];
		$linktoid = $mailscanner->LookupContact($fromemail);
		if (!$linktoid) {
			$linktoid = $mailscanner->LookupAccount($fromemail);
		}

		/** Now Create Ticket **/
		global $current_user;
		if (!$current_user) {
			$current_user = Users::getActiveAdminUser();
		}
		if (!empty($mailrecord->_assign_to)) {
			$usr = $mailrecord->_assign_to;
		} else {
			$usr = $current_user->id;
		}

		// Create trouble ticket record
		$ticket = new HelpDesk();
		$ticket->column_fields['ticket_title'] = $usetitle;
		$ticket->column_fields['description'] = $description;
		$ticket->column_fields['ticketstatus'] = 'Open';
		$ticket->column_fields['assigned_user_id'] = $usr;
		$ticket->column_fields['from_mailscanner'] = 1;
		if ($linktoid) {
			$ticket->column_fields['parent_id'] = $linktoid;
		}
		$ticket->save('HelpDesk');

		if (!$linktoid) {
			$adb->pquery('UPDATE vtiger_troubletickets SET email=? WHERE ticketid=?', array($fromemail, $ticket->id));
		}

		// Associate any attachement of the email to ticket
		$this->__SaveAttachements($mailrecord, 'HelpDesk', $ticket);

		return $ticket->id;
	}

	/**
	 * Add email to CRM record like Contacts/Accounts
	 */
	public function __LinkToRecord($mailscanner, $mailrecord) {
		$linkfocus = false;

		$useemail = false;
		if ($this->lookup == 'FROM') {
			$useemail = $mailrecord->_from;
		} elseif ($this->lookup == 'TO') {
			$useemail = $mailrecord->_to;
		} elseif ($this->lookup == 'CC') {
			$useemail = $mailrecord->_cc;
		}

		if ($this->module == 'Contacts') {
			foreach ($useemail as $email) {
				$linkfocus = $mailscanner->GetContactRecord($email);
				if ($linkfocus) {
					break;
				}
			}
		} elseif ($this->module == 'Accounts') {
			foreach ($useemail as $email) {
				$linkfocus = $mailscanner->GetAccountRecord($email);
				if ($linkfocus) {
					break;
				}
			}
		}

		$returnid = false;
		if ($linkfocus) {
			$returnid = $this->__CreateNewEmail($mailrecord, $this->module, $linkfocus);
		}
		return $returnid;
	}

	/**
	 * Create new Email record (and link to given record) including attachements
	 */
	public function __CreateNewEmail($mailrecord, $module, $linkfocus) {
		global $current_user;
		$reqModule = $_REQUEST['module'];
		if (!$current_user) {
			$current_user = Users::getActiveAdminUser();
		}
		if ($module=='Users') {
			$relid = '-1';
		} else {
			$referenceHandler = vtws_getModuleHandlerFromId(vtws_getEntityId($module).'x'.$linkfocus->id, $current_user);
			$referenceMeta = $referenceHandler->getMeta();
			$relid = getEmailFieldId($referenceMeta, $linkfocus->id);
		}
		$parent_id = "$linkfocus->id@$relid|";
		foreach ((array)$this->otherEmailRelations as $crmid) {
			$relmod = getSalesEntityType($crmid);
			$referenceHandler = vtws_getModuleHandlerFromId(vtws_getEntityId($relmod).'x'.$crmid, $current_user);
			$referenceMeta = $referenceHandler->getMeta();
			$relid = getEmailFieldId($referenceMeta, $crmid);
			$parent_id .= "$crmid@$relid|";
		}
		$element = array(
			'subject' => $mailrecord->_subject,
			'parent_type' => empty($module) ? '' : $module,
			'parent_id' => $parent_id,
			'description' => $mailrecord->getBodyHTML(),
			'assigned_user_id' => $linkfocus->column_fields['assigned_user_id'],
			'date_start' => date('Y-m-d', $mailrecord->_date),
			'email_flag' => 'MAILSCANNER',
			'from_email' => $mailrecord->_from[0],
			'replyto' => $mailrecord->_reply_to[0],
			'saved_toid' => $mailrecord->_to[0],
			'ccmail' => empty($mailrecord->_cc) ? '' : implode(',', $mailrecord->_cc),
			'bccmail' => empty($mailrecord->_bcc) ? '' : implode(',', $mailrecord->_bcc),
		);
		$focus = createEmailRecordWithSave($element);
		$emailid = $focus->id;
		$this->log("Created [$focus->id]: $mailrecord->_subject linked it to " . $linkfocus->id);
		$this->__SaveAttachements($mailrecord, 'Emails', $focus);
		$_REQUEST['module'] = $reqModule;
		return $emailid;
	}

	/**
	 * Save attachments from the email and add it to the module record.
	 */
	public function __SaveAttachements($mailrecord, $basemodule, $basefocus) {
		global $adb;

		// If there is no attachments return
		if (!$mailrecord->_attachments) {
			return;
		}

		$userid = $basefocus->column_fields['assigned_user_id'];
		$setype = "$basemodule Attachment";

		$date_var = $adb->formatDate(date('YmdHis'), true);

		foreach ($mailrecord->_attachments as $filename => $filecontent) {
			$attachid = $adb->getUniqueId('vtiger_crmentity');
			$description = $filename;
			$usetime = $adb->formatDate($date_var, true);

			$adb->pquery(
				'INSERT INTO vtiger_crmentity
				(crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($attachid, $userid, $userid, $userid, $setype, $description, $usetime, $usetime, 1, 0)
			);

			$issaved = $this->__SaveAttachmentFile($attachid, $filename, $filecontent);
			if ($issaved) {
				// Create document record
				$document = new Documents();
				$document->column_fields['notes_title']      = $filename;
				$document->column_fields['filename']         = $filename;
				$document->column_fields['filesize']         = filesize($this->STORAGE_FOLDER.$attachid.'_'.str_replace(' ', '-', $filename));
				$document->column_fields['filestatus']       = 1;
				$document->column_fields['filelocationtype'] = 'I';
				$document->column_fields['folderid']         = 1; // Default Folder
				$document->column_fields['assigned_user_id'] = $userid;
				$document->save('Documents');

				// Link file attached to document
				$adb->pquery('INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)', array($document->id, $attachid));

				// Link document to base record
				$adb->pquery('INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)', array($basefocus->id, $document->id));

				// Link document to Parent entity - Account/Contact/...
				if (!empty($basefocus->column_fields['parent_id'])) {
					if (strpos($basefocus->column_fields['parent_id'], '@')>0) {
						list($eid, $junk) = explode('@', $basefocus->column_fields['parent_id']);
					} else {
						$eid = $basefocus->column_fields['parent_id'];
					}
					$adb->pquery('INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)', array($eid, $document->id));
				}
				// Link Attachement to the Email
				$adb->pquery('INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)', array($basefocus->id, $attachid));
			}
		}
	}

	/**
	 * Save the attachment to the file
	 */
	public function __SaveAttachmentFile($attachid, $filename, $filecontent) {
		global $adb;

		$dirname = $this->STORAGE_FOLDER;
		if (!is_dir($dirname)) {
			mkdir($dirname);
		}

		$description = $filename;
		$filename = str_replace(' ', '-', $filename);
		$saveasfile = $dirname . $attachid . "_$filename";
		if (!file_exists($saveasfile)) {
			$this->log("Saved attachement as $saveasfile\n");
			$fh = fopen($saveasfile, 'wb');
			fwrite($fh, $filecontent);
			fclose($fh);
		}

		$mimetype = MailAttachmentMIME::detect($saveasfile);

		$adb->pquery(
			'INSERT INTO vtiger_attachments SET attachmentsid=?, name=?, description=?, type=?, path=?',
			array($attachid, $filename, $description, $mimetype, $dirname)
		);
		return true;
	}

	/**
	 * trigger  workflow
	 */
	public function __triggerWorkflow($mailscannerrule, $mailrecord, $crmid) {
		global $current_user;
		$workflowid = $mailscannerrule->workflowid;
		if (!$workflowid) {
			return false;
		}

		$context = [
			'IncomingMail_To' => $mailrecord->_to,
			'IncomingMail_From' => $mailrecord->_from,
			'IncomingMail_Body' => $mailrecord->_body,
			'IncomingMail_CC' => $mailrecord->_cc,
			'IncomingMail_BCC' => $mailrecord->_bcc,
			'IncomingMail_ReplyTo' => $mailrecord->_reply_to,
			'IncomingMail_MessageID' => $mailrecord->_uniqueid,
			'IncomingMail_Date' => $mailrecord->_date,
			'IncomingMail_Subject' => $mailrecord->_subject,
		];

		$context = json_encode($context);
		cbwsExecuteWorkflowWithContext($workflowid, json_encode([$crmid]), $context, $current_user);

		return true;
	}
}
?>
