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
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';

class CreateInvFromSO extends VTTask{

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('invoice_duedate_days');
	}

	public function doTask(&$entity) {
		global $adb;

		// Retrieve SO crmid
		// list($so_wsid,$so_crmid) = explode('x',$entity->getId());

		$user = new Users();
		$current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

		// $wsid = vtws_getWebserviceEntityId('SalesOrder', $so_crmid);
		$wsso = vtws_retrieve($entity->getId(), $current_user);

		// Create an invoice from this SO
		// Get the account NO
		list($ws_acc_id, $crm_acc_id) = explode('x', $wsso['account_id']);
		$query = 'SELECT account_no FROM vtiger_account WHERE accountid=?';
		$params = array($crm_acc_id);
		$result = $adb->pquery($query, $params);
		$acc_no = $adb->query_result($result, 0, 'account_no');

		// Get the number of days the duedate should be more than today
		if ($this->invoice_duedate_days != '') {
			$inv_duedate = date('d-m-Y', strtotime("+" . $this->invoice_duedate_days . " days"));
		}

		// refactor the SO webservice object for invoice injection
		if (is_array($wsso)) {
			$data = $wsso;
			// Overwrite data that is different for invoices
			$data['LineItems']			= $wsso['pdoInformation'];
			$data['invoicedate'] 		= date('d-m-Y');
			$data['salesorder_id']		= $wsso['id'];
			$data['customerno']			= $acc_no;
			$data['invoicestatus']		= 'Created';
			$data['sostatus']			= 'Created';
			$data['exact_payment_cond']	= '3 - Betaling binnen 30 dagen';

			if (isset($inv_duedate)) {
				$data['duedate']		= $inv_duedate;
			}
		}

		$invoice = vtws_create('Invoice', $data, $current_user);
	}	
}
?>