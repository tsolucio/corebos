<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class dropForeignKeys extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$fk = array(
				'fk_1_vtiger_account' => 'vtiger_account',
				'fk_1_vtiger_accountbillads' => 'vtiger_accountbillads',
				'fk_1_vtiger_accountscf' => 'vtiger_accountscf',
				'fk_1_vtiger_accountshipads' => 'vtiger_accountshipads',
				'fk_1_vtiger_activity' => 'vtiger_activity',
				'fk_1_vtiger_assets' => 'vtiger_assets',
				'fk_1_vtiger_campaignscf' => 'vtiger_campaignscf',
				'fk_1_vtiger_contactaddress' => 'vtiger_contactaddress',
				'fk_1_vtiger_contactdetails' => 'vtiger_contactdetails',
				'fk_1_vtiger_contactscf' => 'vtiger_contactscf',
				'fk_1_vtiger_contactsubdetails' => 'vtiger_contactsubdetails',
				'fk_1_vtiger_faq' => 'vtiger_faq',
				'fk_1_vtiger_faqcf' => 'vtiger_faqcf',
				'fk_2_vtiger_invoice' => 'vtiger_invoice',
				'fk_1_vtiger_invoicebillads' => 'vtiger_invoicebillads',
				'fk_1_vtiger_invoicecf' => 'vtiger_invoicecf',
				'fk_1_vtiger_invoiceshipads' => 'vtiger_invoiceshipads',
				'fk_1_vtiger_invoicestatushistory' => 'vtiger_invoicestatushistory',
				'fk_1_vtiger_leadaddress' => 'vtiger_leadaddress',
				'fk_1_vtiger_leaddetails' => 'vtiger_leaddetails',
				'fk_1_vtiger_leadscf' => 'vtiger_leadscf',
				'fk_1_vtiger_leadsubdetails' => 'vtiger_leadsubdetails',
				'fk_1_vtiger_notes' => 'vtiger_notes',
				'fk_1_vtiger_parenttabrel' => 'vtiger_parenttabrel',
				'fk_2_vtiger_parenttabrel' => 'vtiger_parenttabrel',
				'fk_1_vtiger_pobillads' => 'vtiger_pobillads',
				'fk_1_vtiger_poshipads' => 'vtiger_poshipads',
				'fk_1_vtiger_potential' => 'vtiger_potential',
				'fk_1_vtiger_potentialscf' => 'vtiger_potentialscf',
				'fk_1_vtiger_pricebook' => 'vtiger_pricebook',
				'fk_1_vtiger_pricebookcf' => 'vtiger_pricebookcf',
				'fk_1_vtiger_productcf' => 'vtiger_productcf',
				'fk_1_vtiger_products' => 'vtiger_products',
				'fk_4_vtiger_purchaseorder' => 'vtiger_purchaseorder',
				'fk_1_vtiger_purchaseordercf' => 'vtiger_purchaseordercf',
				'fk_1_vtiger_quotesbillads' => 'vtiger_quotesbillads',
				'fk_1_vtiger_quotescf' => 'vtiger_quotescf',
				'fk_1_vtiger_quotesshipads' => 'vtiger_quotesshipads',
				'fk_1_vtiger_quotestagehistory' => 'vtiger_quotestagehistory',
				'fk_3_vtiger_salesorder' => 'vtiger_salesorder',
				'fk_1_vtiger_salesordercf' => 'vtiger_salesordercf',
				'fk_1_vtiger_service' => 'vtiger_service',
				'fk_1_vtiger_sobillads' => 'vtiger_sobillads',
				'fk_1_vtiger_soshipads' => 'vtiger_soshipads',
				'fk_1_vtiger_ticketcf' => 'vtiger_ticketcf',
				'fk_1_vtiger_troubletickets' => 'vtiger_troubletickets',
				'fk_1_vtiger_vendor' => 'vtiger_vendor',
				'fk_1_vtiger_vendorcf' => 'vtiger_vendorcf',
			);
			foreach ($fk as $fkname => $tname) {
				$this->ExecuteQuery("ALTER TABLE {$tname} DROP FOREIGN KEY {$fkname}");
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}