<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class cleandatabase_610 extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			// mass edit taxes
			$this->ExecuteQuery("update vtiger_field set masseditable=2 where tabid=14 and columnname='unit_price'");
			// add primary index on fieldmodulerel
			$this->ExecuteQuery("ALTER TABLE `vtiger_fieldmodulerel` ADD PRIMARY KEY(`fieldid`, `module`, `relmodule`)");
			// Update vtiger_modcomments table for performance issue
			$datatypeQuery = "ALTER TABLE vtiger_modcomments MODIFY COLUMN related_to int(19)";
			$this->ExecuteQuery($datatypeQuery, array());

			/////////////////////////////////
			//  VTIGER CRM 6.1  interesting changes
			/////////////////////////////////
			$query = 'SELECT 1 FROM vtiger_currencies WHERE currency_name=?';
			$result = $adb->pquery($query, array('Sudanese Pound'));
			if($adb->num_rows($result) <= 0){
				//Inserting Currency Sudanese Pound to vtiger_currencies
				$this->ExecuteQuery('INSERT INTO vtiger_currencies (currencyid,currency_name,currency_code,currency_symbol) VALUES ('.$adb->getUniqueID("vtiger_currencies").',"Sudanese Pound","SDG","£")',array());
			}
			$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('CFA Franc BCEAO'));
			if(!$adb->num_rows($result)) {
				$this->ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
					array($adb->getUniqueID('vtiger_currencies'), 'CFA Franc BCEAO', 'XOF', 'CFA'));
			}
			$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('CFA Franc BEAC'));
			if(!$adb->num_rows($result)) {
				$this->ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
					array($adb->getUniqueID('vtiger_currencies'), 'CFA Franc BEAC', 'XAF', 'CFA'));
			}
			$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('Haiti, Gourde'));
			if(!$adb->num_rows($result)) {
				$this->ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
					array($adb->getUniqueID('vtiger_currencies'), 'Haiti, Gourde', 'HTG', 'G'));
			}
			$this->ExecuteQuery("UPDATE vtiger_currencies SET currency_symbol=? WHERE currency_code=?", array('₹','INR'));
			$this->ExecuteQuery("UPDATE vtiger_currency_info SET currency_symbol=? WHERE currency_code=?", array('₹','INR'));
			$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('Libya, Dinar'));
			if(!$adb->num_rows($result)) {
				$this->ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
					array($adb->getUniqueID('vtiger_currencies'), 'Libya, Dinar', 'LYD', 'LYD'));
			}

			$sql = "ALTER TABLE vtiger_products MODIFY productname VARCHAR( 100 )";
			$this->ExecuteQuery($sql,array());
			
			$query = "ALTER table vtiger_relcriteria modify comparator varchar(20)";
			$this->ExecuteQuery($query, array());
			$this->ExecuteQuery('ALTER TABLE vtiger_webforms MODIFY COLUMN description TEXT',array());

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
	
}
