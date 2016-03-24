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

class addfixCurrencies01 extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;

			//Changed the Currency Symbol of Moroccan, Dirham to DH
			$this->ExecuteQuery('UPDATE vtiger_currencies SET currency_symbol=? WHERE currency_name=? AND currency_code=?',
				array('DH', 'Moroccan, Dirham', 'MAD'));
			$this->ExecuteQuery('UPDATE vtiger_currencies SET currency_name = ? where currency_name = ? and currency_code = ?',
				array('Hong Kong, Dollars', 'LvHong Kong, Dollars', 'HKD'));
			$this->ExecuteQuery('UPDATE vtiger_currency_info SET currency_name = ? where currency_name = ?',
				array('Hong Kong, Dollars', 'LvHong Kong, Dollars'));

			$checkQuery = 'SELECT 1 FROM vtiger_currencies  WHERE currency_name=?';
			$insertCurrency = 'INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)';
			$checkResult = $adb->pquery($checkQuery,array('Iraqi Dinar'));
			if($adb->num_rows($checkResult) <= 0) {
				$this->ExecuteQuery($insertCurrency, array($adb->getUniqueID('vtiger_currencies'), 'Iraqi Dinar', 'IQD', 'ID'));
			}
			$checkResult = $adb->pquery($checkQuery,array('Maldivian Ruffiya'));
			if($adb->num_rows($checkResult) <= 0) {
				$this->ExecuteQuery($insertCurrency, array($adb->getUniqueID('vtiger_currencies'), 'Maldivian Ruffiya', 'MVR', 'MVR'));
			}
			$checkResult = $adb->pquery($checkQuery, array('Ugandan Shilling'));
			if(!$adb->num_rows($checkResult)) {
				$this->ExecuteQuery($insertCurrency, array($adb->getUniqueID('vtiger_currencies'), 'Ugandan Shilling', 'UGX', 'Sh'));
			}
			$checkResult = $adb->pquery($checkQuery, array('CFP Franc'));
			if(!$adb->num_rows($checkResult)) {
				$this->ExecuteQuery($insertCurrency, array($adb->getUniqueID('vtiger_currencies'), 'CFP Franc', 'XPF', 'F'));
			}

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
	
}
