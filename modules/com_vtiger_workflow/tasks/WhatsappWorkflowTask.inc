<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
use Twilio\Rest\Client;

include_once 'include/integrations/whatsapp/whatsapp.php';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'vendor/autoload.php';

class WhatsappWorkflowTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;
	public $attachmentsinfo = array();
	public $dzattinfo = array();

	public function getFieldNames() {
		return array('messageBody', 'attachmentids', 'phone');
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
	public function doTask(&$entityData) {
		global $current_user, $site_URL, $logbg;
		$clientclass = new corebos_whatsapp();
		$client = $clientclass->whatsappclient;
		$isactive = $clientclass->isActive();
		$sender = $clientclass->senderphone;
		if ($isactive) {
			$entityCache = new VTEntityCache($current_user);
			$ws_id = $entityData->getId();
			$pt = new VTSimpleTemplate($this->messageBody);
			$body = str_replace(
				['<p>','</p>','<strong>','</strong>','<em>','</em>'],
				['','','*','*','_','_'],
				$pt->render($entityCache, $ws_id, [], $entityData->WorkflowContext)
			);
			$phonest = new VTSimpleTemplate($this->phone);
			$this->after_retrieve();
			$count = count($this->attachmentsinfo);
			if ($count>0) {
				$url = $this->attachmentsinfo;
				$attachment = explode(',', $this->attachmentids);
			} else {
				$url = '';
				$attachment = '';
			}
			$messageid = array();
			$phones = $phonest->render($entityCache, $ws_id, [], $entityData->WorkflowContext);
			$phones = trim($phones, ',');
			try {
				if ($sender!='' && $phones!='') {
					$pa = explode(',', $phones);
					foreach ($pa as $phone) {
						$phone = trim($phone);
						if ($phone=='') {
							continue;
						}
						if (substr($phone, 0, 2) == '00') {
							$phone = '+'.substr($phone, 2, strlen($phone));
						}
						if (substr($phone, 0, 1) == '+') {
							$prefix = '+';
						} else {
							$prefix = '';
						}
						$phone = $prefix.preg_replace('/[^0-9]/', '', $phone);
						if ($count==0) {
							$message=$client->messages->create(
								'whatsapp:'.$phone,
								array(
								'from' => 'whatsapp:'.$sender,
								'body' => $body
								)
							);
							$messageid[0]['messageid'] = $message->sid;
							$messageid[0]['body'] = $body;
							$messageid[0]['attachment'] = '';
						} else {
							if ($body!='') {
								$message=$client->messages->create(
									'whatsapp:'.$phone,
									array(
									'from' => 'whatsapp:'.$sender,
									'body' => $body
									)
								);
							}
							$messageid[0]['messageid'] = $message->sid;
							$messageid[0]['body'] = $body;
							$messageid[0]['attachment'] = '';
							for ($j=0; $j<$count; $j++) {
								$message = $client->messages->create(
									'whatsapp:'.$phone,
									array(
										'from' => 'whatsapp:'.$sender,
										'mediaurl' => "$site_URL/".$url[$j],
									)
								);
								$messageid[$j+1]['messageid'] = $message->sid;
								$messageid[$j+1]['body'] = '';
								$messageid[$j+1]['attachment'] = $attachment[$j];
							}
						}
						include_once 'modules/Messages/Messages.php';
						$cnt = count($messageid);
						for ($i=0; $i<$cnt; $i++) {
							Messages::createMessage(array(
								'datenametext' => $phone,
								'type' => 'Whatsapp',
								'uniqueid' => $messageid[$i]['messageid'],
								'description' => $messageid[$i]['body'],
								'attachment' => $messageid[$i]['attachment']
							));
						}
					}
				}
			} catch (Exception $e) {
				$logbg->debug('Error on Whatsapp Task: '. $e->getMessage());
			}
		}
	}
}
?>