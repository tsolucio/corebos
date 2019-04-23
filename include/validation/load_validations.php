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
include_once 'include/validation/Validator.php';
include_once 'include/validation/Validations.php';

class cbValidator extends Validator {

	public $CustomValidations = array(
		'IBAN_BankAccount' => 'validate_IBAN_BankAccount',
		'EU_VAT' => 'validate_EU_VAT',
		'notDuplicate' => 'validate_notDuplicate',
		'expression' => 'validate_expression'
	);

	public function __construct($data, $fields = array()) {
		global $current_language;
		parent::__construct($data, $fields, substr($current_language, 0, 2), 'include/validation/lang');
		foreach ($this->CustomValidations as $validation => $function) {
			$this->addRule($validation, $function, ($validation=='notDuplicate' ? getTranslatedString('COLUMNS_CANNOT_BE_DUPLICATED'):getTranslatedString('INVALID')));
		}
	}
}

?>