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
 *  Module       : Accounts Validation
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
/***********************************************************************************
  param structure array contains all the form fields current values, the ones you want to validate
  return
  %%%OK%%%  to indicate that all validations have passed correctly
  %%%CONFIRM%%%   followed by any message to produce a screen that will ask for confirmation
  using the text following the CONFIRM label
  Any other message will be interpreted as an error message that will be shown to user.
************************************************************************************/
global $log,$currentModule,$adb;

$screen_values = json_decode($_REQUEST['structure'], true);
$blockDuplicateAccounts = GlobalVariable::getVariable('Accounts_BlockDuplicateName', 1, 'Accounts');
if ($blockDuplicateAccounts && isset($screen_values['accountname'])) {
	$query = 'SELECT 1 FROM vtiger_account,vtiger_crmentity WHERE accountname=? and vtiger_account.accountid=vtiger_crmentity.crmid and vtiger_crmentity.deleted!=1';
	$value = vtlib_purify($screen_values['accountname']);
	$params = array($value);
	$id = vtlib_purify($screen_values['record']);
	if (!empty($id)) {
		$query .= ' and vtiger_account.accountid != ?';
		$params[] = $id;
	}
	$result = $adb->pquery($query, $params);
	if ($adb->num_rows($result) > 0) {
		echo getTranslatedString('LBL_ACCOUNT_EXIST', 'Accounts');
		die;
	}
}
echo '%%%OK%%%';