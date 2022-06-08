<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/events/include.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'include/Webservices/Create.php';
require_once 'modules/BusinessActions/BusinessActions.php';

class RecordVersionUtils {
	public $globalvar = 0;
	public $modulelist = '';
	public $baction = 0;
	public $moduleid = 0;

	public function __construct($moduleid) {
		global $adb, $current_user;
		$this->moduleid = $moduleid;
		//check for global variable
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('GlobalVariable');
		$recexists = $adb->pquery(
			'select globalvariableid,module_list from vtiger_globalvariable inner join '.$crmEntityTable.' on crmid=globalvariableid where deleted=0 and gvname=?',
			array('RecordVersioningModules')
		);
		$count = $adb->num_rows($recexists);
		if ($count > 0) {
			$this->modulelist = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $adb->query_result($recexists, 0, 1));
			$this->globalvar = $adb->query_result($recexists, 0, 0);
			if (!in_array($moduleid, $this->modulelist)) {
				$adb->pquery("update vtiger_globalvariable set module_list=CONCAT(module_list,' |##| $moduleid') where globalvariableid=?", array($this->globalvar));
			}
		} else {
			$gv = vtws_create(
				'GlobalVariable',
				array(
					'gvname' => 'RecordVersioningModules',
					'default_check' => '0',
					'value' => '1',
					'mandatory' => '0',
					'blocked' => '0',
					'module_list' => $moduleid,
					'category' => 'System',
					'in_module_list' => '1',
					'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
				),
				$current_user
			);
			$this->modulelist = array($moduleid);
			list($wsid, $this->globalvar) = explode('x', $gv['id']);
		}
		//check for business action
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		$ba = $adb->pquery(
			'select businessactionsid from vtiger_businessactions join '.$crmEntityTable.' on crmid=businessactionsid where deleted=0 and active=? and linklabel=?',
			array('1', 'Revisiones')
		);
		$bacount = $adb->num_rows($ba);
		if ($ba && $bacount>0) {
			$this->baction = $adb->query_result($ba, 0, 0);
			if (!in_array($moduleid, $this->modulelist)) {
				$adb->pquery("update vtiger_businessactions set module_list=CONCAT(module_list,' |##| $moduleid') where businessactionsid=?", array($this->baction));
			}
		} else {
			$bact = vtws_create(
				'BusinessActions',
				array(
					'linklabel' => 'Revisiones',
					'active' => '1',
					'linktype' => 'DETAILVIEWWIDGET',
					'linkurl' => 'module=Utilities&action=UtilitiesAjax&file=revisionblock&record=$RECORD$&currmodule=$MODULE$',
					'mandatory' => '1',
					'module_list' => $moduleid,
					'assigned_user_id' => vtws_getEntityId('Users').'x'.$current_user->id,
				),
				$current_user
			);
			list($wsid, $this->baction) = explode('x', $bact['id']);
		}
	}

	public function recverChangeModuleVisibility($status) {
		if ($status == 'module_disable') {
			$this->disableRecordVersionForModule();
		} else {
			$this->enableRecordVersionForModule();
		}
	}

	public static function recverGetModuleinfo() {
		return BusinessActions::getModuleLinkStatusInfoSortedFlat('DETAILVIEWWIDGET', 'Revisiones');
	}

	/**
	 *Invoked to enable widget for the module.
	 */
	public function enableRecordVersionForModule() {
		global $adb;
		//create fields
		$blockquery = $adb->pquery('select blockid from vtiger_blocks where visible=0 and tabid=? limit 1', array(getTabid($this->moduleid)));
		$blockid = $adb->query_result($blockquery, 0, 0);
		$block = Vtiger_Block::getInstance($blockid);
		$mod = Vtiger_Module::getInstance($this->moduleid);
		$fld = Vtiger_Field::getInstance('revision', $mod);
		if (!$fld) {
			$field1 = new Vtiger_Field();
			$field1->name = 'revision';
			$field1->label= 'Revision';
			$field1->column = 'revision';
			$field1->columntype = 'VARCHAR(100)';
			$field1->uitype = 1;
			$field1->typeofdata = 'V~O';
			$field1->displaytype = 4;
			$field1->presence = 0;
			$block->addField($field1);
			$adb->pquery('update '.$mod->basetable.' set revision=?', array('1'));
		}
		$fld2 = Vtiger_Field::getInstance('revisionactiva', $mod);
		if (!$fld2) {
			$field2 = new Vtiger_Field();
			$field2->name = 'revisionactiva';
			$field2->label= 'Active Revision';
			$field2->column = 'revisionactiva';
			$field2->columntype = 'VARCHAR(3)';
			$field2->uitype = 56;
			$field2->typeofdata = 'C~O';
			$field2->displaytype = 4;
			$field2->presence = 0;
			$block->addField($field2);
			$adb->pquery('update '.$mod->basetable.' set revisionactiva=?', array('1'));
		}
		//create event handler
		$evhandler = $adb->pquery("select is_active,eventhandler_id from vtiger_eventhandlers where handler_class='UtilitiesEventsHandler'", array());
		if ($adb->num_rows($evhandler) > 0) {
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
		$wfquery = $adb->pquery("select workflow_id from com_vtiger_workflows where module_name=? and summary='Update Revision Fields'", array($this->moduleid));
		if ($adb->num_rows($wfquery) == 0) {
			require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
			require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
			require_once 'modules/com_vtiger_workflow/VTWorkflowApplication.inc';
			require_once 'include/events/SqlResultIterator.inc';
			$wm = new VTWorkflowManager($adb);
			$wf = $wm->newWorkflow($this->moduleid);
			$wf->description = 'Update Revision Fields';
			$wf->purpose = 'Initialize revision fields on first save';
			$wf->test = '';
			$wf->executionConditionAsLabel('ON_FIRST_SAVE');
			$wm->save($wf);
			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTUpdateFieldsTask', $wf->id);
			$task->summary = 'Update Revision Fields';
			$task->active=true;
			$task->field_value_mapping = '[{"fieldname":"revision","valuetype":"rawtext","value":"1"},{"fieldname":"revisionactiva","valuetype":"rawtext","value":"true:boolean"}]';
			$tm->saveTask($task);
		}
	}

	/**
	 *Invoked to disable widget for the module.
	 */
	public function disableRecordVersionForModule() {
		global $adb;
		$wfquery = $adb->pquery("select workflow_id from com_vtiger_workflows where module_name=? and summary='Update Revision Fields'", array($this->moduleid));
		if ($wfquery && $adb->num_rows($wfquery)>0) {
			$wfid = $adb->query_result($wfquery, 0, 0);
			$wm = new VTWorkflowManager($adb);
			$wm->delete($wfid);
		}
		$index = array_search($this->moduleid, $this->modulelist);
		unset($this->modulelist[$index]);
		if (count($this->modulelist)>0) {
			$module_del = implode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $this->modulelist);
		} else {
			$module_del = '';
		}
		$adb->pquery("update vtiger_globalvariable set module_list='$module_del' where globalvariableid=?", array($this->globalvar));
		$adb->pquery("update vtiger_businessactions set module_list='$module_del' where businessactionsid=?", array($this->baction));
	}
}
?>
