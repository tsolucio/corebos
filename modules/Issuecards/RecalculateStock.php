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
 *************************************************************************************************
 *  Module       : coreBOS Packing Slip
 *  Version      : 1.0
 *************************************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

function recalculateIssuecardsStock() {
	global $adb;
	if ($_REQUEST['module'] == 'Issuecards') {
		$adb->query('UPDATE vtiger_products SET qtyinstock=0');
	}

	$sql_i = 'SELECT vtiger_inventoryproductrel.productid, sum(vtiger_inventoryproductrel.quantity) AS qty
		FROM vtiger_issuecards
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_issuecards.issuecardid
		INNER JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_issuecards.issuecardid
		WHERE vtiger_crmentity.deleted=0 GROUP BY productid';
	$result_i = $adb->query($sql_i);
	while ($row = $adb->fetchByAssoc($result_i)) {
		$sql_u = "UPDATE vtiger_products SET qtyinstock = qtyinstock - ".$row['qty']." WHERE productid = ".$row['productid'];
		$adb->query($sql_u);
	}
}

recalculateIssuecardsStock();

if (file_exists('modules/Receiptcards/RecalculateStock.php') && $_REQUEST['module']=='Issuecards') {
	require_once 'modules/Receiptcards/RecalculateStock.php';
}
?>
