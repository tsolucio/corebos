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
require_once 'include/ListView/ListViewJSON.php';
global $currentModule;
if (isset($_REQUEST['bmapname'])) {
	$bmapname = vtlib_purify($_REQUEST['bmapname']);
} else {
	$bmapname = $currentModule.'_MassUpsertGridView';
}
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
$match = array();
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$MassUpsert = $cbMap->MassUpsertGridView();
	$fields = $MassUpsert->getColumns();
	$match = $MassUpsert->getMatchFields();
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
			if ($table == 'relatedModule') {
				continue;
			}
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
				'header' => getTranslatedString($label, $currentModule),
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
	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
	$ListFields = array_map(function ($key) use ($fields, $items, $currentModule) {
		$editor = 'text';
		if ($key['columnname'] == 'smownerid') {
			$editor = array(
				'type' => 'select',
				'options' => array(
					'listItems' => $items
				)
			);
		}
		$typeofdata = explode('~', $key['typeofdata']);
		$listFields = array(
			'header' => getTranslatedString($key['fieldlabel'], $currentModule),
			'name' => $key['columnname'],
			'fieldname' => $key['fieldname'],
			'editor' => $editor,
			'active' => 0,
			'type' => $typeofdata,
			'typeofdata' => $typeofdata[1],
			'uitype' => $key['uitype'],
			'tabid' => $key['tabid'],
			'activeModule' => false,
			'relatedModules' => array()
		);
		$fields = array_values($fields);
		foreach ($fields as $value) {
			foreach ($value as $table => $field) {
				if ($field == $key['columnname']) {
					$listFields['active'] = 1;
					break;
				}
			}
		}
		if ($key['uitype'] == Field_Metadata::UITYPE_RECORD_RELATION) {
			$grid = new GridListView($currentModule);
			$grid->tabid = getTabid($currentModule);
			$modules = $grid->findRelatedModule($key['fieldname']);
			if (count($modules) > 1) {
				$listFields['relatedModules'] = $modules;
			}
			foreach ($fields as $label => $value) {
				if (isset($value['relatedModule']) && in_array($value['relatedModule'], $modules)) {
					$listFields['activeModule'] = $value['relatedModule'];
					break;
				}
			}
		}
		return $listFields;
	}, $cachedModuleFields);
	$MatchFields = array_map(function ($key) use ($match, $currentModule) {
		$listFields = array(
			'header' => getTranslatedString($key['fieldlabel'], $currentModule),
			'name' => $key['columnname'],
			'active' => 0,
		);
		if (is_array($match)) {
			foreach ($match as $value) {
				if ($value == $key['columnname']) {
					$listFields['active'] = 1;
					break;
				}
			}
		} else {
			if ($match == $key['columnname']) {
				$listFields['active'] = 1;
			}
		}
		return $listFields;
	}, $cachedModuleFields);

	$tabid = getTabid($currentModule);
	$linksurls = BusinessActions::getAllByType($tabid, array('MASSUPSERTGRID'));
	if (!empty($linksurls['MASSUPSERTGRID'])) {
		$smarty->assign('BALinks', $linksurls['MASSUPSERTGRID']);
	}
	array_multisort($ListFields);
	array_multisort($MatchFields);
	$smarty->assign('EmptyData', json_encode($emptydata));
	$smarty->assign('GridColumns', json_encode($columns));
	$smarty->assign('ListFields', json_encode($ListFields));
	$smarty->assign('MatchFields', json_encode($MatchFields));
	$smarty->assign('bmapname', $bmapname);
	$smarty->assign('moduleShowSearch', false);
	$smarty->assign('showDesert', false);
} else {
	$smarty->assign('showDesert', true);
}
$smarty->assign('moduleView', 'MassCreateGrid');
?>