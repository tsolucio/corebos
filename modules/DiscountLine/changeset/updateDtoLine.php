<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class updateDtoLine extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'DiscountLine';
			if ($this->isModuleInstalled($modname)) {
				$module = Vtiger_Module::getInstance($modname);
				$ev = new VTEventsManager($adb);
				$ev->registerHandler('corebos.entity.link.after', 'modules/DiscountLine/CheckDuplicateRelatedRecords.php', 'CheckDuplicateRelatedRecords');
				$ev->registerHandler('corebos.filter.inventory.getprice', 'modules/DiscountLine/GetPriceHandler.php', 'PriceCalculationGetPriceEventHandler');
				$modAccounts = Vtiger_Module::getInstance('Accounts');
				$modAccounts->unsetRelatedList($module, 'Price Modification', 'get_dependents_list');
				$modAccounts->setRelatedList($module, 'Price Modification', array('ADD','SELECT'));
				$module->setRelatedList($modAccounts, 'Price Modification', array('ADD','SELECT'));
				$modContacts = Vtiger_Module::getInstance('Contacts');
				$modContacts->setRelatedList($module, 'Price Modification', array('ADD','SELECT'));
				$module->setRelatedList($modContacts, 'Price Modification', array('ADD','SELECT'));
				$modProducts = Vtiger_Module::getInstance('Products');
				$modProducts->setRelatedList($module, 'Price Modification', array('ADD','SELECT'));
				$module->setRelatedList($modProducts, 'Price Modification', array('ADD','SELECT'));
				$modServices = Vtiger_Module::getInstance('Services');
				$modServices->setRelatedList($module, 'Price Modification', array('ADD','SELECT'));
				$module->setRelatedList($modServices, 'Price Modification', array('ADD','SELECT'));
				// Gets all the uitype10 accountid and adds them to crmentityrel
				$cnmsg = $adb->getColumnNames('vtiger_discountline');
				if (in_array('accountid', $cnmsg)) {
					$query_result = $adb->pquery('SELECT discountlineid, accountid FROM vtiger_discountline', array());
					while ($_rows = $adb->fetch_array($query_result)) {
						$crmid_discountline = $_rows['discountlineid'];
						$crmid_accounts = $_rows['accountid'];
						$this->ExecuteQuery(
							'INSERT INTO `vtiger_crmentityrel` (`crmid`, `module`, `relcrmid`, `relmodule`) VALUES (?,?,?,?)',
							array($crmid_discountline, 'DiscountLine', $crmid_accounts, 'Accounts')
						);
					}
				}
				$fieldLayout=array(
					'DiscountLine' => array(
						'LBL_DISCOUNTLINE_INFORMATION'=> array(
							'activestatus' => array(
								'columntype'=>'varchar(3)',
								'typeofdata'=>'C~O',
								'uitype'=>'56',
								'displaytype'=>'1',
								'label'=>'Active',
							),
							'returnvalue' => array(
								'columntype'=>'varchar(30)',
								'typeofdata'=>'V~O',
								'uitype'=>'15',
								'displaytype'=>'1',
								'label'=>'Return Value',
								'vals' => array('Unit+Discount', 'Cost+Margin'),
							),
							'cbmapid' => array(
								'columntype'=>'int(11)',
								'typeofdata'=>'V~O',
								'uitype'=>'10',
								'displaytype'=>'1',
								'label'=>'Map',
								'mods' => array('cbMap'),
							),
						),
					),
				);
				$this->massCreateFields($fieldLayout);
				$fieldLayout=array(
					'DiscountLine' => array(
						'accountid'
					),
				);
				$this->massHideFields($fieldLayout);
				$this->ExecuteQuery('update vtiger_discountline set activestatus=?, returnvalue=?', array('1', 'Unit+Discount'));
				coreBOS_Settings::setSetting('KEY_DISCOUNT_MODULE_STATUS', 'on');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			global $adb;
			$ev = new VTEventsManager($adb);
			$ev->unregisterHandler('CheckDuplicateRelatedRecords');
			$ev->unregisterHandler('PriceCalculationGetPriceEventHandler');
			$this->sendMsg('Changeset '.get_class($this).' undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}
}
