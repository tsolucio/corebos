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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'modules/Users/LoginHistory.php';

$response = array(
	'total' => 0,
	'data' => array(),
	'error' => true,
);
$focus = new LoginHistory();
if (isset($_REQUEST['page'])) {
	$page = vtlib_purify($_REQUEST['page']);
} else {
	$page = 1;
}
if (isset($_REQUEST['user_list']) && is_numeric($_REQUEST['user_list'])) {
	$userid = vtlib_purify($_REQUEST['user_list']);
} else {
	$userid = 0;
}
if (isset($_REQUEST['sortColumn'])) {
	$order_by = vtlib_purify($_REQUEST['sortColumn']);
	if (isset($focus->list_fields_name[$order_by])) {
		$order_by = $focus->list_fields_name[$order_by];
	} else {
		$order_by = $focus->default_order_by;
	}
} else {
	$order_by = $focus->default_order_by;
}
if (!isset($_REQUEST['sortAscending'])) {
	$sorder = '';
} elseif ($_REQUEST['sortAscending']=='true') {
	$sorder = 'ASC';
} else {
	$sorder = 'DESC';
}
$response = $focus->getHistoryJSON($userid, $page, $order_by, $sorder);
echo $response;
