<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module    : Elasticsearch Settings
 *  Version   : 1.0
 *  Author    : AT Consulting
 *************************************************************************************************/
global $current_user, $adb, $root_directory;
include_once 'include/Webservices/Create.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'include/integrations/elasticsearch/getFields.php';
require_once 'modules/cbMap/cbMap.php';

$smarty = new vtigerCRM_Smarty();

$isAppActive = false;
$fieldsselected = '';
$types = '';
$analyzed = '';
$labels = '';
$error = '';

$arrfieldsselected = array();
$arrtypes = array();
$arranalyzed = array();
$arrlabels = array();

$moduleid = isset($_REQUEST['module_list']) ? vtlib_purify($_REQUEST['module_list']) : '';
$mapid = isset($_REQUEST['bmapid']) ? vtlib_purify($_REQUEST['bmapid']) : '';

$ip = GlobalVariable::getVariable('ip_elastic_server', '', $moduleid);
$prefix = GlobalVariable::getVariable('ip_elastic_indexprefix', '', $moduleid);
if (!isset($prefix) || $prefix=='') {
	$dir = explode('/', $root_directory);
	$countdir = count($dir)-2;
	$prefix = strtolower($dir[$countdir]);
}
$indexname = $prefix.'_'.strtolower($moduleid.'index');

$table = $adb->pquery('select mapid,fieldnames,fieldtypes,isanalyzed,fieldlabels from elasticsearch_indexes where module=?', array($moduleid));
$tablecnt = $adb->num_rows($table);

if (!empty($moduleid) && $_REQUEST['_op']=='setconfigelasticsearch') {
	$isFormActive = ((empty($_REQUEST['rvactive']) || $_REQUEST['rvactive']!='on') ? '0' : '1');

	//check for global variable ip_elastic_server
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('GlobalVariable');
	$recexists = $adb->pquery(
		'select globalvariableid,module_list from vtiger_globalvariable inner join '.$crmEntityTable.' on crmid=globalvariableid where deleted=0 and gvname=?',
		array('ip_elastic_server')
	);
	$count = $adb->num_rows($recexists);
	$module_list = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $adb->query_result($recexists, 0, 1));
	$gvid = ($count>0 ? $adb->query_result($recexists, 0, 0) : '');

	//check for global variable ip_elastic_indexprefix
	$recexists2 = $adb->pquery(
		'select globalvariableid,module_list from vtiger_globalvariable inner join '.$crmEntityTable.' on crmid=globalvariableid where deleted=0 and gvname=?',
		array('ip_elastic_indexprefix')
	);
	$count2 = $adb->num_rows($recexists2);
	$module_list2 = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $adb->query_result($recexists2, 0, 1));
	$gvid2 = ($count2>0 ? $adb->query_result($recexists2, 0, 0) : '');

	if ($isFormActive=='1') {
		$countfields = isset($_REQUEST['countfields']) ? vtlib_purify($_REQUEST['countfields']) : 0;
		for ($i = 0; $i<$countfields; $i++) {
			$fldindex = $i+1;
			if (isset($_REQUEST['checkf'.$fldindex]) && $_REQUEST['checkf'.$fldindex]=='on') {
				$arrfieldsselected[] = vtlib_purify($_REQUEST['colname'.$fldindex]);
				$arrtypes[] = vtlib_purify($_REQUEST['modulfieldtype'.$fldindex]);
				$arranalyzed[] = isset($_REQUEST['checkanalyzed'.$fldindex]) ? vtlib_purify($_REQUEST['checkanalyzed'.$fldindex]) : '';
				$arrlabels[] = strtolower(str_replace(' ', '', vtlib_purify($_REQUEST['modulfieldlabel'.$fldindex])));
			}
		}
		$fieldsselected = implode('##', $arrfieldsselected);
		$types = implode('##', $arrtypes);
		$analyzed = implode('##', $arranalyzed);
		$labels = implode('##', $arrlabels);

		//insert record in table elasticsearch and create index mapping
		if ($tablecnt == 0) {
			if (isset($ip) && $ip != '') {
				$adb->pquery('insert into elasticsearch_indexes (indexname,module,mapid,fieldnames,fieldtypes,isanalyzed,fieldlabels) values (?,?,?,?,?,?,?)', array($indexname,$moduleid,$mapid,$fieldsselected,$types,$analyzed,$labels));
				createindexmapping($ip, $indexname, $arrlabels, $arrtypes, $arranalyzed, $moduleid);
			} else {
				$error = 1;
			}
		}

		if ($count > 0 && !in_array($moduleid, $module_list)) {
			$adb->pquery("update vtiger_globalvariable set module_list=CONCAT(module_list,' |##| $moduleid') where globalvariableid=?", array($gvid));
		} elseif ($count == 0) {
			vtws_create('GlobalVariable', array(
				'gvname' => 'ip_elastic_server',
				'default_check' => '0',
				'value' => $ip,
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => $moduleid,
				'category' => 'System',
				'in_module_list' => '1',
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
			), $current_user);
		}

		if ($count2 > 0 && !in_array($moduleid, $module_list2)) {
			$adb->pquery("update vtiger_globalvariable set module_list=CONCAT(module_list,' |##| $moduleid') where globalvariableid=?", array($gvid2));
		} elseif ($count2 == 0) {
			vtws_create('GlobalVariable', array(
				'gvname' => 'ip_elastic_indexprefix',
				'default_check' => '0',
				'value' => $prefix,
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => $moduleid,
				'category' => 'System',
				'in_module_list' => '1',
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
			), $current_user);
		}

		//create event handler
		$evhandler = $adb->pquery("select is_active,eventhandler_id from vtiger_eventhandlers where handler_class='ElasticsearchEventsHandler'", array());
		$counteh = $adb->num_rows($evhandler);
		if ($counteh > 0) {
			$isactive1 = $adb->query_result($evhandler, 0, 0);
			$ehid1 = $adb->query_result($evhandler, 0, 1);
			if ($isactive1 != 1) {
				$adb->pquery('update vtiger_eventhandlers set is_active=1 where eventhandler_id=?', array($ehid1));
			}
			$isactive2 = $adb->query_result($evhandler, 1, 0);
			$ehid2 = $adb->query_result($evhandler, 1, 1);
			if ($isactive2 != 1) {
				$adb->pquery('update vtiger_eventhandlers set is_active=1 where eventhandler_id=?', array($ehid2));
			}
		} else {
			$em = new VTEventsManager($adb);
			$em->registerHandler('vtiger.entity.aftersave', 'modules/Utilities/ElasticsearchHandler.php', 'ElasticsearchEventsHandler');
			$em = new VTEventsManager($adb);
			$em->registerHandler('vtiger.entity.beforedelete', 'modules/Utilities/ElasticsearchHandler.php', 'ElasticsearchEventsHandler');
		}

		$isAppActive = true;
	} else {
		//delete index in elasticsearch
		deleteindex($ip, $indexname);
		$adb->pquery('delete from elasticsearch_indexes where module=?', array($moduleid));
		$index = array_search($moduleid, $module_list);
		unset($module_list[$index]);
		if (!empty($module_list)) {
			$module_del = implode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $module_list);
		} else {
			$module_del = '';
		}
		$adb->pquery("update vtiger_globalvariable set module_list='$module_del' where globalvariableid=?", array($gvid));
		$adb->pquery("update vtiger_globalvariable set module_list='$module_del' where globalvariableid=?", array($gvid2));
		$isAppActive = false;
		$mapid = '';
	}
} else {
	if ($tablecnt>0) {
		$isAppActive = true;
		$mapid = $adb->query_result($table, 0, 0);
		$arrfieldsselected = explode('##', $adb->query_result($table, 0, 1));
		$arrtypes = explode('##', $adb->query_result($table, 0, 2));
		$arranalyzed = explode('##', $adb->query_result($table, 0, 3));
		$arrlabels = explode('##', $adb->query_result($table, 0, 4));
	}
}

$entitymodules = getAllowedPicklistModules(0);
$opt = '';
foreach ($entitymodules as $module) {
	if ($moduleid == $module) {
		$selected='selected';
	} else {
		$selected = '';
	}
	$opt.="<option value='$module' $selected>".getTranslatedString($module, $module).'</option>';
}

if ($mapid != '' && $mapid != 0) {
	$mapname = getEntityName('cbMap', $mapid);
} else {
	$mapname[$mapid] = '';
}
//get fields for elasticsearch mapping
$fields = getFields($moduleid, $arrfieldsselected, $arrtypes, $arranalyzed, $arrlabels);

$smarty->assign('isActive', $isAppActive);
$smarty->assign('tablemapid', $mapid);
$smarty->assign('mapname', $mapname[$mapid]);
$smarty->assign('fields', $fields);
$smarty->assign('TITLE_MESSAGE', 'ElasticSearch');
$smarty->assign('MODULELIST', $opt);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('ERROR', $error);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'include/integrations/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/elasticsearch.tpl');
?>
