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

class addNumberDecimalPlaces extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;

			//Currency Decimal places handling
			$this->ExecuteQuery("ALTER TABLE vtiger_account MODIFY COLUMN annualrevenue decimal(25,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_leaddetails MODIFY COLUMN annualrevenue decimal(25,".CurrencyField::$maxNumberOfDecimals.")", array());

			$this->ExecuteQuery("ALTER TABLE vtiger_currency_info MODIFY COLUMN conversion_rate decimal(12,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_productcurrencyrel MODIFY COLUMN actual_price decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_productcurrencyrel MODIFY COLUMN converted_price decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_pricebookproductrel MODIFY COLUMN listprice decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel MODIFY COLUMN listprice decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel MODIFY COLUMN discount_amount decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			// Inventory Details
			$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails MODIFY COLUMN listprice decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails MODIFY COLUMN extgross decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails MODIFY COLUMN discount_amount decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails MODIFY COLUMN extnet decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails MODIFY COLUMN linetax decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("ALTER TABLE vtiger_inventorydetails MODIFY COLUMN linetotal decimal(28,".CurrencyField::$maxNumberOfDecimals.")", array());
			$this->ExecuteQuery("UPDATE vtiger_field SET uitype=71 where tablename='vtiger_inventorydetails' and 
				columnname in ('listprice','extgross','discount_amount','extnet','linetax','linetotal')", array());
			// all the others
			$result = $adb->pquery("SELECT fieldname,tablename,columnname FROM vtiger_field WHERE uitype IN (?,?)",array('71','72'));
			$count = $adb->num_rows($result);
			for($i=0;$i<$count;$i++) {
				$fieldName = $adb->query_result($result,$i,'fieldname');
				$tableName = $adb->query_result($result,$i,'tablename');
				$columnName = $adb->query_result($result,$i,'columnname');
				$tableAndColumnSize = array();
				$tableInfo = $adb->database->MetaColumns($tableName);
				foreach ($tableInfo as $column) {
					$max_length = $column->max_length;
					$scale = $column->scale;
					$tableAndColumnSize[$tableName][$column->name]['max_length'] = $max_length;
					$tableAndColumnSize[$tableName][$column->name]['scale'] = $scale;
				}
				if(!empty($tableAndColumnSize[$tableName][$columnName]['scale'])) {
					$decimalsToChange = CurrencyField::$maxNumberOfDecimals - $tableAndColumnSize[$tableName][$columnName]['scale'];
					if($decimalsToChange != 0) {
						$maxlength = $tableAndColumnSize[$tableName][$columnName]['max_length'] + $decimalsToChange;
						if ($maxlength>65) $maxlength = 65;
						$decimalDigits = $tableAndColumnSize[$tableName][$columnName]['scale'] + $decimalsToChange;
						if ($decimalDigits>10) $decimalDigits = 10;
						$this->ExecuteQuery("ALTER TABLE " .$tableName." MODIFY COLUMN ".$columnName." decimal(?,?)", array($maxlength, $decimalDigits));
					}
				}
			}
			// User configuration field
			$moduleInstance = Vtiger_Module::getInstance('Users');
			$currencyBlock = Vtiger_Block::getInstance('LBL_CURRENCY_CONFIGURATION', $moduleInstance);
			$currency_decimals_field = new Vtiger_Field();
			$currency_decimals_field->name = 'no_of_currency_decimals';
			$currency_decimals_field->label = 'Number Of Currency Decimals';
			$currency_decimals_field->table ='vtiger_users';
			$currency_decimals_field->column = 'no_of_currency_decimals';
			$currency_decimals_field->columntype = 'VARCHAR(2)';
			$currency_decimals_field->typeofdata = 'V~O';
			$currency_decimals_field->uitype = 16;
			$currency_decimals_field->defaultvalue = '2';
			$currency_decimals_field->sequence = 6;
			$currency_decimals_field->helpinfo = "<b>Currency - Number of Decimal places</b> <br/><br/>".
					"Number of decimal places specifies how many number of decimals will be shown after decimal separator.<br/>".
					"<b>Eg:</b> 123.99";
			$currencyBlock->addField($currency_decimals_field);
			$currency_decimals_field->setPicklistValues(array('2','3','4','5','6'));
			$this->ExecuteQuery("UPDATE vtiger_users SET no_of_currency_decimals='2'", array());

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
	
}
