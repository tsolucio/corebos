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

/**
 * Class which Creates Emails, Attachments and Documents
 */
class MailManager_RelationControllerAction extends Vtiger_MailScannerAction {

	function __construct() {
	}

	/**
     * Create new Email record (and link to given record) including attachments
     * @global Users $current_user
     * @global PearDataBase $adb
     * @param  MailManager_Model_Message $mailrecord
     * @param String $module
     * @param CRMEntity $linkfocus
     * @return Integer
     */
	function __CreateNewEmail($mailrecord, $module, $linkfocus) {
		global $current_user, $adb;
		if(!$current_user) {
			$current_user = Users::getActiveAdminUser();
		}
		$handler = vtws_getModuleHandlerFromName('Emails', $current_user);
		$meta = $handler->getMeta();
		if ($meta->hasWriteAccess() != true) {
			return false;
		}

		$focus = new Emails();
		$focus->column_fields['activitytype'] = 'Emails';
		$focus->column_fields['subject'] = $mailrecord->_subject;

		if(!empty($module)) $focus->column_fields['parent_type'] = $module;
		if(!empty($linkfocus->id)) $focus->column_fields['parent_id'] = "$linkfocus->id@-1|";

		$focus->column_fields['description'] = $mailrecord->getBodyHTML();
		$focus->column_fields['assigned_user_id'] = $linkfocus->column_fields['assigned_user_id'];
		$focus->column_fields["date_start"]= date('Y-m-d', $mailrecord->_date);
		$focus->column_fields["email_flag"] = 'MailManager';

		$from=$mailrecord->_from[0];
		$to = $mailrecord->_to[0];
		$cc = (!empty($mailrecord->_cc))? implode(',', $mailrecord->_cc) : '';
		$bcc= (!empty($mailrecord->_bcc))? implode(',', $mailrecord->_bcc) : '';

		//emails field were restructured and to,bcc and cc field are JSON arrays
		$focus->column_fields['from_email'] = $from;
		$focus->column_fields['saved_toid'] = $to;
		$focus->column_fields['ccmail'] = $cc;
		$focus->column_fields['bccmail'] = $bcc;
		$focus->save('Emails');

		$emailid = $focus->id;

		// TODO: Handle attachments of the mail (inline/file)
		$this->__SaveAttachements($mailrecord, 'Emails', $focus);

		return $emailid;
	}

	/**
     * Save attachments from the email and add it to the module record.
     * @global PearDataBase $adb
     * @global String $root_directory
     * @param MailManager_Model_Message $mailrecord
     * @param String $basemodule
     * @param CRMEntity $basefocus
     */
	function __SaveAttachements($mailrecord, $basemodule, $basefocus) {
		global $adb, $root_directory;

		// If there is no attachments return
		if(!$mailrecord->_attachments) return;

		$userid = $basefocus->column_fields['assigned_user_id'];
		$setype = "$basemodule Attachment";

		$date_var = $adb->formatDate(date('YmdHis'), true);

		foreach($mailrecord->_attachments as $filename=>$filecontent) {

            if(empty($filecontent)) continue;

			$attachid = $adb->getUniqueId('vtiger_crmentity');
			$description = $filename;
			$usetime = $adb->formatDate($date_var, true);

			$adb->pquery("INSERT INTO vtiger_crmentity(crmid, smcreatorid, smownerid,
				modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
				Array($attachid, $userid, $userid, $userid, $setype, $description, $usetime, $usetime, 1, 0));

			$issaved = $this->__SaveAttachmentFile($attachid, $filename, $filecontent);

			if($issaved) {
                // To compute file size & type
                $attachRes = $adb->pquery("SELECT * FROM vtiger_attachments WHERE attachmentsid = ?", array($attachid));
                if($adb->num_rows($attachRes)) {
                    $filePath = $adb->query_result($attachRes, 0, 'path');
                    $completeFilePath = $root_directory.$filePath. $attachid.'_'. $filename;
                    if(file_exists($completeFilePath))  {
                        $fileSize = filesize($completeFilePath);
                        $mimetype = MailAttachmentMIME::detect($completeFilePath);
                    }
                }

				// Create document record
				$docInfo = array('title'=>$filename, 'filename'=>$filename, 'assigneduser'=>$userid,
                                    'size'=> $fileSize, 'filetype'=>$mimetype);
				$documentId = $this->createDocument($docInfo);

				// Link file attached to document
				if(!empty($documentId) && !empty($attachid)) {
					$this->relateAttachment($documentId, $attachid);
				}

				// Link document to base record
				if(!empty($basefocus->id) && !empty($documentId)) {
					$this->relatedDocument($basefocus->id, $documentId);
				}

				// Link file attached to emails also, for it to appear on email's page
				if(!empty($basefocus->id) && !empty($attachid)) {
					$this->relateAttachment($basefocus->id, $attachid);
				}
			}
		}
	}

    /**
     * Creates a Document
     * @global Users $current_user
     * @param Array $info
     * @return Integer
     */
	function createDocument($info) {
		global $current_user;
		$handler = vtws_getModuleHandlerFromName('Documents', $current_user);
		$meta = $handler->getMeta();
		if ($meta->hasWriteAccess() != true) {
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
     * @global Users $current_user
     * @param MailManager_Model_Message $mailrecord
     * @param Integer $linkto
     * @return Array
     */
	static function associate($mailrecord, $linkto) {
		global $current_user;
		$instance = new self();

		$modulename = getSalesEntityType($linkto);
		$linkfocus = CRMEntity::getInstance($modulename);
		$linkfocus->retrieve_entity_info($linkto, $modulename);
		$linkfocus->id = $linkto;

		$emailid = $instance->__CreateNewEmail($mailrecord, $modulename, $linkfocus);

		if (!empty($emailid)) {
			MailManager::updateMailAssociation($mailrecord->uniqueid(), $emailid, $linkfocus->id);
		}

		$name = getEntityName($modulename, $linkto);
		$detailInformation =  self::buildDetailViewLink($modulename, $linkfocus->id, $name[$linkto]);
		return $detailInformation;
	}

    /**
     * Returns the information about the Parent 
     * @param String $module
     * @param Integer $record
     * @param String $label
     * @return Array
     */
	static function buildDetailViewLink($module, $record, $label) {
		$detailViewLink = sprintf("<a target='_blank' href='index.php?module=%s&action=DetailView&record=%s'>%s</a>",
                $module, $record, textlength_check($label));
		return array('record'=>$record, 'module'=>$module, 'label'=>$label, 'detailviewlink'=> $detailViewLink);
	}

    /**
     * Returns the related entity for a Mail
     * @global PearDataBase $adb
     * @param integer $mailuid - Mail Number
     * @return Array
     */
	static function associatedLink($mailuid) {
		global $adb;

		$info = MailManager::lookupMailAssociation($mailuid);
		if ($info) {
			return self::getSalesEntityInfo($info['crmid']);
		}
		return false;
	}

    /**
     * Returns the information about the Parent
     * @global PearDataBase $adb
     * @param Integer $crmid
     * @return Array
     */
	static function getSalesEntityInfo($crmid){
		global $adb;
		$result = $adb->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid=? AND deleted=0", array($crmid));
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
	static function ws_modulename($modulewsid) {
		global $adb;
		$result = $adb->pquery("SELECT name FROM vtiger_ws_entity WHERE id=?", array($modulewsid));
		if ($adb->num_rows($result)) return $adb->query_result($result, 0, 'name');
		return false;
	}

    /**
     * Related an attachment to a Email record
     * @global PearDataBase $adb
     * @param Integer $crmId
     * @param Integer $attachId
     */
	function relateAttachment($crmId, $attachId) {
		global $adb;
		$adb->pquery("INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",
			array($crmId, $attachId));
	}

    /**
     * Related a Document to a record
     * @global PearDataBase $adb
     * @param Integer $crmId
     * @param Integer $docId
     */
	function relatedDocument($crmId, $docId) {
		global $adb;
		$adb->pquery("INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)",
					Array($crmId, $docId));
	}
}
?>