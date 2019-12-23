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
 *  Module    : Record Versioning Settings
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
global $current_user, $adb;
include_once 'include/Webservices/Create.php';
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/events/include.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';

$smarty = new vtigerCRM_Smarty();

$moduleid = isset($_REQUEST['module_list']) ? vtlib_purify($_REQUEST['module_list']) : '';
//check for global variable
$recexists = $adb->pquery(
	'select globalvariableid,module_list from vtiger_globalvariable inner join vtiger_crmentity on crmid=globalvariableid where deleted=0 and gvname=?',
	array('RecordVersioningModules')
);
$count = $adb->num_rows($recexists);
if ($count > 0) {
	$module_list = explode(' |##| ', $adb->query_result($recexists, 0, 1));
	$gvid = $adb->query_result($recexists, 0, 0);
} else {
	$module_list = array();
	$gvid = 0;
}
$isAppActive = false;
if (!empty($moduleid) && $_REQUEST['_op']=='setconfigrecordversioning') {
	$isFormActive = ((empty($_REQUEST['rvactive']) || $_REQUEST['rvactive']!='on') ? '0' : '1');
	//check for business action
	$ba = $adb->pquery(
		'select businessactionsid,module_list from vtiger_businessactions join vtiger_crmentity on crmid=businessactionsid where deleted=0 and active=? and linklabel=?',
		array('1', 'Revisiones')
	);
	$bacount = $adb->num_rows($ba);
	if ($ba && $bacount>0) {
		$baid = $adb->query_result($ba, 0, 0);
		$module_listba = $adb->query_result($ba, 0, 1);
	} else {
		$baid = 0;
		$module_listba = '';
	}
	//check for workflow
	$wfquery = $adb->pquery("select workflow_id from com_vtiger_workflows where module_name=? and summary='updaterevisionwf'", array($moduleid));
	$wfcount = $adb->num_rows($wfquery);

	if ($isFormActive=='1') {
		if ($count > 0 && !in_array($moduleid, $module_list)) {
			$adb->pquery("update vtiger_globalvariable set module_list=CONCAT(module_list,' |##| $moduleid') where globalvariableid=?", array($gvid));
		} elseif ($count==0) {
			vtws_create('GlobalVariable', array(
				'gvname' => 'RecordVersioningModules',
				'default_check' => '0',
				'value' => '1',
				'mandatory' => '0',
				'blocked' => '0',
				'module_list' => $moduleid,
				'category' => 'System',
				'in_module_list' => '1',
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
			), $current_user);
		}

		if ($bacount > 0 && !in_array($moduleid, $module_list)) {
			$adb->pquery("update vtiger_businessactions set module_list=CONCAT(module_list,' |##| $moduleid') where businessactionsid=?", array($baid));
		} elseif ($bacount==0) {
			vtws_create('BusinessActions', array(
				'linklabel' => 'Revisiones',
				'active' => '1',
				'linktype' => 'DETAILVIEWWIDGET',
				'linkurl' => 'module=Utilities&action=UtilitiesAjax&file=revisionblock&record=$RECORD$&currmodule=$MODULE$',
				'mandatory' => '1',
				'module_list' => $moduleid,
				'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
			), $current_user);
		}

		//create fields
		$blockquery = $adb->pquery('select blockid from vtiger_blocks where visible=0 and tabid=? limit 1', array(getTabid($moduleid)));
		$blockid = $adb->query_result($blockquery, 0, 0);
		$block = Vtiger_Block::getInstance($blockid);

		$mod = Vtiger_Module::getInstance($moduleid);
		$fld = Vtiger_Field::getInstance('revision', $mod);
		if (!$fld) {
			$field1 = new Vtiger_Field();
			$field1->name = 'revision';
			$field1->label= 'Revision';
			$field1->column = 'revision';
			$field1->columntype = 'VARCHAR(100)';
			$field1->uitype = 1;
			$field1->typeofdata = 'V~O';
			$field1->displaytype = 2;
			$field1->presence = 0;
			$block->addField($field1);
		}
		$fld2 = Vtiger_Field::getInstance('revisionactiva', $mod);
		if (!$fld2) {
			$field2 = new Vtiger_Field();
			$field2->name = 'revisionactiva';
			$field2->label= 'Active Revision';
			$field2->column = 'revisionactiva';
			$field2->columntype = 'VARCHAR(3)';
			$field2->uitype = 1;
			$field2->typeofdata = 'V~O';
			$field2->displaytype = 2;
			$field2->presence = 0;
			$block->addField($field2);
		}
		//create event handler
		$evhandler = $adb->pquery("select is_active,eventhandler_id from vtiger_eventhandlers where handler_class='UtilitiesEventsHandler'", array());
		$counteh = $adb->num_rows($evhandler);
		if ($counteh > 0) {
			$isactive = $adb->query_result($evhandler, 0, 0);
			$ehid = $adb->query_result($evhandler, 0, 1);
			if ($isactive != 1) {
				$adb->pquery('update vtiger_eventhandlers set is_active=1 where eventhandler_id=?', array($ehid));
			}
		} else {
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.filter.listview.querygenerator.before', 'modules/Utilities/UtilitiesHandler.php', 'UtilitiesEventsHandler');
		}
		//create workflow
		if ($wfcount == 0) {
			$emm = new VTEntityMethodManager($adb);
			$emm->addEntityMethod($moduleid, 'updaterevisionwf', 'modules/Utilities/updaterevisionwf.php', 'updaterevisionwf');
			require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
			require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
			require_once 'modules/com_vtiger_workflow/VTWorkflowApplication.inc';
			require_once 'include/events/SqlResultIterator.inc';
			$wm = new VTWorkflowManager($adb);
			$wf = $wm->newWorkflow($moduleid);
			$wf->description = 'updaterevisionwf';
			$wf->test = '';
			$wf->executionConditionAsLabel('ON_FIRST_SAVE');
			$wm->save($wf);

			$tm = new VTTaskManager($adb);
			$taskType ='VTEntityMethodTask' ;
			$workflowId =$wf->id;
			$task = $tm->createTask($taskType, $workflowId);
			$task->summary ='updaterevisionwf';
			$task->active=true;
			$task->methodName ='updaterevisionwf';
			$task->subject='updaterevisionwf';
			$tm->saveTask($task);
		}
		$isAppActive = true;
	} else {
		$wfid = $adb->query_result($wfquery, 0, 0);
		$wm = new VTWorkflowManager($adb);
		$wm->delete($wfid);

		$index = array_search($moduleid, $module_list);
		unset($module_list[$index]);
		if (count($module_list)>0) {
			$module_del = implode(' |##| ', $module_list);
		} else {
			$module_del = '';
		}
		$adb->pquery("update vtiger_globalvariable set module_list='$module_del' where globalvariableid=?", array($gvid));
		$adb->pquery("update vtiger_businessactions set module_list='$module_del' where businessactionsid=?", array($baid));
		$isAppActive = false;
	}
} else {
	if ($count>0 && in_array($moduleid, $module_list)) {
		$isAppActive = true;
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

$smarty->assign('isActive', $isAppActive);

$smarty->assign('TITLE_MESSAGE', getTranslatedString('Record Versioning', $currentModule));
$smarty->assign('MODULELIST', $opt);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('ISADMIN', is_admin($current_user));
$smarty->display('modules/Utilities/recordversioning.tpl');
?>
