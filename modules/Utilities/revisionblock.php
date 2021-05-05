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
require_once 'modules/Vtiger/DeveloperWidget.php';
global $currentModule;

class REVISIONBLOCK {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new REVISIONBLOCK_DetailViewBlock());
	}
}

class REVISIONBLOCK_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'REVISION';

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $adb;
		$this->context = $context;
		$smarty = $this->getViewer();
		$id = $this->getFromContext('record');
		$currmodule = $this->getFromContext('currmodule');
		include_once "modules/$currmodule/$currmodule.php";
		$focus = new $currmodule;
		$entityidfield = $focus->table_index;
		$table_name  = $focus->table_name;
		$queryfield = $adb->pquery(
			'select columnname from vtiger_field join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid where uitype=4 and name=?',
			array($currmodule)
		);
		if ($adb->num_rows($queryfield)==0) {
			$uniquefield = $focus->list_link_field;
		} else {
			$uniquefield = $adb->query_result($queryfield, 0, 0);
		}
		$seqnors = $adb->pquery("select $uniquefield from $table_name where $entityidfield=?", array($id));
		$seqno = $adb->query_result($seqnors, 0, 0);
		if ($focus->denormalized) {
			$dnjoin = 'INNER JOIN '.$focus->crmentityTable." as vtiger_crmentity ON vtiger_crmentity.crmid = $table_name.$entityidfield";
		} else {
			$dnjoin = "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $table_name.$entityidfield";
		}
		$revisiones=$adb->pquery(
			"select $entityidfield,revision,modifiedtime
			from $table_name "
			.$dnjoin
			." where deleted=0 and revisionactiva=0 and $uniquefield=? order by revision",
			array($seqno)
		);
		$number=$adb->num_rows($revisiones);
		$arr = array();
		for ($i=0; $i<$number; $i++) {
			if ($adb->query_result($revisiones, $i, 1) == null || $adb->query_result($revisiones, $i, 1) == '') {
				$revision = 0;
			} else {
				$revision = $adb->query_result($revisiones, $i, 1);
			}
			$arr[$i]['unique'] = $adb->query_result($revisiones, $i, 0);
			$arr[$i]['revision'] = sprintf("%'06s", $revision);
			$arr[$i]['modifiedtime'] = $adb->query_result($revisiones, $i, 2);
		}
		$smarty->assign('ID', $id);
		$smarty->assign('MODULE', $currmodule);
		$smarty->assign('NUMBER', $number);
		$smarty->assign('REVISIONES', $arr);
		return $smarty->fetch('modules/Utilities/revisionblock.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new REVISIONBLOCK_DetailViewBlock();
	echo $smq->process($_REQUEST);
}