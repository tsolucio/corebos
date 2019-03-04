<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTEmailRecipientsTemplate.inc';
require_once 'modules/Emails/mail.php';
include_once 'include/Webservices/VtigerEmailOperation.php';

class VTEmailTask extends VTTask {
	// Sending email takes more time, this should be handled via queue all the time.
	public $executeImmediately = false;
	public $attachmentsinfo = array();
	public $dzattinfo = array();

	public function getFieldNames() {
		return array('fromname', 'fromemail', 'replyto', 'subject', 'content', 'recepient', 'emailcc', 'emailbcc', 'attfieldnames', 'attachmentids');
	}
	public function after_retrieve() {
		global $adb;
		$this->attachmentsinfo = array();
		$this->dzattinfo = array();
		if (!empty($this->attachmentids)) {
			$aids = explode(',', trim($this->attachmentids, ','));
			$query = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid, vtiger_notes.filename, vtiger_notes.filesize
				FROM vtiger_attachments
				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
				INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
				INNER JOIN vtiger_notes ON vtiger_notes.notesid=vtiger_seattachmentsrel.crmid
				WHERE deleted=0 AND vtiger_seattachmentsrel.crmid = ?';
			foreach ($aids as $docid) {
				$docrs = $adb->pquery($query, array($docid));
				if ($docrs && $adb->num_rows($docrs)==1) {
					$attname = $adb->query_result($docrs, 0, 'path').$adb->query_result($docrs, 0, 'attachmentsid').'_'.$adb->query_result($docrs, 0, 'name');
					$this->attachmentsinfo[] = $attname;
					$this->dzattinfo[] = array(
						'docid' => $docid,
						'name' => $adb->query_result($docrs, 0, 'filename'),
						'size' => $adb->query_result($docrs, 0, 'filesize'),
					);
				} else {
					$this->attachmentids = str_replace($docid.',', '', $this->attachmentids);
				}
			}
		}
	}
	public function doTask(&$entity) {
		global $logbg;
		$util = new VTWorkflowUtils();

		$admin = $util->adminUser();
		$module = $entity->getModuleName();

		$entityCache = new VTEntityCache($admin);

		$from_name = $from_email = $replyto = '';
		if (!empty($this->fromname)) {
			$fnt = new VTEmailRecipientsTemplate($this->fromname);
			$from_name = $fnt->render($entityCache, $entity->getId());
		}
		if (!empty($this->fromemail)) {
			$fet = new VTEmailRecipientsTemplate($this->fromemail);
			$from_email = $fet->render($entityCache, $entity->getId());
		}
		if (!empty($this->replyto)) {
			$rt = new VTEmailRecipientsTemplate($this->replyto);
			$replyto = $rt->render($entityCache, $entity->getId());
		}
		$et = new VTEmailRecipientsTemplate($this->recepient);
		$to_email = $et->render($entityCache, $entity->getId());
		if (empty($this->emailcc)) {
			$cc = '';
		} else {
			$ecct = new VTEmailRecipientsTemplate($this->emailcc);
			$cc = $ecct->render($entityCache, $entity->getId());
		}
		if (empty($this->emailbcc)) {
			$bcc = '';
		} else {
			$ebcct = new VTEmailRecipientsTemplate($this->emailbcc);
			$bcc = $ebcct->render($entityCache, $entity->getId());
		}
		$mailto = GlobalVariable::getVariable('Workflow_Send_Email_ToCCBCC', '');//For tests
		if ($mailto != '') {
			$to_email = $mailto;
			if (!empty($cc)) {
				$cc = $mailto;
			}
			if (!empty($bcc)) {
				$bcc = $mailto;
			}
		}
		$to_email = preg_replace('/,,+/', ',', $to_email);
		$cc = preg_replace('/,,+/', ',', $cc);
		$bcc = preg_replace('/,,+/', ',', $bcc);
		if (strlen(trim($to_email, " \t\n,")) == 0 && strlen(trim($cc, " \t\n,")) == 0 && strlen(trim($bcc, " \t\n,")) == 0) {
			return ;
		}
		if (!empty($this->attachmentsinfo)) {
			$this->after_retrieve();
		} else {
			$this->attachmentsinfo = '';
		}
		if (!empty($this->attfieldnames)) {
			if (substr($this->attfieldnames, -24) == ' : (Documents) filename)') {
				global $adb;
				$this->attfieldnames = substr($this->attfieldnames, 1, strlen($this->attfieldnames)-25);
				$rsfld = $adb->pquery(
					'select 1 from vtiger_field
					inner join vtiger_fieldmodulerel on vtiger_field.fieldid = vtiger_fieldmodulerel.fieldid
					where vtiger_fieldmodulerel.module=? and vtiger_fieldmodulerel.relmodule = ? and uitype=10 and tabid=? and fieldname=?',
					array($module,'Documents',getTabid($module),$this->attfieldnames)
				);
				if ($rsfld && $adb->num_rows($rsfld, 0, 'uitype') == 1) {
					$query = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid
						FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
						WHERE deleted=0 AND vtiger_seattachmentsrel.crmid = ?';
					$docid = $entity->get($this->attfieldnames);
					if (!empty($docid)) {
						list($void,$docid) = explode('x', $docid);
						$docrs = $adb->pquery($query, array($docid));
						if ($docrs && $adb->num_rows($docrs)==1) {
							$attname = $adb->query_result($docrs, 0, 'path').$adb->query_result($docrs, 0, 'attachmentsid').'_'.$adb->query_result($docrs, 0, 'name');
							$this->attachmentsinfo[] = $attname;
						}
					}
				}
			} else {
				$attfield = new VTSimpleTemplate('$'.$this->attfieldnames);
				$pathattfield = $attfield->render($entityCache, $entity->getId());
				$this->attachmentsinfo[] = $pathattfield;
			}
		}
		$st = new VTSimpleTemplate($this->subject);
		$subject = $st->render($entityCache, $entity->getId());
		$ct = new VTSimpleTemplate($this->content);
		$content = $ct->render($entityCache, $entity->getId());
		$logo = (strpos($this->content, '(general : (__VtigerMeta__) logo)')===false) ? '' : '1';
		$qrScan = (strpos($this->content, '(general : (__VtigerMeta__) scanQRCode')===false) ? '' : '1';
		$element['saved_toid'] = $to_email;
		$element['parent_type'] = $module;
		$element['parent_id'] = $entity->getId();
		$element['from_email'] = $from_email;
		$element['ccmail'] = $cc;
		$element['bccmail'] = $bcc;
		$element['subject'] = $subject;
		$element['description'] = $content;
		$emailid = $this->createEmailRecord($element);
		list($wsid, $crmid) = explode('x', $emailid);
		$rdo = send_mail($module, $to_email, $from_name, $from_email, $subject, $content, $cc, $bcc, $this->attachmentsinfo, $crmid, $logo, $replyto, $qrScan);
		$logbg->debug("VTEmailTask\nTo: $to_email\nFromName: $from_name\nFromEmail: $from_email\nCC: $cc\nBCC: $bcc\nResult: $rdo");
		$util->revertUser();
	}

	private function createEmailRecord($element) {
		global $adb, $log;
		$user = Users::getActiveAdminUser();
		$elementType = 'Emails';
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $elementType);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$date = new DateTimeField(null);
		$element['date_start'] = $date->getDisplayDate($user);
		$element['time_start'] = date('H:i:s');
		$element['activitytype'] = 'Emails';
		$element['assigned_user_id'] = $user->id;
		$element['email_flag'] = 'SENT';
		$result = $handler->create($elementType, $element);
		if (!empty($result['id']) && !empty($this->attachmentids)) {
			list($void, $id) = explode('x', $result['id']);
			$aids = explode(',', trim($this->attachmentids, ','));
			$query = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid
					FROM vtiger_attachments
					INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
					INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
					WHERE deleted=0 AND vtiger_seattachmentsrel.crmid = ?';
			$insrel = 'INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)';
			foreach ($aids as $docid) {
				$docrs = $adb->pquery($query, array($docid));
				if ($docrs && $adb->num_rows($docrs)==1) {
					$adb->pquery($insrel, array($id, $adb->query_result($docrs, 0, 'attachmentsid')));
				}
			}
		}
		return $result['id'];
	}
}
?>