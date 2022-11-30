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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

if (!empty($_REQUEST['PROCESSSETTINGS'])) {
	$settings = vtlib_purify($_REQUEST['PROCESSSETTINGS']);
	if (getSalesEntityType($_REQUEST['PROCESSSETTINGS'])=='cbMap') {
		$settings = coreBOS_Rule::evaluate($settings, $_REQUEST);
	}
	$settingmodule = getSalesEntityType($settings);
	if (vtlib_isModuleActive($settingmodule) && isRecordExists($settings)) {
		$settingobj = CRMEntity::getInstance($settingmodule);
		$settingobj->retrieve_entity_info($settings, $settingmodule);
		if ($settingobj->column_fields['active']=='1' && $settingobj->column_fields['semodule']==$_REQUEST['module']) {
			$FieldMapName = getEntityName('cbMap', $settingobj->column_fields['fieldmap']);
			$_REQUEST['FILTERFIELDSMAP'] = $FieldMapName[$settingobj->column_fields['fieldmap']];
			$_REQUEST['FILTERVALMAP'] = $settingobj->column_fields['valmap'];
			$_REQUEST['FILTERDEPMAP'] = $settingobj->column_fields['depmap'];
		} else {
			$_REQUEST['FILTERFIELDSMAP'] = '';
			$_REQUEST['FILTERVALMAP'] = '';
			$_REQUEST['FILTERDEPMAP'] = '';
		}
		$relfield = getFirstFieldForModule($_REQUEST['module'], $settingmodule);
		if (!empty($relfield)) {
			$_REQUEST[$relfield] = $settings;
		}
		$_REQUEST['CANCELGO'] = 'index.php?action=index&module='.urlencode($_REQUEST['module']);
	}
}

if (isInsideApplication('modules/'.$_REQUEST['module'].'/EditView.php')) {
	include_once 'modules/'.$_REQUEST['module'].'/EditView.php';
} else {
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_OPERATION_NOT_SUPPORTED', 'Settings'));
	$smarty->display('applicationmessage.tpl');
}