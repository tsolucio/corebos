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
include_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';

function CBQuestionMViewFunction($entityData) {
	global $adb;
	if ($entityData->WorkflowEvent==VTWorkflowManager::$ON_EVERY_SAVE ||
		$entityData->WorkflowEvent==VTWorkflowManager::$ON_FIRST_SAVE ||
		$entityData->WorkflowEvent==VTWorkflowManager::$ON_MODIFY ||
		$entityData->WorkflowEvent==VTWorkflowManager::$ON_DELETE
	) {
		$qs = $adb->pquery(
			'select cbquestionid, uniqueid, qname
				from vtiger_cbquestion
				inner join vtiger_crmentity on crmid=cbquestionid
				where deleted=0 and qmodule=? and mviewwf=?',
			array($entityData->getModuleName(), '1')
		);
		list($void, $eid) = explode('x', $entityData->getId());
		while ($cbq = $adb->fetch_array($qs)) {
			$vname = str_replace(' ', '_', $cbq['qname']);
			$adb->query('delete from '.$vname.' where '.$cbq['uniqueid'].'='.$eid);
		}
		if ($entityData->WorkflowEvent != VTWorkflowManager::$ON_DELETE) {
			include_once 'modules/cbQuestion/cbQuestion.php';
			$qs->MoveFirst();
			while ($cbq = $adb->fetch_array($qs)) {
				$vname = $vname = str_replace(' ', '_', $cbq['qname']);
				$sql = cbQuestion::getSQL($cbq['cbquestionid']);
				$sql = preg_replace('/where (vtiger_crmentity\.)?deleted\s*=\s*0/gi', 'where '.$cbq['uniqueid'].'='.$eid.' AND vtiger_crmentity.deleted=0', $sql);
				$adb->query('INSERT INTO '.$vname.' '.$sql);
			}
		}
	}
}
?>