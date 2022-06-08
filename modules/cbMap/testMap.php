<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Map
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'Smarty_setup.php';
require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';
error_reporting(-1);
ini_set('display_errors', 1);
global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $singlepane_view;

$smarty = new vtigerCRM_Smarty();

$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$tool_buttons = array(
	'EditView' => 'no',
	'CreateView' => 'no',
	'index' => 'yes',
	'Import' => 'no',
	'Export' => 'no',
	'Merge' => 'no',
	'DuplicatesHandling' => 'no',
	'Calendar' => 'no',
	'moduleSettings' => 'no',
);
$smarty->assign('CHECK', $tool_buttons);

if (empty($_REQUEST['record'])) {
	$smarty->assign('ERROR_MESSAGE', 'Missing Map ID (record)');
	$smarty->display('modules/cbMap/testMap.tpl');
	die();
}
$mapid = vtlib_purify($_REQUEST['record']);

$focus = new cbMap();
$focus->id = $mapid;
$focus->mode = '';
$focus->retrieve_entity_info($mapid, $currentModule);
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);

$recordName = array_values(getEntityName($currentModule, $focus->id));
$recordName = $recordName[0];
$smarty->assign('NAME', $recordName);
$smarty->assign('UPDATEINFO', updateInfo($focus->id));
$smarty->assign('MAPTYPE', $focus->column_fields['maptype']);

$mapinfo = array();

$contentok = processcbMap::isXML(html_entity_decode($focus->column_fields['content'], ENT_QUOTES, 'UTF-8'));

if ($contentok !== true) {
	$smarty->assign('MAPINFO', $mapinfo);
	$smarty->assign('ERROR_MESSAGE', '<b>Incorrect Content</b><br>'.$contentok);
	$smarty->display('modules/cbMap/testMap.tpl');
	die();
}

switch ($focus->column_fields['maptype']) {
	case 'Condition Query':
		if (!empty($_REQUEST['testrecord'])) {
			$testrecord = vtlib_purify($_REQUEST['testrecord']);
			if (strpos($testrecord, 'x')>0) {
				list($wsid, $testrecord) = explode('x', $testrecord);
			}
		} else {
			$testrecord = 74;
		}
		$currentModule = getSalesEntityType($testrecord);
		$mapinfo = (array) $focus->ConditionQuery($testrecord);
		$currentModule = 'cbMap';
		$mapinfo['TEST RECORD'] = "<p class='slds-app-launcher__tile-body'>Testing with $testrecord</p>";
		break;
	case 'Condition Expression':
		if (!empty($_REQUEST['testrecord'])) {
			$testrecord = vtlib_purify($_REQUEST['testrecord']);
			if (strpos($testrecord, 'x')===false) {
				$testrecord = vtws_getEntityId(getSalesEntityType($testrecord)).'x'.$testrecord;
			}
		} else {
			$testrecord = '11x74';
		}
		list($wsid, $crmid) = explode('x', $testrecord);
		$currentModule = getSalesEntityType($crmid);
		$mapinfo = (array) $focus->ConditionExpression($testrecord);
		$currentModule = 'cbMap';
		$mapinfo['TEST RECORD'] = "<p class='slds-app-launcher__tile-body'>Testing with $testrecord</p>";
		break;
	case 'Mapping':
		if (!empty($_REQUEST['testrecord'])) {
			$testrecord = vtlib_purify($_REQUEST['testrecord']);
			if (strpos($testrecord, 'x')>0) {
				list($wsid, $testrecord) = explode('x', $testrecord);
			}
			$testModule = getSalesEntityType($testrecord);
			$sofocus = CRMEntity::getInstance($testModule);
			$sofocus->retrieve_entity_info($testrecord, $testModule);
			$recfields = $sofocus->column_fields;
		} elseif (isset($_REQUEST['testrecord'])) {
			$recfields = array();
		} else {
			$sofocus = CRMEntity::getInstance('SalesOrder');
			$sofocus->retrieve_entity_info(10569, 'SalesOrder');
			$recfields = $sofocus->column_fields;
		}
		foreach ($_REQUEST as $key => $value) {
			if ($key=='module' || $key=='testrecord' || $key=='record' || $key=='action') {
				continue;
			}
			$recfields[$key] = $value;
		}
		$mapinfo = $focus->Mapping($recfields, array('sentin'=>'notmodified'));
		break;
	case 'Record Access Control':
		$rac = $focus->RecordAccessControl();
		$rac->setRelatedRecordID(6004);
		foreach (array('create','retrieve','update','delete') as $op) {
			echo 'Listview '.$op.' = '.$rac->hasListViewPermissionTo($op)."<br>";
			echo 'DetailView '.$op.' = '.$rac->hasDetailViewPermissionTo($op)."<br>";
		}
		foreach (array('create','retrieve','update','delete','select') as $op) {
			echo 'RelatedList Invoice '.$op.' = '.$rac->hasRelatedListPermissionTo($op, 'Invoice')."<br>";
			echo 'RelatedList Potentials '.$op.' = '.$rac->hasRelatedListPermissionTo($op, 'Potentials')."<br>";
			echo 'RelatedList ProjectMilestone '.$op.' = '.$rac->hasRelatedListPermissionTo($op, 'ProjectMilestone')."<br>";
			echo 'RelatedList ProjectTask '.$op.' = '.$rac->hasRelatedListPermissionTo($op, 'ProjectTask')."<br>";
		}
		break;
	case 'Record Set Mapping':
		$rsm = $focus->RecordSetMapping();
		$mapinfo = $rsm->getFullRecordSet();
		break;
	case 'ListColumns':
		$rsm = $focus->ListColumns();
		$mapinfo = $rsm->getCompleteMapping();
		break;
	case 'Kanban':
		$mapinfo = $focus->Kanban();
		break;
	case 'DuplicateRelations':
		$rsm = $focus->DuplicateRelations();
		$mapinfo = $rsm->getCompleteMapping();
		break;
	case 'RelatedPanes':
		$mapinfo = $focus->RelatedPanes(array(74));
		break;
	case 'Import':
		$mapinfo = $focus->Import()->getCompleteMapping();
		$mapinfo['TargetModule'] = $focus->Import()->getMapTargetModule();
		break;
	case 'Map fields':
		$mapinfo = $focus->readMappingType();
		$mapinfo['TargetModule'] = $focus->getMapTargetModule();
		$mapinfo['OriginModule'] = $focus->getMapOriginModule();
		break;
	case 'MasterDetailLayout':
		$mapinfo = $focus->MasterDetailLayout();
		break;
	case 'SendMail':
		$mapinfo = $focus->getMapMessageMailer();
		break;
	case 'Block Access':
		$mapinfo = $focus->getBlockAccessBlockInfo();
		$mapinfo['OriginModule'] = $focus->getMapOriginModule();
		break;
	case 'IOMap':
		$mapinfo['InputFields'] = $focus->IOMap()->readInputFields();
		$mapinfo['OutputFields'] = $focus->IOMap()->readOutputFields();
		break;
	case 'Search and Update':
		$mapinfo = $focus->read_map();
		break;
	case 'FieldInfo':
		$mapinfo = $focus->FieldInfo();
		break;
	case 'GlobalSearchAutocomplete':
		$mapinfo = $focus->GlobalSearchAutocomplete();
		break;
	case 'FieldDependency':
		$mapinfo = $focus->FieldDependency();
		break;
	case 'Validations':
		$mapinfo = $focus->Validations(
			array(
				'accountname' => 'Chemex',
				'industry' => 'Banking',
				'email1' => 'sdsdsd',
			),
			74
		);
		break;
	case 'Field Set Mapping':
		$fsm = $focus->FieldSetMapping();
		$mapinfo = $fsm->getFieldSet();
		break;
	case 'ApplicationMenu':
		if (!empty($_REQUEST['testrecord'])) {
			$mapinfo = json_decode($focus->ApplicationMenu(['menuname' => $_REQUEST['testrecord']]));
		} else {
			$mapinfo = json_decode($focus->ApplicationMenu());
		}
		break;
	case 'Detail View Layout Mapping':
		$mapinfo = $focus->DetailViewLayoutMapping();
		break;
	case 'Webservice Mapping':
		if (!empty($_REQUEST['testrecord'])) {
			$testrecord = vtlib_purify($_REQUEST['testrecord']);
			if (strpos($testrecord, 'x')>0) {
				list($wsid, $testrecord) = explode('x', $testrecord);
			}
		} else {
			$testrecord = 74;
		}
		$setype = getSalesEntityType($testrecord);
		$focus2 = CRMEntity::getInstance($setype);
		$focus2->retrieve_entity_info($testrecord, $setype);
		$context = array(
			'myvariable' => 'my var',
		);
		$mapinfo = $focus->WebserviceMapping($focus2->column_fields, $context);
		break;
	case 'DecisionTable':
		$context = array(
			'season' => isset($_REQUEST['season']) ? $_REQUEST['season'] : 'Fall',
			'guestcount' => isset($_REQUEST['guest']) ? $_REQUEST['guest'] : 8,
			'numyears' => isset($_REQUEST['numyears']) ? $_REQUEST['numyears'] : 2,
			'record_id' => 74,
		);
		foreach ($_REQUEST as $key => $value) {
			if ($key=='record') {
				continue;
			}
			$context[$key] = $value;
		}
		$mapinfo = array(
			'result' => $focus->DecisionTable($context),
			'info' => $focus->mapExecutionInfo,
		);
		break;
	default:
		break;
}
$smarty->assign('MAPINFO', $mapinfo);
$smarty->display('modules/cbMap/testMap.tpl');
?>
