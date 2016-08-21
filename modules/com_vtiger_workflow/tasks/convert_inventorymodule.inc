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

class ConvertInventoryModule extends VTTask{

	public $executeImmediately = true;

	public function getFieldNames() {
		return array();
	}

	public function doTask(&$entity) {

		global $adb, $log;

		$user = new Users();
		$current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

		// Get the salesorder in question
		$wsso = vtws_retrieve($entity->getId(), $current_user);

		// // Start with the salesorder and overwrite what's needed to overwrite
		$data = $wsso;

		$data['pdoInformation'] = $wsso['pdoInformation'];
		$data['salesorder_id'] = $wsso['id'];
		$data['invoicedate'] = date('d-m-Y');
		$data['invoicestatus'] = 'AutoCreated';
		$data['createdtime'] = date('Y-m-d H:i:s');
		$data['modifiedtime'] = date('Y-m-d H:i:s');

		$_REQUEST['action'] = '';
		$_REQUEST['ajxaction'] = '';

		$invoice = vtws_create('Invoice', $data, $current_user);
	}
}
?>