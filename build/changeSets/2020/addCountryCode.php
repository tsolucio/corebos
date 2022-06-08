<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addCountryCode extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
			$this->markApplied(false);
		} else {
			include_once 'vtlib/Vtiger/Module.php';
			$module = Vtiger_Module::getInstance('Accounts');
			$block = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $module);

			// https://github.com/datasets/country-codes/blob/master/data/country-codes.csv
			$file = 'https://raw.githubusercontent.com/datasets/country-codes/master/data/country-codes.csv';
			$lines = array_map('str_getcsv', explode(PHP_EOL, file_get_contents($file)));
			array_walk($lines, function (&$line) use ($lines) {
				$line = @array_combine($lines[0], $line);
				$line = empty($line['ISO3166-1-Alpha-2']) ? false : $line['ISO3166-1-Alpha-2'];
			});
			array_shift($lines);
			array_unshift($lines, '--None--');
			$lines = array_filter($lines);

			$field				=	new Vtiger_Field();
			$field->name		=	'bill_countrycode';
			$field->label		=	'bill_countrycode';
			$field->table		=	'vtiger_account';
			$field->column		=	'bill_countrycode';
			$field->columntype	=	'VARCHAR(20)';
			$field->uitype		=	15;
			$field->typeofdata	=	'V~O';

			$block->addField($field);
			$field->setPicklistValues($lines);

			$field				=	new Vtiger_Field();
			$field->name		=	'ship_countrycode';
			$field->label		=	'ship_countrycode';
			$field->table		=	'vtiger_account';
			$field->column		=	'ship_countrycode';
			$field->columntype	=	'VARCHAR(20)';
			$field->uitype		=	15;
			$field->typeofdata	=	'V~O';

			$block->addField($field);
			$field->setPicklistValues($lines);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->markUndone(false);
			$this->sendMsg('Changeset '.get_class($this).' undone!');
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
		}
		$this->finishExecution();
	}

	public function isApplied() {
		$done = parent::isApplied();
		if (!$done) {
			global $adb;
			$r = $adb->query("SHOW COLUMNS FROM `vtiger_account` LIKE '%_countrycode'");
			if ($adb->num_rows($r) > 0) {
				$done = true;
			}
		}
		return $done;
	}
}