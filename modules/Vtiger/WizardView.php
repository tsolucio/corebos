<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of coreBOS Customizations.
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
require_once 'modules/Utilities/showSetOfFieldsWidget.php';

global $adb;
if (empty($smarty)) {
	global $theme, $app_strings, $current_language;
	$smarty = new vtigerCRM_Smarty();
}
if (isset($_REQUEST['bmapname'])) {
	$bmapname = vtlib_purify($_REQUEST['bmapname']);
} else {
	$bmapname = $currentModule.'_Wizard';
}
$data = array();
if (isset($_REQUEST['data'])) {
	$data = json_decode($_REQUEST['data'], true);
}
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$cbMapWz = $cbMap->Wizard();
	if (empty($cbMapWz)) {
		$smarty->assign('showDesert', true);
	} else {
		$setfield = new showSetOfFields_DetailViewBlock();
		$smarty->assign('showDesert', false);
		$smarty->assign('wizardTitle', $cbMapWz['title']);
		$smarty->assign('wizardOperation', $cbMapWz['operation']);
		$smarty->assign('wizardInstantShow', $cbMapWz['instantshow']);
		$smarty->assign('wizardTotal', $cbMapWz['totalsteps']);
		$smarty->assign('wizardSteps', $cbMapWz['steps']);
		$views = array();
		$step = 0;
		foreach ($cbMapWz['steps'] as $key => $value) {
			if (!is_numeric($value['detailviewlayoutmap'])) {
				$views[$key] = '';
				continue;
			}
			$context = array(
				'mapid' => $value['detailviewlayoutmap'],
			);
			$view = $setfield->process($context);
			$smarty->assign('WizardStep', $step);
			$views[$key] = $view;
			$step++;
		}
		$smarty->assign('wizardViews', $views);
		$smarty->assign('isModal', empty($data) ? 0 : true);
		$smarty->assign('gridInstance', !empty($data) ? $data['grid'] : '');
		$smarty->assign('RecordID', !empty($data) ? $data['recordid'] : 0);
		$smarty->assign('SubWizardInfo', '');
		if ($cbMapWz['instantshow'] && !empty($data['modname']) && !empty($cbMapWz['subwizardmainfield'])) {
			$focus = CRMEntity::getInstance($data['modname']);
			$focus->retrieve_entity_info($data['recordid'], $data['modname']);
			$smarty->assign('SubWizardInfo', $focus->column_fields[$cbMapWz['subwizardmainfield']]);
		}
	}
} else {
	$smarty->assign('showDesert', true);
}
$smarty->assign('moduleView', 'Wizard');
$smarty->assign('moduleShowSearch', false);
?>