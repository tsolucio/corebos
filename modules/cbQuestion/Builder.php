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
require_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc';
require_once 'vtlib/Vtiger/controllers/ActionController.php';
require_once 'modules/cbMap/actions/mapactions.php';
require_once 'include/Webservices/GetRelatedModulesOneToMany.php';
global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

if (!isset($tool_buttons)) {
	$tool_buttons = Button_Check($currentModule);
}

$focus = CRMEntity::getInstance($currentModule);
if (!empty($_REQUEST['record'])) {
	$record = vtlib_purify($_REQUEST['record']);
	$focus->id = $record;
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->name=$focus->column_fields[$focus->list_link_field];
	if (empty($focus->column_fields['qmodule'])) {
		$fields = array('fields'=>array());
	} else {
		$fields = vtws_describe($focus->column_fields['qmodule'], $current_user);
	}
	if ($focus->column_fields['qtype']!='Mermaid') {
		$smarty->assign('QSQL', cbQuestion::getSQL($record));
	}
	$smarty->assign('headerurl', 'index.php?action=DetailView&module='.$currentModule.'&record='.$record);
} else {
	$fields = array('fields'=>array());
	$smarty->assign('headerurl', 'index.php?action=ListView&module=cbQuestion');
}
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
$smarty->assign('fieldData', json_encode($focus->convertColumns2DataTable()));
$smarty->assign('fieldArray', json_encode(array_values($flds)));
$smarty->assign('validOperations', json_encode(array_values($valops)));
$fldnecol = $adb->query(
	'SELECT concat(vtiger_tab.name,fieldname) as mfld,columnname
	FROM vtiger_field
	INNER JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_field.tabid
	WHERE fieldname!=columnname and fieldname!="assigned_user_id" and fieldname!="created_user_id"'
);
$fnec = array();
while ($r = $fldnecol->FetchRow()) {
	$fnec[$r['mfld']] = $r['columnname'];
}
$smarty->assign('fieldNEcolumn', json_encode($fnec));

$_REQUEST['fieldsmodule'] = $focus->column_fields['qmodule'];
$smarty->assign('fieldTableRelation', json_encode(mapactions_Action::getFieldTablesForModule(true)));
if (!empty($_REQUEST['record'])) {
	try {
		$smarty->assign('rel1tom', GetRelatedModulesOneToMany($focus->column_fields['qmodule'], $current_user));
	} catch (\Throwable $th) {
		$smarty->assign('rel1tom', '');
	}
}
$actormodules = $adb->query('SELECT name FROM vtiger_ws_entity WHERE handler_path="include/Webservices/VtigerActorOperation.php"');
$amods = $amodsi18n = array();
while ($r = $actormodules->FetchRow()) {
	$amods[] = $r['name'];
	$amodsi18n[] = array(
		getTranslatedString($r['name'], $r['name']),
		$r['name'],
		($focus->column_fields['qmodule'] == $r['name'] ? 'selected' : '')
	);
}
$smarty->assign('actorModules', json_encode($amods));
$smarty->assign('isActorModule', in_array($focus->column_fields['qmodule'], $amods));
$smarty->assign('APP', $app_strings);
$mod_strings = array_merge($mod_strings, return_module_language($current_user->column_fields['language'], 'com_vtiger_workflow'));
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('WSID', vtws_getEntityId('cbQuestion').'x');
$smarty->assign('ID', $focus->id);
$smarty->assign('RECORDID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('MODULES', array_merge(getPicklistValuesSpecialUitypes('1613', '', $focus->column_fields['qmodule']), $amodsi18n));
$smarty->assign('targetmodule', $focus->column_fields['qmodule']);
$smarty->assign('bqname', $focus->column_fields['qname']);
$smarty->assign('bqcollection', $focus->column_fields['qcollection']);
$smarty->assign('sqlquery', $focus->column_fields['sqlquery']);
$smarty->assign('qpagesize', $focus->column_fields['qpagesize']);
$smarty->assign('qtype', empty($focus->column_fields['qtype']) ? 'Table' : $focus->column_fields['qtype']);

if (empty($focus->column_fields['typeprops'])) {
	$smarty->assign('typeprops', '"{}"');
} else {
	$smarty->assign('typeprops', decode_html(str_replace('\\', '', $focus->column_fields['typeprops'])));
}

$smarty->assign('questioncolumns', decode_html($focus->column_fields['qcolumns']));
$smarty->assign('cbqconditons', empty($focus->column_fields['qcondition']) ? null : decode_html($focus->column_fields['qcondition']));
$emgr = new VTExpressionsManager($adb);
$smarty->assign('FNDEFS', json_encode($emgr->expressionFunctionDetails()));
$smarty->assign('QTYPES', getAssignedPicklistValues('qtype', $current_user->roleid, $adb));
$smarty->assign('cancelgo', 'index.php?module=cbQuestion&action=DetailView&record='.$focus->id);
$smarty->display('modules/cbQuestion/Builder.tpl');
?>