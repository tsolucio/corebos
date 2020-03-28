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
	$smarty->assign('cancelgo', 'index.php?module=cbQuestion&action=DetailView&record='.$focus->id);
	$smarty->display('modules/cbQuestion/Builder.tpl');
}
?>