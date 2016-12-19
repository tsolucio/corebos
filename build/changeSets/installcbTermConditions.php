<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class installcbTermConditions extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$toinstall = array('cbTermConditions');
			foreach ($toinstall as $module) {
				if ($this->isModuleInstalled($module)) {
					vtlib_toggleModuleAccess($module,true);
					$this->sendMsg("$module activated!");
				} else {
					$this->installManifestModule($module);
				}
			}
			$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('INVENTORYTERMSANDCONDITIONS'));

			$cbtandc = Vtiger_Module::getInstance('cbTermConditions');

			$invoice = Vtiger_Module::getInstance('Invoice');
			$invblock = Vtiger_Block::getInstance('LBL_INVOICE_INFORMATION', $invoice);
			$field1 = Vtiger_Field::getInstance('tandc', $invoice);
			if ($field1) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid='.$field1->id);
			} else {
				$field1 = new Vtiger_Field();
				$field1->name = 'tandc';
				$field1->label = 'Terms and Conditions';
				$field1->table = 'vtiger_invoice';
				$field1->column = 'tandc';
				$field1->columntype = 'int(11)';
				$field1->typeofdata = 'I~O';
				$field1->displaytype = '1';
				$field1->uitype = '10';
				$field1->masseditable = '0';
				$invblock->addField($field1);
				$field1->setRelatedModules(Array('cbTermConditions'));
			}
			$cbtandc->setRelatedList($invoice, 'Invoice', Array('ADD'), 'get_dependents_list');

			$salesorder = Vtiger_Module::getInstance('SalesOrder');
			$soblock = Vtiger_Block::getInstance('LBL_SO_INFORMATION', $salesorder);
			$field2 = Vtiger_Field::getInstance('tandc', $salesorder);
			if ($field2) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid='.$field2->id);
			} else {
				$field2 = new Vtiger_Field();
				$field2->name = 'tandc';
				$field2->label = 'Terms and Conditions';
				$field2->table = 'vtiger_salesorder';
				$field2->column = 'tandc';
				$field2->columntype = 'int(11)';
				$field2->typeofdata = 'I~O';
				$field2->displaytype = '1';
				$field2->uitype = '10';
				$field2->masseditable = '0';
				$soblock->addField($field2);
				$field2->setRelatedModules(Array('cbTermConditions'));
			}
			$cbtandc->setRelatedList($salesorder, 'SalesOrder', Array('ADD'), 'get_dependents_list');

			$quotes = Vtiger_Module::getInstance('Quotes');
			$qublock = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $quotes);
			$field3 = Vtiger_Field::getInstance('tandc', $quotes);
			if ($field3) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid='.$field3->id);
			} else {
				$field3 = new Vtiger_Field();
				$field3->name = 'tandc';
				$field3->label = 'Terms and Conditions';
				$field3->table = 'vtiger_quotes';
				$field3->column = 'tandc';
				$field3->columntype = 'int(11)';
				$field3->typeofdata = 'I~O';
				$field3->displaytype = '1';
				$field3->uitype = '10';
				$field3->masseditable = '0';
				$qublock->addField($field3);
				$field3->setRelatedModules(Array('cbTermConditions'));
			}
			$cbtandc->setRelatedList($quotes, 'Quotes', Array('ADD'), 'get_dependents_list');

			$purchaseorder = Vtiger_Module::getInstance('PurchaseOrder');
			$poblock = Vtiger_Block::getInstance('LBL_PO_INFORMATION', $purchaseorder);
			$field4 = Vtiger_Field::getInstance('tandc', $purchaseorder);
			if ($field4) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid='.$field4->id);
			} else {
				$field4 = new Vtiger_Field();
				$field4->name = 'tandc';
				$field4->label = 'Terms and Conditions';
				$field4->table = 'vtiger_purchaseorder';
				$field4->column = 'tandc';
				$field4->columntype = 'int(11)';
				$field4->typeofdata = 'I~O';
				$field4->displaytype = '1';
				$field4->uitype = '10';
				$field4->masseditable = '0';
				$poblock->addField($field4);
				$field4->setRelatedModules(Array('cbTermConditions'));
			}
			$cbtandc->setRelatedList($purchaseorder, 'PurchaseOrder', Array('ADD'), 'get_dependents_list');

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
