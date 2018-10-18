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
 *  Module       : Template script to import PriceBook prices from a CSV file
 *  Version      : 1.0
 *    This script reads in a csv file with the separator a colon.
 *    The first row must be a header with the three supported columns:
 *      "pricebookid","productid","listprice"
 *    The pricebookid and productid must be internal CRMID of each record
 *    The productid can be the CRMID of a service also.
 *************************************************************************************************/

$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

$current_user = Users::getActiveAdminUser();

$file = $argv[1];

if (!file_exists($file) || !is_readable($file)) {
	echo 'No suitable file specified' . PHP_EOL;
	die;
}

function csv_to_array($file = '', $length = 0, $delimiter = ',') {
	$header = null;
	$data = array();
	if (($handle = fopen($file, 'r')) !== false) {
		while (($row = fgetcsv($handle, $length, $delimiter)) !== false) {
			if (!$header) {
				$header = $row;
			} else {
				$data[] = array_combine($header, $row);
			}
		}
		fclose($handle);
	}
	return $data;
}
$inssql = 'INSERT INTO vtiger_pricebookproductrel(pricebookid, productid, listprice, usedcurrency) VALUES (?,?,?,?)';
$i=0;
foreach (csv_to_array($file) as $row) {
	//print_r($row);
	$rs = $adb->pquery('select currency_id from vtiger_pricebook where pricebookid=?', array($row['pricebookid']));
	if ($rs && $adb->num_rows($rs)>0) {
		$curid = $adb->query_result($rs, 0);
	} else {
		$curid = 1;
	}
	$adb->pquery($inssql, array($row['pricebookid'],$row['productid'],$row['listprice'],$curid));
	echo $row['pricebookid'].' - '.$row['productid'] . PHP_EOL;
	//if ($i++==10) break;  // for testing before full launch
}
?>