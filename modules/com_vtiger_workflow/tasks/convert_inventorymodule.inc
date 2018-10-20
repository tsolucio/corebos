<?php
/*************************************************************************************************
 * Copyright 2018 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
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
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/EditViewUtils.php';

class ConvertInventoryModule extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('convertto');
	}

	public function doTask(&$entity) {
		global $current_user, $logbg, $from_wf;
		$from_wf = true;
		$util = new VTWorkflowUtils();
		$util->adminUser();
		// Get the origin record in question
		$wsso = vtws_retrieve($entity->getId(), $current_user);
		// Start with the inventory module values and overwrite what's needed to overwrite
		$data = $wsso;
		$cbfrommodule = $entity->getModuleName();
		if ($cbfrommodule=='Quotes' && $this->convertto=='Invoice') {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$toEntity = CRMEntity::getInstance($this->convertto);
			$fromEntity->column_fields = $data;
			$toEntity->column_fields = $data;
			$result = getConvertQuoteToInvoice($toEntity, $fromEntity, $entity->getId());
			$data = $result->column_fields;
		} elseif ($cbfrommodule=='Quotes' && $this->convertto=='SalesOrder') {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$toEntity = CRMEntity::getInstance($this->convertto);
			$fromEntity->column_fields = $data;
			$toEntity->column_fields = $data;
			$result = getConvertQuoteToSoObject($toEntity, $fromEntity, $entity->getId());
			if (empty($result->column_fields['sostatus'])) {
				$result->column_fields['sostatus'] = 'AutoCreated';
			}
			if (empty($result->column_fields['invoicestatus'])) {
				$result->column_fields['invoicestatus'] = 'AutoCreated';
			}
			$data = $result->column_fields;
		} elseif ($cbfrommodule=='SalesOrder' && $this->convertto=='Invoice') {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$toEntity = CRMEntity::getInstance($this->convertto);
			$fromEntity->column_fields = $data;
			$toEntity->column_fields = $data;
			$result = getConvertSoToInvoice($toEntity, $fromEntity, $entity->getId());
			$data = $result->column_fields;
		} elseif (in_array($cbfrommodule, getInventoryModules())) {
			$bmapname = $cbfrommodule.'2'.$this->convertto;
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$data = $cbMap->Mapping($data, $data);
			}
		} else {
			$fromEntity = CRMEntity::getInstance($cbfrommodule);
			$record = preg_replace('/[^0-9]/', '', substr($entity->data['id'], strpos($entity->data['id'], 'x')));
			$associated_prod = getAssociatedProducts($cbfrommodule, $fromEntity, $record);

			if (empty($associated_prod[1]['entityType1'])) {
				$from_wf = false;
				$util->revertUser();
				$logbg->debug('ConvertInventoryModule: converting from {$cbfrommodule} to {$this->convertto} failed because there is no related products');
				return;
			}
			$toEntity = CRMEntity::getInstance($this->convertto);
			$data = $toEntity->column_fields;
			$data['assigned_user_id'] = $current_user->id;
			$data['created_user_id'] = $current_user->id;
			$data['account_id'] = getRelatedAccountContact($record, 'Accounts');
			$data['contact_id'] = getRelatedAccountContact($record, 'Contacts');
			$data['subject'] = $cbfrommodule.'2'.$this->convertto;
			$data['quotestage'] = 'AutoCreated';
			$data['invoicestatus'] = 'AutoCreated';
			$data['sostatus'] = 'AutoCreated';
			$data['postatus'] = 'AutoCreated';
			if (GlobalVariable::getVariable('Application_B2B', '1')) {
				require_once 'modules/Accounts/Accounts.php';
				$acct_focus = new Accounts();
				$acct_focus->retrieve_entity_info($data['account_id'], 'Accounts');
				$data['bill_city'] =  isset($acct_focus->column_fields['bill_city']) ? $acct_focus->column_fields['bill_city'] : '';
				$data['ship_city'] =  isset($acct_focus->column_fields['ship_city']) ? $acct_focus->column_fields['ship_city'] : '';
				$data['bill_street'] = isset($acct_focus->column_fields['bill_street']) ? $acct_focus->column_fields['bill_street'] : '';
				$data['ship_street'] = isset($acct_focus->column_fields['ship_street']) ? $acct_focus->column_fields['ship_street'] : '';
				$data['bill_state'] = isset($acct_focus->column_fields['bill_state']) ? $acct_focus->column_fields['bill_state'] : '';
				$data['ship_state'] = isset($acct_focus->column_fields['ship_state']) ? $acct_focus->column_fields['ship_state'] : '';
				$data['bill_code'] = isset($acct_focus->column_fields['bill_code']) ? $acct_focus->column_fields['bill_code'] : '';
				$data['ship_code'] = isset($acct_focus->column_fields['ship_code']) ? $acct_focus->column_fields['ship_code'] : '';
				$data['bill_country'] = isset($acct_focus->column_fields['bill_country']) ? $acct_focus->column_fields['bill_country'] : '';
				$data['ship_country'] = isset($acct_focus->column_fields['ship_country']) ? $acct_focus->column_fields['ship_country'] : '';
				$data['bill_pobox'] = isset($acct_focus->column_fields['bill_pobox']) ? $acct_focus->column_fields['bill_pobox'] : '';
				$data['ship_pobox'] = isset($acct_focus->column_fields['ship_pobox']) ? $acct_focus->column_fields['ship_pobox'] : '';
			} else {
				require_once 'modules/Contacts/Contacts.php';
				$cto_focus = new Contacts();
				$cto_focus->retrieve_entity_info($data['contact_id'], 'Contacts');
				$data['bill_city'] = isset($cto_focus->column_fields['mailingcity']) ? $cto_focus->column_fields['mailingcity'] : '';
				$data['ship_city'] = isset($cto_focus->column_fields['othercity']) ? $cto_focus->column_fields['othercity'] : '';
				$data['bill_street'] = isset($cto_focus->column_fields['mailingstreet']) ? $cto_focus->column_fields['mailingstreet'] : '';
				$data['ship_street'] = isset($cto_focus->column_fields['otherstreet']) ? $cto_focus->column_fields['otherstreet'] : '';
				$data['bill_state'] = isset($cto_focus->column_fields['mailingstate']) ? $cto_focus->column_fields['mailingstate'] : '';
				$data['ship_state'] = isset($cto_focus->column_fields['otherstate']) ? $cto_focus->column_fields['otherstate'] : '';
				$data['bill_code'] = isset($cto_focus->column_fields['mailingzip']) ? $cto_focus->column_fields['mailingzip'] : '';
				$data['ship_code'] = isset($cto_focus->column_fields['otherzip']) ? $cto_focus->column_fields['otherzip'] : '';
				$data['bill_country'] = isset($cto_focus->column_fields['mailingcountry']) ? $cto_focus->column_fields['mailingcountry'] : '';
				$data['ship_country'] = isset($cto_focus->column_fields['othercountry']) ? $cto_focus->column_fields['othercountry'] : '';
				$data['bill_pobox'] = isset($cto_focus->column_fields['mailingpobox']) ? $cto_focus->column_fields['mailingpobox'] : '';
				$data['ship_pobox'] = isset($cto_focus->column_fields['otherpobox']) ? $cto_focus->column_fields['otherpobox'] : '';
			}
			$data['hdnTaxType'] = GlobalVariable::getVariable('Inventory_Tax_Type_Default', 'individual', $this->convertto);
			$data['conversion_rate'] = 1;
			$data['currency_id'] = vtws_getEntityId('Currency').'x1';
			$bmapname = $cbfrommodule.'2'.$this->convertto;
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$data = $cbMap->Mapping($data, $data);
			}
			$handler = vtws_getModuleHandlerFromName($this->convertto, $current_user);
			$meta = $handler->getMeta();
			$data = DataTransform::sanitizeRetrieveEntityInfo($data, $meta);
			$data = DataTransform::sanitizeData($data, $meta);
			$related = array();
			$list_price_sum = 0;
			for ($i = 1; $i<=count($associated_prod); $i++) {
				$related['productid'] = $associated_prod[$i]['hdnProductId'.$i];
				$related['comment'] = '';
				$related['qty'] = $associated_prod[$i]['qty'.$i];
				$related['listprice'] = $associated_prod[$i]['listPrice'.$i];
				$list_price_sum += ($related['qty'] * $related['listprice']);
				$related['discount'] = 0 ;
				$related['discount_type'] = 0;
				$related['discount_percentage'] = 0;
				$related['discount_amount'] = 0;
				$data['pdoInformation'][] = $related;
			}
			$data['hdnGrandTotal']= $list_price_sum;
			$data['hdnSubTotal']= $list_price_sum;
		}
		$holdaction = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		$holdajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
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