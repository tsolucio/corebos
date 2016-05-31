<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTEmailRecipientsTemplate.inc');
require_once('modules/Emails/mail.php');
class VTEmailTask extends VTTask{
	// Sending email takes more time, this should be handled via queue all the time.
	public $executeImmediately = false;

	public function getFieldNames(){
		return array("fromname", "fromemail", "replyto", "subject", "content", "recepient", 'emailcc', 'emailbcc');
	}
	public function doTask(&$entity){
		$util = new VTWorkflowUtils();

		$admin = $util->adminUser();
		$module = $entity->getModuleName();

		$entityCache = new VTEntityCache($admin);

		$from_name = $from_email = '';
		if(!empty($this->fromname)){
			$fnt = new VTEmailRecipientsTemplate($this->fromname);
			$from_name = $fnt->render($entityCache,$entity->getId());
		}
		if(!empty($this->fromemail)){
			$fet = new VTEmailRecipientsTemplate($this->fromemail);
			$from_email = $fet->render($entityCache,$entity->getId());
		}
		if(!empty($this->replyto)){
			$rt = new VTEmailRecipientsTemplate($this->replyto);
			$replyto = $rt->render($entityCache,$entity->getId());
		}
		$et = new VTEmailRecipientsTemplate($this->recepient);
		$to_email = $et->render($entityCache, $entity->getId());
		$ecct = new VTEmailRecipientsTemplate($this->emailcc);
		$cc = $ecct->render($entityCache, $entity->getId());
		$ebcct = new VTEmailRecipientsTemplate($this->emailbcc);
		$bcc = $ebcct->render($entityCache, $entity->getId());
		$mailto = GlobalVariable::getVariable('Workflow_Send_Email_ToCCBCC','');//For tests
		if($mailto != ''){
			$to_email = $mailto;
			if(!empty($cc))$cc = $mailto;
			if(!empty($bcc))$bcc = $mailto;
		}
		$to_email = preg_replace('/,,+/', ',', $to_email);
		$cc = preg_replace('/,,+/', ',', $cc);
		$bcc = preg_replace('/,,+/', ',', $bcc);
		if(strlen(trim($to_email, " \t\n,")) == 0 && strlen(trim($cc, " \t\n,")) == 0 &&
				strlen(trim($bcc, " \t\n,")) == 0) {
			return ;
		}

		$st = new VTSimpleTemplate($this->subject);
		$subject = $st->render($entityCache, $entity->getId());
		$ct = new VTSimpleTemplate($this->content);
		$content = $ct->render($entityCache, $entity->getId());
		$logo = (strpos($this->content, '(general : (__VtigerMeta__) logo)')===false) ? '' : '1';
		send_mail($module,$to_email,$from_name,$from_email,$subject,$content, $cc, $bcc,'','',$logo,$replyto);

		$util->revertUser();
	}
}
?>