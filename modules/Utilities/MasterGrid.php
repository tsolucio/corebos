<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************/
// block://mastergrid:modules/Utilities/MasterGrid.php:RECORDID=$RECORD$

require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbMap/processmap/cbMasterGrid.php';
require_once 'include/QueryGenerator/QueryGenerator.php';
global $currentModule;

class mastergrid {

	public static function getWidget($name) {
		return (new mastergrid_EditViewBlock());
	}
}

class mastergrid_EditViewBlock extends DeveloperBlock {

	protected $widgetName = 'masterGrid';

	public function process($context = false) {
		global $adb, $site_URL, $currentModule, $current_user;
		$this->context = $context;
		$smarty = $this->getViewer();
		$bmapname = $currentModule.'_MasterGrid';
		if (!empty($this->getFromContext('bmapname'))) {
			$bmapname = vtlib_purify($this->getFromContext('bmapname'));
		}
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
		if (!$cbMapid) {
			return false;
		}
		$BAInfo = json_decode($this->getFromContext('BusinessActionInformation'), true);
		$cbMap = cbMap::getMapByID($cbMapid);
		$MapMG = $cbMap->cbMasterGrid();
		$ID = $this->getFromContext('RECORDID');
		$smarty->assign('linkid', $BAInfo['linkid']);
		$smarty->assign('module', $MapMG['module']);
		$smarty->assign('relatedfield', $MapMG['relatedfield']);
		$smarty->assign('GridFields', $MapMG['fields']);
		$rows = array();
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'EditView') {
			$qfields = array('id');
			$matchFields = array();
			foreach ($MapMG['fields'] as $r) {
				$qfields[] = $r['name'];
				$matchFields[$r['columnname']] = array(
					$r['name'],
					$r['uitype'],
					isset($r['searchin']) ? $r['searchin'] : ''
				);
			}
			$record = $_REQUEST['record'];
			$qg = new QueryGenerator($MapMG['module'], $current_user);
			$qg->setFields($qfields);
			$qg->addReferenceModuleFieldCondition($currentModule, $MapMG['relatedfield'], 'id', $record, 'e', 'and');
			$sql = $qg->getQuery();
			$results = $adb->pquery($sql, array());
			$noOfRows = $adb->num_rows($results);
			if ($noOfRows > 0) {
				$entityField = getEntityField($MapMG['module']);
				while ($row = $adb->fetch_array($results)) {
					$currentRow = array();
					if (isset($row['smownerid'])) {
						$currentRow['assigned_user_id'] = $row['smownerid'];
						$currentRow['assigned_user_id_displayValue'] = getUserFullName($row['smownerid']);
					}
					foreach ($row as $key => $value) {
						if (!empty($matchFields[$key][2])) {
							$displayValue = getEntityName($matchFields[$key][2], $value);
							$currentRow[$matchFields[$key][0].'_displayValue'] = $displayValue[$value];
						}
						$currentRow[$matchFields[$key][0]] = $value;
					}
					$currentRow['id'] = $row[$entityField['entityid']];
					$rows[] = $currentRow;
				}
			}
		}
		$smarty->assign('GridData', json_encode($rows));
		return $smarty->fetch('MasterGrid.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new mastergrid_EditViewBlock();
	echo $smq->process($_REQUEST);
}
