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
require_once 'include/ListView/ListViewJSON.php';

class genMassUpsertGridView extends generatecbMap {

	public function generateMap() {
		global $adb;
		include 'modules/cbMap/generatemap/GenMapHeader.php';
		$Map = $this->getMap();
		$xml = $this->getXMLContent();
		$module = $Map->column_fields['targetname'];
		$mapname = $Map->column_fields['mapname'];
		$smarty->assign('maptype', 'MassUpsertGridView');
		$smarty->assign('mapname', $mapname);
		$smarty->assign('module', $module);
		$smarty->assign('mapcontent', json_encode($xml));
		$smarty->assign('MapID', $Map->id);
		$ModuleFields = getModuleFieldsInfo($module);
		if (isset($xml->columns)) {
			$fields = (array)$xml->columns;
			$ListFields = array_map(function ($key) use ($fields, $module) {
				$typeofdata = explode('~', $key['typeofdata']);
				$listFields = array(
					'header' => getTranslatedString($key['fieldlabel'], $module),
					'name' => $key['columnname'],
					'active' => 0,
					'typeofdata' => $typeofdata[1],
					'activeModule' => false,
					'relatedModules' => array()
				);
				if (isset($fields['field'])) {
					$fields = array_values((array)$fields['field']);
					if (count($fields) == 1) {
						if ($fields[0] == $key['columnname']) {
							$listFields['active'] = 1;
						}
					} else {
						foreach ($fields as $field) {
							if ($field->name == $key['columnname']) {
								$listFields['active'] = 1;
								break;
							}
						}
					}
				}
				if ($key['uitype'] == Field_Metadata::UITYPE_RECORD_RELATION) {
					$grid = new GridListView($module);
					$grid->tabid = getTabid($module);
					$modules = $grid->findRelatedModule($key['fieldname']);
					if (count($modules) > 1) {
						$listFields['relatedModules'] = $modules;
					}
					foreach ($fields as $label => $value) {
						if (isset($value->relatedModule) && in_array($value->relatedModule, $modules)) {
							$listFields['activeModule'] = (string)$value->relatedModule;
							break;
						}
					}
				}
				return $listFields;
			}, $ModuleFields);
		} else {
			$ListFields = array_map(function ($key) use ($module) {
				$typeofdata = explode('~', $key['typeofdata']);
				$listFields = array(
					'header' => getTranslatedString($key['fieldlabel'], $module),
					'name' => $key['columnname'],
					'active' => 0,
					'typeofdata' => $typeofdata[1],
					'relatedModules' => array()
				);
				if ($key['uitype'] == Field_Metadata::UITYPE_RECORD_RELATION) {
					$grid = new GridListView($module);
					$grid->tabid = getTabid($module);
					$modules = $grid->findRelatedModule($key['fieldname']);
					if (count($modules) > 1) {
						$listFields['relatedModules'] = $modules;
					}
				}
				return $listFields;
			}, $ModuleFields);
		}
		if (isset($xml->match)) {
			$match = (array)$xml->match;
			$MatchFields = array_map(function ($key) use ($match, $module) {
				$listFields = array(
					'header' => getTranslatedString($key['fieldlabel'], $module),
					'name' => $key['columnname'],
					'active' => 0
				);
				if (isset($match['field'])) {
					$match = array_values((array)$match['field']);
					if (count($match) == 1) {
						if ($match[0] == $key['columnname']) {
							$listFields['active'] = 1;
						}
					} else {
						foreach ($match as $field) {
							if ($field == $key['columnname']) {
								$listFields['active'] = 1;
								break;
							}
						}
					}
				}
				return $listFields;
			}, $ModuleFields);
		} else {
			$MatchFields = array_map(function ($key) use ($module) {
				$listFields = array(
					'header' => getTranslatedString($key['fieldlabel'], $module),
					'name' => $key['columnname'],
					'active' => 0
				);
				return $listFields;
			}, $ModuleFields);
		}
		array_multisort($ListFields);
		array_multisort($MatchFields);
		$smarty->assign('MapFields', $ListFields);
		$smarty->assign('MatchFields', $MatchFields);
		$smarty->display('modules/cbMap/MassUpsertGrid.tpl');
	}
}
?>