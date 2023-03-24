<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Settings/MailScanner/core/MailScannerAction.php';
require_once 'modules/Settings/MailScanner/core/MailAttachmentMIME.php';
require_once 'include/utils/utils.php';
require_once 'modules/Emails/mail.php';

/**
 * Class which Creates Emails, Attachments and Documents
 */
class MailManager_RelationControllerAction extends Vtiger_MailScannerAction {

	/**
	 * Create new Email record (and link to given record) including attachments
	 * @global Users $current_user
	 * @param  MailManager_Model_Message $mailrecord
	 * @param string $module
	 * @param CRMEntity $linkfocus
	 * @return integer
	 */
	public function __CreateNewEmail($mailrecord, $module, $linkfocus) {
		global $current_user;
		if (!$current_user) {
			$current_user = Users::getActiveAdminUser();
		}
		$handler = vtws_getModuleHandlerFromName('Emails', $current_user);
		$meta = $handler->getMeta();
		if (!$meta->hasWriteAccess()) {
			return false;
		}
		$element = array(
			'subject' => $mailrecord->_subject,
			'parent_type' => empty($module) ? '' : $module,
			'parent_id' => empty($linkfocus->id) ? '' : "$linkfocus->id@-1|",
			'description' => $mailrecord->getBodyHTML(),
			'assigned_user_id' => $linkfocus->column_fields['assigned_user_id'],
			'date_start' => date('Y-m-d', $mailrecord->_date),
			'email_flag' => 'MailManager',
			'from_email' => $mailrecord->_from[0],
			'replyto' => $mailrecord->_reply_to[0],
			'saved_toid' => $mailrecord->_to[0],
			'ccmail' => empty($mailrecord->_cc) ? '' : implode(',', $mailrecord->_cc),
			'bccmail' => empty($mailrecord->_bcc) ? '' : implode(',', $mailrecord->_bcc),
			'bounced' => '0',
			'clicked' => '0',
			'spamreport' => '0',
			'delivered' => '1',
			'dropped' => '0',
			'open' => '1',
			'unsubscribe' => '0',
		);
		$focus = createEmailRecordWithSave($element);
		$emailid = $focus->id;
		$this->__SaveAttachements($mailrecord, 'Emails', $focus, $module, $linkfocus);
		return $emailid;
	}

	/**
	 * Save attachments from the email and add it to the module record.
	 * @global PearDataBase $adb
	 * @global string $root_directory
	 * @param MailManager_Model_Message $mailrecord
	 * @param string $basemodule
	 * @param CRMEntity $basefocus
	 */
	public function __SaveAttachements($mailrecord, $basemodule, $basefocus, $relate2module = '', $relate2focus = '') {
		global $adb, $root_directory;

		// If there is no attachments return
		if (!$mailrecord->_attachments) {
			return;
		}

		$userid = $basefocus->column_fields['assigned_user_id'];
		$setype = "$basemodule Attachment";

		$date_var = $adb->formatDate(date('YmdHis'), true);

		foreach ($mailrecord->_attachments as $filename => $filecontent) {
			if (empty($filecontent)) {
				continue;
			}

			$attachid = $adb->getUniqueId('vtiger_crmentity');
			$description = $filename;
			$usetime = $adb->formatDate($date_var, true);

			$adb->pquery(
				'INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($attachid, $userid, $userid, $userid, $setype, $description, $usetime, $usetime, 1, 0)
			);

			$issaved = $this->__SaveAttachmentFile($attachid, $filename, $filecontent);

			if ($issaved) {
				// To compute file size & type
				$attachRes = $adb->pquery('SELECT * FROM vtiger_attachments WHERE attachmentsid = ?', array($attachid));
				if ($adb->num_rows($attachRes)) {
					$filePath = $adb->query_result($attachRes, 0, 'path');
					$completeFilePath = $root_directory.$filePath. $attachid.'_'. $filename;
					if (file_exists($completeFilePath)) {
						$fileSize = filesize($completeFilePath);
						$mimetype = MailAttachmentMIME::detect($completeFilePath);
					}
				}

				// Create document record
				$docInfo = array('title'=>$filename, 'filename'=>$filename, 'assigneduser'=>$userid, 'size'=> $fileSize, 'filetype'=>$mimetype);
				$documentId = $this->createDocument($docInfo);

				// Link file attached to document
				if (!empty($documentId) && !empty($attachid)) {
					$this->relateAttachment($documentId, $attachid);
				}

				// Link document to base record
				if (!empty($basefocus->id) && !empty($documentId)) {
					$this->relatedDocument($basefocus->id, $documentId);
				}

				// Link file attached to emails also, for it to appear on email's page
				if (!empty($basefocus->id) && !empty($attachid)) {
					$this->relateAttachment($basefocus->id, $attachid);
				}

				// Link document to related record
				if (!empty($relate2focus) && !empty($relate2focus->id) && !empty($documentId)) {
					$this->relatedDocument($relate2focus->id, $documentId);
				}
			}
		}
	}

	/**
	 * Creates a Document
	 * @global Users $current_user
	 * @param array $info
	 * @return integer
	 */
	public function createDocument($info) {
		global $current_user;
		$handler = vtws_getModuleHandlerFromName('Documents', $current_user);
		$meta = $handler->getMeta();
		if (!$meta->hasWriteAccess()) {
			return false;
		}
		$document = CRMEntity::getInstance('Documents');
		$document->column_fields['notes_title']      = $info['title'];
		$document->column_fields['filename']         = $info['filename'];
		$document->column_fields['filesize']         = $info['size'];
		$document->column_fields['filetype']         = $info['filetype'];
		$document->column_fields['filestatus']       = 1;
		$document->column_fields['filelocationtype'] = 'I';
		$document->column_fields['folderid']         = 1; // Default Folder
		$document->column_fields['assigned_user_id'] = $info['assigneduser'];
		$document->save('Documents');
		return $document->id;
	}

	/**
	 *
	 * @param MailManager_Model_Message $mailrecord
	 * @param integer $linkto
	 * @return array
	 */
	public static function associate($mailrecord, $linkto) {
		$instance = new self(0);

		$modulename = getSalesEntityType($linkto);
		$linkfocus = CRMEntity::getInstance($modulename);
		$linkfocus->retrieve_entity_info($linkto, $modulename);
		$linkfocus->id = $linkto;

		$emailid = $instance->__CreateNewEmail($mailrecord, $modulename, $linkfocus);

		if (!empty($emailid)) {
			MailManager::updateMailAssociation($mailrecord->uniqueid(), $emailid, $linkfocus->id);
		}

		$name = getEntityName($modulename, $linkto);
		return  self::buildDetailViewLink($modulename, $linkfocus->id, $name[$linkto]);
	}

	/**
	 * Returns the information about the Parent
	 * @param string $module
	 * @param integer $record
	 * @param string $label
	 * @return array
	 */
	public static function buildDetailViewLink($module, $record, $label) {
		$detailViewLink = sprintf("<a target='_blank' href='index.php?module=%s&action=DetailView&record=%s'>%s</a>", $module, $record, textlength_check($label));
		return array('record'=>$record, 'module'=>$module, 'label'=>$label, 'detailviewlink'=> $detailViewLink);
	}

	/**
	 * Returns the related entity for a Mail
	 * @global PearDataBase $adb
	 * @param integer $mailuid - Mail Number
	 * @return array
	 */
	public static function associatedLink($mailuid) {
		$info = MailManager::lookupMailAssociation($mailuid);
		if ($info) {
			return self::getSalesEntityInfo($info['crmid']);
		}
		return false;
	}

	/**
	 * Returns the information about the Parent
	 * @global PearDataBase $adb
	 * @param integer $crmid
	 * @return array
	 */
	public static function getSalesEntityInfo($crmid) {
		global $adb;
		$result = $adb->pquery('SELECT setype FROM vtiger_crmobject WHERE crmid=? AND deleted=0', array($crmid));
		if ($adb->num_rows($result)) {
			$modulename = $adb->query_result($result, 0, 'setype');
			$recordlabels = getEntityName($modulename, array($crmid));
			return self::buildDetailViewLink($modulename, $crmid, $recordlabels[$crmid]);
		}
	}

	/**
	 *
	 * @global PearDataBase $adb
	 * @param <type> $modulewsid
	 * @return <type>
	 */
	public static function ws_modulename($modulewsid) {
		global $adb;
		$result = $adb->pquery('SELECT name FROM vtiger_ws_entity WHERE id=?', array($modulewsid));
		if ($adb->num_rows($result)) {
			return $adb->query_result($result, 0, 'name');
		}
		return false;
	}

	/**
	 * Related an attachment to a Email record
	 * @global PearDataBase $adb
	 * @param integer $crmId
	 * @param integer $attachId
	 */
	public function relateAttachment($crmId, $attachId) {
		global $adb;
		$adb->pquery('INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)', array($crmId, $attachId));
	}

	/**
	 * Related a Document to a record
	 * @global PearDataBase $adb
	 * @param integer $crmId
	 * @param integer $docId
	 */
	public function relatedDocument($crmId, $docId) {
		global $adb;
		$adb->pquery('INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)', array($crmId, $docId));
	}
}
?>