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
require_once('Smarty_setup.php');
require_once('modules/cbMap/cbMap.php');
require_once('modules/cbMap/processmap/processMap.php');

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $singlepane_view;

$smarty = new vtigerCRM_Smarty();

$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);

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

$contentok = processcbMap::isXML(htmlspecialchars_decode($focus->column_fields['content']));

if ($contentok !== true) {
	$smarty->assign('ERROR_MESSAGE', '<b>Incorrect Content</b><br>'.$contentok);
	$smarty->display('modules/cbMap/testMap.tpl');
	die();
}

$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);

$recordName = array_values(getEntityName($currentModule, $focus->id));
$recordName = $recordName[0];
$smarty->assign('NAME', $recordName);
$smarty->assign('UPDATEINFO',updateInfo($focus->id));
$smarty->assign('MAPTYPE', $focus->column_fields['maptype']);
$mapinfo = array();
switch ($focus->column_fields['maptype']) {
	case 'Condition Query':
		$mapinfo = $focus->ConditionQuery(74);
		break;
	case 'Condition Expression':
		$mapinfo = $focus->ConditionExpression('11x74');
		break;
	case 'Mapping':
		$sofocus = CRMEntity::getInstance('SalesOrder');
		$sofocus->retrieve_entity_info(10569, 'SalesOrder');
		$mapinfo = $focus->Mapping($sofocus->column_fields,array('sentin'=>'notmodified'));
		break;
	case 'Record Access Control':
		$rac = $focus->RecordAccessControl();
		$rac->setRelatedRecordID(6004);
		foreach (array('create','retrieve','update','delete') as $op) {
			echo 'Listview '.$op.' = '.$rac->hasListViewPermissionTo($op)."<br>";
			echo 'DetailView '.$op.' = '.$rac->hasDetailViewPermissionTo($op)."<br>";
		}
		foreach (array('create','retrieve','update','delete','select') as $op) {
			echo 'RelatedList Invoice '.$op.' = '.$rac->hasRelatedListPermissionTo($op,'Invoice')."<br>";
			echo 'RelatedList Potentials '.$op.' = '.$rac->hasRelatedListPermissionTo($op,'Potentials')."<br>";
			echo 'RelatedList ProjectMilestone '.$op.' = '.$rac->hasRelatedListPermissionTo($op,'ProjectMilestone')."<br>";
			echo 'RelatedList ProjectTask '.$op.' = '.$rac->hasRelatedListPermissionTo($op,'ProjectTask')."<br>";
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
	case 'DuplicateRelations':
			$rsm = $focus->DuplicateRelations();
			$mapinfo = $rsm->getCompleteMapping();
			break;
	case 'RelatedPanes':
			$mapinfo = $focus->RelatedPanes();
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
	case 'FieldDependency':
		$mapinfo = $focus->FieldDependency()->getCompleteMapping();
		$mapinfo['TargetModule'] = $focus->FieldDependency()->getMapTargetModule();
		$mapinfo['OriginModule'] = $focus->FieldDependency()->getMapOriginModule();
		break;
	default:

		break;
}
$smarty->assign('MAPINFO', $mapinfo);
$smarty->display('modules/cbMap/testMap.tpl');
?>
