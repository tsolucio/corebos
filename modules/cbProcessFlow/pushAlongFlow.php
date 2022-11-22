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

// PUSH ALONG FLOW ACTION: block://pushAlongFlow:modules/cbProcessFlow/pushAlongFlow.php:recordid=$RECORD$

require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbProcessFlow/cbProcessFlow.php';
global $currentModule;

class pushAlongFlow {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new pushAlongFlow_DetailViewBlock());
	}
}

class pushAlongFlow_DetailViewBlock extends DeveloperBlock {
	// Implement widget functionality
	protected $widgetName = 'pushAlongFlow';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $adb, $current_user;
		$this->context = $context;
		$recid = $this->getFromContext('id');
		if (empty($recid) || $recid == '$RECORD$') {
			return '';
		}
		$pflowid = $this->getFromContext('pflowid');
		$rs = $adb->pquery(
			'select pffield, pfcondition
			from vtiger_cbprocessflow
			inner join vtiger_crmentity on crmid=cbprocessflowid
			where deleted=0 and cbprocessflowid=?',
			array($pflowid)
		);
		if (!$rs || $adb->num_rows($rs)==0) {
			return getTranslatedString('LBL_NO_DATA');
		}
		$pfcondition = $rs->fields['pfcondition'];
		if (!empty($pfcondition) && !coreBOS_Rule::evaluate($pfcondition, $recid)) {
			return getTranslatedString('LBL_NO_DATA');
		}
		$processflow = $pflowid;
		$pffield = $rs->fields['pffield'];

		$module = getSalesEntityType($recid);
		if ($module == '') {
			return '';
		}

		$askifsure = $this->getFromContext('askifsure');
		$showas = strtolower($this->getFromContext('showas'));
		$queryGenerator = new QueryGenerator($module, $current_user);
		$queryGenerator->setFields(array($pffield));
		$queryGenerator->addCondition('id', $recid, 'e', $queryGenerator::$AND);
		$query = $queryGenerator->getQuery();
		$rs = $adb->query($query);
		$tabid = getTabId($module);
		$new_pffield = getColumnnameByFieldname($tabid, $pffield);
		$fromstate = $rs->fields[$new_pffield];
		if (empty($this->getFromContext('structure'))) {
			$screenvalues = '';
		} else {
			$screenvalues = json_decode($this->getFromContext('structure'), true);
		}
		$smarty = $this->getViewer();
		if ($showas=='buttons') {
			$smarty->assign('SHOW_GRAPH_AS', 'BUTTONS');
			$graph = cbProcessFlow::getDestinationStatesButtons($processflow, $fromstate, $recid, $askifsure, $screenvalues);
			if ($graph=='') {
				$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
				$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_NO_DATA'));
				$smarty->assign('APMSG_DIVID', uniqid());
				$graph = $smarty->fetch('applicationmessage.tpl');
			}
		} else {
			$smarty->assign('SHOW_GRAPH_AS', 'MERMAID');
			$graph = cbProcessFlow::getDestinationStatesGraph($processflow, $fromstate, $recid, $askifsure, $screenvalues);
			if ($graph=='') {
				$graph = "graph LR\n".'A("'.getTranslatedString('LBL_NO_DATA').'")';
			}
		}
		$smarty->assign('FLOWGRAPH', $graph);
		$mod = Vtiger_Module::getInstance($module);
		$fld = Vtiger_Field::getInstance($pffield, $mod);
		$smarty->assign('module', $module);
		$smarty->assign('uitype', $fld->uitype);
		$smarty->assign('fieldName', $fld->name);
		$smarty->assign('pflowid', $pflowid);
		$smarty->assign('isInEditMode', !empty($this->getFromContext('editmode')));
		return $smarty->fetch('modules/cbProcessFlow/PushAlongFlow.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$paf = new pushAlongFlow_DetailViewBlock();
	echo $paf->process($_REQUEST);
}
