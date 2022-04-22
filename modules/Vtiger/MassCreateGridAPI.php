<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'Smarty_setup.php';
require_once 'include/Webservices/MassCreate.php';
global $current_user;
Vtiger_Request::validateRequest();
$newData = array();
$searchon = array();
$module = vtlib_purify($_REQUEST['moduleName']);
$data = vtlib_purify($_REQUEST['data']);
$data = json_decode($data, true);
foreach ($data as $row) {
	unset($row['_attributes']);
	$currentRow = array();
	foreach ($row as $field => $value) {
		if (!is_array($value)) {
			if ($field == 'smownerid') {
				$value = '19x'.$value;
				$field = 'assigned_user_id';
				unset($row['smownerid']);
			} else {
				$searchon[] = $field;
			}
			$currentRow[$field] = $value;
		}
	}
	$newData[] = array(
		'elementType' => $module,
		'referenceId' => '',
		'searchon' => implode(',', $searchon),
		'element' => $currentRow
	);
}
$response = MassCreate($newData, $current_user);
echo json_encode($response);
?>
