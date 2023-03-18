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
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/SMSNotifier/SMSNotifier.php';

class VTSMSTask extends VTTask {
	public $executeImmediately = true;
	public $content;
	public $sms_recepient;

	public function getFieldNames() {
		return array('content', 'sms_recepient');
	}

	public function doTask(&$entity) {
		global $logbg;

		$logbg->debug('> SMSTask');
		if (SMSNotifier::checkServer()) {
			global $current_user;
			$util = new VTWorkflowUtils();
			$admin = $util->adminUser();
			$ws_id = $entity->getId();
			$entityCache = new VTEntityCache($admin);
			$et = new VTSimpleTemplate($this->sms_recepient);
			$recepient = $et->render($entityCache, $ws_id, [], $entity->WorkflowContext);
			$recepients = explode(',', $recepient);
			$ct = new VTSimpleTemplate($this->content);
			$content = $ct->render($entityCache, $ws_id, [], $entity->WorkflowContext);
			$relatedCRMid = substr($ws_id, stripos($ws_id, 'x')+1);
			$relatedModule = $entity->getModuleName();
			/** Pickup only non-empty numbers */
			$tonumbers = array();
			foreach ($recepients as $tonumber) {
				if (!empty($tonumber)) {
					$tonumbers[] = $tonumber;
				}
			}
			if (!empty($tonumbers)) {
				$inBucketServeUrl = GlobalVariable::getVariable('Debug_Email_Send_To_Inbucket', '');
				if (!empty($inBucketServeUrl)) {
					require_once 'modules/Emails/mail.php';
					require_once 'modules/Emails/Emails.php';
					$logmsg = '(SMSTask) sending email to inbucket';
					$this->logmessages[] = $logmsg;
					$logbg->debug($logmsg);
					return send_mail('Email', 'sms@notification.tld', 'corebos inbucket', 'corebos@inbucket.tld', $tonumbers, $content);
				} else {
					$logmsg = '(SMSTask) sending sms '.$tonumbers.' with '.$relatedCRMid;
					SMSNotifier::sendsms($content, $tonumbers, $current_user->id, $relatedCRMid, $relatedModule);
				}
			} else {
				$logmsg = '(SMSTask) not called: there are no phone numbers';
			}
			$util->revertUser();
		} else {
			$logmsg = '(SMSTask) not called: no service is active';
		}
		$this->logmessages[] = $logmsg;
		$logbg->debug($logmsg);
		$logbg->debug('< SMSTask');
	}
}
?>