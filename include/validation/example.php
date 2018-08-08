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
 *************************************************************************************************
 *  Module       : Example Validation
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
/***********************************************************************************
  param structure array contains all the form fields current values, the ones you want to validate
  return
  %%%OK%%%  to indicate that all validations have passed correctly
  %%%CONFIRM%%%   followed by any message to produce a screen that will ask for confirmation using the text following the CONFIRM label
  Any other message will be interpreted as an error message that will be shown to user.
************************************************************************************/
global $log,$currentModule,$adb;
include_once 'include/validation/load_validations.php';

$screen_values = json_decode($_REQUEST['structure'], true);
$v = new cbValidator(array('iban_number' => $screen_values['iban_number']));
$v->rule('required', 'iban_number');
$v->rule('IBAN_BankAccount', 'iban_number');
if ($v->validate()) {
	echo "Yay! We're all good!";
} else {
	// Errors
	print_r($v->errors());
}
//echo '%%%OK%%%';