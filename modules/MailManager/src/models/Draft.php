<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'data/CRMEntity.php';
include_once 'include/Webservices/Query.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
include_once 'modules/MailManager/config.inc.php';
include_once 'modules/MailManager/src/controllers/UploadController.php';
include_once __DIR__ .'/DraftFolder.php';

class MailManager_Model_DraftEmail {

	public static $totalDraftCount;

	public static function getInstance() {
		return new self();
	}

	public function folderInstance() {
		return new MailManager_Model_DraftFolder();
	}

	public function searchDraftMails($q, $type, $page, $limit, $folder) {
		if ($type == 'all') {
			$where = $this->constructAllClause($q);
		} else {
			$where = $type ." LIKE '%". $q ."%'" ;
		}
		$where = " AND ".$where;
		return $this->getDrafts($page, $limit, $folder, $where);
	}

	public function constructAllClause($query) {
		$fields = array('bccmail','ccmail','subject','saved_toid','description');
		for ($i=0; $i<count($fields); $i++) {
			if ($i == count($fields)-1) {
				$clause .=  $fields[$i]." LIKE '%".$query."%'";
			} else {
				$clause .=  $fields[$i]." LIKE '%".$query."%' OR ";
			}
		}
		return $clause;
	}

	public function getDrafts($page, $limit, $folder, $where = null) {
		global $current_user;
		$handler = vtws_getModuleHandlerFromName('Emails', $current_user);
		$meta = $handler->getMeta();
		if (!$meta->hasReadAccess()) {
			return false;
		}

		if (!empty($page)) {
			$limitClause = 'LIMIT '.($limit*$page).', '.$limit;
		} else {
			$limitClause = 'LIMIT 0, '.$limit;
		}
		$query = "SELECT * FROM Emails where email_flag='SAVED' $where $limitClause;";
		$draftMails = vtws_query($query, $current_user);
		for ($i=0; $i<count($draftMails); $i++) {
			foreach ($draftMails[$i] as $fieldname => $fieldvalue) {
				if ($fieldname == 'saved_toid' || $fieldname == 'ccmail' || $fieldname == 'bccmail') {
					if (!empty($fieldvalue)) {
						$value = implode(',', json_decode($fieldvalue, true));
						if (strlen($value) > 45) {
							$value = substr($value, 0, 45).'....';
						}
						$draftMails[$i][$fieldname] = $value;
					}
				} elseif ($fieldname == 'id') {
					$emailId = vtws_getIdComponents($fieldvalue);
					$draftMails[$i][$fieldname] = $emailId[1];
				}
			}
		}
		if ($where) {
			$folder->setPaging($limit*$page+1, $limit*$page+$limit, $limit, count($draftMails), $page);
		} else {
			$total = $this->getTotalDraftCount();
			$folder->setPaging($limit*$page+1, $limit*$page+$limit, $limit, $total, $page);
		}
		$folder->setMails($draftMails);

		return $draftMails ;
	}

	public function getTotalDraftCount() {
		global $current_user;
		if (empty(self::$totalDraftCount)) {
			$draftMails = vtws_query("SELECT * FROM Emails where email_flag='SAVED';", $current_user);
			self::$totalDraftCount = count($draftMails);
			return self::$totalDraftCount;
		} else {
			return self::$totalDraftCount;
		}
	}

	public function getDraftMail($request) {
		global $current_user;
		$handler = vtws_getModuleHandlerFromName('Emails', $current_user);
		$meta = $handler->getMeta();
		if (!$meta->hasReadAccess()) {
			return false;
		}
		$id = vtws_getWebserviceEntityId('Emails', $request->get('id'));
		$draftMail = vtws_query("SELECT * FROM Emails where id = $id;", $current_user);
		$emailId = vtws_getIdComponents($id);
		$draftMail['attachments'] = $this->getAttachmentDetails($emailId[1]);
		$draftMail[0]['id'] = $request->get('id');
		return $draftMail;
	}

	public function getAttachmentDetails($crmid) {
		global $adb;
		// FIX: Validate the incoming ID
		if (empty($crmid)) {
			return false;
		}
		$documentRes = $adb->pquery(
			'SELECT *
				FROM vtiger_senotesrel
				INNER JOIN vtiger_crmentity ON vtiger_senotesrel.notesid = vtiger_crmentity.crmid AND vtiger_senotesrel.crmid = ?
				INNER JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_senotesrel.notesid
				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
				INNER JOIN vtiger_attachments ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
				WHERE vtiger_crmentity.deleted = 0',
			array($crmid)
		);
		$draftMail = array();
		if ($adb->num_rows($documentRes)) {
			for ($i=0; $i<$adb->num_rows($documentRes); $i++) {
				$draftMail[$i]['name'] = $adb->query_result($documentRes, $i, 'filename');
				$filesize = $adb->query_result($documentRes, $i, 'filesize');
				$draftMail[$i]['size'] = $this->getFormattedFileSize($filesize);
				$draftMail[$i]['docid'] = $adb->query_result($documentRes, $i, 'notesid');
				$draftMail[$i]['path'] = $adb->query_result($documentRes, $i, 'path');
				$draftMail[$i]['fileid'] = $adb->query_result($documentRes, $i, 'attachmentsid');
				$draftMail[$i]['attachment'] = $adb->query_result($documentRes, $i, 'name');
			}
		}
		return $draftMail;
	}

	public function saveDraft($request) {
		global $current_user;

		if (!MailManager::checkModuleWriteAccessForCurrentUser('Emails')) {
			return false;
		}

		$email = CRMEntity::getInstance('Emails');

		$to_string = rtrim($request->get('to'), ',');
		$cc_string = rtrim($request->get('cc'), ',');
		$bcc_string= rtrim($request->get('bcc'), ',');

		$parentIds = $this->getParentFromEmails($to_string);

		$emailId = $request->get('emailid');
		$subject = $request->get('subject');

		$email = CRMEntity::getInstance('Emails');
		$email->column_fields['assigned_user_id'] = $current_user->id;
		$email->column_fields['date_start'] = date('Y-m-d');
		$email->column_fields['time_start'] = date('H:i');
		$email->column_fields['parent_id'] = $parentIds;
		$email->column_fields['subject'] =  (!empty($subject)) ? $subject : 'No Subject';
		$email->column_fields['description'] = $request->get('body');
		$email->column_fields['activitytype'] = 'Emails';
		$email->column_fields['from_email'] = $this->getFromEmailAddress();
		$email->column_fields['saved_toid'] = (!empty($to_string)) ? $to_string : 'SAVED';
		$email->column_fields['ccmail'] = $cc_string;
		$email->column_fields['bccmail'] = $bcc_string;
		$email->column_fields['email_flag'] = 'SAVED';

		if (empty($emailId)) {
			$email->save('Emails');
		} else {
			$email->id = $emailId;
			$email->mode = 'edit';
			$email->save('Emails');
		}
		//save parent and email relation, to show up in Emails section of the parent
		$this->saveEmailParentRel($email->id, $parentIds);

		return $email->id;
	}

	public function saveEmailParentRel($emailId, $parentIds) {
		global $adb;
		$myids = explode('|', $parentIds);  //2@71|
		if (!empty($emailId)) {
			$adb->pquery('delete from vtiger_seactivityrel where activityid=?', array($emailId)); //remove all previous relation
		}
		for ($i=0; $i<(count($myids)); $i++) {
			$realid = explode('@', $myids[$i]);
			if (!empty($realid[0]) && !empty($emailId)) {
				// this is needed as we might save the mail in draft mode earlier
				$result = $adb->pquery('SELECT * FROM vtiger_seactivityrel WHERE crmid=? AND activityid=?', array($realid[0], $emailId));
				if (!$adb->num_rows($result)) {
					$adb->pquery('INSERT INTO vtiger_seactivityrel(crmid, activityid) VALUES(?,?)', array($realid[0], $emailId));
				}
			}
		}
	}

	public function getFromEmailAddress() {
		global $adb, $current_user;
		$fromEmail = false;
		$smtpFromResult = $adb->pquery('SELECT from_email_field FROM vtiger_systems WHERE server_type=?', array('email'));
		if ($smtpFromResult && $adb->num_rows($smtpFromResult)) {
			$fromEmail = decode_html($adb->query_result($smtpFromResult, 0, 'from_email_field'));
		}
		if (empty($fromEmail)) {
			$fromEmail = $current_user->column_fields['email1'];
		}
		return $fromEmail;
	}

	public function saveAttachment($request) {
		//need to handle earlier as Emails save will save the uploaded files from $_FILES
		$uploadResponse = $this->handleUpload();

		$emailId = $this->saveDraft($request);

		if ($emailId != false) {
			if ($uploadResponse && $uploadResponse['success'] == true) {
				// Link document to base record
				if (!empty($uploadResponse['docid'])) {
					$this->saveEmailDocumentRel($emailId, $uploadResponse['docid']);
				}
				if (!empty($uploadResponse['attachid'])) {
					$this->saveAttachmentRel($emailId, $uploadResponse['attachid']);
				}
			}
			$uploadResponse['emailid'] = $emailId;
		} else {
			$uploadResponse['error'] = true;
		}
		return $uploadResponse;
	}

	public function getParentFromEmails($to_string) {
		global $current_user;
		$parentIds = '';
		if (!empty($to_string)) {
			$toArray = explode(',', $to_string);
			foreach ($toArray as $to) {
				$relatedtos = MailManager::lookupMailInVtiger(trim($to), $current_user);
				if (!empty($relatedtos) && is_array($relatedtos)) {
					for ($i=0; $i<count($relatedtos); $i++) {
							$relateto = vtws_getIdComponents($relatedtos[$i]['record']);
							$parentIds .= $relateto[1].'@1|';
					}
				}
			}
		}
		return $parentIds;
	}

	public function handleUpload() {
		$allowedFileExtension = array();
		$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000, 'Emails');
		$uploadLimit = ConfigPrefs::get('MAXUPLOADLIMIT', $upload_maxsize);
		$filePath = decideFilePath();

		$upload = new MailManager_Uploader($allowedFileExtension, $uploadLimit);

		return $upload->handleUpload($filePath, false);
	}

	public function saveEmailDocumentRel($emailId, $documentId) {
		global $adb;
		if (!empty($emailId) && !empty($documentId)) {
			$adb->pquery(
				'INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)',
				array($emailId, $documentId)
			);
		}
	}

	public function saveAttachmentRel($crmid, $attachId) {
		global $adb;
		if (!empty($crmid) && !empty($attachId)) {
			$adb->pquery(
				'INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)',
				array($crmid, $attachId)
			);
		}
	}

	public function deleteMail($ids) {
		$focus = CRMEntity::getInstance('Emails');
		$idList = explode(',', $ids);
		foreach ($idList as $id) {
			$focus->trash('Emails', $id);
		}
	}

	public function deleteAttachment($request) {
		global $adb;
		$emailid = $request->get('emailid');
		$docid = $request->get('docid');
		if (!empty($docid) && !empty($emailid)) {
			$adb->pquery('DELETE FROM vtiger_senotesrel WHERE crmid = ? AND notesid = ?', array($emailid, $docid));
			return true;
		}
		return false;
	}

	public function getFormattedFileSize($filesize) {
		if ($filesize < 1024) {
			$filesize = sprintf('%0.2f', round($filesize, 2)).'b';
		} elseif ($filesize > 1024 && $filesize < 1048576) {
			$filesize = sprintf('%0.2f', round($filesize/1024, 2)).'kB';
		} elseif ($filesize > 1048576) {
			$filesize = sprintf('%0.2f', round($filesize/(1024*1024), 2)).'MB';
		}
		return $filesize;
	}
}
?>
