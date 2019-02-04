<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once("include/integrations/whatsapp/whatsapp.php");
class WhatsappWorkflowTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('messageBody', 'attachmentids');
	}

	public function doTask(&$entityData) {    
            $moduleName = $entityData->getModuleName();
            $data = $entityData->getData();
            $clientclass = new corebos_whatsapp();
            $client = $clientclass->whatsappclient;
            $sender = $clientclass->senderphone;
            $body = $this->messageBody;
            //will change it using default value from utilites
            $phone = $data['phone'];
            
            if($sender!=''){
            $message=$client->messages->create(
                'whatsapp:'.$phone,
                array(
                    'from' => 'whatsapp:'.$sender,
                    'body' => $body
                )
            );
           
            $messageid = $message->sid;
            include_once("modules/Messages/Messages.php");
            $msg = new Messages();
            $msg->column_fields["messagesname"] = date("Y-m-d H:i").' '.$phone;
            $msg->column_fields["messagesuniqueid"] = $messageid;
            $msg->column_fields["messagestype"] = 'Whatsapp';
            $msg->column_fields["assigned_user_id"] = '19x1';
            $msg->column_fields["description"] = $body;
            $msg->save("Messages");
            }
	}
}
?>