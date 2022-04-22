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
global $currentModule;
$focus = CRMEntity::getInstance($currentModule);
$fields = $focus->list_fields;
$users = get_user_array();
$items = array();
foreach ($users as $id => $username) {
	if (!empty($id)) {
		$items[] = array(
			'text' => $username,
			'value' => $id
		);
	}
}
$emptydata = array();
$columns = array();
foreach ($fields as $label => $value) {
	$tempColumns = array();
	foreach ($value as $table => $column) {
		if ($column == 'smownerid') {
			$editor = array(
				'type' => 'select',
				'options' => array(
					'listItems' => $items
				)
			);
		} else {
			$editor = 'text';
		}
		$columns[] = array(
			'header' => $label,
			'name' => $column,
			'editor' => $editor
		);
		array_push($emptydata, array($column => ''));
	}
}
if (!is_admin($current_user)) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	exit;
}
$smarty->assign('EmptyData', json_encode($emptydata));
$smarty->assign('GridColumns', json_encode($columns));
$smarty->assign('moduleView', 'MassCreateGrid');
$smarty->assign('moduleShowSearch', false);
?>