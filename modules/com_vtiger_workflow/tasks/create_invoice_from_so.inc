<?php
/*************************************************************************************************
 * Copyright 2016 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. MajorLabel reserves all rights not expressly
 * granted by the License. coreBOS distributed by MajorLabel is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : MajorLabel
 *************************************************************************************************/
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('include/Webservices/Retrieve.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

class CreateInvFromSO extends VTTask{

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('invoice_duedate_days','exact_payment_cond');
	}

	public function doTask(&$entity) {
		global $adb;

		$user = new Users();
		$current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

		// Get invoice instance
		$focus = CRMEntity::getInstance('Invoice');

		// Split the webservice ID from the CRM id
		list($so_wsid,$soid) = explode('x',$entity->getId());

		// Create the SO instance
		$so_focus = CRMEntity::getInstance('SalesOrder');
		$so_focus->id = $soid;
		// Fill the SO instance with the correct SO data
		$so_focus->retrieve_entity_info($soid, "SalesOrder");

		// Convert the SO to an invoice, overwriting the existing invoice instance
		$focus = getConvertSoToInvoice($focus, $so_focus, $soid);

		 // Get the webservice version of the SO
		$wsso = vtws_retrieve($entity->getId(), $current_user);

		// Fill some fields the 'getConvertSoToInvoice' missed
		$focus->column_fields['pdoInformation'] = $wsso['pdoInformation'];
		$focus->column_fields['assigned_user_id'] = $wsso['assigned_user_id'];
		$focus->column_fields['exact_payment_cond'] = $this->exact_payment_cond;
		$focus->column_fields['invoicestatus'] = 'AutoCreated';

		// Get the number of days the duedate should be more than today
		if ($this->invoice_duedate_days != '') {
			$focus->column_fields['duedate'] = date('d-m-Y', strtotime("+" . $this->invoice_duedate_days . " days"));
		}	

		$webserviceObject = VtigerWebserviceObject::fromName($adb,'Invoice');
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject,$current_user,$adb,$log);
		$meta = $handler->getMeta();

		// Sanitize for insert through webservice
		$sanitized_focus = DataTransform::sanitizeReferences($focus->column_fields, $meta);

		$invoice = vtws_create('Invoice', $sanitized_focus, $current_user);	
	}	
}
?>