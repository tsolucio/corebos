<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/cbQuestion/cbQuestion.php';
require_once 'Smarty_setup.php';
require_once 'include/Webservices/DescribeObject.php';
require_once 'modules/com_vtiger_workflow/expression_functions/cbexpSQL.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

if (empty($_REQUEST['record'])) {
	$smarty->assign('cancelgo', 'index.php?module=cbQuestion&action=index');
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_NO_RECORD'));
	$smarty->display('applicationmessage.tpl');
} else {
	if (isset($tool_buttons)==false) {
		$tool_buttons = Button_Check($currentModule);
	}
	$focus = CRMEntity::getInstance($currentModule);
	$record = vtlib_purify($_REQUEST['record']);
	$focus->id = $record;
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->name=$focus->column_fields[$focus->list_link_field];

	$fields = vtws_describe($focus->column_fields['qmodule'], $current_user);
	$flds = array(array(
		'text' => getTranslatedString('Custom', 'Reports'),
		'value' => 'custom',
	));
	foreach ($fields['fields'] as $finfo) {
		$flds[] = array(
			'text' => $finfo['label'],
			'value' => $finfo['name'],
		);
	}
	$valops = array(array(
		'text' => getTranslatedString('Custom', 'Reports'),
		'value' => 'custom',
	));
	foreach (cbexpsql_supportedFunctions() as $vopk => $vopv) {
		$valops[] = array(
			'text' => $vopv,
			'value' => $vopk,
		);
	}
	$fieldData = array(
		array(
			'fieldname' => 'custom',
			'operators' => 'custom',
			'alias' => '',
			'sort' => 'NONE',
			'group' => '0',
			'instruction' => '',
		),
	);
	$smarty->assign('fieldData', json_encode($fieldData));
	$smarty->assign('fieldArray', json_encode(array_values($flds)));
	$smarty->assign('validOperations', json_encode(array_values($valops)));
	$smarty->assign('APP', $app_strings);
	$mod_strings = array_merge($mod_strings, return_module_language($current_user->column_fields['language'], 'com_vtiger_workflow'));
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
	$smarty->assign('ID', $focus->id);
	$smarty->assign('RECORDID', $focus->id);
	$smarty->assign('MODE', $focus->mode);
	$smarty->assign('MODULES', getPicklistValuesSpecialUitypes('1613', '', $module));
	$smarty->assign('targetmodule', $focus->column_fields['qmodule']);
	$smarty->assign('bqname', $focus->column_fields['qname']);
	$smarty->assign('bqcollection', $focus->column_fields['qcollection']);
	$smarty->assign('sqlquery', $focus->column_fields['sqlquery']);
	$smarty->assign('qpagesize', $focus->column_fields['qpagesize']);
	$smarty->assign('qtype', $focus->column_fields['qtype']);
	$smarty->assign('QTYPES', getAssignedPicklistValues('qtype', $current_user->roleid, $adb));
	$smarty->assign('cancelgo', 'index.php?module=cbQuestion&action=DetailView&record='.$focus->id);
	$smarty->display('modules/cbQuestion/Builder.tpl');
}
?>