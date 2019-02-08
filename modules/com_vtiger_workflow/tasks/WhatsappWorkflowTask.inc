<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'include/integrations/whatsapp/whatsapp.php';

class WhatsappWorkflowTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('messageBody', 'attachmentids', 'phone');
	}

	public function doTask(&$entityData) {
		$moduleName = $entityData->getModuleName();
		$data = $entityData->getData();
		$clientclass = new corebos_whatsapp();
		$client = $clientclass->whatsappclient;
		$sender = $clientclass->senderphone;
		$body = $this->messageBody;
		$phone = $data[$this->phone];

		if ($sender!='') {
			$message=$client->messages->create(
				'whatsapp:'.$phone,
				array(
					'from' => 'whatsapp:'.$sender,
					'body' => $body
				)
			);

			$messageid = $message->sid;
			include_once 'modules/Messages/Messages.php';
			Messages::createMessage(array(
				'datenametext' => $phone,
				'type' => 'Whatsapp',
				'uniqueid' => $messageid,
				'description' => $body,
			));
		}
	}
}
?>