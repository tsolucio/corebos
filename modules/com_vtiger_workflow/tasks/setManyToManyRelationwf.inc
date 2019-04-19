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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/Webservices/Retrieve.php';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'data/CRMEntity.php';
require_once 'include/Webservices/SetRelation.php';

class setManyToManyRelationwf extends VTTask {
	public $executeImmediately = true;
	public $relrecords = array();
	public $selAct = '';

	public function getFieldNames() {
		return array('relAction', 'idlist');
	}

	public function doTask(&$entity) {
		global $adb, $current_user;
		$idlistArr = explode(',', $this->idlist);
		$idlistNewArr = array();
		foreach ($idlistArr as $recId) {
			$recmod = getSalesEntityType($recId);
			$modres = $adb->query("SELECT id FROM vtiger_ws_entity WHERE name = '$recmod'");
			$modresid = $adb->query_result($modres, 0, 'id');
			$idlistNewArr[] = $modresid.'x'.$recId;
		}
		if ($this->relAction == 'addrel') {
			vtws_setrelation($entity->id, $idlistNewArr, $current_user);
		}
		if ($this->relAction == 'dellAllrel' || $this->relAction == 'delrel') {
			list($modid,$recid) = explode('x', $entity->getId());
			$recmod = $entity->getModuleName();
			$modobj = CRMEntity::getInstance($recmod);
			foreach ($idlistArr as $rid) {
				$return_module = getSalesEntityType($rid);
				$modobj->unlinkRelationship($recid, $return_module, $rid);
				$modobj->trackUnLinkedInfo($return_module, $rid, $recmod, $recid);
			}
		}
	}

	public function after_retrieve() {
		global $adb, $current_user;
		if (!empty($this->relAction)) {
			$this->selAct = $this->relAction;
		}
		if (!empty($this->idlist)) {
			$idlistArr = explode(',', $this->idlist);
			foreach ($idlistArr as $relcrmid) {
				$rmod = getSalesEntityType($relcrmid);
				$this->relrecords[] = array(
					'recid' => $relcrmid,
					'entityName' => array_values(getEntityName($rmod, $relcrmid))[0],
					'entityType' => getTranslatedString($rmod, $rmod),
				);
			}
		}
	}
}
?>
