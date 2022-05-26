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
require_once 'config.inc.php';
require_once 'include/utils/CommonUtils.php';
require_once 'modules/Messages/Messages.php';
require_once 'modules/Emails/mail.php';
include_once 'include/Webservices/VtigerEmailOperation.php';
include_once 'include/integrations/sendgrid/sendgrid.php';
require_once 'data/CRMEntity.php';

class VTEmailTask extends VTTask {
	// Sending email takes more time, this should be handled via queue all the time.
	public $executeImmediately = false;
	public $attachmentsinfo = array();
	public $dzattinfo = array();
	private $entityrecord = 0;
	private $entityid = 0;
	private $entitymodule = '';
	private $entityinfo = '';

	public function getFieldNames() {
		return array('fromname', 'fromemail', 'replyto', 'subject', 'content', 'recepient', 'emailcc', 'emailbcc', 'attfieldnames', 'attachmentids', 'attach_icalendar', 'cbmsgtemplate', 'cbmsgtemplate_display', 'sgmsgtemplate', 'linktemplate');
	}
	public function after_retrieve() {
		global $adb, $current_user;
		$this->attachmentsinfo = array();
		$this->dzattinfo = array();
		if (!empty($this->attachmentids)) {
			$GenDocActive = vtlib_isModuleActive('evvtgendoc');
			include_once 'modules/evvtgendoc/OpenDocument.php';
			$GenDocPDF = ($GenDocActive && OpenDocument::PDFConversionActive());
			$aids = explode(',', trim($this->attachmentids, ','));
			$query = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid, vtiger_notes.filename, vtiger_notes.filesize,
					vtiger_notes.template, vtiger_notes.template_for
				FROM vtiger_attachments
				INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
				INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
				INNER JOIN vtiger_notes ON vtiger_notes.notesid=vtiger_seattachmentsrel.crmid
				WHERE deleted=0 AND vtiger_seattachmentsrel.crmid = ?';
			foreach ($aids as $docid) {
				$docrs = $adb->pquery($query, array($docid));
				if ($docrs && $adb->num_rows($docrs)==1) {
					$istemplate = $adb->query_result($docrs, 0, 'template');
					$istemplatefor = $adb->query_result($docrs, 0, 'template_for');
					if ($GenDocActive && $this->entityrecord && $istemplate=='1' && $istemplatefor==$this->entitymodule) {
						$prevdocid = $docid;
						$format = ($GenDocPDF ? 'pdf' : 'odt');
						$docfname = OpenDocument::doGenDocMerge($this->entityid, $docid, $format);
						$mergeTemplateName = $adb->query_result($docrs, 0, 'filename');
						if (GlobalVariable::getVariable('Workflow_Email_GenDoc_Attachment_Name', 'Name', $this->entitymodule)=='Number') {
							$numfld = getModuleSequenceField($this->entitymodule);
							if (is_null($numfld)) {
								$name = str_replace(' ', '_', $this->entityinfo[$this->entityid]);
							} else {
								$queryGenerator = new QueryGenerator($this->entitymodule, $current_user);
								$queryGenerator->setFields(array($numfld['name']));
								$queryGenerator->addCondition('id', $this->entityid, 'e');
								$nfq = $queryGenerator->getQuery();
								$rsnf = $adb->query($nfq);
								$name = str_replace(' ', '_', $rsnf->fields[$numfld['name']]);
							}
						} else {
							$name = str_replace(' ', '_', $this->entityinfo[$this->entityid]);
						}
						$docid = OpenDocument::saveAsDocument($this->entityid, $this->entitymodule, $format, $mergeTemplateName, $docfname, $name);
						$docrs = $adb->pquery($query, array($docid));
						$this->attachmentids = str_replace($prevdocid, $docid, $this->attachmentids);
					}
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
		global $logbg,$adb;
		$util = new VTWorkflowUtils();

		$admin = $util->adminUser();
		$module = $entity->getModuleName();
		$this->entityrecord = $entity->getId();
		list($void, $this->entityid) = explode('x', $this->entityrecord);
		$this->entitymodule = $module;
		$this->entityinfo = getEntityName($module, $this->entityid);

		$entityCache = new VTEntityCache($admin);

		$from_name = $from_email = $replyto = '';
		if (!empty($this->fromname)) {
			$fnt = new VTEmailRecipientsTemplate($this->fromname);
			$from_name = $fnt->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		}
		if (!empty($this->fromemail)) {
			$fet = new VTEmailRecipientsTemplate($this->fromemail);
			$from_email = $fet->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		}
		if (!empty($this->replyto)) {
			$rt = new VTEmailRecipientsTemplate($this->replyto);
			$replyto = $rt->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		}
		if (isset($entity->WorkflowContext['SendEmailTo'])) {
			$et = new VTEmailRecipientsTemplate($entity->WorkflowContext['SendEmailTo']);
		} else {
			$et = new VTEmailRecipientsTemplate($this->recepient);
		}
		$to_email = $et->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		if (empty($this->emailcc)) {
			$cc = '';
		} else {
			$ecct = new VTEmailRecipientsTemplate($this->emailcc);
			$cc = $ecct->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		}
		if (empty($this->emailbcc)) {
			$bcc = '';
		} else {
			$ebcct = new VTEmailRecipientsTemplate($this->emailbcc);
			$bcc = $ebcct->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
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
		$to_email = trim(preg_replace('/,,+/', ',', $to_email), ',');
		$cc = trim(preg_replace('/,,+/', ',', $cc), ',');
		$bcc = preg_replace('/\s+/', '', $bcc);
		$bcc = trim(preg_replace('/,,+/', ',', $bcc), ',');
		$Recipients = cbEventHandler::do_filter('corebos.filter.workflowEmail.Recipients', array($to_email, $cc, $bcc, $entity, $this));
		$to_email = $Recipients[0];
		$cc = $Recipients[1];
		$bcc = $Recipients[2];
		unset($Recipients);
		if (strlen(trim($to_email, " \t\n,")) == 0 && strlen(trim($cc, " \t\n,")) == 0 && strlen(trim($bcc, " \t\n,")) == 0) {
			$util->revertUser();
			return;
		}
		if (!empty($entity->WorkflowContext['SendThisSubject'])) {
			$this->subject = $entity->WorkflowContext['SendThisSubject'];
		}
		if (!empty($entity->WorkflowContext['SendThisBody'])) {
			$this->content = $entity->WorkflowContext['SendThisBody'];
		}
		if (!empty($entity->WorkflowContext['SendThisMsgTemplate'])) {
			if (strpos($entity->WorkflowContext['SendThisMsgTemplate'], 'x')>0) {
				list($wsid, $entity->WorkflowContext['SendThisMsgTemplate']) = explode('x', $entity->WorkflowContext['SendThisMsgTemplate']);
			}
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias('MsgTemplate');
			$mtrs = $adb->pquery(
				'select subject,template,templateonlytext
				from vtiger_msgtemplate
				inner join '.$crmEntityTable.' on msgtemplateid=vtiger_crmentity.crmid
				where vtiger_crmentity.deleted=0 and msgtemplateid=?',
				array($entity->WorkflowContext['SendThisMsgTemplate'])
			);
			if ($mtrs && $adb->num_rows($mtrs)>0) {
				if (!empty($mtrs->fields['subject'])) {
					$this->subject = $mtrs->fields['subject'];
				}
				if (!empty($mtrs->fields['template'])) {
					$this->content = $mtrs->fields['template'];
				}
				$crmEntityTable1 = CRMEntity::getcrmEntityTableAlias('Documents');
				$docrs = $adb->pquery(
					'select notesid
					from vtiger_senotesrel
					inner join '.$crmEntityTable1.' on vtiger_crmentity.crmid=vtiger_senotesrel.notesid
					where vtiger_crmentity.deleted=0 and vtiger_senotesrel.crmid=?',
					array($entity->WorkflowContext['SendThisMsgTemplate'])
				);
				while (!$docrs->EOF) {
					$doc = $docrs->FetchRow();
					if (strpos($this->attachmentids, $doc['notesid'])===false) {
						$this->attachmentids .= ($this->attachmentids=='' ? $doc['notesid'] : ','.$doc['notesid']);
					}
				}
			}
		}
		if (!empty($this->linktemplate) && !empty($this->cbmsgtemplate)) {
			$mail_templateid = $this->cbmsgtemplate;
			list($tabid,$record_id) = explode('x', $entity->getId());
			$msgtemp_res = $adb->pquery(
				'select subject,template,templateonlytext
				from vtiger_msgtemplate
				inner join vtiger_crmentity on msgtemplateid=crmid
				where deleted=0 and msgtemplateid=?',
				array($mail_templateid)
			);
			if ($msgtemp_res && $adb->num_rows($msgtemp_res) > 0) {
				if (!empty($msgtemp_res->fields['subject'])) {
					$this->subject = $msgtemp_res->fields['subject'];
				}
				if (!empty($msgtemp_res->fields['template'])) {
					$this->content = $msgtemp_res->fields['template'];
				}
				$this->subject = getMergedDescription($this->subject, $record_id, $entity->moduleName, $entity->WorkflowContext);
				$this->content = getMergedDescription($this->content, $record_id, $entity->moduleName, $entity->WorkflowContext);
				$mailtemp_docrs = $adb->pquery(
					'select notesid
					from vtiger_senotesrel
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_senotesrel.notesid
					where vtiger_crmentity.deleted=0 and vtiger_senotesrel.crmid=?',
					array($mail_templateid)
				);
				while (!$mailtemp_docrs->EOF) {
					$doc = $mailtemp_docrs->FetchRow();
					if (strpos($this->attachmentids, $doc['notesid'])===false) {
						$this->attachmentids .= ($this->attachmentids=='' ? $doc['notesid'] : ','.$doc['notesid']);
					}
				}
			}
		}
		$ids = array();
		if (!empty($entity->WorkflowContext['MergeTemplateWith'])) {
			$ids = explode(',', $entity->WorkflowContext['MergeTemplateWith']);
		}
		foreach ($ids as $id) {
			if (strpos($id, 'x')>0) {
				list($wsid, $id) = explode('x', $id);
				$module = vtws_getEntityName($wsid);
			} else {
				$module = getSalesEntityType($id);
			}
			$this->subject = getMergedDescription($this->subject, $id, $module, $entity->WorkflowContext);
			$this->content = getMergedDescription($this->content, $id, $module, $entity->WorkflowContext);
		}
		$this->after_retrieve();
		if (!empty($entity->WorkflowContext['wfgenerated_file'])) {
			$sendifempty = GlobalVariable::getVariable('Report_Send_Scheduled_ifEmpty', 1);
			if ($sendifempty) {
				for ($y=0; $y < count($entity->WorkflowContext['wfgenerated_file']); $y++) {
					$this->attachmentsinfo[] = $entity->WorkflowContext['wfgenerated_file'][$y]['path'].$entity->WorkflowContext['wfgenerated_file'][$y]['name'];
				}
			} else {
				for ($y=0; $y < count($entity->WorkflowContext['wfgenerated_file']); $y++) {
					if ($entity->WorkflowContext['wfgenerated_file'][$y]['hascontent']) {
						$this->attachmentsinfo[] = $entity->WorkflowContext['wfgenerated_file'][$y]['path'].$entity->WorkflowContext['wfgenerated_file'][$y]['name'];
					}
				}
				if (empty($this->attachmentsinfo)) {
					return;
				}
			}
		}
		if (!empty($this->attfieldnames)) {
			if (substr($this->attfieldnames, -24) == ' : (Documents) filename)') {
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
				$pathattfield = $attfield->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
				$this->attachmentsinfo[] = $pathattfield;
			}
		}
		if (!empty($this->attach_icalendar) && $this->attach_icalendar == 'on' && $entity->moduleName == 'cbCalendar') {
			list($tabid,$recordid) = explode('x', $entity->getId());
			if (isRecordExists($recordid)) {
				$cbCalendar = CRMEntity::getInstance('cbCalendar');
				$cbCalendar->retrieve_entity_info($recordid, 'cbCalendar');
				$icalfilename = $cbCalendar->getiCalendar($cbCalendar);
				if (!empty($icalfilename)) {
					$this->attachmentsinfo[] = $icalfilename;
				}
			}
		}
		$st = new VTSimpleTemplate($this->subject);
		$subject = $st->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		$ct = new VTSimpleTemplate($this->content);
		$content = $ct->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
		$logo = (strpos($this->content, '(general : (__VtigerMeta__) logo)')===false) ? '' : '1';
		$qrScan = (strpos($this->content, '(general : (__VtigerMeta__) scanQRCode')===false) ? '' : '1';
		$brScan = (strpos($this->content, '(general : (__VtigerMeta__) scanBarCode')===false) ? '' : '1';
		$element = array();
		$element['saved_toid'] = $to_email;
		$element['parent_type'] = $module;
		$element['parent_id'] = $entity->getId();
		$element['from_email'] = $from_email;
		$element['replyto'] = $replyto;
		$element['ccmail'] = $cc;
		$element['bccmail'] = $bcc;
		$element['subject'] = $subject;
		$element['description'] = $content;
		$emailid = $this->createEmailRecord($element);
		//Create a Message to register this email
		$crmid = $this->createMessage($this->entityid, $module, $subject, $this->summary);
		if ($crmid=='') {
			list($wsid, $crmid) = explode('x', $emailid);
		}
		if (!empty($this->linktemplate) && !empty($this->sgmsgtemplate)) {
			include_once 'include/integrations/sendgrid/sendgrid.php';
			$sdInstance = new corebos_sendgrid();
			try {
				$res = $sdInstance->sendemailtemplate($this->sgmsgtemplate, $entity->getId(), $entity->moduleName, $to_email, $subject, $this->attachmentsinfo, $from_name, $from_email, $cc, $bcc, $replyto);
				$logbg->debug(
					'SG template mail was sent',
					[
						'To' => $to_email,
						'FromName' => $from_name,
						'FromEmail' => $from_email,
						'CC' => $cc,
						'BCC' => $bcc,
						'Result' => $res,
						'AttInfo' => $this->attachmentsinfo,
					]
				);
			} catch (Exception $e) {
				$logbg->debug('SG template mail failed: '. $e->getMessage());
			}
			return;
		}
		$rdo = send_mail($module, $to_email, $from_name, $from_email, $subject, $content, $cc, $bcc, $this->attachmentsinfo, $crmid, $logo, $replyto, $qrScan, $brScan);
		$logbg->debug(
			'EmailWFTask',
			[
				'To' => $to_email,
				'FromName' => $from_name,
				'FromEmail' => $from_email,
				'CC' => $cc,
				'BCC' => $bcc,
				'Result' => $rdo,
				'AttInfo' => $this->attachmentsinfo,
			]
		);
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

	private function createMessage($to_id, $to_module, $to_name, $desc_name, $templateid = 0) {
		global $adb, $current_user;

		$focus = new Messages();

		$focus->column_fields['messagename'] = $to_name;
		$focus->column_fields['messagesrelatedto'] = $to_id;
		$no_message = false;
		switch ($to_module) {
			case 'Accounts':
				$focus->column_fields['account_message'] = $to_id;
				break;
			case 'Contacts':
				$focus->column_fields['contact_message'] = $to_id;
				break;
			case 'Leads':
				$focus->column_fields['lead_message'] = $to_id;
				break;
			case 'Potentials':
				$result = $adb->pquery('SELECT potential_no,related_to FROM vtiger_potential WHERE potentialid=?', array($to_id));
				$related_to = $adb->query_result($result, 0, 'related_to');
				$potential_no = $adb->query_result($result, 0, 'potential_no');
				$desc_name .= ' '.getTranslatedString('LBL_FROM', 'MailManager').' '.getTranslatedString('SINGLE_Potentials', 'Potentials').' '.$potential_no;
				if (getSalesEntityType($related_to) == 'Accounts') {
					$focus->column_fields['account_message'] = $related_to;
				} elseif (getSalesEntityType($related_to) == 'Contacts') {
					$focus->column_fields['contact_message'] = $related_to;
				}
				break;
			default:
				$no_message = true;
				break;
		}
		if (!$no_message) {
			$focus->column_fields['messagetype'] = 'email';
			if ($templateid != 0) {
				$focus->column_fields['email_tplid'] = $templateid;
				$etinfo = getEntityName('MsgTemplate', array($templateid));
				$focus->column_fields['description'] = $etinfo[$templateid];
			} else {
				$focus->column_fields['description'] = "\n".getTranslatedString('Workflow', 'com_vtiger_workflow').' '.getTranslatedString('Task').': '.$desc_name."\n\n";
			}
			$focus->column_fields['assigned_user_id'] = $current_user->id;
			$focus->mode = '';
			$focus->save('Messages');
			$message_id = $focus->id;
		} else {
			$message_id = '';
		}
		return $message_id;
	}
}
?>
