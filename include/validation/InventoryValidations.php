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
require_once 'modules/cbMap/processmap/Validations.php';
global $log,$currentModule,$adb,$current_user;

$screen_values = json_decode($_REQUEST['structure'], true);
$message = '%%%OK%%%';
$products = Validations::loadProductValuesFromScreenValues($screen_values);
foreach ($products as $product) {
	if ($product['type'] == 'Products') {
		$q = $adb->pquery('SELECT divisible, discontinued FROM vtiger_products WHERE productid = ?', array($product['crmid']));
	} else {
		// Was a service
		$q = $adb->pquery('SELECT divisible, discontinued FROM vtiger_service WHERE serviceid = ?', array($product['crmid']));
	}
	if ($adb->query_result($q, 0, 'divisible') === '0') {
		$divisible = false;
	} else {
		$divisible = true;
	}
	if ((int)$adb->query_result($q, 0, 'discontinued') !== 1 && (int)$product['deleted'] === 0 && $screen_values['mode'] == '') {
		$message = $product['name'].' '.getTranslatedString('IS_DISCONTINUED', 'Products');
		break;
	}
	if (!$divisible && (float)$product['qty'] != (int)$product['qty']) {
		if (empty($DIVISIBLE_WARNING)) {
			$DIVISIBLE_WARNING = 'DIVISIBLE_WARNING';
		}
		$message = $product['name'].' '.getTranslatedString($DIVISIBLE_WARNING, 'Products');
		break;
	}
}

echo $message;