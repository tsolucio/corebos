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

			include_once 'include/Webservices/Create.php';
			global $current_user, $adb;
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$tacrs = $adb->query('select tandc from vtiger_inventory_tandc limit 1');
			if ($tacrs and $adb->num_rows($tacrs)>0) {
				$tac = $adb->query_result($tacrs, 0, 0);
			} else {
				$tac = '';
			}
			$values =  array(
				'isdefault' => '1',
				'reference' => 'Default T&C',
				'formodule' => '',
				'tandc' => $tac,
				'assigned_user_id' => $usrwsid,
			);

			$cbtandc = Vtiger_Module::getInstance('cbTermConditions');
			$toaddfield = array('Invoice', 'SalesOrder', 'Quotes', 'PurchaseOrder');
			$created = false;
			foreach ($toaddfield as $module) {
				$values['formodule'] = $module;
				try {
					$id = vtws_create('cbTermConditions', $values, $current_user);
					$created = true;
				} catch (Exception $e) {
					$id = false;
					break;
				}
				if ($id) {
					$mod = Vtiger_Module::getInstance($module);
					$modblock = Vtiger_Block::getInstance('LBL_TERMS_INFORMATION', $mod);
					$field1 = Vtiger_Field::getInstance('tandc', $mod);
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
						$modblock->addField($field1);
						$field1->setRelatedModules(Array('cbTermConditions'));
					}
					$cbtandc->setRelatedList($mod, $module, Array('ADD'), 'get_dependents_list');
				}
			}

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			if ($created) {
				$this->markApplied(false);
				$this->ExecuteQuery('DROP TABLE vtiger_inventory_tandc');
				$this->ExecuteQuery('DROP TABLE vtiger_inventory_tandc_seq');
			} else {
				$this->sendMsgError('Changeset '.get_class($this).' has produced an error creating records, please try applying once more.');
			}
		}
		$this->finishExecution();
	}

}
