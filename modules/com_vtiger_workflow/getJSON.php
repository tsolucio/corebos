<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'VTWorkflow.php';

$response = array(
	'total' => 0,
	'data' => array(),
	'error' => true,
);
$focus = new Workflow();
if (isset($_REQUEST['page'])) {
	$page = vtlib_purify($_REQUEST['page']);
} else {
	$page = 1;
}
$conds = '';
$params = array();
if (!empty($_REQUEST['filters'])) {
	$filters = json_decode(vtlib_purify($_REQUEST['filters']), true);
	if (json_last_error() == JSON_ERROR_NONE && count($filters)>0) {
		$conds = array();
		foreach ($filters as $filter) {
			switch ($filter['path']) {
				case 'Module':
					if (!empty($filter['value']) && $filter['value'] != 'all') {
						$conds[] = 'module_name=?';
						$params[] = $filter['value'];
					}
					break;
				case 'Description':
					if (!empty($filter['value'])) {
						$conds[] = 'summary like ?';
						$params[] = '%' . $filter['value'] . '%';
					}
					break;
				case 'Purpose':
					if (!empty($filter['value'])) {
						$conds[] = 'purpose like ?';
						$params[] = '%' . $filter['value'] . '%';
					}
					break;
				case 'Trigger':
					if (!empty($filter['value']) && $filter['value'] != 'all') {
						$conds[] = 'execution_condition=?';
						$params[] = $filter['value'];
					}
					break;
				case 'Status':
					if (!empty($filter['value']) && $filter['value'] != 'all') {
						$conds[] = 'active=?';
						$params[] = $filter['value'];
					}
					break;
				default:
			}
		}
		if (empty($conds)) {
			$conds = '';
		} else {
			$conds = 'where '.implode(' and ', $conds);
		}
	}
}
if (!empty($_REQUEST['sorder'])) {
	$order_by = '';
	$sorder = json_decode(vtlib_purify($_REQUEST['sorder']), true);
	if (json_last_error() == JSON_ERROR_NONE && count($sorder)>0) {
		$order_by = array();
		foreach ($sorder as $order) {
			$order_by[] = $focus->list_fields_name[$order['path']].' '.$order['direction'];
		}
		$order_by = implode(', ', $order_by);
	}
} else {
	$order_by = $focus->default_order_by.' '.$focus->default_sort_order;
}
echo $focus->getWorkFlowJSON($conds, $params, $page, $order_by);
