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
require_once('include/utils/EditViewUtils.php');

class ConvertInventoryModule extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('convertto');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg, $from_wf;
		$from_wf = true;
		$util = new VTWorkflowUtils();
		$admin = $util->adminUser();

		// Get the origin record in question
		$wsso = vtws_retrieve($entity->getId(), $current_user);

		// Start with the inventory module values and overwrite what's needed to overwrite
		$data = $wsso;
		$cbfrommodule = $entity->getModuleName();
		if ($cbfrommodule=='Quotes' and $this->convertto=='Invoice') {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$toEntity = CRMEntity::getInstance($this->convertto);
			$fromEntity->column_fields = $data;
			$toEntity->column_fields = $data;
			$result = getConvertQuoteToInvoice($toEntity, $fromEntity, $entity->getId());
			$data = $result->column_fields;
		} elseif ($cbfrommodule=='Quotes' and $this->convertto=='SalesOrder') {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$toEntity = CRMEntity::getInstance($this->convertto);
			$fromEntity->column_fields = $data;
			$toEntity->column_fields = $data;
			$result = getConvertQuoteToSoObject($toEntity, $fromEntity, $entity->getId());
			$data = $result->column_fields;
		} elseif ($cbfrommodule=='SalesOrder' and $this->convertto=='Invoice') {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$toEntity = CRMEntity::getInstance($this->convertto);
			$fromEntity->column_fields = $data;
			$toEntity->column_fields = $data;
			$result = getConvertSoToInvoice($toEntity, $fromEntity, $entity->getId());
			$data = $result->column_fields;
		} else {
			$bmapname = $cbfrommodule.'2'.$this->convertto;
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$data = $cbMap->Mapping($data, $data);
			}
		}

		$holdaction = $_REQUEST['action'];
		$holdajxaction = $_REQUEST['ajxaction'];
		$_REQUEST['action'] = '';
		$_REQUEST['ajxaction'] = '';

		$invmod = vtws_create($this->convertto, $data, $current_user);

		$_REQUEST['action'] = $holdaction;
		$_REQUEST['ajxaction'] = $holdajxaction;
		$from_wf = false;
		$util->revertUser();
		$logbg->debug('ConvertInventoryModule: From '.$cbfrommodule.' '.$entity->getId().' to '.$this->convertto);
		$logbg->debug('ConvertInventoryModule: From '.print_r($wsso, true));
		$logbg->debug('ConvertInventoryModule: To '.print_r($data, true));
		$logbg->debug('ConvertInventoryModule: Result '.print_r($invmod, true));
	}
}
?>